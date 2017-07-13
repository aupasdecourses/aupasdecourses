(function($){

	var screenWidth = 1300;
	var mobileWidth = 1200;
	var resizing = null;

	updateScreenWidth();

	$(document).ready(function(){
		checkStickyHeader();
		// Close menu only when clicking on links or close menu
		$(document).on('click', '#supermenu .dropdown-menu', function (e) {
			if (jQuery(e.target).hasClass('close-menu') || e.target.nodeName.toLowerCase() === 'a') {
				return;
			} else {
				e.stopPropagation();
			}
		});

		$('#supermenu [data-toggle=dropdown]').on('click', function(event) {
			if (screenWidth > mobileWidth){
				superMenuManageClick(this, event);
			} else {
				event.preventDefault();
				event.stopPropagation();
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

		$(window).resize(function() {
			if (resizing) {
				clearTimeout(resizing);
			}
			resizing = setTimeout(function() {
				updateScreenWidth();
			}, 50);
		});

	});

	function superMenuManageClick(self, event) {
		if (!$(self).parent('li').first().hasClass('open') || !$(self).hasClass('level0')) {
			event.preventDefault();
			event.stopPropagation();
			var templateContent = $(self).siblings('.template-content').html();

			$('.menu-template-content').remove();
			$(self).parents('div.level0 ul').after('<div class="menu-template-content">' + templateContent + '</div>');
			$(self).parent().siblings().removeClass('open');
			$(self).parent().toggleClass('open');

			if ($(self).parent().find('.dropdown-submenu.active').length > 0) {
				$(self).parent().find('.dropdown-submenu.active').find('a.dropdown-toggle').first().click();
			} else {
				$(self).parent().find('.dropdown-submenu').first().find('a.dropdown-toggle').first().click();
			}
		} else {
			if($(self).hasClass('level0') && $(self).parent('li').first().hasClass('open')) {
				event.preventDefault();
				event.stopPropagation();
				$(self).parent().find('.close-menu').click();
			}
			else {
				event.preventDefault();
				event.stopPropagation();
			}
		}
	}

	function updateScreenWidth() {
		screenWidth = (window.innerWidth > 0) ? window.innerWidth : screen.width;
	}

	function checkStickyHeader() {
		var stickyOffset = 60;
		if (screenWidth > mobileWidth) {
			stickyOffset = 1;
			$('body').removeClass('sticky-header');
		}
		if (screenWidth < mobileWidth) {
			if ($(window).scrollTop() > stickyOffset){
				$('body').addClass('sticky-header');
			} else{
				$('body').removeClass('sticky-header');
			}
		}
	}

})(jQuery);
