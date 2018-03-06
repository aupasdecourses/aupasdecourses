///PROCESS JSON FOR SHOPS ///

//Make getJSON asachrynous, to get elements from geocode1 function
$.ajaxSetup({
  async: false
});

function getjson(url) {
  var data = [];
  $.getJSON(url, function(json) {
    $.each(json, function(index, entry) {
      data.push(entry);
    });
  });
  return data;
}

var redMarker = L.AwesomeMarkers.icon({
  icon: 'user',
  prefix: 'fa',
  markerColor: 'red',
});

var greenMarker = L.AwesomeMarkers.icon({
  icon: 'user',
  prefix: 'fa',
  markerColor: 'green',
});

/* affiche infos sur pop up commercant */
function setMarker(data) {
  var markers = [];
  $.each(data, function(i, d) {    
    if (d.lat !== "" && d.long !== "" && d.lat !== null && d.long !== null) {
      // if (d.postcode.substring(0, 3) == "750") {
      //   var marker = L.marker([d.lat, d.long], {
      //     icon: redMarker
      //   });
      // } else {
        var marker = L.marker([d.lat, d.long], {
          icon: greenMarker
        });
      // }
      marker.bindPopup(
        '<div>' + d.name + '</div><div>' 
        + d.street + ' ( ' + d.city + ' ) </div><div>'
        + d.phone + '</div><div>'
        + 'Horaires ouvertures : ' + d.timetable 
        + '</div>'
      );
      markers.push(marker);
    }
  });
  return markers;
}

//Tile
// var city = L.tileLayer('http://91.121.51.120/osm_tiles/{z}/{x}/{y}.png', {
//     attribution: '© OpenStreetMap contributors'
// });
var city = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
  attribution: '© OpenStreetMap contributors'
});

///Get and set markers


data = JSON.parse(JSON.stringify(json_data_for_shops));
var lieu = setMarker(data);


//Map Bounds
var group = new L.featureGroup(lieu);

//map
var map = L.map('mapShops', {
  maxZoom: 18,
  center: [48.8888208, 2.3194718],
  zoom: 10,
  layers: city
});

map.fitBounds(group.getBounds());

//Add Cluster with markers
// var layer_commercant = new L.layerGroup();
$.each(lieu, function(i, d) {
  d.addTo(map);
});
// var cluster = new L.MarkerClusterGroup();
// $.each(lieu,function(i,d){
// 	cluster.addLayer(d);
// });
// map.addLayer(cluster);


//Geolocalisation
map.locate({
  setView: false
});

function onLocationFound(e) {
  L.marker(e.latlng).addTo(map).bindPopup("Vous êtes ici!").openPopup();
}
map.on('locationfound', onLocationFound);

function onLocationError(e) {
  alert(e.message);
}
map.on('locationerror', onLocationError);

/* barre de recherche Google API */
var geocoder = new google.maps.Geocoder(google_key);

function googleGeocoding(text, callResponse) {
  geocoder.geocode({
    address: text
  }, callResponse);
}

function formatJSON(rawjson) {
  var json = {},
    key, loc, disp = [];
  for (var i in rawjson) {
    key = rawjson[i].formatted_address;

    loc = L.latLng(rawjson[i].geometry.location.lat(), rawjson[i].geometry.location.lng());

    json[key] = loc; //key,value format
  }
  return json;
}

var searchControl = new L.Control.Search({
  sourceData: googleGeocoding,
  formatData: formatJSON,
  markerLocation: true,
  autoType: false,
  autoCollapse: true,
  minLength: 2,
  zoom: 16,
});

map.addControl(searchControl);