
jQuery(document).ready(function($) {

	if ( nice_vars.wp_version >= '3.5' ){

		// WP 3.5+ uploader
		var file_frame;
		window.formfield = '';

		$('body').on('click', '.upload_button', function(e) {

			e.preventDefault();

			var button = $(this);
			var niceformfield = $(this).prev('input').attr( 'name' );

			// Create the media frame.
			file_frame = wp.media.frames.file_frame = wp.media( {
				frame: 'post',
				state: 'insert',
				title: button.data( 'uploader_title' ),
				button: {
					text: nice_vars.use_this_file//button.data( 'uploader_button_text' )
				},
				multiple: $( this ).data( 'multiple' ) == '0' ? true : false  // Set to true to allow multiple files to be selected
			} );

			file_frame.on( 'menu:render:default', function( view ) {
				// Store our views in an object.
				var views = {};

				// Unset default menu items
				view.unset( 'library-separator' );
				view.unset( 'gallery' );
				view.unset( 'featured-image' );
				view.unset( 'embed' );

				// Initialize the views in our view object.
				view.set( views );
			} );

			// When an image is selected, run a callback.
			file_frame.on( 'insert', function() {

				var selection = file_frame.state().get('selection');
				selection.each( function( attachment, index ) {
					attachment = attachment.toJSON();
					if ( 0 === index ) {
						// place first attachment in field
						jQuery('#' + niceformfield ).val(attachment.url);

						btnContent = '<img src="' + attachment.url + '" alt="" /><a href="#" class="mlu_remove">' + nice_vars.remove_image_text + '</a>';

						jQuery('#' + niceformfield).siblings( '.screenshot').slideDown().html(btnContent);

					} else {
						// Multiple?
					}
				});
			});

			// Finally, open the modal
			file_frame.open();

		});


		// WP 3.5+ uploader
		var file_frame;
		window.formfield = '';
	//}



	} else {

		jQuery('.upload_button').click(function() {
		 formfield = jQuery(this).prev( 'input').attr( 'name' );
		 postID = jQuery(this).attr( 'rel' );
		 nicetitle = jQuery(this).parents( '.section').find('.heading').text();
		 tb_show( nicetitle, 'media-upload.php?post_id=' + postID + '&amp;TB_iframe=true' );
		 return false;
		});

		window.original_send_to_editor = window.send_to_editor;

		window.send_to_editor = function(html) {

			if ( formfield ) {
				imgurl = jQuery('img',html).attr('src');

				jQuery('#' + formfield ).val(imgurl);

				itemurl = jQuery(html).html(html).find( 'img').attr( 'src' );

				btnContent = '<img src="' + imgurl + '" alt="" /><a href="#" class="mlu_remove">Remove Image</a>';

				jQuery( '#' + formfield).siblings( '.screenshot').slideDown().html(btnContent);

				tb_remove();

			} else {

				window.original_send_to_editor(html);

			}

		}


	}

	jQuery( '.mlu_remove').live( 'click', function(event) {
		jQuery(this).hide();
		jQuery(this).parents().parents().children( '.nice-upload').attr( 'value', '' );
		jQuery(this).parents( '.screenshot').slideUp();

		return false;
	});

});