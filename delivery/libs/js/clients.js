 
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
    icon: 'user',
    prefix:'fa',
    markerColor: 'red',
  });

var greenMarker = L.AwesomeMarkers.icon({
    icon: 'user',
    prefix:'fa',
    markerColor: 'green',
  });

function setMarker(data){
	var markers = [];
	$.each(data,function(i,d){
			if(d.lat!=="" && d.lon!=="" && d.lat!==null && d.lon!==null){
				if(d.Adresse.substring(0, 3) == "750"){
					var marker = L.marker([d.lat, d.lon], {icon: redMarker});
				}else{
					var marker = L.marker([d.lat, d.lon], {icon: greenMarker});
				}
				marker.bindPopup('<div>'+d.Nom+'</div><div>'+d.Adresse+'</div><div>Lat: '+d.lat+'</div><div>Lon:'+d.lon+'</div>');
				markers.push(marker);
			}
		});
	return markers;
}

//Tile
var city = L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
    attribution: '© OpenStreetMap contributors'
});

///Get and set markers
var data=getjson('../../../libs/json/clients.json');
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