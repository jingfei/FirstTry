@extends('layouts.blank')
@section('content')
<style>
#map-canvas{width:100%; height:600px;}
</style>
<div id="map-canvas"></div>

<script>
// The following example creates a marker in Stockholm, Sweden
// using a DROP animation. Clicking on the marker will toggle
// the animation between a BOUNCE animation and no animation.

var taipei = new google.maps.LatLng(23.978567,120.979531); //, 23.978567);
var parliament = new google.maps.LatLng(23.978567,120.979531); //, 23.978567);
var markers=[];
var map;

var neighborhoods = [
	new google.maps.LatLng(23.00,120.23),
    new google.maps.LatLng(23.04,120.25),
	new google.maps.LatLng(23.43,120.36),
	new google.maps.LatLng(23.96,120.50),
	new google.maps.LatLng(24.20,120.65),
	new google.maps.LatLng(24.38,120.75),
	new google.maps.LatLng(24.67,120.90),
	new google.maps.LatLng(24.87,121.06),
	new google.maps.LatLng(25.06,121.37),
	new google.maps.LatLng(25.04,121.54),
];

function initialize() {
	var mapOptions = {
	  zoom: 7.5,
	  center: taipei
	};

	map = new google.maps.Map(document.getElementById('map-canvas'),
			mapOptions);
	marker = new google.maps.Marker({position:new google.maps.LatLng(25.0141242,121.426906), map:map});

	for (var i = 0; i < neighborhoods.length; i++) {
		addMarkerWithTimeout(neighborhoods[i], (i+1) * 300);
	}

//	marker = new google.maps.Marker({
//	map:map,
//	draggable:true,
//	animation: google.maps.Animation.DROP,
//	position: parliament
//});



google.maps.event.addListener(marker, 'click', toggleBounce);
}
var j=1;
function addMarkerWithTimeout(position, timeout) {
	  window.setTimeout(function() {
		  	  var foot=(j==2?"left":"right");
		      markers.push(new google.maps.Marker({
				    position: position,
					map: map,
					icon: "img/food"+foot+".png",
			        animation: google.maps.Animation.DROP
			  }));
			  if(++j>2) j=1;
			  
	  }, timeout);
}


function toggleBounce() {

	if (marker.getAnimation() != null) {
		marker.setAnimation(null);
	} else {
		marker.setAnimation(google.maps.Animation.BOUNCE);
	}
}

google.maps.event.addDomListener(window, 'load', initialize);

</script>

@stop


