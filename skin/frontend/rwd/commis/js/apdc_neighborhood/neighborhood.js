(function($){

  $(document).ready(function() {
    $('.skip-neighborhood').on('click', '#neighborhood_ok', function() {
      if (neighborhoodIUnderstoodUrl) {
        $('.neighborhood_informations').slideUp();
        $.post(
          neighborhoodIUnderstoodUrl,
          {
            ajax:1,
            iunderstood:1
          },
          function() {
            $('.skip-neighborhood .count').hide();
          }
        );
      }
    });
  });

})(jQuery);
