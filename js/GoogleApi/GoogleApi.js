var autocomplete_addr_edit;
var autocomplete_billing;
var autocomplete_shipping;
var autocomplete_landingpage;

// this function is call as callback of gmaps (see template page/html/footer_js.phtml)
function GmapsLoaded()
{
  jQuery(document).trigger('GmapsLoaded');
}

function GoogleApiCustomerEdit() {
    autocomplete_addr_edit = new google.maps.places.Autocomplete((document.getElementById('street_1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_addr_edit.addListener('place_changed', function() {
        var place = autocomplete_addr_edit.getPlace();
        jQuery('#street_1').val(GA_GetData(place, 'street_number') + ' ' + GA_GetData(place, 'route'));
        jQuery('#city').val(GA_GetData(place, 'locality'));
        jQuery('#zip').val(GA_GetData(place, 'postal_code'));
        jQuery('#country').val(GA_GetData(place, 'country'));
    });
}

/**
 * GA_GetData 
 * 
 * @param object place 
 * @param string type : postal_code, country, locality (city), street_number, route, administrative_area_level_2 (region)
 * 
 * @return void
 */
function GA_GetData(place, type) {
  var types = [];
  for (var i=0; i < place.address_components.length; ++i) {
    types = place.address_components[i].types;
    for (var j = 0; j < types.length; ++j) {
      if (types[j] == type) {
        if (type == 'country') {
          return place.address_components[i].short_name;
        }
        return place.address_components[i].long_name;
      }
    }
  }
  return '';
}

function GoogleApiCustomcheck() {
    autocomplete_billing = new google.maps.places.Autocomplete((document.getElementById('billing:street1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_billing.addListener('place_changed', function() {
        var place = autocomplete_billing.getPlace();

        jQuery('#billing\\:street1').val(GA_GetData(place, 'street_number') + ' ' + GA_GetData(place, 'route'));
        jQuery('#billing\\:city').val(GA_GetData(place, 'locality'));
        jQuery('#billing\\:postcode').val(GA_GetData(place, 'postal_code'));
        jQuery('#billing\\:country').val(GA_GetData(place, 'country'));
        var region = GA_GetData(place, 'administrative_area_level_2');
        jQuery("#billing\\:region_id option").filter(function() {
          return jQuery(this).text() == region;
        }).prop('selected', true);
    });

    autocomplete_shipping = new google.maps.places.Autocomplete((document.getElementById('shipping:street1')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_shipping.addListener('place_changed', function() {
        var place = autocomplete_shipping.getPlace();
        jQuery('#shipping\\:street1').val(GA_GetData(place, 'street_number') + ' ' + GA_GetData(place, 'route'));
        jQuery('#shipping\\:city').val(GA_GetData(place, 'locality'));
        jQuery('#shipping\\:postcode').val(GA_GetData(place, 'postal_code'));
        jQuery('#shipping\\:country').val(GA_GetData(place, 'country'));
        var region = GA_GetData(place, 'administrative_area_level_2');
        jQuery("#shipping\\:region_id option").filter(function() {
          return jQuery(this).text() == region;
        }).prop('selected', true);
    });
}

function GoogleApiLandingpage() {
    autocomplete_landingpage = new google.maps.places.Autocomplete((document.getElementById('GoogleAutoCompleteInput')),{ types: ['geocode'], componentRestrictions: {country: "fr"}});
    autocomplete_landingpage.addListener('place_changed', function(){
        $j('#address-bar').addClass('has-value');
        $j('#GoogleAutoCompleteInput').siblings('button').show();
        var place = autocomplete_landingpage.getPlace();
        var zipcode = GA_GetData(place, 'postal_code');
        $j('#GoogleAutoCompleteZipcode').val(zipcode);
    });
    if ($j('#GoogleAutoCompleteInput').val() !== '') {
        $j('#GoogleAutoCompleteInput').next('button').show();
    }
}
