
		var map = L.map('map').setView([40.72, -74.2], 13);

//		L.tileLayer('https://{s}.tiles.mapbox.com/v3/{id}/{z}/{x}/{y}.png', {
//			maxZoom: 18,
//			attribution: 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, ' +
//				'<a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, ' +
//				'Imagery © <a href="http://mapbox.com">Mapbox</a>',
//			id: 'examples.map-i86knfo3'
//		}).addTo(map);


		var imageUrl = 'http://www.lib.utexas.edu/maps/historical/newark_nj_1922.jpg',
	    imageBounds = [[40.712216, -74.22655], [40.773941, -74.12544]];

		L.imageOverlay(imageUrl, imageBounds).addTo(map);
		
//		L.marker([51.5, -0.09]).addTo(map)
//			.bindPopup("<b>Hello world!</b><br />I am a popup.").openPopup();
//
//		L.circle([51.508, -0.11], 500, {
//			color: 'red',
//			fillColor: '#f03',
//			fillOpacity: 0.5
//		}).addTo(map).bindPopup("I am a circle.");
//
//		L.polygon([
//			[51.509, -0.08],
//			[51.503, -0.06],
//			[51.51, -0.047]
//		]).addTo(map).bindPopup("I am a polygon.");
//
//
//		var popup = L.popup();
//
//		function onMapClick(e) {
//			popup
//				.setLatLng(e.latlng)
//				.setContent("You clicked the map at " + e.latlng.toString())
//				.openOn(map);
//		}
//
//		map.on('click', onMapClick);
