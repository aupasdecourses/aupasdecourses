(function($){
  var screenWidth = 1000;
  var mobileWidth = 771;
  updateScreenWidth();
	$(document).ready(function(){
		$('ul.dropdown-menu [data-toggle=dropdown]').on('click', function(event) {
      if (screenWidth > mobileWidth) {
        if (!$(this).parent('li').first().hasClass('open')) {
          event.preventDefault(); 
          event.stopPropagation();
          $(this).parent().siblings().removeClass('open');
          $(this).parent().toggleClass('open');
        } else {
          window.location.href = $(this).attr('href');
        }
      } else {
        window.location.href = $(this).attr('href');
      }
		});
    $('.dropdown-toggle').on('click', function(event) {
      if (screenWidth > mobileWidth) {
        if (!$(this).parent('li').first().hasClass('open')) {
          var self = $(this);
          window.setTimeout(function() {
            self.siblings('.dropdown-menu').find('.dropdown-submenu').first().toggleClass('open');
          }, 0);
        } else if ($(this).hasClass('level0')) {
          window.location.href = $(this).attr('href');
        }
      } else {
        window.location.href = $(this).attr('href');
      }
    });

    $(window).scroll(function() {
      if ($(this).scrollTop() > 60){  
        $('body').addClass('sticky-header');
      } else{
        $('body').removeClass('sticky-header');
      }
    });
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

})(jQuery);

