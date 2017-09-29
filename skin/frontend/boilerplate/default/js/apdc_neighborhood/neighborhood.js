(function($){

  $(document).ready(function() {
    $(document).on('click', '#neighborhood_ok', function() {
      if (neighborhoodIUnderstoodUrl) {
        $('.neighborhood_informations').slideUp();
        $.post(
          neighborhoodIUnderstoodUrl,
          {
            ajax:1,
            iunderstood:1
          },
          function() {
            $('.header-neighborhood-container .count').hide();
          }
        );
      }
    });
  });

})(jQuery);
