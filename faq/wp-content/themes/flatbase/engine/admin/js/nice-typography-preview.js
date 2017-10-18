/**
 * NiceThemes Typography Live Preview
 *
 * @description Generates a live preview using the
 * setting specified in a "custom typography" field.
 *
 * @since 1.1.1
 */

(function ($) {

  niceTypographyPreview = {

	/**

	 * loadPreviewButtons()
	 *
	 * @description Setup a "preview" button next to each typography field.
	 * @since 4.7.0
	 */

	loadPreviewButtons: function () {

	 var previewButtonHTML = '<a href="#" class="nice-typography-preview-button" title="' + 'Preview your customized typography settings' + '"><span>' + '</span></a>';

	 $( 'input.nice-typography-last' ).each( function ( i ) {
	 	$( this ).after( previewButtonHTML );
	 });

	 // Register event handlers.
	 niceTypographyPreview.handleEvents();

	}, // End loadPreviewButtons()

	/**
	 * handleEvents()
	 *
	 * @description Handle the events.
	 * @since 4.7.0
	 */

	handleEvents: function () {
		$( 'a.nice-typography-preview-button' ).live( 'click', function () {
			niceTypographyPreview.generatePreview( $( this ) );
			return false;
		});

		$( 'a.preview_remove' ).live( 'click', function () {
			niceTypographyPreview.closePreview( $( this ) );
			return false;
		});
	},

	/**
	 * closePreview()
	 *
	 * @description Close the preview.
	 * @since 4.7.0
	 */

	 closePreview: function ( target ) {
		target.parents( '.section' ).find( '.nice-typography-preview-button .refresh' ).removeClass( 'refresh' );
	 	target.parents( '.typography-preview-container' ).remove();
	 },

	/**
	 * generatePreview()
	 *
	 * @description Generate the typography preview.
	 * @since 4.7.0
	 */

	generatePreview: function ( target ) {
		var previewText = '<div class="preview-text">The quick brown fox jumps over the lazy dog.</div>';
		var previewHTML = '';
		var previewStyles = '';

		// Get the control parent element.
		var controls	= target.parents( '.controls' );
		var explain		= target.parents( '.controls' ).next( '.explain' );

		var fontSize	= controls.find( '.nice-typography-size' ).val();
		var fontFace	= controls.find( '.nice-typography-family' ).val();
		var fontStyle	= controls.find( '.nice-typography-style' ).val();
		var fontColor	= controls.find( '.nice-typography-color' ).val();
		var lineHeight	= ( parseInt( fontSize )  / 2 ) + parseInt( fontSize ); // Calculate pleasant line-height for the selected font size.

		if ( ! fontColor )	fontColor = '#333';
		if ( ! fontSize )	fontSize = '16';

		// Generate array of non-Google fonts.
		var nonGoogleFonts = new Array(
										'Arial, sans-serif',
										'Verdana, Geneva, sans-serif',
										'&quot;Trebuchet MS&quot;, Tahoma, sans-serif',
										'Georgia, serif',
										'&quot;Times New Roman&quot;, serif',
										'Tahoma, Geneva, Verdana, sans-serif',
										'Palatino, &quot;Palatino Linotype&quot;, serif',
										'&quot;Helvetica Neue&quot;, Helvetica, sans-serif',
										'Calibri, Candara, Segoe, Optima, sans-serif',
										'&quot;Myriad Pro&quot;, Myriad, sans-serif',
										'&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif',
										'&quot;Arial Black&quot;, sans-serif',
										'&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif',
										'Geneva, Tahoma, Verdana, sans-serif',
										'Impact, Charcoal, sans-serif',
										'Courier, &quot;Courier New&quot;, monospace'
									);

		// Remove "current" class from previously modified typography field.
		$( '.typography-preview' ).removeClass( 'current' );

		// Prepare selected fontFace for testing.
		var fontFaceTest = fontFace.replace( /"/g, '&quot;' );

		// Load Google WebFonts, if we need to.
		if ( jQuery.inArray( fontFaceTest, nonGoogleFonts ) == -1 ) { // -1 is returned if the item is not found in the array.

			// Prepare fontFace for use in the WebFont loader.
			var fontFaceString = fontFace;

			// Handle fonts that require specific weights when being included.
			switch ( fontFaceString ) {
				case 'Allan':
				case 'Cabin Sketch':
				case 'Corben':
				case 'UnifrakturCook':
					fontFaceString += ':700';
				break;

				case 'Buda':
				case 'Open Sans Condensed':
					fontFaceString += ':300';
				break;

				case 'Coda':
				case 'Sniglet':
					fontFaceString += ':800';
				break;

				case 'Raleway':
					fontFaceString += ':100';
				break;
			}


			fontFaceString += '::latin';
			fontFaceString = fontFaceString.replace( / /g, '+' );

			// Add the fontFace in quotes for use in the style declaration, if the selected font has a number in it.
			var specificFonts = new Array( 'Goudy Bookletter 1911' );

			if ( jQuery.inArray( fontFace, specificFonts ) > -1 ) {
				var fontFace = "'" + fontFace + "'";
			}

			WebFontConfig = {
			google: { families: [ fontFaceString ] }
			};

			if ( $( 'script.google-webfonts-script' ).length ) { $( 'script.google-webfonts-script' ).remove(); }

				(function() {
				var wf = document.createElement( 'script' );
				wf.src = ('https:' == document.location.protocol ? 'https' : 'http') +
				'://ajax.googleapis.com/ajax/libs/webfont/1/webfont.js';
				wf.type = 'text/javascript';
				wf.async = 'true';
				var s = document.getElementsByTagName( 'script' )[0];
				s.parentNode.insertBefore( wf, s );

				$( wf ).addClass( 'google-webfonts-script' );

				})();

		}

		// Construct styles.
		previewStyles += 'font: ' + fontStyle + ' ' + fontSize + 'px' + ' ' + fontFace + ';';
		if ( fontColor ) { previewStyles += ' color: ' + fontColor + ';'; }

		// Construct preview HTML.
		var previewHTMLInner = jQuery( '<div />' ).addClass( 'current' ).addClass( 'typography-preview' ).html( previewText ).prepend( '<a href="#" class="preview_remove"><i class="icon-cancel"></i></a>' );

		previewHTML = jQuery( '<div />' ).addClass( 'typography-preview-container' ).html( previewHTMLInner );

		// If no preview display is present, add one.
		if ( ! explain.next( '.typography-preview-container' ).length ) {
			previewHTML.find( '.typography-preview .preview-text' ).attr( 'style', previewStyles );
			explain.after( previewHTML );
		} else {
		// Otherwise, just update the styles of the existing preview.
			explain.next( '.typography-preview-container' ).find( '.typography-preview .preview-text' ).attr( 'style', previewStyles );
		}

		// Set the button to "refresh" mode.
		controls.find( '.nice-typography-preview-button span' ).addClass( 'refresh' );
	}


  }; // End niceTypographyPreview Object // Don't remove this, or the sky will fall on your head.

/*-----------------------------------------------------------------------------------*/
/* Execute the above methods in the niceTypographyPreview object.
/*-----------------------------------------------------------------------------------*/

	$(document).ready(function () {

		niceTypographyPreview.loadPreviewButtons();

	});

})(jQuery);