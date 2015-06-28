@extends('layouts.blank')
@section('content')
<div style="text-align:right">最後更新時間：{{$time}}</div>
<div id="gmap" style="width:100%;height:600px;"></div>

<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="http://d3js.org/topojson.v1.min.js"></script>
<script>
d3.json("../json/twCounty2010merge.topo.json", function (error, data) {
		topo_tem = topojson.feature(data, data.objects.layer1);
		topo_rain = topojson.feature(data, data.objects.layer1);
		topo_weather = topojson.feature(data, data.objects.layer1);

//		prj = d3.geo.mercator().center([120.979531, 23.978567]).scale(5000);
//		path = d3.geo.path().projection(prj);
	build = function(svg,prj,path){
		colorMap = d3.scale.linear().domain([0,50]).range(["#24e2b0","#029156"]);
		var rain=JSON.parse('{{$rain}}');
		var rain2 = new Array();
		for(var i = 0, len = rain.length; i < len; i+=1) 
			rain2[rain[i].COUNTYNAME] = rain[i].value;

		for(var i = 0, len = topo_rain.features.length; i < len; i+=1) {
			var rain_name=topo_rain.features[i].properties.name;
			topo_rain.features[i].properties.value = (rain2[rain_name] ? rain2[topo_rain.features[i].properties.name] : 0);
		}
		/* blocks */
		blocks = d3.select("svg").selectAll("path").data(topo_rain.features).enter().append("path").attr("fill",function(d){        
			return colorMap(d.properties.value); 
			}).attr("d", path).attr("opacity",0.6);    

		var tem=JSON.parse('{{$tem}}');
		var tem2 = new Array();
		for(var i = 0, len = tem.length; i < len; i+=1) 
			tem2[tem[i].COUNTYNAME] = tem[i].value-270;

		for(var i = 0, len = topo_tem.features.length; i < len; i+=1) {
			var tem_name=topo_tem.features[i].properties.name;
			topo_tem.features[i].properties.value = (tem2[tem_name] ? tem2[topo_tem.features[i].properties.name] : 0);
		}
		/* dorling */
		radiusMap=d3.scale.linear().domain([0,50]).range([0,70]);

		dorling = d3.select("svg").selectAll("circle").data(topo_tem.features).enter().append("g");
		dorling.append("circle")
			.each(function(it){
					it.properties.r = (it.properties.value ? radiusMap(Math.sqrt(it.properties.value)) : 0 );
					it.properties.c = path.centroid(it);
					it.properties.x=400;
					it.properties.y=300;
					it.properties.color="#eadd00";
					})
		.attr("cx", function(it){return it.properties.c[0];})
			.attr("cy", function(it){return it.properties.c[1];})
			.attr("r", function(it){return it.properties.r;})
			.attr("fill", function(it){return it.properties.color;})
			.on('mouseover', function(it){console.log('ya'); return $(this).attr('fill',"purple");});
		dorling
			.append("text")
			.each(function(it){it.properties.c = path.centroid(it);})
			.attr("x", function(it){return it.properties.c[0]+10;})
			.attr("y", function(it){return it.properties.c[1]+10;})
			.style("fill", "#2d2d2d")
			.style("font-weight", "bold")
			.text(function(it){return ((it.properties.value+270)/10.0)+"°C";});
			

		var weather=JSON.parse('{{$weather}}');
		var weather2 = new Array();
		for(var i = 0, len = weather.length; i < len; i+=1) 
			weather2[weather[i].COUNTYNAME] = weather[i].value;
		for(var i = 0, len = topo_weather.features.length; i < len; i+=1) {
			var weather_name=topo_weather.features[i].properties.name;
			topo_weather.features[i].properties.value = (weather2[weather_name] ? weather2[topo_weather.features[i].properties.name] : 0);
		}
		/* image */
		   dorling_i = d3.select("svg").selectAll("rect").data(topo_weather.features).enter().append("svg:image")
		   .each(function(it){
			it.properties.c = path.centroid(it);
		   it.properties.x=400;
		   it.properties.y=300;
		   if(it.properties.value==8) it.properties.img="img/08.png";
		   if(it.properties.value==12) it.properties.img="img/12.png";
		   if(it.properties.value==34) it.properties.img="img/34.png";
		   })
		   .attr("x", function(it){return it.properties.c[0]-10+590;})
		   .attr("y", function(it){return it.properties.c[1]-10+110;})
		   .attr("xlink:href",function(it){return it.properties.img;})
		   .attr("width", "20px")
		   .attr("height", "20px");
		 /**/
	};

		gm = {
			opt: { center: new google.maps.LatLng(23.8,121.0), zoom: 7.5, minZoom: 7},
	 		ov: new google.maps.OverlayView(),
		};
		gm.map = new google.maps.Map($("#gmap")[0], gm.opt);
		gm.marker = new google.maps.Marker({position:new google.maps.LatLng(25.0141242,121.426906), map:gm.map});
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
			coord1 = prj2([110,27]);
			coord2 = prj2([130,21]);
			w = coord2[0] - coord1[0];
			h = coord2[1] - coord1[1];
			gm.svg.style("position", "absolute")
				.style("top", coord1[1]+"px")
				.style("left", coord1[0]+"px")
				.style("width", w+"px")
				.style("height",h+"px")
				.attr("viewBox","0 0 "+w+" "+h);
			gm.svg.selectAll("path").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")").attr("d",path2);
			gm.svg.selectAll("image").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")")
				.each(function(it) {
						// use sqrt root for correct mapping from value to area
						it.properties.r = radiusMap(Math.sqrt(it.properties.value));
						it.properties.c = centroid = path2.centroid(it);
						})
			.attr("x",function(it) { return it.properties.x + it.properties.c[0] - 420; })
				.attr("y",function(it) { return it.properties.y + it.properties.c[1] - 320; });
			gm.svg.selectAll("circle").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")")
				.each(function(it) {
						// use sqrt root for correct mapping from value to area
						it.properties.r = radiusMap(Math.sqrt(it.properties.value));
						it.properties.c = centroid = path2.centroid(it);
						})
			.attr("cx",function(it) { return it.properties.x + it.properties.c[0] - 400; })
				.attr("cy",function(it) { return it.properties.y + it.properties.c[1] - 300; });
			gm.svg.selectAll("text").attr("transform","translate("+(-coord1[0])+" "+(-coord1[1])+")")
				.each(function(it) {
						// use sqrt root for correct mapping from value to area
						it.properties.c = centroid = path2.centroid(it);
						})
			.attr("x",function(it) { return it.properties.x + it.properties.c[0] - 410; })
				.attr("y",function(it) { return it.properties.y + it.properties.c[1] - 295; });

		};
		gm.ov.setMap(gm.map);
		google.maps.event.addListener(gm.map, "zoom_changed", function() {
			//	force.start();
				});

});
</script>

@stop


