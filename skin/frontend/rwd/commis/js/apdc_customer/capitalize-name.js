(function($) {

  $(document).ready(function() {
    $('form#form-validate').on('change, blur', '#firstname, #lastname', function() {
      var value = $(this).val().trim();
      $(this).val(toTitleCase(value));
    });
  });

  function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
})(jQuery);
