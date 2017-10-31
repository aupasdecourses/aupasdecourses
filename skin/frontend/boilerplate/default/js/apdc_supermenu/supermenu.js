(function($){

	function superMenuManageClick(self, event) {
		if (!$(self).parent('li').first().hasClass('open') || !$(self).hasClass('level0')) {
			event.preventDefault();
			event.stopPropagation();
      var templateContent = $(self).siblings('.template-content').html();
      var screenWidth = jQuery(window).width();

			$('.menu-template-content').remove();
      $(self).parents('div.level0 ul').after('<ul class="menu-template-content">' + templateContent + '</ul>');
      if (screenWidth > 1200) {
        $('.menu-template-content').find('.visible-xs').remove();
      }
			$(self).parent().siblings().removeClass('open');
			$(self).parent().toggleClass('open');

			if ($(self).parent().find('.dropdown-submenu.active').length > 0) {
				$(self).parent().find('.dropdown-submenu.active').find('a.dropdown-toggle').first().click();
			}
		} else {
			if($(self).hasClass('level0') && $(self).parent('li').first().hasClass('open')) {
				event.preventDefault();
				event.stopPropagation();
        $(self).parent('li').first().removeClass('open');
			}
			else {
				event.preventDefault();
				event.stopPropagation();
			}
		}
	}

	$(document).ready(function(){
		// Close menu only when clicking on links or close menu
		$(document).on('click', '#supermenu .dropdown-menu', function (e) {
			if (jQuery(e.target).hasClass('close-menu') || e.target.nodeName.toLowerCase() === 'a') {
				return;
			} else {
				e.stopPropagation();
			}
		});

		$('#supermenu [data-toggle=dropdown]').on('click', function(event) {
				superMenuManageClick(this, event);
		});

		$('.deploy-menu-button').on('click', function(event) {
			event.preventDefault();
			event.stopPropagation();
			$(this).parents('li').first().find('.open').removeClass('open');
			$(this).parents('li').first().siblings().removeClass('open');
			$(this).parents('li').first().toggleClass('open');
		});

	});

})(jQuery);
