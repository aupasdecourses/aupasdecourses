/*
	General JS for the NiceThemes Admin Section
*/

(function($){
	var wrap;

	function show() {
		var parent = $('#nice-modal'),
			content = $('#nice-modal-content'),
			loaded = false;

		wrap.removeClass('hidden');

	}

	function hide() {

		wrap.fadeOut( 200, function() {
			wrap.addClass('hidden').css( 'display', '' );
			$('#nice-modal-frame').remove();
		});
	}


	$( document ).ready(function() {

		wrap = $('#nice-modal-wrap');
		wrap.find('.nice-modal-close').on( 'click', function() {
			hide();
		});

		if ( php_data.activated == 'true' )
			show();
	});

}(jQuery));