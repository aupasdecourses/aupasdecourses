<?php
/**
 * Table of Contents (media.php)
 *
 *	- nice_jpeg_quality()
 *	- nice_embed()
 *	- get_nice_image_path()
 *	- nice_image()
 *	- vt_resize()
 *
 */

/**
 * nice_jpeg_quality()
 *
 * Set the compression quality for images.
 * Takes the value from the theme options. If it's not defined, it set it to the default value = 90
 * Later used for the filters.
 *
 * @since 1.0.6
 *
 * @return (int)
 */
 
if ( ! function_exists( 'nice_jpeg_quality' ) ) :

	function nice_jpeg_quality() {
		
		global $nice_options;
		
		if ( isset( $nice_options['nice_jpeg_quality'] ) && $nice_options['nice_jpeg_quality'] > 0 && $nice_options['nice_jpeg_quality'] <= 100 )
			return $nice_options['nice_jpeg_quality'];
	
		return 90;
	}

endif;

add_filter( 'jpeg_quality', 			'nice_jpeg_quality' );
add_filter( 'wp_editor_set_quality', 	'nice_jpeg_quality' );

/**
 * nice_embed()
 *
 * nicely embed videos
 *
 * @since 1.0.0
 *
 * @param (array) [field, width, height, class, id]
 * @return (str/bool) html/0
 */
if ( ! function_exists( 'nice_embed' ) ) :

function nice_embed( $args )
{
	//Defaults
	$field 		= 'embed';
	$width 		= null;
	$height		= null;
	$class 		= 'video';
	$id			= null;
	$wrap		= true;
	$echo 		= true;
	$embed		= '';
	$embed_id	= 'nice_embed';

	if ( ! is_array( $args ) ) parse_str( $args, $args );

	extract( $args );

	if ( empty( $embed ) ){

		if( empty( $id ) )
		{
			global $post;
			$id = $post->ID;
		}

		$embed = get_post_meta( $id, $field, true );

	}

	if ( !empty ( $embed ) ) :

		$embed = html_entity_decode( $embed ); // Decode HTML entities.

		$embed = nice_add_html_att ( array( 'tag' => 'id', 'value' => $embed_id, 'code' => $embed ) );

		if ( $width || $height ){

			$embed = nice_set_html_att ( array ( 'tag' =>'width', 'value' => $width, 'code' => $embed ) );
			$embed = nice_set_html_att ( array ( 'tag' =>'height', 'value' => $height, 'code' => $embed ) );

		}

		if ( $url = nice_get_html_att( array( 'html' => $embed, 'tag' => 'src' ) ) ){

			if ( strpos( $url, 'youtube' ) > 0 ) {

				$url = nice_add_url_param( array ( 'url' => $url, 'tag' =>'enablejsapi', 'value' => '1' ) );

			} elseif ( strpos( $url, 'vimeo' ) > 0 ) {

				$url = nice_add_url_param( array ( 'url' => $url, 'tag' =>'api', 'value' => '1' ) );
				$url = nice_add_url_param( array ( 'url' => $url, 'tag' =>'player_id', 'value' => $embed_id ) );

			}

			$embed = nice_set_html_att ( array ( 'tag' => 'src', 'value' => $url, 'code' => $embed ) );

		}

		if ( nice_bool( $wrap ) )
			$html = '<div class="'. $class .'">' . $embed . '</div>';
		else
			$html =  $embed ;


		if ( nice_bool( $echo ) ) echo $html;
		else return $html;

	else :

		return false;

	endif;

}

endif;


/**
 * get_nice_image_path()
 *
 * Get image path / works with WPMU
 *
 * @since 1.0.0
 *
 * @param int $thumb_id
 * @return string $scr containing the full path
 */
function get_nice_image_path ( $thumb_id = null, $full_path = false ) {

	$src = wp_get_attachment_url( $thumb_id );

	global $blog_id;

	if ( isset( $blog_id ) && $blog_id > 0 ) {

		$imageParts = explode( '/files/', $src );

		if ( isset( $imageParts[1] ) ) {

			if ( $full_path )
				$src = $imageParts[0] . '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
			else
				$src = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];

		}

	}

	return $src;
}


/**
 * nice_image()
 *
 * display image. If $scr not defined search for
 * featured image or images associated to the post.
 * it uses timthumb.php for resizing
 *
 * @since 1.0.0
 *
 * @param (array) arguments for the function.
 * @return (str) html
 */
if ( ! function_exists( 'nice_image' ) ) {

function nice_image ( $args ) {

	global $post;
	global $nice_options;

	$width			= null;
	$height			= null;
	$class			= '';
	$quality		= 90;
	$limit			= 1;
	$offset			= 0;
	$id				= null;
	$echo 			= true;
	$is_auto_image	= false;
	$src			= '';
	$meta			= '';
	$alignment		= 'c';
	$size			= '';
	$noheight		= '';


	// new vars.
	$alt			= '';
	$title			= '';
	$link			= '';
	$thumb_id		= '';
	$nice_timthumb_resize = true; /* deprecated */
	$nice_wp_resize	= false;

	if ( ! is_array( $args ) ) parse_str( $args, $args );

	extract( $args );

	// Set post ID
	if ( empty( $id ) ) $id = $post->ID;

	// Get standard size, if not defined
	if ( empty( $width ) && empty( $height ) ) {
		$width 	= get_option( 'thumbnail_size_w', '150' );
		$height = get_option( 'thumbnail_size_h', '150' );
	}

	if ( $src <> '' ) {
		$src = esc_attr( $src );
	}

	if ( ! $src ) :

		/* start searching for the image */
		// the ID is an attachment
		if ( get_post_type( $id ) == 'attachment' ):

			// get the data from the attachment
			$thumb_id = $id;
			$src = get_nice_image_path( $id );


		// they send a post/page/cpt
		elseif ( has_post_thumbnail( $id ) ) :

			$thumb_id = get_post_thumbnail_id( $id );

			$src = get_nice_image_path( $thumb_id );

		// they send nothing, get image from the content.
		else:

			// check the first attachment
			$attachments = get_children( array(	'post_parent' 		=> $id,
												'numberposts' 		=> $limit,
												'post_type' 		=> 'attachment',
												'post_mime_type' 	=> 'image',
												'order' 			=> 'DESC',
												'orderby' 			=> 'menu_order date'
												)
										);

			// Search for and get the post attachment
			if ( ! empty( $attachments ) ) :

				// get the first attachment.
				$thumb_id = get_post_thumbnail_id( array_shift( array_values( $attachments ) )->ID );
				$src = get_nice_image_path( array_shift( array_values( $attachments ) )->ID );

			else :

				// retrieve the post content to find the first <img> appearance
				$matches = '';
				$post = get_post( $id );

				ob_start();
				ob_end_clean();
				$how_many = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );

				if ( ! empty( $matches[1][0] ) ) $src = $matches[1][0];


			endif;

		endif;

	endif;


	// OUTPUT
	$output = '';

	// Image CSS class.
	if ( $class ) $class = 'nice-image ' . esc_attr( $class ); else $class = 'nice-image';

	// Image metadata
	if ( $thumb_id ){

		if ( $alt == '' ) 	$alt 	= esc_attr( get_post_meta( $thumb_id, '_wp_attachment_image_alt', true ) );
		if ( $title == '' ) $title 	= esc_attr( get_the_title( $thumb_id ) );

	}

	if ( nice_bool( get_option( 'nice_wp_resize_crop' ) ) ){

		$vt_crop = get_option( 'nice_wp_resize_crop' );

		// resize image

		if ( $thumb_id ){
			// resize image
			$vt_image = vt_resize( $thumb_id, '', $width, $height, $vt_crop );

			$src = $vt_image['url'];

		} elseif ( $src ){

			$vt_image = vt_resize( '', $src, $width, $height, $vt_crop );

			$src = $vt_image['url'];

		}

	} else {

		// use the thumbnail sizes
		if ( $size )
			$thumb_size = $size; // for predefined size
		else
			$thumb_size = array( $width, $height ); // on the fly size

		//$img_link = get_the_post_thumbnail( $thumb_id, $thumb_size, array( 'class' => 'nice-image ' . esc_attr( $class ) ) );
		$img_data = wp_get_attachment_image_src( $thumb_id, $thumb_size );

		if ( ! empty( $img_data ) )
			$src = $img_data[0];

	}

	// Start generating the output for the different resizing options.
	if ( ! empty( $vt_image['url'] ) ){

		$output .= '<img src="' .  esc_url( $vt_image['url'] ) . '" class="' . $class . '"  title="' . $title . '" alt="' . $alt . '"  />';

	} elseif( ! empty( $src ) ) {

		$set_width = ' width="' . esc_attr( $width ) . '" ';
		$set_height = '';

		if ( ! $noheight && 0 < $height )
		$set_height = ' height="' . esc_attr( $height ) . '" ';

		// WP resize without cropping.
		$output .= '<img src="' .  esc_url( $src ) . '" class="' . $class . '"  title="' . $title . '" alt="' . $alt . '" ' . $set_width . ' ' . $set_height . ' />';

	}

	if ( nice_bool( $echo ) ) echo $output; else return $output;
}
}


/*-----------------------------------------------------------------------------------*/
/* vt_resize - Resize images dynamically using wp built in functions
/*-----------------------------------------------------------------------------------*/
/*
 * Resize images dynamically using wp built in functions
 * Victor Teixeira
 *
 * php 5.2+
 *
 * Exemplo de uso:
 *
 * <?php
 * $thumb = get_post_thumbnail_id();
 * $image = vt_resize( $thumb, '', 140, 110, true );
 * ?>
 * <img src="<?php echo $image[url]; ?>" width="<?php echo $image[width]; ?>" height="<?php echo $image[height]; ?>" />
 *
 * @param int $attach_id
 * @param string $img_url
 * @param int $width
 * @param int $height
 * @param bool $crop
 * @return array
 */
if ( ! function_exists( 'vt_resize' ) ) :

	function vt_resize( $attach_id = null, $img_url = null, $width, $height, $crop = false ) {

		// Cast $width and $height to integer
		$width = intval( $width );
		$height = intval( $height );

		// this is an attachment, so we have the ID
		if ( $attach_id ) {
			$image_src = wp_get_attachment_image_src( $attach_id, 'full' );
			$file_path = get_attached_file( $attach_id );
		// this is not an attachment, let's use the image url
		} else if ( $img_url ) {
			$file_path = parse_url( esc_url( $img_url ) );
			$file_path = $_SERVER['DOCUMENT_ROOT'] . $file_path['path'];

			// Look for Multisite Path
			if( file_exists( $file_path ) === false ){

				global $blog_id;
				$file_path = parse_url( $img_url );

				if ( preg_match( "/files/", $file_path['path'] ) ) {

					$path = explode( '/', $file_path['path'] ) ;

					foreach( $path as $k => $v ){

						if( $v == 'files' ){
							$path[$k-1] = 'wp-content/blogs.dir/' . $blog_id;
						}

					}

					$path = implode( '/',$path );
				}

				$file_path = $_SERVER['DOCUMENT_ROOT'] . $path;
			}
			//$file_path = ltrim( $file_path['path'], '/' );
			//$file_path = rtrim( ABSPATH, '/' ).$file_path['path'];

			$orig_size = getimagesize( $file_path );

			$image_src[0] = $img_url;
			$image_src[1] = $orig_size[0];
			$image_src[2] = $orig_size[1];
		}

		$file_info = pathinfo( $file_path );

		// check if file exists
		if ( !isset( $file_info['dirname'] ) && !isset( $file_info['filename'] ) && !isset( $file_info['extension'] )  )
			return;

		$base_file = $file_info['dirname'].'/'.$file_info['filename'].'.'.$file_info['extension'];
		if ( !file_exists($base_file) )
			return;

		$extension = '.'. $file_info['extension'];

		// the image path without the extension
		$no_ext_path = $file_info['dirname'].'/'.$file_info['filename'];

		$cropped_img_path = $no_ext_path.'-'.$width.'x'.$height.$extension;

		// checking if the file size is larger than the target size
		// if it is smaller or the same size, stop right here and return
		if ( $image_src[1] > $width ) {
			// the file is larger, check if the resized version already exists (for $crop = true but will also work for $crop = false if the sizes match)
			if ( file_exists( $cropped_img_path ) ) {
				$cropped_img_url = str_replace( basename( $image_src[0] ), basename( $cropped_img_path ), $image_src[0] );

				$vt_image = array (
					'url' => $cropped_img_url,
					'width' => $width,
					'height' => $height
				);
				return $vt_image;
			}

			// $crop = false or no height set
			if ( $crop == false OR !$height ) {
				// calculate the size proportionaly
				$proportional_size = wp_constrain_dimensions( $image_src[1], $image_src[2], $width, $height );
				$resized_img_path = $no_ext_path.'-'.$proportional_size[0].'x'.$proportional_size[1].$extension;

				// checking if the file already exists
				if ( file_exists( $resized_img_path ) ) {
					$resized_img_url = str_replace( basename( $image_src[0] ), basename( $resized_img_path ), $image_src[0] );

					$vt_image = array (
						'url' => $resized_img_url,
						'width' => $proportional_size[0],
						'height' => $proportional_size[1]
					);
					return $vt_image;
				}
			}

			// check if image width is smaller than set width
			$img_size = getimagesize( $file_path );
			if ( $img_size[0] <= $width ) $width = $img_size[0];

			// Check if GD Library installed
			if ( ! function_exists ( 'imagecreatetruecolor' ) ) {
				echo 'GD Library Error: imagecreatetruecolor does not exist - please contact your webhost and ask them to install the GD library';
				return;
			}

			// no cache files - let's finally resize it
			if ( function_exists( 'wp_get_image_editor' ) ) {
				$image = wp_get_image_editor( $file_path );
				if ( ! is_wp_error( $image ) ) {
					$image->resize( $width, $height, $crop );
					$save_data = $image->save();
					if ( isset( $save_data['path'] ) ) $new_img_path = $save_data['path'];
				}
			} else {
				$new_img_path = image_resize( $file_path, $width, $height, $crop );
			}

			$new_img_size = getimagesize( $new_img_path );
			$new_img = str_replace( basename( $image_src[0] ), basename( $new_img_path ), $image_src[0] );

			// resized output
			$vt_image = array (
				'url' => $new_img,
				'width' => $new_img_size[0],
				'height' => $new_img_size[1]
			);

			return $vt_image;
		}

		// default output - without resizing
		$vt_image = array (
			'url' => $image_src[0],
			'width' => $width,
			'height' => $height
		);

		return $vt_image;
	}

endif;

?>