(function($) {
  'use strict';

  $.fn.StickySidebar = function() {
    var minWidth = 992;
    var $container = $('.category_view_content');
    var $sidebar = $(this);
    var filterLastScroll = $(document).scrollTop();
    var sidebarHeight = $sidebar.outerHeight(true);
    var $toolbar = $container.find('.toolbar .pager');
    var originTransition = null;
    var $header = $('header .page-header-container');
    var stickyFilterIsDisplayed = false;
    var stickySidebar = function () {
      if ($(window).width() >= minWidth) {
        var scroll = $(document).scrollTop();
        var containerTop = parseFloat($container.offset().top) + parseInt($container.css('margin-top'), 10);
        var offset = Math.max(0, (scroll - containerTop));
        if (offset > 0) {
          if ($sidebar.find('> .is_sticky').length === 0) {
            $sidebar.wrapInner('<div class="is_sticky"></div>');
          }
          if (originTransition === null) {
            originTransition = $sidebar.find('> .is_sticky').css('transition');
          }
          var stickyHeight = $sidebar.find('> .is_sticky').outerHeight(true);
          var containerHeight = $container.outerHeight(true);
          var toolbarHeight = $toolbar.outerHeight(true) + parseInt($toolbar.css('margin-top'), 10) + parseInt($toolbar.css('margin-bottom'), 10);
          var sidebarWidth = $sidebar.width();
          if ((offset + stickyHeight) >= (containerHeight-toolbarHeight)) {
            var bottomOffset = parseFloat(containerHeight - (offset + stickyHeight + toolbarHeight));
            $sidebar.find('> .is_sticky').css('transition', 'none');
            $sidebar.find('> .is_sticky').css('transform', 'translateY(' + bottomOffset + 'px)');
          } else {
            var headerHeight = $header.outerHeight(true) + parseInt($header.css('margin-top'), 10) + parseInt($header.css('margin-bottom'), 10);
            $sidebar.find('> .is_sticky').css('transition', originTransition);
            $sidebar.find('> .is_sticky').css('transform', 'translateY(' + headerHeight + 'px)');
          }
          $sidebar.find('> .is_sticky').css('width', sidebarWidth + 'px');
        } else {
          $sidebar.find('> .is_sticky').contents().unwrap();
        }
        filterLastScroll = scroll;
      } else {
        $sidebar.find('> .is_sticky').contents().unwrap();
        $sidebar.find('> .is_sticky').css('transform', '');
      }
    }
    $(window).on('scroll', stickySidebar);
    $(window).on('resize', stickySidebar);
    stickySidebar();
    return stickySidebar;
  };
  var stickMe = null;

  function adjustMaxHeight($elt) {
    var maxheight = 0;
    var maxheightorigin = $elt.find('>a').height();
    if ($elt.hasClass('category_left_menu')) {
      if (!$elt.data('maxheightorigin')) {
        $elt.data('maxheightorigin', parseFloat($elt.css('max-height')));
      }
      maxheightorigin = $elt.data('maxheightorigin');
    }

    if ($elt.hasClass('open')) {
      var $li = $elt.find('>ul li');
      if ($li.length) {
        maxheight = $li.length * parseFloat($li.outerHeight(true));
        maxheight += parseFloat($li.parent('ul').prev('a').outerHeight(true));
        $elt.css('max-height', maxheight + 'px');
      }
    } else {
      $elt.css('max-height', maxheightorigin + 'px');
    }
    window.setTimeout(stickMe, 300);
  }

  $(document).ready(function() {
    stickMe = $('.category_left_menu').parents('.col-md-3').StickySidebar();

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
