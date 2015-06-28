@extends('layouts.blank')
@section('content')
{{ HTML::style('css/nv.d3.css') }}
<script src="http://d3js.org/d3.v3.min.js"></script>
{{HTML::script('js/nv.d3.js')}}
<style>
#content{height:350px;}
</style>
<body class='with-3d-shadow with-transitions' style="margin-top:100px;height:350px">
<svg id="chart1"></svg>

<script>
var histcatexplong = [
{
	"key" : "血壓" ,
		"values" : {{$blood}}
} ,
{
	"key" : "脈搏" ,
	"values" : {{$pulse}}
} ,
{
	"key" : "體溫" ,
	"values" : {{$tem}}
} ,
{
	"key" : "體重" ,
	"values" : {{$weight}}
} 
];

var colors = d3.scale.category20();

var chart;
nv.addGraph(function() {
		chart = nv.models.stackedAreaChart()
		.useInteractiveGuideline(true)
		.x(function(d) { return d[0] })
		.y(function(d) { return d[1] })
		.controlLabels({stacked: "Stacked"})
		.duration(300);

		chart.xAxis.tickFormat(function(d) { console.log(d); return d3.time.format('%x')(new Date(d)) });
		chart.yAxis.tickFormat(d3.format(',f'));

		chart.legend.vers('furious');

		chart.color(['#0199a4','#09d6e7','#2dd1dd','#5ef4eb']);

		d3.select('#chart1')
		.datum(histcatexplong)
		.transition().duration(1000)
		.call(chart)
		.each('start', function() {
			setTimeout(function() {
				d3.selectAll('#chart1 *').each(function() {
					if(this.__transition__)
					this.__transition__.duration = 1;
					})
				}, 0)
			});

nv.utils.windowResize(chart.update);
return chart;
});


</script>

@stop


