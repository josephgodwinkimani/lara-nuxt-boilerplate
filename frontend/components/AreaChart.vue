<!-- components/AreaChart.vue -->
<!-- D3.js area chart for activity metrics -->
<template>
  <div ref="chartContainer"></div>
</template>

<script setup>
import * as d3 from 'd3'

// Define the component's props that can be passed from its parent component
const props = defineProps({
  data: Array,
  height: { type: Number, default: 300 },
})

// Reactive reference to the chart container DOM element
const chartContainer = ref(null)

// Draw Chart
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
    .domain([0, d3.max(data, (d) => d.active_users)])
    .range([height, 0])

  const area = d3
    .area()
    .x((d) => x(d.date))
    .y0(height)
    .y1((d) => y(d.active_users))
    .curve(d3.curveMonotoneX)

  g.append('g').attr('transform', `translate(0,${height})`).call(d3.axisBottom(x))

  g.append('g').call(d3.axisLeft(y))

  g.append('path').datum(data).attr('fill', '#3b82f6').attr('fill-opacity', 0.6).attr('d', area)
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