<?php
/*
*	Articles.
*/

add_action( 'init', 'add_nice_article' );

if ( ! function_exists( 'add_nice_article' ) ) :

	function add_nice_article()
	{
	$labels = array(
		'name' 					=> __( 'Articles', 'nicethemes' ),
		'singular_name' 		=> __( 'Article', 'nicethemes' ),
		'add_new' 				=> __( 'Add New', 'nicethemes' ),
		'add_new_item' 			=> __( 'Add New Article', 'nicethemes' ),
		'edit_item' 			=> __( 'Edit Article', 'nicethemes' ),
		'new_item' 				=> __( 'New Article', 'nicethemes' ),
		'view_item' 			=> __( 'View Article', 'nicethemes' ),
		'search_items' 			=> __( 'Search Articles', 'nicethemes' ),
		'not_found' 			=> __( 'No Articles found', 'nicethemes' ),
		'not_found_in_trash' 	=> __( 'No Articles found in Trash', 'nicethemes' ),
		'parent_item_colon' 	=> ''
	);

	$args = array(
		'labels' 				=> $labels,
		'public' 				=> true,
		'publicly_queryable' 	=> true,
		'show_ui' 				=> true,
		'query_var' 			=> true,
		'rewrite' 				=> array( 'slug' => 'article' ),
		'capability_type' 		=> 'page',
		'hierarchical' 			=> false,
		'menu_icon' 			=> nice_admin_menu_icon( 'btn-article.png' ),
		'menu_position' 		=> null,
		'supports' 				=> array( 'title', 'editor', 'thumbnail', 'page-attributes', 'comments', 'author', 'revisions' )
	);

	register_post_type( 'article', $args );

	}

endif;

// Taxonomies for articles.
function create_article_taxonomies() {

	$nice_category_labels = array(
		'name' =>				__( 'Categories', 'nicethemes' ),
		'singular_name' => 		__( 'Category', 'nicethemes' ),
		'search_items' =>  		__( 'Categories', 'nicethemes' ),
		'all_items' =>			__( 'All Categories', 'nicethemes' ),
		'parent_item' => 		__( 'Parent Category', 'nicethemes' ),
		'parent_item_colon' => 	__( 'Parent Category:', 'nicethemes' ),
		'edit_item' => 			__( 'Edit Category', 'nicethemes' ),
		'update_item' => 		__( 'Update Category', 'nicethemes' ),
		'add_new_item' => 		__( 'Add New Category', 'nicethemes' ),
		'new_item_name' => 		__( 'New Category', 'nicethemes' )
	);

	register_taxonomy( 'article-category', array( 'article' ) ,
							array(
								'hierarchical' 	=> true,
								'labels' 		=> $nice_category_labels,
								'show_ui' 		=> true,
								'query_var' 	=> true,
								'rewrite' 		=> array( 'slug' => 'article-category' ),
							)
					 );

	$nice_tag_labels = array(
		'name' =>				__( 'Tags', 'nicethemes' ),
		'singular_name' => 		__( 'Tag', 'nicethemes' ),
		'search_items' =>  		__( 'Tags', 'nicethemes' ),
		'all_items' =>			__( 'All Tags', 'nicethemes' ),
		'parent_item' => 		__( 'Parent Tag', 'nicethemes' ),
		'parent_item_colon' => 	__( 'Parent Tag:', 'nicethemes' ),
		'edit_item' => 			__( 'Edit Tag', 'nicethemes' ),
		'update_item' => 		__( 'Update Tag', 'nicethemes' ),
		'add_new_item' => 		__( 'Add New Tag', 'nicethemes' ),
		'new_item_name' => 		__( 'New Tag', 'nicethemes' )
	);

	register_taxonomy( 'article-tag', array( 'article', 'faq' ) ,
							array(
								'hierarchical' => false,
								'labels' => $nice_tag_labels,
								'show_ui' => true,
								'query_var' => true,
								'rewrite' => array( 'slug' => 'article-tag' ),
							)
					 );


}

add_action( 'init', 'create_article_taxonomies', 0 );

?>