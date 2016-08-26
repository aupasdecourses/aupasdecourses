document.observe("dom:loaded", function() {
    // var addressField = jQuery('#billing\\:street1').closest('li');
    // var autocompleteField = addressField.clone();
    // autocompleteField.find('input').attr('id', 'autocomplete').attr('name', 'aoe_addressautocomplete').removeClass('required-entry').attr('title', null);
    // autocompleteField.find('label').attr('class', null).attr('for', 'aoe_addressautocomplete').text('Autocomplete');
    // autocompleteField.find('em').hide()
    // addressField.first().before(autocompleteField)

    var autocomplete_billing = new google.maps.places.Autocomplete((document.getElementById('billing:street1')),{ types: ['geocode']});
    google.maps.event.addListener(autocomplete_billing, 'place_changed', function() {
        var place_billing = autocomplete_billing.getPlace();
        jQuery('#billing\\:street1').val(place_billing.address_components[0]['long_name'] + ' ' + place_billing.address_components[1]['long_name']);
        jQuery('#billing\\:city').val(place_billing.address_components[2]['long_name']);
        jQuery('#billing\\:postcode').val(place_billing.address_components[6]['long_name']);
        jQuery('#billing\\:country').val(place_billing.address_components[5]['long_name']);
        jQuery("#billing\\:region_id").val(place_billing.address_components[4]['long_name']);;
    });

    var autocomplete_shipping = new google.maps.places.Autocomplete((document.getElementById('shipping:street1')),{ types: ['geocode']});
    google.maps.event.addListener(autocomplete_shipping, 'place_changed', function() {
        var place_shipping = autocomplete_shipping.getPlace();
        jQuery('#shipping\\:street1').val(place_shipping.address_components[0]['long_name'] + ' ' + place_shipping.address_components[1]['long_name']);
        jQuery('#shipping\\:city').val(place_shipping.address_components[2]['long_name']);
        jQuery('#shipping\\:postcode').val(place_shipping.address_components[6]['long_name']);
        jQuery('#shipping\\:country').val(place_shipping.address_components[5]['long_name']);
        jQuery("#shipping\\:region_id").val(place_shipping.address_components[4]['long_name']);
    });
});