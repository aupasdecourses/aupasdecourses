(function($){
  var screenWidth = 1000;
  var mobileWidth = 771;
  updateScreenWidth();
	$(document).ready(function(){
    checkStickyHeader();
    $(document).on('click', function(event) {
      window.setTimeout(setMenuOpen, 0);
    });
    $('#supermenu').on('click', function(event) {
      window.setTimeout(setMenuOpen, 0);
    });
		$('.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
      if (screenWidth > mobileWidth) {
        if (!$(this).parent('li').first().hasClass('open')) {
          event.preventDefault(); 
          event.stopPropagation();
          var templateContent = $(this).siblings('.template-content').html();

          $('.menu-template-content').remove();
          $(this).parents('div.level0 ul').after('<div class="menu-template-content">' + templateContent + '</div>');
          $(this).parent().siblings().removeClass('open');
          $(this).parent().toggleClass('open');
        } else {
          event.preventDefault(); 
          event.stopPropagation();
        }
      } else {
        event.preventDefault();
        event.stopPropagation();
      }
		});
    $('.dropdown-toggle').on('click', function(event) {
      if (screenWidth > mobileWidth) {
        if (!$(this).parent('li').first().hasClass('open')) {
          var self = $(this);
          window.setTimeout(function() {
            if (self.siblings('.dropdown-menu').find('.dropdown-submenu.active').length > 0) {
              self.siblings('.dropdown-menu').find('.dropdown-submenu.active').find('a.dropdown-toggle').first().click();
            } else {
              self.siblings('.dropdown-menu').find('.dropdown-submenu').first().find('a.dropdown-toggle').first().click();
            }
          }, 0);
        }
      } else {
        event.preventDefault();
        event.stopPropagation();
        $(this).parents('li').first().find('.open').removeClass('open');
        $(this).parents('li').first().siblings().removeClass('open');
        $(this).parents('li').first().toggleClass('open');
      }
    });

    $(window).scroll(checkStickyHeader);

    $('.deploy-menu-button').on('click', function(event) {
      event.preventDefault();
      event.stopPropagation();
      $(this).parents('li').first().find('.open').removeClass('open');
      $(this).parents('li').first().siblings().removeClass('open');
      $(this).parents('li').first().toggleClass('open');
    });

	});

  var resizing = null;
  $(window).resize(function() {
    if (resizing) {
      clearTimeout(resizing);
    }
    resizing = setTimeout(function() {
      updateScreenWidth();
    }, 50);
  });


  function updateScreenWidth() {
    screenWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
  }
  function setMenuOpen() {
    if ($('#supermenu .open').length > 0) {
      $('body').addClass('menu-open');
    } else {
      $('body').removeClass('menu-open');
    }
  }
  function checkStickyHeader() {
    var stickyOffset = 60;
    if (screenWidth > mobileWidth) {
      stickyOffset = 1;
    }
    if ($(window).scrollTop() > stickyOffset){  
      $('body').addClass('sticky-header');
    } else{
      $('body').removeClass('sticky-header');
    }
  }

})(jQuery);

