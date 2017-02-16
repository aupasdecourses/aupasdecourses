 
///PROCESS JSON FOR COMMERCANTS ///

 //Make getJSON asachrynous, to get elements from geocode1 function
 $.ajaxSetup({
   async: false
 });

function getjson(url) {
	var data=[];
	$.getJSON(url,function(json){
		$.each(json,function(index,entry){
			data.push(entry);
		});
	});
	return data;
}

var redMarker = L.AwesomeMarkers.icon({
    icon: 'coffee',
    markerColor: 'red'
  });

function setMarker(data){
	var lieu=new L.LayerGroup();
	$.each(data,function(i,d){
			if(d.lat!=="" && d.lon!==""){
				var marker = L.marker([d.lat, d.lon], {icon: redMarker}).bindPopup('<div>'+d.nom+'</div><div>'+d.adresse+'</div>').addTo(lieu);
			}
		});
	return lieu
}

function randomWaypoints(){
	var boundNS=[48.878673, 48.896845];
	var boundWE=[2.297206, 2.343726];
	var waypoints =[L.latLng(48.8888208,2.3194718)]
	var nbway=10;
	var lat=0
	var lon=0

	for (var i = 0; i<nbway; i++) {
		lat=Math.random() * (boundNS[1] - boundNS[0]) + boundNS[0];
		lon=Math.random() * (boundWE[1] - boundWE[0]) + boundWE[0];
		waypoints.push(L.latLng(lat,lon));
	}

	return waypoints

}


var city = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
});

///DISPLAY COMMERCANT LAYER ///
var data=getjson('../../../libs/json/location_commercant.json');
var lieu = setMarker(data);

//Routing Layer
// var Way =[];
// $.each(data,function(index,d){
//     if ( d.lat !== "" || d.lon !== ""){
//             Way.push(L.latLng(d.lat,d.lon));
//     }
// });

var Way =randomWaypoints();

var redMarker = L.AwesomeMarkers.icon({
    icon: 'coffee',
    markerColor: 'blue'
  });

var routing = L.Routing.control({
    waypoints: Way,
    routeWhileDragging: true,
    geocoder: L.Control.Geocoder.nominatim(),
    language: 'fr',
    distanceTemplate: '{value} {unit}',
    summaryTemplate: '<h2>Parcours: {name}</h2><h3>{distance}, {time}</h3>',
    show: false,
    collapsible: true,
    createMarker: function(i, wp) {
        var options = {
                draggable: this.draggableWaypoints,
                clickable:true,
                icon:redMarker
            },
            marker = L.marker(wp.latLng, options).bindPopup("Waypoint n°"+i);

        return marker;
    }
});

var map = L.map('map',{
    center: [48.8888208, 2.3194718],
    zoom:10,
    layers: [city,lieu,routing]
});

//Geolocalisation
map.locate({setView: false});
function onLocationFound(e) {L.marker(e.latlng).addTo(map).bindPopup("Vous êtes ici!").openPopup();}
map.on('locationfound', onLocationFound);
function onLocationError(e) {alert(e.message);}
map.on('locationerror', onLocationError);

var baseLayers = {
    "Paris": city,
};

var overlays = {
    "Commerçants": lieu,
    "Itinéraire":routing
};
routing.addTo(map);
L.control.layers(baseLayers, overlays).addTo(map);