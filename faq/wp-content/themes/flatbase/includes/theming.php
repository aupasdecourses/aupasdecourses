<?php
/**
 * 	Table of Contents
 *
 *	- nice_theme_setup()
 *	- nice_custom_styling()
 *	- nice_custom_fonts()
 *	- nice_load_web_fonts()
 *	- nice_standard_fonts()
 *	- nice_theme_body_classes()
 *	- Nice_Walker
 *	- nice_upload_mimes()
 *
 */

if ( ! function_exists( 'nice_theme_setup' ) ) {

	function nice_theme_setup() {

		$hard_crop = get_option( 'nice_wp_resize_crop' );

		if ( $hard_crop == 'true' ) {
			$hard_crop = true;
		} else {
			$hard_crop = false;
		}

		add_image_size( 'nice-template-blog', 480, 480, $hard_crop );
		add_image_size( 'nice-single-post', 730, 338, $hard_crop );
		add_image_size( 'nice-template-masonry-blog', 580, 405, $hard_crop );
		add_image_size( 'nice-template-search', 665, 285, $hard_crop );

		add_theme_support(
			'post-formats',
			array(
				'video'
				)
		);

		add_post_type_support( 'article', 'post-formats' );

	 }

}

add_action( 'after_setup_theme', 'nice_theme_setup' );


/**
 * nice_copyright_filter()
 *
 * Set the copyright arguments for this the function nice_copyright()
 *
 * @since 1.0.0
 *
 */

function nice_copyright_filter( $args ){

	global $nice_options;

	$text = '';

	$custom_copyright_enable = get_option( 'nice_custom_copyright_enable' );

	if ( isset( $custom_copyright_enable ) && ( $custom_copyright_enable == 'true' ) ) {

		$custom_copyright_text = get_option( 'nice_custom_copyright_text' );

		if ( isset( $custom_copyright_text ) && ( $custom_copyright_text <> '' ) ) {
			$text .= $custom_copyright_text;
		}

	} else {

		$text = '<a href="http://nicethemes.com/theme/flatbase/" target="_blank" title="Flatbase WordPress Knowlegebase Theme">Flatbase</a> ' . sprintf( __( 'by %s', 'nicethemes' ), '<a href="http://nicethemes.com" title="Wordpress Nice Themes">Nice Themes</a>' ) .' &copy; ' . date( 'Y' ). '. &mdash; ' . sprintf( __( 'Powered by %s', 'nicethemes' ), '<a href="http://wordpress.org">WordPress</a>' ) . '.';

	}

	$args['text'] = $text;

	return $args;
}

add_filter( 'nice_copyright_default_args', 'nice_copyright_filter' );

/**
 * nice_logo_filter()
 *
 * Set the logo arguments for the function nice_logo.
 *
 * @since 1.0.0
 *
 */

 function nice_logo_filter( $args ){

	global $nice_options;

	$nice_logo = get_option( 'nice_logo' );
	if ( isset( $nice_logo ) && $nice_logo <> '' )
		$args['logo'] = $nice_logo;

	$nice_logo_retina = get_option( 'nice_logo_retina' );
	if ( isset( $nice_logo_retina ) && $nice_logo_retina <> '' )
		$args['logo'] = $nice_logo_retina;

	$nice_text_title = get_option( 'nice_texttitle' );
	if ( isset( $nice_text_title ) && $nice_text_title == 'true' )
		$args['text_title'] = true;

	$args['after'] = '';

	return $args;
}

add_filter( 'nice_logo_default_args', 'nice_logo_filter' );


/**
 * nicethemes_gallery_filter()
 *
 * Set the gallery arguments for the function nice_gallery
 *
 * @since 1.0.0
 *
 */

function nicethemes_gallery_filter( $args ){

	$args['columns'] = 5;

	return $args;
}
add_filter( 'nicethemes_gallery_default_args', 'nicethemes_gallery_filter' );

/*-----------------------------------------------------------------------------------*/
/* Add Custom Styling */
/*-----------------------------------------------------------------------------------*/

add_action( 'wp_enqueue_scripts', 'nice_custom_styling', 8 );

if ( ! function_exists( 'nice_custom_styling' ) ) {

	function nice_custom_styling() {

		global $nice_options;

		$output = '';

		wp_enqueue_style( 'nice-options-styles', get_template_directory_uri() . '/includes/css/nice-options.css' );

		// working with options for the live customizer.
		$nice_accent_color = get_option( 'nice_accent_color' );
		if ( isset( $nice_accent_color )  && $nice_accent_color != '' ) {

			$output .= '.entry blockquote{ border-left-color:' . $nice_accent_color. '; }' . "\n";
			$output .= '.nice-infoboxes .item:hover a.read-more{ border-bottom-color:' . $nice_accent_color . '; }' . "\n";
			$output .= 'a, .wp-pagenavi span.current, #extended-footer a:hover, .liked i, .nice-knowledgebase ul li:hover i, span.required { color: ' . $nice_accent_color . '; }' . "\n";

			$output .= '#navigation ul li a .bar, #navigation ul li a .bar:before, #navigation ul li a .bar:after, #navigation ul li a .bar, .nice-contact-form input[type="submit"]:hover, #commentform .button:hover, #respond input[type="submit"]:hover, .single .featured-image a, .post .featured-image a, .blog-masonry #posts-ajax-loader-button:hover, .nice-gallery .thumb a, #tabs .inside .tags a:hover{ background-color: ' . $nice_accent_color . '; }' . "\n";

		}

		$nice_header_background_color = get_option( 'nice_header_background_color' );
		if ( isset( $nice_header_background_color ) && $nice_header_background_color ) {
			$output .= '#header { background-color: ' . $nice_header_background_color . '; }' . "\n";
		}

		$nice_header_background_image = get_option( 'nice_header_background_image' );
		if ( isset( $nice_header_background_image ) && $nice_header_background_image ) {
			$output .= '#header { background-image : url(' . $nice_header_background_image . '); }' . "\n";
		}

		$nice_header_background_image_position = get_option( 'nice_header_background_image_position' );
		if ( isset( $nice_header_background_image_position ) && $nice_header_background_image_position ) {
			$output .= '#header { background-position : ' . $nice_header_background_image_position. '; }' . "\n";
		}

		$nice_header_background_image_repeat = get_option( 'nice_header_background_image_repeat' );
		if ( isset( $nice_header_background_image_repeat ) && $nice_header_background_image_repeat ) {
			$output .= '#header { background-repeat : ' . $nice_header_background_image_repeat. '; }' . "\n";
		}

		$nice_layout_type = get_option( 'nice_layout_type' );
		if ( isset( $nice_layout_type ) && $nice_layout_type == 'boxed' ) {
			$output .= '#wrapper, #header { margin: 0 auto; max-width: 1180px; }' . "\n";
		}

		$nice_background_image = get_option( 'nice_background_image' );
		if ( isset( $nice_background_image ) && $nice_background_image ) {
			$output .= 'body, .bg-image { background-image : url(' . $nice_background_image . '); }' . "\n";
		}

		$nice_background_color = get_option( 'nice_background_color' );
		if ( ( isset( $nice_background_color ) && $nice_background_color ) ) {
			$output .= 'body, .bg-image { background-color : ' . $nice_background_color . '; }' . "\n";
		}

		$nice_background_image_position = get_option( 'nice_background_image_position' );
		if ( isset( $nice_background_image_position ) && $nice_background_image_position ) {
			$output .= 'body, .bg-image { background-position : ' . $nice_background_image_position . '; }' . "\n";
		}

		$nice_background_image_repeat = get_option( 'nice_background_image_repeat' );
		if ( isset( $nice_background_image_repeat ) && $nice_background_image_repeat ) {
			$output .= 'body, .bg-image { background-repeat : ' . $nice_background_image_repeat . '; }' . "\n";
		}

		$nice_logo_height = get_option( 'nice_logo_height' );
		if ( isset( $nice_logo_height )  && $nice_logo_height != '' ) {
			$output .= '#header #top #logo #default-logo, #header #top #logo #retina-logo { height : ' . $nice_logo_height . 'px; }' . "\n";
		}

		if ( isset( $output ) && $output != '' ) {
			wp_add_inline_style( 'nice-options-styles', $output );
		}

	}
}


add_action( 'wp_enqueue_scripts', 'nice_custom_fonts', 20 );

/*
*/

if ( ! function_exists( 'nice_custom_fonts' ) ) {

	function nice_custom_fonts() {

		global $nice_options;

		$output = '';

		wp_enqueue_style( 'nice-options-styles', get_template_directory_uri() . '/includes/css/nice-options.css' );

		if ( isset( $nice_options['nice_custom_typography'] ) && ( nice_bool( $nice_options['nice_custom_typography'] ) ) ) {

			if ( isset( $nice_options['nice_font_body'] ) && $nice_options['nice_font_body'] )
				$output .= 'body { ' . nice_custom_font_css( $nice_options['nice_font_body'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_nav'] ) && $nice_options['nice_font_nav'] )
				$output .= '#navigation .nav li a { ' . nice_custom_font_css( $nice_options['nice_font_nav'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_subnav'] ) && $nice_options['nice_font_subnav'] )
				$output .= '#top #navigation .nav li ul li a { ' . nice_custom_font_css( $nice_options['nice_font_subnav'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_headings'] ) && $nice_options['nice_font_headings'] )
				$output .= 'h1, h2, h3, h4, h5, h6, #call-to-action { ' . nice_custom_font_css( $nice_options['nice_font_headings'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_buttons'] ) && $nice_options['nice_font_buttons'] )
				$output .= '.button-primary, .button-blue, .button-secondary, .header .nav li.current-page a, .header .nav-callout, .cta-button, input[type="submit"], button, #commentform .button, #respond input[type="submit"], .nice-contact-form input[type="submit"], .blog-masonry #posts-ajax-loader-button { ' . nice_custom_font_css( $nice_options['nice_font_buttons'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_inputs'] ) && $nice_options['nice_font_inputs'] )
				$output .= 'input, textarea { ' . nice_custom_font_css( $nice_options['nice_font_inputs'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_infobox_title'] ) && $nice_options['nice_font_infobox_title'] )
				$output .= '.nice-infoboxes .infobox-title { ' . nice_custom_font_css( $nice_options['nice_font_infobox_title'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_infobox_content'] ) && $nice_options['nice_font_infobox_content'] )
				$output .= '.infobox .entry-excerpt{ ' . nice_custom_font_css( $nice_options['nice_font_infobox_content'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_welcome_message'] ) && $nice_options['nice_font_welcome_message'] )
				$output .= '.welcome-message h2 { ' . nice_custom_font_css( $nice_options['nice_font_welcome_message'] ) . ' }' . "\n";

			if ( isset( $nice_options['nice_font_welcome_message_extended'] ) && $nice_options['nice_font_welcome_message_extended'] )
				$output .= '.welcome-message p, .welcome-message p a { ' . nice_custom_font_css( $nice_options['nice_font_welcome_message_extended'] ) . ' }' . "\n";

		}

		// Add Text title and tagline if text title option is enabled
		if ( isset( $nice_options['nice_texttitle'] ) && ( nice_bool( $nice_options['nice_texttitle'] ) ) ) {

			if ( $nice_options['nice_font_site_title'] ){

				$output .= '#header #top #logo a .text-logo { '. nice_custom_font_css( $nice_options['nice_font_site_title'] ).' }' . "\n";

			}
		}

		if ( isset( $output ) && $output != '' ) {
			$output = strip_tags( '/* Nice Custom Fonts */' . "\n\n" . $output );
			wp_add_inline_style( 'nice-options-styles', $output );
		}

	}

} // endif;


add_action( 'wp_head', 'nice_load_web_fonts', 15 );

if ( ! function_exists( 'nice_load_web_fonts' ) ) {

	function nice_load_web_fonts() {

		global $google_fonts;
		$fonts = '';
		$html = '';

		global $nice_options;

		// Go through the options
		if ( ! empty( $nice_options ) &&
				(
					( isset( $nice_options['nice_texttitle'] ) && ( nice_bool( $nice_options['nice_texttitle'] ) ) ) ||
					( isset( $nice_options['nice_custom_typography'] ) && ( nice_bool( $nice_options['nice_custom_typography'] ) ) )
				)
			) {

			foreach ( $nice_options as $option ) :

				if ( is_array( $option ) && isset( $option['family'] ) ) {

					foreach ( $google_fonts as $font ) :

						if ( ( $option['family'] == $font['name'] ) && ( ! strstr( $fonts, $font['name'] ) ) ) {

							$fonts .= $font['name'] . $font['variant'] . "|";

						}

					endforeach;
				}

			endforeach;

			// Output google font css in header
			if ( $fonts ) {

				$fonts = str_replace( " ", "+", $fonts );
				$html .= "\n\n<!-- Nice Google fonts -->\n";
				$html .= '<link href="http' . ( is_ssl() ? 's' : '' ) . '://fonts.googleapis.com/css?family=' . $fonts . '" rel="stylesheet" type="text/css" />' . "\n";
				$html = str_replace( '|"', '"', $html);

				echo $html . "\n\n";

			} if ( ( ! nice_bool( $nice_options['nice_custom_typography'] ) ) ){

					nice_standard_fonts();

			}

		} else {
				// fix for updated themes where no typography options were saved
				nice_standard_fonts();

		}

	} // End nice_load_web_fonts()

}

if ( ! function_exists( 'nice_standard_fonts' ) ) {

	function nice_standard_fonts() { ?>
		<link href='http<?php if ( is_ssl() ) echo 's'; ?>://fonts.googleapis.com/css?family=Lato:300,400,700,900,300italic,400italic,700italic' rel='stylesheet' type='text/css'>
		<link href='http<?php if ( is_ssl() ) echo 's'; ?>://fonts.googleapis.com/css?family=Nunito:300,400,700' rel='stylesheet' type='text/css'>
	<?php
	}

}


// Add the nice bar above each parent item for the main menu.
class Nice_Walker_Nav_Menu extends Walker_Nav_Menu {

// add main/sub classes to li's and links
 function start_el( &$output, $item, $depth = 0, $args = array(), $current_category = 0 ) {
	global $wp_query;
	$indent = ( $depth > 0 ? str_repeat( "\t", $depth ) : '' ); // code indent

	// depth dependent classes
	$depth_classes = array(
		( $depth == 0 ? 'main-menu-item' : 'sub-menu-item' ),
		( $depth >=2 ? 'sub-sub-menu-item' : '' ),
		( $depth % 2 ? 'menu-item-odd' : 'menu-item-even' ),
		'menu-item-depth-' . $depth
	);
	$depth_class_names = esc_attr( implode( ' ', $depth_classes ) );

	// passed classes
	$classes = empty( $item->classes ) ? array() : (array) $item->classes;
	$class_names = esc_attr( implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item ) ) );

	// build html
	$output .= $indent . '<li id="nav-menu-item-'. $item->ID . '" class="' . $depth_class_names . ' ' . $class_names . '">';

	if ( $depth < 1 )
		$args->link_after = '<mark class="bar"></mark>';
	else
		$args->link_after = '';

	// link attributes
	$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
	$attributes .= ! empty( $item->target )	 ? ' target="' . esc_attr( $item->target	 ) .'"' : '';
	$attributes .= ! empty( $item->xfn )		? ' rel="'	. esc_attr( $item->xfn		) .'"' : '';
	$attributes .= ! empty( $item->url )		? ' href="'   . esc_attr( $item->url		) .'"' : '';
	$attributes .= ' class="menu-link ' . ( $depth > 0 ? 'sub-menu-link' : 'main-menu-link' ) . '"';

	$item_output = sprintf( '%1$s<a%2$s>%3$s%4$s%5$s</a>%6$s',
		$args->before,
		$attributes,
		$args->link_before,
		apply_filters( 'the_title', $item->title, $item->ID ),
		$args->link_after,
		$args->after
	);

	// build html
	$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
}
}

/**
 * nice_back_to_top()
 *
 * Adds the code for the back to top button (if enabled through options)
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_back_to_top' ) ) {

	function nice_back_to_top() {

		global $nice_options;

		if ( isset( $nice_options['nice_back_to_top'] ) && $nice_options['nice_back_to_top'] == 'true' ): ?>
			<a href="#" class="backtotop">
				<i class="fa fa-angle-up"></i>
			</a>
		<?php endif;

		return true;
	}

}

add_action( 'wp_footer', 'nice_back_to_top' );

?>