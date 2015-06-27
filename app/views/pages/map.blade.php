@extends('layouts.blank')
@section('content')
<style>
#map{width:600px; height:450px; margin-left:-230px; margin-top:-100px;}
</style>
<div id="gmap" style="width:100%;height:400px;margin:0 auto"></div>

<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://d3js.org/topojson.v1.min.js"></script>
<script>
d3.json("../json/twCounty2010merge.topo.json", function (error, data) {
		topo = topojson.feature(data, data.objects.layer1);

//		prj = d3.geo.mercator().center([120.979531, 23.978567]).scale(5000);
//		path = d3.geo.path().projection(prj);
	build = function(svg,prj,path){
		colorMap = d3.scale.linear().domain([0,32]).range(["#000","#f00"]);
		var popData=JSON.parse('[{"COUNTYNAME":"南投縣","population":6},{"COUNTYNAME":"台中市","population":30},{"COUNTYNAME":"台北市","population":49},{"COUNTYNAME":"台南市","population":17},{"COUNTYNAME":"台東縣","population":2},{"COUNTYNAME":"嘉義市","population":6},{"COUNTYNAME":"嘉義縣","population":2},{"COUNTYNAME":"基隆市","population":1},{"COUNTYNAME":"宜蘭縣","population":3},{"COUNTYNAME":"屏東縣","population":10},{"COUNTYNAME":"彰化縣","population":4},{"COUNTYNAME":"新北市","population":34},{"COUNTYNAME":"新竹市","population":6},{"COUNTYNAME":"新竹縣","population":1},{"COUNTYNAME":"桃園市","population":28},{"COUNTYNAME":"澎湖縣","population":1},{"COUNTYNAME":"花蓮縣","population":6},{"COUNTYNAME":"苗栗縣","population":5},{"COUNTYNAME":"金門縣","population":1},{"COUNTYNAME":"雲林縣","population":8},{"COUNTYNAME":"高雄市","population":32}]');

		var population = new Array();

		for(var i = 0, len = popData.length; i < len; i+=1) {
		population[popData[i].COUNTYNAME] = popData[i].population;
		}

		for(var i = 0, len = topo.features.length; i < len; i+=1) {
		topo.features[i].properties.value = population[topo.features[i].properties.name]
		}

		/* blocks */
		blocks = d3.select("svg").selectAll("path").data(topo.features).enter().append("path").attr("fill",function(d){        
			return colorMap(d.properties.value); 
			}).attr("d", path).attr("opacity",0.5);    

		/* dorling */
		radiusMap=d3.scale.linear().domain([0,32]).range([0,70]);

		dorling = d3.select("svg").selectAll("circle").data(topo.features).enter().append("circle")
			.each(function(it){
					it.properties.r = (it.properties.value ? radiusMap(Math.sqrt(it.properties.value)) : 0 );
					it.properties.c = path.centroid(it);
					it.properties.x=400;
					it.properties.y=300;
					it.properties.color="blue";
					})
		.attr("cx", function(it){return it.properties.c[0];})
			.attr("cy", function(it){return it.properties.c[1];})
			.attr("r", function(it){return it.properties.r;})
			.attr("fill", function(it){return it.properties.color;})
			.on('mouseover', function(it){console.log('ya'); return $(this).attr('fill',"purple");});

		/* image 
		   dorling = d3.select("svg#map").selectAll("circle").data(topo.features).enter().append("svg:image")
		   .attr("xlink:href","img/Poop.png")
		   .each(function(it){
		   it.properties.x=400;
		   it.properties.y=300;
		   })
		   .attr("x", function(it){return it.properties.c[0]-10;})
		   .attr("y", function(it){return it.properties.c[1]-10;})
		   .attr("width", "20px")
		   .attr("height", "20px");
		 */
	};

		gm = {
			opt: { center: new google.maps.LatLng(23.8,121.0), zoom: 7, minZoom: 7},
	 		ov: new google.maps.OverlayView()
		};
		gm.map = new google.maps.Map($("#gmap")[0], gm.opt);
		gm.ov.onAdd = function() {
			gm.svg = d3.select(this.getPanes().overlayLayer).append("svg");
			prj2 = googleProjection(gm.ov.getProjection());
			path2 = d3.geo.path().projection(prj2);
			build(gm.svg, prj2, path2);
		};
		function googleProjection(prj) {
			return function(lnglat) {
				ret = prj.fromLatLngToDivPixel(new google.maps.LatLng(lnglat[1],lnglat[0]))
					return [ret.x, ret.y]
			};
		}
		gm.ov.draw = function() {
			prj2 = googleProjection(gm.ov.getProjection());
			path2 = d3.geo.path().projection(prj2);
			coord1 = prj2([120,27]);
			coord2 = prj2([130,22]);
			w = coord2[0] - coord1[0];
			h = coord2[1] - coord1[1];
			gm.svg.style("position", "absolute")
				.style("top", coord1[1]+"px")
				.style("left", coord1[0]+"px")
				.style("width", w+"px")
				.style("height",h+"px")
				.attr("viewBox","0 0 "+w+" "+h);
			gm.svg.selectAll("path").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")").attr("d",path2);
			gm.svg.selectAll("circle").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")")
				.each(function(it) {
						// use sqrt root for correct mapping from value to area
						it.properties.r = radiusMap(Math.sqrt(it.properties.value));
						it.properties.c = centroid = path2.centroid(it);
						})
			.attr("cx",function(it) { return it.properties.x + it.properties.c[0] - 400; })
				.attr("cy",function(it) { return it.properties.y + it.properties.c[1] - 300; });

		};
		gm.ov.setMap(gm.map);
		google.maps.event.addListener(gm.map, "zoom_changed", function() {
			//	force.start();
				});

});
</script>

@stop


