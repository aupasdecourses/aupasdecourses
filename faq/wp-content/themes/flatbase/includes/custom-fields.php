<?php

// add action nice_fields()
add_action( 'admin_head', 'nice_fields' );


/**
 * nice_fields()
 *
 * Load array with custom fields depending on post type,
 * save the array into wp options.
 *
 * @since 1.0.0
 *
 */

function nice_fields()
{

	$nice_fields = array();

	global $post;

	if ( get_post_type() == 'page' || ! get_post_type() ) {

		$nice_fields[] = array( 	'name'	=> 'page-info',
									'label'	=> __( 'Page Information', 'nicethemes' ),
									'type'	=> 'info',
									'desc'	=> __( '<p>You can select the page template from the "Template" dropdown on the "Page Attributes" box.</p><p>If you are using the "Gallery Template" plase be sure to upload images from the "Add Media" button at the top of this page. (Don\'t insert the gallery in the page content, just add the images).</p>', 'nicethemes' ) );


	}

	/* Post */
	if ( get_post_type() == 'post' || ! get_post_type() ) {


		$nice_fields[] = array (  	'name'	=> 'embed',
									'std'	=> '',
									'label'	=> __( 'Video Embed Code', 'nicethemes' ),
									'type'	=> 'textarea',
									'desc'	=> __( 'Enter the video embed code for your video (YouTube, Vimeo or similar).', 'nicethemes' )
									);

	} // end post

	/* Post */
	if ( get_post_type() == 'article' || ! get_post_type() ) {


		$nice_fields[] = array (  	'name'	=> 'embed',
									'std'	=> '',
									'label'	=> __( 'Video Embed Code', 'nicethemes' ),
									'type'	=> 'textarea',
									'desc'	=> __( 'Enter the video embed code for your video (YouTube, Vimeo or similar).', 'nicethemes' )
									);

	} // end post


	/* Infoboxes */
	if ( get_post_type() == 'infobox' || ! get_post_type() ) {

		$nice_fields[] = array( 	'name'	=> 'infobox-item-info',
									'label'	=> __( 'Info Box Image', 'nicethemes' ),
									'type'	=> 'info',
									'desc'	=> __( 'Info Boxes Items use the WordPress featured image as the feedback image. Don\'t know what featured images are? How to use them? <a href="http://en.support.wordpress.com/featured-images/#setting-a-featured-image">Take a look at WordPress docs on Featured Images</a>.', 'nicethemes' )
			);

		$nice_fields[] = array (
									'name'	=> 'infobox_readmore',
									'std'	=> '',
									'label'	=> __( '"Read more" URL', 'nicethemes' ),
									'type'	=> 'text',
									'desc'	=> __( 'Add an URL for your Read More button in your Info Box on homepage (optional)', 'nicethemes' )
			);

		$nice_fields[] = array (
									'name'	=> 'infobox_readmore_text',
									'std'	=> '',
									'label'	=> __( '"Read more" Text', 'nicethemes' ),
									'type'	=> 'text',
									'desc'	=> __( 'Add the anchor text for the "Read More" link.', 'nicethemes' )
			);

		$nice_fields[] = array (
									'name'	=> 'infobox_readmore_window',
									'std'	=> '',
									'label'	=> __( 'Open in a new window/tab', 'nicethemes' ),
									'type'	=> 'checkbox',
									'desc'	=> __( 'Tick this option if you want your link to be opened in a new window/tab (optional)', 'nicethemes' )
								);

	} // end infobox



	if ( get_option( 'nice_custom_fields' ) != $nice_fields ) update_option( 'nice_custom_fields', $nice_fields );

}


?>