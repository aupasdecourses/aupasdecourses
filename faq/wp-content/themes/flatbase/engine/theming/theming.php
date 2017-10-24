<?php
/**
 * Table of Contents (theming.php)
 *
 *	- nice_wp_footer()
 *	- nice_tracking_code()
 *	- nice_twitter_script()
 *	- nice_wp_head()
 *	- nice_favicon()
 *	- nice_custom_css()
 *	- nice_custom_js()
 *	- nice_excerpt()
 *	- nice_load_textdomain()
 *	- nice_sidebar_position_class()
 *	- nice_browser_class()
 *	- nice_custom_font_css()
 *	- nice_upload_mimes()
 *
 */

/**
 * nice_wp_footer()
 *
 * include all the functions to be triggered
 * when wp_footer is invoked.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_wp_footer' ) ) :

	function nice_wp_footer(){

		if ( function_exists( 'nice_tracking_code' ) ) {
				nice_tracking_code();
		}

	}

endif;

add_action( 'wp_footer', 'nice_wp_footer' );

/**
 * nice_tracking_code()
 *
 * print tracking code, from nice_options
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_tracking_code' ) ) :

	function nice_tracking_code(){

		$str = get_option( 'nice_tracking_code' );
		if ( $str != "" ) echo stripslashes( $str ) . "\n";

	}

endif;

/**
 * nice_twitter_script()
 *
 * get twitter scripts to include latest tweets.
 *
 * @since 1.0.0
 * @deprecated 1.0.6
 *
 */
if ( ! function_exists( 'nice_twitter_script' ) ) :

	function nice_twitter_script ( $id, $username, $limit ){

		?>

		<script type="text/javascript">
		function relative_time(time_value) {
			var values = time_value.split(" ");
			time_value = values[1] + " " + values[2] + ", " + values[5] + " " + values[3];
			var parsed_date = Date.parse(time_value);
			var relative_to = (arguments.length > 1) ? arguments[1] : new Date();
			var delta = parseInt((relative_to.getTime() - parsed_date) / 1000);
			delta = delta + (relative_to.getTimezoneOffset() * 60);
			if (delta < 60) {
			return 'less than a minute ago';
			} else if(delta < 120) {
			return 'about a minute ago';
			} else if(delta < (60*60)) {
			return (parseInt(delta / 60)).toString() + ' <?php _e( 'minutes ago', 'nicethemes' ); ?>';
			} else if(delta < (120*60)) {
			return 'about an hour ago';
			} else if(delta < (24*60*60)) {
			return 'about ' + (parseInt(delta / 3600)).toString() + ' <?php _e( 'hours ago', 'nicethemes' ); ?>';
			} else if(delta < (48*60*60)) {
			return '1 day ago';
			} else {
			return (parseInt(delta / 86400)).toString() + ' <?php _e( 'days ago', 'nicethemes' ); ?>';
			}
		}

		function NiceTwitterCallback(twitters) {
		var statusHTML = [];
		for (var i=0; i<twitters.length; i++){
		var username = twitters[i].user.screen_name;
		var status = twitters[i].text.replace(/((https?|s?ftp|ssh)\:\/\/[^"\s\<\>]*[^.,;'">\:\s\<\>\)\]\!])/g, function(url) {
		return '<a href="'+url+'">'+url+'</a>';
		}).replace(/\B@([_a-z0-9]+)/ig, function(reply) {
		return reply.charAt(0)+'<a href="http://twitter.com/'+reply.substring(1)+'">'+reply.substring(1)+'</a>';
		});
		statusHTML.push( '<li><span>'+status+'</span> <a style="font-size:85%" href="http://twitter.com/'+username+'/statuses/'+twitters[i].id_str+'">'+relative_time(twitters[i].created_at)+'</a></li>' );
		}
		document.getElementById( 'twitter_update_list_<?php echo $id; ?>' ).innerHTML = statusHTML.join( '' );
		}
		</script>

		<script type="text/javascript" src="https://api.twitter.com/1/statuses/user_timeline.json?id=<?php echo $username; ?>&amp;callback=NiceTwitterCallback&amp;count=<?php echo $limit; ?>"></script>
		<?php
	}

endif;

/**
 * nice_wp_head()
 *
 * include all the functions to be triggered
 * when wp_head is invoked.
 *
 * @since 1.0.0
 * @updated 1.0.2
 *
 */

if ( ! function_exists( 'nice_wp_head' ) ) :

	function nice_wp_head(){

		if ( function_exists( 'nice_favicon' ) ) {
				nice_favicon();
		}

		if ( function_exists( 'nice_skin_css' ) ) {
				nice_skin_css();
		}

		if ( function_exists( 'nice_custom_css' ) ) {
				nice_custom_css();
		}

		if ( function_exists( 'nice_custom_js' ) ) {
				nice_custom_js();
		}

	}

endif;

add_action( 'wp_head', 'nice_wp_head', 10 );


/**
 * nice_favicon()
 *
 * print tracking favicon, from nice_options
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_favicon' ) ):

	function nice_favicon(){

		$favicon = '';
		$favicon = get_option( 'nice_favicon' );
		if ( $favicon <> '' ){
			echo "\n" . "<!-- Custom Favicon -->\n";
			echo '<link rel="shortcut icon" href="' . esc_url( $favicon ) . '"/>' . "\n";
		}
	}

endif;

/**
 * nice_custom_css()
 *
 * include custom.css and print custom css from nice options
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_custom_css' ) ) :

	function nice_custom_css(){

		$custom_css = '';
		$custom_css = get_option( 'nice_custom_css' );
		if ( $custom_css <> '' ){
			$html = "\n" . "<!-- Custom CSS -->\n" . "<style type=\"text/css\">\n" . $custom_css . "</style>\n\n";
			echo stripslashes( $html );
		}

		if ( file_exists( get_stylesheet_directory() . '/custom.css' ) )
			echo "\n" . "<!-- Custom CSS -->\n" . '<link href="'. get_template_directory_uri() . '/custom.css" rel="stylesheet" type="text/css" />' . "\n";
	}

endif;

/**
 * nice_skin_css()
 *
 *
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_skin_css' ) ) :

	function nice_skin_css(){

		$skin = '';
		$skin = get_option( 'nice_skin_stylesheet' );
		if ( $skin <> '' ){
			$html = "\n" . "<!-- Skin Stylesheet -->\n" . '<link id="skin" href="'. get_template_directory_uri() . '/skins/' . $skin . '" rel="stylesheet" type="text/css" />' . "\n";
			echo stripslashes( $html );
		}
	}

endif;

/**
 * nice_custom_js()
 *
 * print custom js from nice options, include custom.js if exists
 *
 * @since 1.0.25
 *
 */
if ( ! function_exists( 'nice_custom_js' ) ) :

	function nice_custom_js(){

		$custom_js = '';
		$custom_js = get_option( 'nice_custom_js' );
		if ( $custom_js <> '' ) {
			$html = "\n" . "<!-- Custom JS (inline) -->\n" . "<script type=\"text/javascript\">\n" . $custom_js . "</script>\n\n";
			echo stripslashes( $html );
		}

		if ( file_exists( get_template_directory_uri() . '/custom.js' ) )
			echo "\n" . "<!-- Custom JS -->\n" . '<script type="text/javascript" src="'. get_template_directory_uri() . '/custom.js" ></script>' . "\n";
	}

endif;


/**
 * nice_excerpt()
 *
 * shorten excerpts as you want.
 *
 * @param (int) $length of excerpt
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_excerpt' ) ) :

	function nice_excerpt( $length = 200 ){

		global $post;

		$nice_excerpt = substr( get_the_excerpt(), 0, $length ); //truncate excerpt according to $len
		if(strlen( $nice_excerpt ) < strlen( get_the_excerpt() ) ) {
			$nice_excerpt = $nice_excerpt . "...";
		}
		echo "<p>" . $nice_excerpt . "</p>" . "\n";
	}

endif;

/* WP image support */
if ( function_exists( 'add_theme_support' ) ) {

	if ( function_exists( 'add_image_size' ) )
		add_theme_support( 'post-thumbnails' );

	add_theme_support( 'automatic-feed-links' );
}

/* comments reply */
if ( ! function_exists( 'nice_comment_reply' ) ){
	function nice_comment_reply(){
		if ( is_singular() ) wp_enqueue_script( 'comment-reply' );
	}
}

add_action( 'wp_print_scripts', 'nice_comment_reply', 10 );

/**
 * nice_load_textdomain()
 *
 * load textdomain
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_load_textdomain' ) ) :

	function nice_load_textdomain() {
		load_theme_textdomain( 'nicethemes', get_template_directory() . '/lang' );
	}

endif;

add_action( 'init', 'nice_load_textdomain', 10 );

/**
 * nice_sidebar_position_class()
 *
 * set bodyclass for the sidebar alignment.
 *
 * @since 1.0.1
 *
 */
add_filter( 'body_class', 'nice_sidebar_position_class' );

if ( ! function_exists( 'nice_sidebar_position_class' ) ) {

	function nice_sidebar_position_class( $classes ) {

		$position = get_option( 'nice_sidebar_position' );

		if( $position == 'left' ) {

			$classes[] = 'sidebar-left';

		} elseif ( $position == 'right' ){

			$classes[] = 'sidebar-right';

		} elseif ( $position == 'none' ){

			$classes[] = 'sidebar-none';

		} else {

			$classes[] = 'sidebar-right';
		}

		return $classes;

	}

}

/**
 * nice_browser_class()
 *
 * set bodyclass for the visitor browser
 *
 * @since 1.0.1
 *
 */
add_filter( 'body_class', 'nice_browser_class' );

if ( ! function_exists( 'nice_browser_class' ) ) :

	function nice_browser_class( $classes )
	{
		global $is_lynx, $is_gecko, $is_IE, $is_opera, $is_NS4, $is_safari, $is_chrome, $is_iphone;

		if		( $is_lynx) 	$classes[] = 'lynx';
		elseif	( $is_gecko ) 	$classes[] = 'gecko';
		elseif	( $is_opera ) 	$classes[] = 'opera';
		elseif	( $is_NS4 ) 	$classes[] = 'ns4';
		elseif	( $is_safari ) 	$classes[] = 'safari';
		elseif	( $is_chrome ) 	$classes[] = 'chrome';
		elseif	( $is_IE ) 		$classes[] = 'ie';
		else 					$classes[] = 'unknown';
		if		( $is_iphone) 	$classes[] = 'iphone';

		return $classes;
	}

endif;


/**
 * nice_custom_font_css()
 *
 * return the css for custom font option.
 *
 * @since 1.0.3
 *
 */

if ( ! function_exists( 'nice_custom_font_css' ) ) :

	function nice_custom_font_css( $option ) {

		global $google_fonts;

		foreach ( $google_fonts as $google_font ) {

			// if it is a google font, then put it between ''
			if ( $option[ 'family' ] == $google_font[ 'name' ] )
				$option[ 'family' ] = "'" . $option[ 'family' ] . "', arial, sans-serif";

		} // END foreach

		if ( !@$option["style"] && !@$option["size"] && !@$option["color"] ){
			return 'font-family: ' . stripslashes( $option["family"] ) . ';';
		} else{
			if ( !@$option["size"]){
				return 'font-family: ' . stripslashes( $option["family"] ) . '; font-style: ' . $option["style"] . '; color: ' . $option["color"] . ' !important;';
			} else {
				return 'font: ' . $option["style"] . ' ' . $option["size"] . 'px ' . stripslashes( $option["family"] ) . '; color: ' . $option["color"] . ' !important;';
			}
		}

	}

endif;


/**
 * nice_blog_url()
 *
 * return the url of the page configured for posts.
 *
 * @since 1.0.6
 *
 */

if ( ! function_exists( 'nice_blog_url' ) ) :

	function nice_blog_url(){

		if ( $posts_page_id = get_option( 'page_for_posts' ) ){
			return home_url( get_page_uri( $posts_page_id ) );
		} else {
			return home_url();
		}

	}

endif;


/**
 * nice_add_version_meta()
 *
 * Add generator metadata with the fw and theme version.
 *
 * @since 1.0.6
 *
 */
function nice_add_version_meta() {

	$nice_framework_version = get_option( 'nice_framework_version' );
	$nice_theme = wp_get_theme();

	echo '<meta name="generator" content="' . $nice_theme->get( 'Name' ) . ' ' . $nice_theme->get( 'Version' ) .'" />' . "\n";
	echo '<meta name="generator" content="Nice Framework ' . $nice_framework_version . '" />' . "\n";
}

add_action( 'wp_head', 'nice_add_version_meta', 8 );

/**
 * nice_user_ip()
 *
 * Get the visitor IP address
 *
 * @since 1.0.6
 *
 */
if ( ! function_exists( 'nice_user_ip' ) ) :

	function nice_user_ip() {
		return $_SERVER['REMOTE_ADDR'];
	}

endif;


/**
 * nice_upload_mimes()
 *
 * Adds more support for file uploads in the theme.
 *
 * @since 1.1.2
 *
 */

add_filter( 'upload_mimes', 'nice_upload_mimes' );

if ( ! function_exists( 'nice_upload_mimes' ) ) {

	function nice_upload_mimes( $existing_mimes = array() ) {

		//  Add file extension 'extension' with mime type 'mime/type'
		$existing_mimes['ico'] = 'image/ico';

		// return the new full result
		return $existing_mimes;
	}

}

/*-----------------------------------------------------------------------------------*/
/*	Enable shortcodes in Text Widgets
/*-----------------------------------------------------------------------------------*/

add_filter( 'widget_text', 'shortcode_unautop' );
add_filter( 'widget_text', 'do_shortcode' );

/*-----------------------------------------------------------------------------------*/
/*	Enable shortcodes in the excerpt
/*-----------------------------------------------------------------------------------*/

add_filter( 'the_excerpt', 'do_shortcode' );

?>