(function($) {

  $(document).ready(function() {
    $(document).on('change blur', 'form.form-validate #firstname, form.form-validate #lastname', function() {
      var value = $(this).val().trim();
      $(this).val(toTitleCase(value));
    });
  });

  function toTitleCase(str) {
    return str.replace(/\w\S*/g, function(txt){return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();});
  }
})(jQuery);
