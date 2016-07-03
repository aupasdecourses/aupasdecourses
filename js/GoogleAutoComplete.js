// globals declaration
var placeSearch;
var	autocomplete;

// Google handle, triger, hook
function initGoogleAutocomplete() {
	autocomplete = new google.maps.places.Autocomplete(
			/** @type {!HTMLInputElement} */(document.getElementById('GoogleAutoComplete')),
			{types: ['geocode']});

	autocomplete.addListener('place_changed', fillInAddress);
}

function getPlaceKey(place, key) {
	for (var obj in place.address_components) {
		for (var t in place.address_components[obj].types) {
			if (place.address_components[obj].types[t] == key) {
				return place.address_components[obj].short_name;
			}
		}
	}
	return 'not found';
}

function fillInAddress() {
	var	place = autocomplete.getPlace();
	var	zipcode = getPlaceKey(place, 'postal_code');

	console.debug(place);
	if (zipcode != 'not found') {
		document.getElementById('GoogleAutoCompleteZipcode').value = zipcode;
	}
}

// input onFocus
function GoogleGeolocate() {
	if (navigator.geolocation) {
		navigator.geolocation.getCurrentPosition(function(position) {
			var geolocation = {
				lat: position.coords.latitude,
				lng: position.coords.longitude
			};
			var circle = new google.maps.Circle({
				center: geolocation,
				radius: position.coords.accuracy
			});
			autocomplete.setBounds(circle.getBounds());
		});
	}
}
