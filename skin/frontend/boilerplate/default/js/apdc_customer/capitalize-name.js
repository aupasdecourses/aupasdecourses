(function($) {

  $(document).ready(function() {
    $(document).on('change, blur', 'input[name="firstname"], input[name="lastname"]', function() {
      var value = $(this).val().trim();
      $(this).val(toTitleCase(value));
    });
    $('form#co-billing-form').on('change, blur', 'input[name="billing[firstname]"], input[name="billing[lastname]"], input[name="shipping[firstname]"], input[name="shipping[lastname]"], #contactvoisin', function() {
      var value = $(this).val().trim();
      $(this).val(toTitleCase(value));
    });
  });

  function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
})(jQuery);
