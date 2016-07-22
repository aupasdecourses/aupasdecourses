var autocomplete_addr_edit;
var autocomplete_billing;
var autocomplete_shipping;
var autocomplete_landingpage;

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

function GoogleApiCustomerEdit() {
    autocomplete_addr_edit = new google.maps.places.Autocomplete((document.getElementById('street_1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_addr_edit.addListener('place_changed', function() {
        var place_shipping = autocomplete_addr_edit.getPlace();
        jQuery('#street_1').val(place_shipping.address_components[0]['long_name'] + ' ' + place_shipping.address_components[1]['long_name']);
        jQuery('#city').val(place_shipping.address_components[2]['long_name']);
        jQuery('#zip').val(place_shipping.address_components[6]['long_name']);
        jQuery('#country').val(place_shipping.address_components[5]['short_name']);
    });
}

function GoogleApiCustomcheck() {
    autocomplete_billing = new google.maps.places.Autocomplete((document.getElementById('billing:street1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_billing.addListener('place_changed', function() {
        var place_billing = autocomplete_billing.getPlace();
        jQuery('#billing\\:street1').val(place_billing.address_components[0]['long_name'] + ' ' + place_billing.address_components[1]['long_name']);
        jQuery('#billing\\:city').val(place_billing.address_components[2]['long_name']);
        jQuery('#billing\\:postcode').val(place_billing.address_components[6]['long_name']);
        jQuery('#billing\\:country').val(place_billing.address_components[5]['long_name']);
        jQuery("#billing\\:region_id").val(place_billing.address_components[4]['long_name']);;
    });

    autocomplete_shipping = new google.maps.places.Autocomplete((document.getElementById('shipping:street1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_shipping.addListener('place_changed', function() {
        var place_shipping = autocomplete_shipping.getPlace();
        jQuery('#shipping\\:street1').val(place_shipping.address_components[0]['long_name'] + ' ' + place_shipping.address_components[1]['long_name']);
        jQuery('#shipping\\:city').val(place_shipping.address_components[2]['long_name']);
        jQuery('#shipping\\:postcode').val(place_shipping.address_components[6]['long_name']);
        jQuery('#shipping\\:country').val(place_shipping.address_components[5]['long_name']);
        jQuery("#shipping\\:region_id").val(place_shipping.address_components[4]['long_name']);
    });
}

var googleRsl = false;

function GoogleApiLandingpage() {
    autocomplete_landingpage = new google.maps.places.Autocomplete((document.getElementById('GoogleAutoComplete')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_landingpage.addListener('place_changed', function() {
        var place = autocomplete_landingpage.getPlace();
		var	zipcode = getPlaceKey(place, 'postal_code');

		if (zipcode != 'not found') {
			document.getElementById('GoogleAutoCompleteZipcode').value = zipcode;
			googleRsl = true;
		} else {
			googleRsl = false;
			$j('#form-quartier').addClass('has-error');
			alert('veuillez entree une adresse complete');
		}
    });
}
