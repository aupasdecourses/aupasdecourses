<?php
/*
*	FAQ.
*/

add_action( 'init', 'add_nice_faq' );

if ( ! function_exists( 'add_nice_faq' ) ) :

	function add_nice_faq()
	{
	  $labels = array(
		'name' 					=> __( 'FAQs', 'nicethemes' ),
		'singular_name' 		=> __( 'FAQ', 'nicethemes' ),
		'add_new' 				=> __( 'Add New', 'nicethemes' ),
		'add_new_item' 			=> __( 'Add New FAQ', 'nicethemes' ),
		'edit_item' 			=> __( 'Edit FAQ', 'nicethemes' ),
		'new_item' 				=> __( 'New FAQ', 'nicethemes' ),
		'view_item' 			=> __( 'View FAQ', 'nicethemes' ),
		'search_items' 			=> __( 'Search FAQs', 'nicethemes' ),
		'not_found' 			=> __( 'No FAQs found', 'nicethemes' ),
		'not_found_in_trash' 	=> __( 'No FAQs found in Trash', 'nicethemes' ),
		'parent_item_colon' 	=> ''
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array( 'slug' => 'faq' ),
		'capability_type' 		=> 'post',
		'hierarchical' 			=> false,
		'menu_icon' 			=> nice_admin_menu_icon( 'btn-faq.png' ),
		'menu_position' 		=> null,
		'supports' 				=> array( 'title', 'editor', 'page-attributes' )
	);

	register_post_type( 'faq', $args );

	}

endif;


if ( ! function_exists( 'nice_faq_title' ) ) :

	function nice_faq_title( $title ){

		 $screen = get_current_screen();

		 if  ( $screen->post_type == 'faq' ) $title = __( 'Enter the FAQ question', 'nicethemes');

		 return $title;
	}

endif;

add_filter( 'enter_title_here', 'nice_faq_title' );

?>