<?php
/**
 * Table of Contents (functions.php)
 *
 *	- is_admin_niceframework()
 *	- is_admin_post()
 *	- nice_set_html_att()
 *	- nice_admin_notice()
 *	- nice_bool()
 *	- nicethemes_more_themes_rss()
 *	- nicethemes_theme_url()
 *	- nice_admin_menu_icon()
 *
 */

/**
 * is_admin_niceframework()
 *
 * check if current page is part of niceframework
 * @since 1.0.0
 *
 * @return (bool)
 */
if ( ! function_exists( 'is_admin_niceframework' ) ) :

	function is_admin_niceframework(){

		if ( isset ( $_REQUEST['page'] ) ){

			$page = $_REQUEST['page'];// sanitize

			if ( substr( $page, 0, 4 ) == 'nice' ) return true;

		}

		return false;

	}

endif;

/**
 * is_admin_post()
 *
 * check if current page is a post page
 *
 * @since 1.0.0
 *
 * @return (bool)
 */

if ( ! function_exists( 'is_admin_post' ) ) :

	function is_admin_post() {

		if ( is_admin_niceframework() ) return true;
		//$_current_url =  strtolower( strip_tags( trim( $_SERVER['REQUEST_URI'] ) ) ); if ( ( substr( basename( $_current_url ), 0, 8 ) == 'post.php' ) || substr( basename( $_current_url ), 0, 12 ) == 'post-new.php' )

		return false;

	}

endif;

/**
 * nice_set_html_att()
 *
 * set attributes to an html element
 *
 * @since 1.0.0
 *
 * @return (bool)
 */
if ( ! function_exists( 'nice_set_html_att' ) ) :

	function nice_set_html_att( $args ){

		// defaults
		$separator = '=';

		if ( ! is_array( $args ) ) parse_str( $args, $args );

		extract( $args );

		if ( $tag && $value ) :

			$regex = '/' . $tag . $separator . '"(.*?)"/';

			$new_value = $tag . $separator . '"' . $value . '"';

			$code = preg_replace( $regex , $new_value , stripslashes( $code ) );

		endif;

		return $code;

	}

endif;

/**
 * nice_add_html_att()
 *
 * add attributes to an html element
 *
 * @since 1.0.1
 *
 * @return (bool)
 */
if ( ! function_exists( 'nice_add_html_att' ) ) :

	function nice_add_html_att( $args ){

		// defaults
		$separator = '=';

		if ( ! is_array( $args ) ) parse_str( $args, $args );

		extract( $args );

		if ( $tag && $value ) :

			$code = preg_replace( "/(<\b[^><]*)>/i", "$1 $tag$separator\"$value\">", $code );

		endif;

		return $code;

	}

endif;

/**
 * nice_get_html_att()
 *
 * get attribute from an html element
 *
 * @since 1.0.1
 *
 * @return (value) or (bool)
 */
if ( ! function_exists( 'nice_get_html_att' ) ) :

	function nice_get_html_att( $args ){

		// defaults
		$separator = '=';

		if ( ! is_array( $args ) ) parse_str( $args, $args );

		extract( $args );

		if ( $html && $tag ) :

			$r = '/' . preg_quote( $tag ) . '=([\'"])?((?(1).+?|[^\s>]+))(?(1)\1)/is' ;

			if ( preg_match ( $r, $html, $match ) ) {
				return urldecode( $match[2] );
			}

		endif;

		return false;

	}

endif;


if ( ! function_exists( 'nice_add_url_param' ) ) :

	function nice_add_url_param ( $args ){

		if ( ! is_array( $args ) ) parse_str( $args, $args );

		extract( $args );

		if ( $url ) {

			$url_data = parse_url ( $url );

			if( ! isset ( $url_data["query"] ) )
				$url_data["query"]="";

			$params = array();

			parse_str( $url_data['query'], $params );

			$params[$tag] = $value;

			$url_data['query'] = http_build_query( $params );

			return nice_build_url( $url_data );
		}

		return false;
	}

endif;


if ( ! function_exists( 'nice_build_url' ) ) :

	function nice_build_url( $url_data ){

		$url = '';

		if ( isset( $url_data['host'] ) ) {

			$url .= $url_data['scheme'] . '://';

			if ( isset( $url_data['user'] ) ) {

				$url .= $url_data['user'];

				if ( isset ( $url_data['pass'] ) ) {
					$url .= ':' . $url_data['pass'];
				}

				$url .= '@';
			}

			$url .= $url_data['host'];

			if ( isset ( $url_data['port']) ) {
				$url .= ':' . $url_data['port'];
			}
		}

		$url .= $url_data['path'];

		if ( isset ( $url_data['query'] ) ) {
			$url .= '?' . $url_data['query'];
		}

		if ( isset ( $url_data['fragment'] ) ) {
			$url .= '#' . $url_data['fragment'];
		}

		return $url;
	}

endif;

/**
 * nice_admin_notice()
 *
 * set attributes to an html element
 *
 * @since 1.0.0
 *
 * @return (bool)
 */
if ( ! function_exists( 'nice_admin_notice' ) ) :

	function nice_admin_notice(){
		/* notices for updates - cominnnggg soooonnnn */

	}

endif;

add_action( 'admin_notices', 'nice_admin_notice' );

/**
 * nice_bool()
 *
 * solve the bool php problem for strings
 *
 * @since 1.0.1
 *
 * @return (bool)
 */
if ( ! function_exists( 'nice_bool' ) ) :

	function nice_bool( $value = false ){

		if ( is_string ( $value ) ) {

			if ( $value && strtolower( $value ) !== 'false' )
				return true;
			else
				return false;

		} else {

			return ( $value ? true : false );
		}

	}

endif;

/**
 * nicethemes_more_themes_rss()
 *
 * fetch the rss feed for themes.
 *
 * @since 1.0.2
 *
 * @return (obj)
 */

if ( ! function_exists( 'nicethemes_more_themes_rss' ) ) :

	function nicethemes_more_themes_rss(){

		include_once( ABSPATH . WPINC . '/feed.php' );

		$rss = fetch_feed( 'http://demo.nicethemes.com/feed/?post_type=theme' );
		if ( ! is_wp_error( $rss ) ) {
			return $rss->get_items();
		}

		return false;

	}

endif;

/**
 * nicethemes_theme_url()
 *
 * build the nicetheme.com theme url.
 *
 * @since 1.0.2
 *
 * @return (string)
 */

if ( ! function_exists( 'nicethemes_theme_url' ) ) :

	function nicethemes_theme_url( $name = '' ){

		return 'http://nicethemes.com/theme/' . trim( sanitize_title( $name ) ) . '/';

	}

endif;


/**
 * nice_unit_wrapper()
 *
 * wrap some value with a unit symbol (i.e.: $12, 29mt, 500px)
 *
 * @since 1.0.6
 *
 * @return (string) (false on error)
 */

if ( ! function_exists( 'nice_unit_wrapper' ) ) :

	function nice_unit_wrapper( $value, $symbol, $symbol_position = 'before' ){

		if ( empty ( $symbol ) ) return false;

		if ( $symbol_position == 'before' ) :
			return $symbol_position . $value;
		else:
			return $value . $value;
		endif;

		return false;

	}

endif;


/**
 * nice_admin_menu_icon()
 *
 * Get the icon path for the CPTs/etc
 * Since WP3.8, the dashboard changed completely,
 * different skins are available and the icons can be light or dark.
 *
 * @since 1.1.0
 *
 * @return (string)
 */
if ( ! function_exists( 'nice_admin_menu_icon' ) ) :

	function nice_admin_menu_icon( $nice_icon = 'btn-nicethemes.png' ){

		// if WP is higher dan 3.8 alpha and the admin color is not light, return the white icons
		if ( version_compare( $GLOBALS['wp_version'], '3.8-alpha', '>' ) && ( get_user_option( 'admin_color' ) != 'light' ) ) {
			$icon = get_template_directory_uri() . '/engine/admin/images/light/' . $nice_icon;
		} else {
			$icon = get_template_directory_uri() . '/engine/admin/images/' . $nice_icon;
		}

		return $icon;

	}

endif;

?>