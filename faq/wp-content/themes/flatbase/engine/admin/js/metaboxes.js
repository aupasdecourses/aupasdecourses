jQuery(document).ready(function() {


	// actions
	if ( jQuery('#post-format-0').is(':checked') ) nice_tumblogStandard();
	jQuery('#post-format-0').click( nice_tumblogStandard );

	if ( jQuery('#post-format-aside').is(':checked') ) nice_tumblogAside();
	jQuery('#post-format-aside').click( nice_tumblogAside );

	if ( jQuery('#post-format-video').is(':checked') ) nice_tumblogVideo();
	jQuery('#post-format-video').click( nice_tumblogVideo );

	if ( jQuery('#post-format-image').is(':checked') ) nice_tumblogImage();
	jQuery('#post-format-image').click( nice_tumblogImage );

	if ( jQuery('#post-format-quote').is(':checked') ) nice_tumblogQuote();
	jQuery('#post-format-quote').click( nice_tumblogQuote );

	if ( jQuery('#post-format-link').is(':checked') ) nice_tumblogLink();
	jQuery('#post-format-link').click( nice_tumblogLink );

	if ( jQuery('#post-format-gallery').is(':checked') ) nice_tumblogGallery();
	jQuery('#post-format-gallery').click( nice_tumblogGallery );

	if ( jQuery('#post-format-audio').is(':checked') ) nice_tumblogAudio();
	jQuery('#post-format-audio').click( nice_tumblogAudio );


});

function nice_tumblog_fix_css(){
	jQuery( '#nice-metabox' ).addClass( 'nice-row' );
	$row = jQuery( 'tr:visible:last td, tr:visible:last th', '#nice-metabox' );
	$row.addClass( 'border-bottom-none' );
}

function nice_tumblogStandard(){
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_embed' ).show();
	nice_tumblog_fix_css();

}

function nice_tumblogAside(){
}

function nice_tumblogVideo(){
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_embed').show();
	nice_tumblog_fix_css();
}

function nice_tumblogImage(){
	jQuery( 'tr#nicethemes_embed' ).hide();
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).show();
	nice_tumblog_fix_css();
}

function nice_tumblogQuote(){
	// show info_quotes
	jQuery( 'tr#nicethemes_embed' ).hide();
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).show();
	jQuery( 'tr#nicethemes_quote' ).show();
	jQuery( 'tr#nicethemes_quote-author' ).show();
	nice_tumblog_fix_css();
}

function nice_tumblogLink(){
	jQuery( 'tr#nicethemes_embed' ).hide();
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).show();
	jQuery( 'tr#nicethemes_link').show();
	nice_tumblog_fix_css();
}

function nice_tumblogGallery(){
	jQuery( 'tr#nicethemes_embed' ).hide();
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_gallery-info' ).show();
	nice_tumblog_fix_css();
}

function nice_tumblogAudio(){
	jQuery( 'tr#nicethemes_embed' ).hide();
	jQuery( 'tr#nicethemes_link' ).hide();
	jQuery( 'tr#nicethemes_link-info' ).hide();
	jQuery( 'tr#nicethemes_image-info' ).hide();
	jQuery( 'tr#nicethemes_quote' ).hide();
	jQuery( 'tr#nicethemes_quote-author' ).hide();
	jQuery( 'tr#nicethemes_quote-info' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).hide();
	jQuery( 'tr#nicethemes_audio_oga' ).hide();
	jQuery( 'tr#nicethemes_audio_mp3' ).show();
	jQuery( 'tr#nicethemes_audio_oga' ).show();
	nice_tumblog_fix_css();
}
