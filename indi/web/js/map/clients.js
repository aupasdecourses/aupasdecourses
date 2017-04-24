///PROCESS JSON FOR CUSTOMERS ///

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
    icon: 'user',
    prefix:'fa',
    markerColor: 'red',
  });

var greenMarker = L.AwesomeMarkers.icon({
    icon: 'user',
    prefix:'fa',
    markerColor: 'green',
  });

/* affiche infos sur pop up client */
function setMarker(data){
	var markers = [];
	$.each(data,function(i,d){
			if(d.lat!=="" && d.lon!=="" && d.lat!==null && d.lon!==null){
				if(d.addr.substring(0, 3) == "750"){
					var marker = L.marker([d.lat, d.lon], {icon: redMarker});
				}else{
					var marker = L.marker([d.lat, d.lon], {icon: greenMarker});
				}
				marker.bindPopup('<div>'+d.nom_client+'</div><div>'+d.ville+'</div><div>'+d.addr+'</div><div>Lat: '+d.lat+'</div><div>Lon:'+d.lon+'</div>');
				markers.push(marker);
			}
		});
	return markers;
}

//Tile
var city = L.tileLayer('http://91.121.51.120/osm_tiles/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
});

///Get and set markers


data = JSON.parse(JSON.stringify(json_data));
var lieu = setMarker(data);

//Map Bounds
var group = new L.featureGroup(lieu);

//map
var map = L.map('map',{
	maxZoom: 18,
    center: [48.8888208, 2.3194718],
    zoom:10,
    layers: city
});

map.fitBounds(group.getBounds());

//Add Cluster with markers
var cluster = new L.MarkerClusterGroup();
$.each(lieu,function(i,d){
	cluster.addLayer(d);
});
map.addLayer(cluster);


//Geolocalisation
map.locate({setView: false});
function onLocationFound(e) {L.marker(e.latlng).addTo(map).bindPopup("Vous êtes ici!").openPopup();}
map.on('locationfound', onLocationFound);
function onLocationError(e) {alert(e.message);}
map.on('locationerror', onLocationError);

/* barre de recherche Google API */
var geocoder = new google.maps.Geocoder();
function googleGeocoding(text, callResponse)
{
	geocoder.geocode({address: text}, callResponse);
}
function formatJSON(rawjson)
{
	var json = {},
	key, loc, disp = [];
	for(var i in rawjson)
	{
		key = rawjson[i].formatted_address;

		loc = L.latLng( rawjson[i].geometry.location.lat(), rawjson[i].geometry.location.lng() );

		json[ key ]= loc;	//key,value format
	}
	return json;
}
map.addControl( new L.Control.Search({
	sourceData: googleGeocoding,
	formatData: formatJSON,
	markerLocation: true,
	autoType: false,
	autoCollapse: true,
	minLength: 2
}) );

