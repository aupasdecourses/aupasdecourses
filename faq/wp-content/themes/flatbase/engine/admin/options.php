<?php
/**
 *	Table of Contents (options.php)
 *
 *	- nice_formbuilder()
 *	- nice_option_get_text()
 *	- nice_option_get_select()
 *	- nice_option_get_textarea()
 *	- nice_option_get_file()
 *	- nice_option_get_checkbox()
 *	- nice_option_get_radio()
 *	- nice_option_get_color()
 *	- nice_option_get_date()
 *	- nice_option_get_select_multiple()
 *	- nice_option_get_typography()
 *	- nice_option_get_info()
 *	- nice_option_get_password()
 *
 */


/**
 * nice_formbuilder()
 *
 * retrieve the options array, creating the html structure for the
 * options menu.
 *
 * @since 1.0.0
 *
 * @param array $nice_options. Theme Options.
 * @return object with menu and content.
 */

if ( ! function_exists( 'nice_formbuilder' ) ) :

function nice_formbuilder( $nice_options ){

	$interface = new stdClass();
	$interface->menu = '';
	$interface->content = '';

	foreach ( $nice_options as $key => $option ) :

		if ( $option['type'] != 'heading'  ) {
			$class = '';

			if ( isset( $option['class'] ) ) $class = $option['class'];

			$interface->content .= '<div class="section section-' . $option['type'] . ' ' .  $class  . '">' . "\n";

			if ( ( $option['type'] != 'upload' ) && ( $option['type'] != 'color' ) && ( $option['type'] != 'heading' ) ){

				$interface->content .= '<h3 class="heading"><label for="' . esc_attr( $option['id'] ) . '">' .  esc_html( $option['name'] )  . '</label></h3>' . "\n";
			} else {

				$interface->content .= '<h3 class="heading">' . esc_html( $option['name'] )  . '</h3>' . "\n";

			}

			if ( $option['type'] != 'checkbox' && ( $option['type'] != 'info' ) && $option['desc'] != '' ) {

				$interface->content .= '<a id="btn-help-' . $option['id'] . '" class="nice-help-button">' .  __( 'Help', 'nicethemes' )  . '</a>' . "\n";
			}

			$interface->content .= '<div class="option">' . "\n" . '<div class="controls">' . "\n";

		 }

		$select_value = '';

		switch ( $option['type'] ) {

			case 'text':
				$interface->content .= nice_option_get_text( $option );
			break;

			case 'password':
				$interface->content .= nice_option_get_password( $option );
			break;

			case 'select':
				$interface->content .= nice_option_get_select( $option );
			break;

			case 'textarea':
				$interface->content .= nice_option_get_textarea( $option );
			break;

			case 'upload':
				$interface->content .= nice_option_get_file( $option );
			break;

			case 'checkbox':
				$interface->content .= nice_option_get_checkbox( $option );
			break;

			case 'radio':
				$interface->content .= nice_option_get_radio( $option );
			break;

			case 'color':
				$interface->content .= nice_option_get_color( $option );
			break;

			case 'date':
				$interface->content .= nice_option_get_date( $option );
			break;

			case 'select_multiple':
				$interface->content .= nice_option_get_select_multiple( $option );
			break;

			case 'info':
				$interface->content .= nice_option_get_info( $option );
			break;

			case 'slider':
				$interface->content .= nice_option_get_slider( $option );
			break;

			case 'heading' :

				if ( $key >= 2 ) $interface->content .= '</div>' . "\n";
				$jquery_click_hook = preg_replace( '/[^a-zA-Z0-9\s]/', '', strtolower( $option['name'] ) );
				$jquery_click_hook = str_replace( ' ', '-', $jquery_click_hook );
				$jquery_click_hook	 = 'nice-option-' . $jquery_click_hook;
				$interface->menu 	.= '<li><a title="' .  esc_attr( $option['name'] )  . '" href="#' .   $jquery_click_hook   . '">' .  esc_html( $option['name'] )  . '</a><div></div></li>' . "\n";
				$interface->content .= '<div class="group" id="' . $jquery_click_hook . '"><h2>' . esc_html( $option['name']  ). '</h2>' . "\n";

			break;

			case 'typography':
				$interface->content .= nice_option_get_typography( $option );
			break;
		}

		$explain_class = 'explain';

		if ( $option['type'] != 'heading' ) {

			if ( $option['type'] != 'checkbox' ) $interface->content .= '<br />';
			else $explain_class = 'explain-checkbox';

			if ( ! isset( $option['desc'] ) ) {

				$explain_value = '';

			} else {

				$explain_value = $option['desc'];

			}

			$interface->content .= '</div><div id="nice-help-' . $option['id'] . '" class="' . $explain_class . '">';

			if ( $option['type'] == 'checkbox' )
				$interface->content .= '<label for="' . $option['id'] . '">' .  $explain_value  . '</label>';
			else
				$interface->content .= $explain_value;

			$interface->content .= '</div>' . "\n";
			$interface->content .= '<div class="clear"></div></div></div>' . "\n";
		}

	endforeach;

	$interface->content .= '</div>';

	return $interface;

}

endif;


/**
 * nice_option_get_text()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_option_get_text' ) ) :

	function nice_option_get_text( $option ){

		$val = $option['std'];
		$tip = $option['tip'];
		$std = get_option( $option['id'] );
		if ( $std != '' ) $val = $std;

		return '<input class="nice-input" name="' . $option['id'] . '" id="' . $option['id'] . '" type="' . $option['type'] . '" value="' . esc_attr( $val ) . '" />';

	}

endif;

/**
 * nice_option_get_select()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the select input.
 */

if ( ! function_exists( 'nice_option_get_select' ) ) :

	function nice_option_get_select( $option ){

		$html = '<select class="nice-input" name="' . $option['id'] . '" id="' . $option['id'] . '">' . "\n";

		$select_value = get_option( $option['id'] );

		foreach ( $option['options'] as $o => $n ) {

			$selected = '';

			if ( $select_value != '' ) {
				$selected = selected( $select_value, $o, false );
			} else {
				if ( isset( $option['std'] ) )
					$selected = selected( $option['std'], $o, false);
			}

				$html .= '<option value="' . esc_attr( $o ) . '" ' . $selected . '>';
				$html .= esc_html( $n );
				$html .= '</option>' . "\n";

		}

		$html .= '</select>' . "\n";

		return $html;
	}

endif;

/**
 * nice_option_get_textarea()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the textarea input.
 */

if ( ! function_exists( 'nice_option_get_textarea' ) ) :

	function nice_option_get_textarea( $option ){

		$cols = '8';
		$ta_value = '';

		if ( isset ( $option['std'] ) ) :

			$ta_value = $option['std'];

			if ( isset ( $option['options'] ) ) {
				$ta_options = $option['options'];
				if ( isset ( $ta_options['cols'] ) )
					$cols = $ta_options['cols'];
				else
					$cols = '8';
			}

		endif;

		$std = get_option( $option['id'] );
		if ( $std != "" ) $ta_value = stripslashes( $std );

		return '<textarea class="nice-input" name="' .  $option['id']  . '" id="' .  $option['id']  . '" cols="' . $cols . '" rows="8">' . esc_textarea( $ta_value ) . '</textarea>' . "\n";

	}

endif;

/**
 * nice_option_get_file()
 *
 * Retrieve option info in order to return the field in html code. Works
 * with wordpress mediauploader.
 * If there's an image, it shows it with a "remove" button
 * Check medialibrary.php
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the file input.
 */

if ( ! function_exists( 'nice_option_get_file' ) ) :

	function nice_option_get_file( $option ){

		$post_id = houdini_get_post ( $option['id'] );

		$src = get_option( $option['id'] );
		if ( $src == '' ) $src = $option['std'];

		$html = '<input id="' . $option['id'] . '" class="nice-upload" type="text" size="36" name="' . $option['id'] . '" value="' . esc_attr( $src ) . '" autocomplete="off" /><input id="upload_image_button" class="upload_button nice-input" type="button" value="' . __( 'Upload Image', 'nicethemes' ) . '" rel="' . $post_id . '" />';

		$html .= '<div class="screenshot" id="' . '_image">' . "\n";

		if ( $src != '' ){
			$html .= '<img src="' . $src . '" alt="" />' . '';
			$html .= '<a href="#" class="mlu_remove">' . __( 'Remove Image', 'nicethemes') . '</a>' . "\n";
		}

		$html .= '</div>' . "\n";

		return $html;

	}

endif;

/**
 * nice_option_get_checkbox()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the checkbox input.
 */

if ( ! function_exists( 'nice_option_get_checkbox' ) ) :

	function nice_option_get_checkbox( $option ){

		$checked = '';

		$option_std = get_option( $option['id'] );

		if ( ! empty( $option_std ) ) {

			$checked = checked( $option_std, 'true', false );

		} else{

			$checked = checked( $option['std'], 'true', false );

		}

		return '<input type="checkbox" class="checkbox nice-input" name="' . $option['id'] . '" id="' . $option['id'] . '" value="true" ' . $checked . ' />';
	}

endif;


/**
 * nice_option_get_radio()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the radio input.
 */

if ( ! function_exists( 'nice_option_get_radio' ) ) :

	function nice_option_get_radio( $option ){

		$html = '';
		$select_value = get_option( $option['id'] );

		foreach ( $option['options'] as $o => $n )
		{
			$checked = '';
			if ( $select_value != '' ) {
				$checked = checked( $select_value, $o, false );
			} else {
				$checked = checked( $option['std'], $o, false );
			}
			$html .= '<input class="nice-input nice-radio" type="radio" name="' . $option['id'] . '" value="' . $o . '" ' . $checked . ' />' . esc_html( $n ) . '<br />';
		}

		return $html;

	}

endif;

/**
 * nice_option_get_color()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the color input.
 */

if ( ! function_exists( 'nice_option_get_color' ) ) :

	function nice_option_get_color( $option ){

		$std = $option['std'];
		$db  = get_option( $option['id'] );

		if ( $db != "" ) { $std = $db; }

		$html  = '<div id="' . $option['id'] . '_picker" class="colorSelector"><div></div></div>';
		$html .= '<input class="nice-color" name="' . $option['id'] . '" id="' . $option['id'] . '" type="text" value="' . esc_attr( $std ) . '" />';

		return $html;

	}

endif;

/**
 * nice_option_get_date()
 *
 * Retrieve option info in order to return the field in html code + js date picker.
 *
 * @since 1.0.1
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the date input.
 */

if ( ! function_exists( 'nice_option_get_date' ) ) :

	function nice_option_get_date( $option ){

		$std = $option['std'];
		$db  = get_option( $option['id'] );

		if ( $db != "" ) { $std = $db; }

		$html = '<input class="nice-date" name="' . $option['id'] . '" id="' . $option['id'] . '" type="text" value="' . esc_attr( $std ) . '" />';
		$html .= '<input type="hidden" name="datepicker-image" value="' . get_template_directory_uri() . '/engine/admin/images/calendar.png" />';

		return $html;

	}

endif;

/**
 * nice_option_get_select_multiple()
 *
 * Retrieve option info in order to return the field in html code + js date picker.
 *
 * @since 1.0.12
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the date input.
 */

if ( ! function_exists( 'nice_option_get_select_multiple' ) ) :

	function nice_option_get_select_multiple( $option ){

		$html = '<select class="nice-input" style="height:auto;" name="' . $option['id'] . '[]" id="' . $option['id'] . '[]" multiple="multiple" >' . "\n";

		$select_value = get_option( $option['id'] );

		foreach ( $option['options'] as $o => $n ) {

			$selected = '';

			if ( $select_value != '' ) {

				if ( is_array( $select_value) ){
					if ( ( $key = array_search( $n, $select_value ) ) !== FALSE ) { $selected = ' selected="selected"'; }
				}

			} else {

				if ( isset( $option['std'] ) )
					$selected = selected( $option['std'], $o, false );

			}

			$html .= '<option value="' . esc_attr( $o ) . '"' . $selected . '>';
			$html .= esc_html( $n );
			$html .= '</option>' . "\n";

		}

		$html .= '</select>' . "\n";

		return $html;

	}

endif;

/**
 * nice_option_get_typography()
 *
 * Retrieve option info in order to return the field in html code + js date picker.
 *
 * @since 1.0.12
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the date input.
 */

if ( ! function_exists( 'nice_option_get_typography' ) ) :

	function nice_option_get_typography( $option ){

		$db  = get_option( $option['id'] );
		$std = $option['std'];

		if ( ! is_array( $db ) || empty( $db ) ) {
			$std = $option['std'];
		}

		// ----------
		// font family

		$font_family = $std['family'];
		if ( $db['family'] != "" ) { $font_family = $db['family']; }

		$font01 = '';
		$font02 = '';
		$font03 = '';
		$font04 = '';
		$font05 = '';
		$font06 = '';
		$font07 = '';
		$font08 = '';
		$font09 = '';
		$font10 = '';
		$font11 = '';
		$font12 = '';
		$font13 = '';
		$font14 = '';
		$font15 = '';
		$font16 = '';
		$font17 = '';

		if ( strpos( $font_family, 'Arial, sans-serif' ) !== false ) { $font01 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Verdana, Geneva' ) !== false ) { $font02 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Trebuchet' ) !== false ) { $font03 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Georgia' ) !== false ) { $font04 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Times New Roman' ) !== false ) { $font05 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Tahoma, Geneva' ) !== false ) { $font06 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Palatino' ) !== false ) { $font07 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Helvetica' ) !== false ) { $font08 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Calibri' ) !== false ) { $font09 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Myriad' ) !== false ) { $font10 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Lucida' ) !== false ) { $font11 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Arial Black' ) !== false ) { $font12 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Gill' ) !== false ) { $font13 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Geneva, Tahoma' ) !== false ) { $font14 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Impact' ) !== false ) { $font15 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Courier' ) !== false ) { $font16 = 'selected="selected"'; }
		if ( strpos( $font_family, 'Century Gothic' ) !== false ) { $font17 = 'selected="selected"'; }

		$html = '<select class="nice-typography nice-typography-family" name="'. esc_attr( $option['id'].'_family' ) . '" id="'. esc_attr( $option['id'].'_family') . '">' . "\n";
		$html .= '<option value="Arial, sans-serif" '. $font01 .'>Arial</option>' . "\n";
		$html .= '<option value="Verdana, Geneva, sans-serif" '. $font02 .'>Verdana</option>' . "\n";
		$html .= '<option value="&quot;Trebuchet MS&quot;, Tahoma, sans-serif"'. $font03 .'>Trebuchet</option>' . "\n";
		$html .= '<option value="Georgia, serif" '. $font04 .'>Georgia</option>' . "\n";
		$html .= '<option value="&quot;Times New Roman&quot;, serif"'. $font05 .'>Times New Roman</option>' . "\n";
		$html .= '<option value="Tahoma, Geneva, Verdana, sans-serif"'. $font06 .'>Tahoma</option>' . "\n";
		$html .= '<option value="Palatino, &quot;Palatino Linotype&quot;, serif"'. $font07 .'>Palatino</option>' . "\n";
		$html .= '<option value="&quot;Helvetica Neue&quot;, Helvetica, sans-serif" '. $font08 .'>Helvetica</option>' . "\n";
		$html .= '<option value="Calibri, Candara, Segoe, Optima, sans-serif"'. $font09 .'>Calibri</option>' . "\n";
		$html .= '<option value="&quot;Myriad Pro&quot;, Myriad, sans-serif"'. $font10 .'>Myriad Pro</option>' . "\n";
		$html .= '<option value="&quot;Lucida Grande&quot;, &quot;Lucida Sans Unicode&quot;, &quot;Lucida Sans&quot;, sans-serif"'. $font11 .'>Lucida</option>' . "\n";
		$html .= '<option value="&quot;Arial Black&quot;, sans-serif" '. $font12 .'>Arial Black</option>' . "\n";
		$html .= '<option value="&quot;Gill Sans&quot;, &quot;Gill Sans MT&quot;, Calibri, sans-serif" '. $font13 .'>Gill Sans</option>' . "\n";
		$html .= '<option value="Geneva, Tahoma, Verdana, sans-serif" '. $font14 .'>Geneva</option>' . "\n";
		$html .= '<option value="Impact, Charcoal, sans-serif" '. $font15 .'>Impact</option>' . "\n";
		$html .= '<option value="Courier, &quot;Courier New&quot;, monospace" '. $font16 .'>Courier</option>' . "\n";
		$html .= '<option value="&quot;Century Gothic&quot;, sans-serif" '. $font17 .'>Century Gothic</option>' . "\n";

		// Google webfonts
		global $google_fonts;

		sort( $google_fonts );

		$html .= '<option value="">———— ' . __( 'Google Fonts', 'nicethemes' ) .' ————</option>' . "\n";
		foreach ( $google_fonts as $key => $google_font ) :
			$font[$key] = '';
			$font[$key] = selected( $font_family, $google_font['name'], false );
			$name = $google_font['name'];
			$html .= '<option value="' . esc_attr( $name ) . '" '. $font[$key] .'>' . esc_html( $name ) . '</option>' . "\n";
		endforeach;

		$html .= '</select>' . "\n\n";


		// ----------
		// font weight

		$font_weight = $std['style'];
		if ( $db['style'] != "" ) { $font_weight = $db['style']; }

		$thin = ''; $thinitalic = ''; $normal = ''; $italic = ''; $bold = ''; $bolditalic = '';
		$thin 		= selected( $font_weight, '300', false );
		$thinitalic = selected( $font_weight, '300 italic', false );
		$normal 	= selected( $font_weight, 'normal', false );
		$italic 	= selected( $font_weight, 'italic', false );
		$bold 		= selected( $font_weight, 'bold', false );
		$bolditalic	= selected( $font_weight, 'bold italic', false );

		if ( ( $thin == '' ) && ( $thinitalic == '' ) && ( $normal == '') && ( $italic == '' ) && ( $bold == '' ) && ( $bolditalic == '' ) ){
			$normal = 'selected="selected"';
		}

		$html .= '<select class="nice-typography nice-typography-style" name="'. esc_attr( $option['id'] . '_style' ) . '" id="' . esc_attr( $option['id'] . '_style' ) . '">';
		$html .= '<option value="300" '. $thin .'>Thin</option>';
		$html .= '<option value="300 italic" '. $thinitalic .'>Thin/Italic</option>';
		$html .= '<option value="normal" '. $normal .'>Normal</option>';
		$html .= '<option value="italic" '. $italic .'>Italic</option>';
		$html .= '<option value="bold" '. $bold .'>Bold</option>';
		$html .= '<option value="bold italic" '. $bolditalic .'>Bold/Italic</option>';
		$html .= '</select>';

		// --------
		// font size
		if ( isset( $std['size'] ) ) {

			$font_size = $std['size'];

			if ( $db['size'] != '' ) { $font_size = $db['size']; }

			$html .= '<select class="nice-typography nice-typography-size" name="'. esc_attr( $option['id'] . '_size' ) . '" id="'. esc_attr( $option['id'].'_size') . '" >' . "\n";

			for ( $i = 9; $i < 71; $i++ ) {

				$active = selected( $font_size, strval( $i ), false );
				$html .= '<option value="' . esc_attr( $i ) .'" ' . $active . '>' . esc_html( $i . ' px' ) .'</option>' . "\n";

			}

			$html .= '</select>' . "\n\n";

		}

		// ----------
		// font color

		if ( isset( $std['color'] ) ){

			$font_color = $std['color'];

			if ( $db['color'] != "" ) { $font_color = $db['color']; }

			$html .= '<div id="' . esc_attr( $option['id'] . '_color_picker' ) . '" class="colorSelector"><div></div></div>' . "\n";
			$html .= '<input class="nice-color nice-typography-color" name="' .  esc_attr( $option['id'] . '_color' ) . '" id="' . esc_attr( $option['id'] . '_color' ) . '" type="text" value="' . esc_attr( $font_color ) . '" />' . "\n\n";

		}

		$html .= '<input type="hidden" class="nice-typography-last" />';

		return $html;

	}

endif;

/**
 * nice_option_get_info()
 *
 * Display an information field.
 *
 * @since 1.0.5
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_option_get_info' ) ) :

	function nice_option_get_info( $option ){

		return $option['desc'];

	}

endif;

/**
 * nice_option_get_password()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.6
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_option_get_password' ) ) :

	function nice_option_get_password( $option ){

		$val = $option['std'];
		$tip = $option['tip'];
		$std = get_option( $option['id'] );
		if ( $std != "" ) $val = $std;

		return '<input class="nice-input" name="' . $option['id']  . '" id="' . $option['id'] . '" type="' . $option['type'] . '" value="' .  esc_attr( $val ) . '" />';

	}

endif;

/**
 * nice_option_get_slider()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.6
 *
 * @param array item $option. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_option_get_slider' ) ) :

	function nice_option_get_slider( $option ){

		$db  = get_option( $option['id'] );
		$std = $option['std'];

		if ( ! is_array( $db ) || empty( $db ) ) {
			$std = $option['std'];
		}

		$output  = '<div id="' . $option['id'] . '_slider" ></div>';
		$output .= '<input type="text" name="' . $option['id'] . '" id="' . $option['id'] . '" />';

		return $output;

	}

endif;

?>