<!-- components/LineChart.vue -->
<!--  D3.js line chart for trend data -->
<template>
    <div ref="chartContainer"></div>
</template>

<script setup>
import * as d3 from 'd3'

const props = defineProps({
  data: Array,
  height: { type: Number, default: 300 },
})

const chartContainer = ref(null)

const drawChart = () => {
  if (!props.data || !chartContainer.value) return

  d3.select(chartContainer.value).selectAll('*').remove()

  const margin = { top: 20, right: 30, bottom: 40, left: 40 }
  const width = chartContainer.value.clientWidth - margin.left - margin.right
  const height = props.height - margin.top - margin.bottom

  const svg = d3
    .select(chartContainer.value)
    .append('svg')
    .attr('width', width + margin.left + margin.right)
    .attr('height', height + margin.top + margin.bottom)

  const g = svg.append('g').attr('transform', `translate(${margin.left},${margin.top})`)

  const parseDate = d3.timeParse('%Y-%m-%d')
  const data = props.data.map((d) => ({
    ...d,
    date: parseDate(d.date),
  }))

  const x = d3
    .scaleTime()
    .domain(d3.extent(data, (d) => d.date))
    .range([0, width])

  const y = d3
    .scaleLinear()
    .domain([0, d3.max(data, (d) => d.cumulative)])
    .range([height, 0])

  const line = d3
    .line()
    .x((d) => x(d.date))
    .y((d) => y(d.cumulative))
    .curve(d3.curveMonotoneX)

  g.append('g').attr('transform', `translate(0,${height})`).call(d3.axisBottom(x))

  g.append('g').call(d3.axisLeft(y))

  g.append('path')
    .datum(data)
    .attr('fill', 'none')
    .attr('stroke', '#3b82f6')
    .attr('stroke-width', 2)
    .attr('d', line)
}

onMounted(() => {
  drawChart()
})

watch(
  () => props.data,
  () => {
    drawChart()
  },
  { deep: true }
)
</script>
