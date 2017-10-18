<?php
/*
*	Infobox
*/

add_action( 'init', 'add_nice_infoboxes' );

if ( ! function_exists( 'add_nice_infoboxes' ) ) :

	function add_nice_infoboxes(){

	  $labels = array(
					'name'					=> __( 'Info Boxes', 'nicethemes' ),
					'singular_name'			=> __( 'Info Box', 'nicethemes' ),
					'add_new'				=> __( 'Add New', 'nicethemes' ),
					'add_new_item'			=> __( 'Add New Info Box', 'nicethemes' ),
					'edit_item'				=> __( 'Edit Info Box', 'nicethemes' ),
					'new_item'				=> __( 'New Info Box', 'nicethemes' ),
					'view_item'				=> __( 'View Info Box', 'nicethemes' ),
					'search_items'			=> __( 'Search Info Boxes', 'nicethemes' ),
					'not_found'				=> __( 'No Info Boxes found', 'nicethemes' ),
					'not_found_in_trash'	=> __( 'No Info Boxes found in Trash', 'nicethemes' ),
					'parent_item_colon'		=> ''
				  );

	  $args = array(
					'labels'				=> $labels,
					'public'				=> false,
					'publicly_queryable'	=> false,
					'show_ui'				=> true,
					'query_var'				=> true,
					'rewrite'				=> true,
					'capability_type'		=> 'post',
					'hierarchical'			=> false,
					'menu_icon'				=> nice_admin_menu_icon( 'btn-infobox.png' ),
					'menu_position'			=> null,
					'supports'				=> array( 'title','editor', 'thumbnail', 'page-attributes' )
				  );

		register_post_type( 'infobox', $args );
	}

endif;

?>