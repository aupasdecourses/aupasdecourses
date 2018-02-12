(function($) {
  'use strict';

  function adjustMaxHeight($elt) {
    var maxheight = 0;
    var maxheightorigin = $elt.find('>a').height();
    
    if ($elt.hasClass('open')) {
      var $li = $elt.find('>ul li');
      if ($li.length) {
        maxheight = $li.length * $li.height();
        $elt.css('max-height', maxheight + 'px');
      }
    } else {
      $elt.css('max-height', maxheightorigin + 'px');
    }
  }

  $(document).ready(function() {
    $('.category_left_menu li.parent.open').each(function() {
      adjustMaxHeight($(this));
    });
    $('.category_left_menu li.parent span.toggle-link').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      $(this).parents('li').toggleClass('open');
      adjustMaxHeight($(this).parents('li'));
    });

    $('.category_left_menu .toggle-mobile-menu').on('click', function(e) {
      e.preventDefault();
      e.stopPropagation();
      var $leftMenu = $(this).parents('.category_left_menu');
      $leftMenu.toggleClass('open');
      adjustMaxHeight($leftMenu);
    });
  });
})(jQuery);
