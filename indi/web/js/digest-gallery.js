$(document).ready(function() {
	var gallery = $('#ticket-gallery');

	if (gallery.length) {
		var value = $('#ticket-gallery .item:first').attr('value');

		$('#ticket-show').empty();

		OpenSeadragon({
			id: 'ticket-show',
			prefixUrl: '/media/osd/',
			tileSources: {
				type: 'image',
				url: value
			}
		});
	}
});

$('#ticket-gallery .link').on('click', function(event){
	var value = $(this).attr('value');

	$('#ticket-show').empty();

	OpenSeadragon({
		id: 'ticket-show',
		prefixUrl: '/media/osd/',
		tileSources: {
			type: 'image',
			url: value
		}
	});
});
