// Centralized API client with multi-format support
import { useFetch, useRuntimeConfig, type UseFetchOptions } from 'nuxt/app'
import yaml from 'js-yaml'
import Papa from 'papaparse'
import { ref } from 'vue'
import { useAuthStore } from '~/stores/auth'
import { useNotifications } from './useNotifications'

interface ApiResponse<T = any> {
  data: T
  meta?: any
  errors?: any[]
}

export const useApi = () => {
  const config = useRuntimeConfig()
  const { showNotification } = useNotifications()

  const apiRequest = async <T = any>(
    endpoint: string,
    options: UseFetchOptions<T> & {
      format?: 'json' | 'xml' | 'yaml' | 'csv'
    } = {}
  ) => {
    const { format = 'json', ...fetchOptions } = options

    const headers: Record<string, string> = {
      Accept: getAcceptHeader(format),
      'Content-Type': 'application/json',
      ...((fetchOptions.headers as Record<string, string>) || {}),
    }

    const finalOptions: UseFetchOptions<T> = {
      ...fetchOptions,
      baseURL: config?.public?.apiBase,
      headers,
      onResponseError({ response }) {
        const error = response._data?.message || 'An error occurred'
        showNotification(error, 'error')
      },
    }

    try {
      const { data, error, refresh, pending } = await useFetch<T>(endpoint, finalOptions)

      if (error.value) {
        throw new Error(error.value.message || 'API request failed')
      }

      // Parse non-JSON responses
      if (format !== 'json' && typeof data.value === 'string') {
        const parsed = parseResponse(data.value as string, format)
        return { data: ref(parsed), error, refresh, pending }
      }

      return { data, error, refresh, pending }
    } catch (err) {
      console.error('API Error:', err)
      throw err
    }
  }

  const downloadFile = async (
    endpoint: string,
    filename: string,
    format: 'csv' | 'xml' | 'yaml'
  ) => {
    try {
      const authStore = useAuthStore()
      const headers: Record<string, string> = {
        Accept: getAcceptHeader(format),
      }

      if (authStore.token) {
        headers['Authorization'] = `Bearer ${authStore.token}`
      }

      const response = await $fetch(endpoint, {
        baseURL: config?.public?.apiBase,
        headers,
        responseType: 'blob',
      })

      const url = URL.createObjectURL(response as Blob)
      const link = document.createElement('a')
      link.href = url
      link.download = `${filename}.${format}`
      document.body.appendChild(link)
      link.click()
      document.body.removeChild(link)
      URL.revokeObjectURL(url)

      showNotification(`File downloaded: ${filename}.${format}`, 'success')
    } catch (err) {
      console.error('Download Error:', err)
      showNotification('Download failed', 'error')
    }
  }

  return {
    apiRequest,
    downloadFile,
  }
}

function getAcceptHeader(format: string): string {
  switch (format) {
    case 'xml':
      return 'application/xml'
    case 'yaml':
      return 'application/x-yaml'
    case 'csv':
      return 'text/csv'
    default:
      return 'application/json'
  }
}

function parseResponse(response: string, format: string) {
  try {
    switch (format) {
      case 'xml':
        const parser = new DOMParser()
        const doc = parser.parseFromString(response, 'application/xml')
        return xmlToJson(doc.documentElement)

      case 'yaml':
        return yaml.load(response)

      case 'csv':
        const parsed = Papa.parse(response, { header: true, dynamicTyping: true })
        return parsed.data

      default:
        return JSON.parse(response)
    }
  } catch (err) {
    console.error('Parse Error:', err)
    return response
  }
}

function xmlToJson(xml: Element): any {
  const result: any = {}

  if (xml.attributes.length > 0) {
    result['@attributes'] = {}
    for (let i = 0; i < xml.attributes.length; i++) {
      const attr = xml.attributes[i]
      result['@attributes'][attr.nodeName] = attr.nodeValue
    }
  }

  if (xml.hasChildNodes()) {
    for (let i = 0; i < xml.childNodes.length; i++) {
      const child = xml.childNodes[i]

      if (child.nodeType === 1) {
        // Element node
        const nodeName = child.nodeName
        if (result[nodeName] === undefined) {
          result[nodeName] = xmlToJson(child as Element)
        } else {
          if (!Array.isArray(result[nodeName])) {
            result[nodeName] = [result[nodeName]]
          }
          result[nodeName].push(xmlToJson(child as Element))
        }
      } else if (child.nodeType === 3 && child.nodeValue?.trim()) {
        // Text node
        return child.nodeValue.trim()
      }
    }
  }

  return result
}
