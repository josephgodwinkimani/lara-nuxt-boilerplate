<!-- components/PieChart.vue -->
<!-- D3.js pie chart for distribution data -->
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

  const width = chartContainer.value.clientWidth
  const height = props.height
  const radius = Math.min(width, height) / 2 - 20

  const svg = d3
    .select(chartContainer.value)
    .append('svg')
    .attr('width', width)
    .attr('height', height)

  const g = svg.append('g').attr('transform', `translate(${width / 2},${height / 2})`)

  const color = d3.scaleOrdinal(d3.schemeCategory10)

  const pie = d3
    .pie()
    .value((d) => d.count)
    .sort(null)

  const arc = d3.arc().innerRadius(0).outerRadius(radius)

  const arcs = g.selectAll('.arc').data(pie(props.data)).enter().append('g').attr('class', 'arc')

  arcs
    .append('path')
    .attr('d', arc)
    .attr('fill', (d, i) => color(i))

  arcs
    .append('text')
    .attr('transform', (d) => `translate(${arc.centroid(d)})`)
    .attr('text-anchor', 'middle')
    .text((d) => `${d.data.status}: ${d.data.percentage}%`)
    .style('font-size', '12px')
    .style('fill', 'white')
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