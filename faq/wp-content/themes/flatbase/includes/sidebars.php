<?php
/* Theme Sidebars */

if ( ! function_exists( 'nice_sidebars_init' ) ) {

	function nice_sidebars_init(){

		global $nice_options;

		if ( ! function_exists( 'register_sidebar' ) ) { return; }

		register_sidebar( array(
							'name' 			=> 'Primary',
							'id' 			=> 'primary',
							'description' 	=> __( 'Appears in the blog section of the site.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s clearfix">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'Page',
							'id' 			=> 'page',
							'description' 	=> __( 'Appears in the sidebar of the default page template.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s clearfix">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'FAQ',
							'id' 			=> 'faq',
							'description' 	=> __( 'Appears in the sidebar of the FAQ templates.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s clearfix">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'Knowledgebase (Articles)',
							'id' 			=> 'knowledgebase',
							'description' 	=> __( 'Appears in the sidebar of the knowledgebase articles.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s clearfix">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'Pre Footer 1',
							'id' 			=> 'pre-footer-1',
							'description' 	=> __( 'Appears in the pre footer section of the site, the first column.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'Pre Footer 2',
							'id' 			=> 'pre-footer-2',
							'description' 	=> __( 'Appears in the pre footer section of the site, the second column.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		$nice_footer_columns = ( ! empty( $nice_options['nice_footer_columns'] ) ) ? $nice_options['nice_footer_columns'] : '4';

		if ( $nice_footer_columns == '3' || $nice_footer_columns == '4' ){

			register_sidebar( array(
								'name' 			=> 'Pre Footer 3',
								'id' 			=> 'pre-footer-3',
								'description' 	=> __( 'Appears in the pre footer section of the site, the third column.', 'nicethemes' ),
								'before_widget' => '<div class="box widget %2$s">',
								'after_widget' 	=> '</div>',
								'before_title' 	=> '<h4 class="widgettitle">',
								'after_title' 	=> '</h4>',
								)
							);
		}

		if ( $nice_footer_columns == '4' ){

			register_sidebar( array(
								'name' 			=> 'Pre Footer 4',
								'id' 			=> 'pre-footer-4',
								'description' 	=> __( 'Appears in the pre footer section of the site, the fourth column.', 'nicethemes' ),
								'before_widget' => '<div class="box widget %2$s">',
								'after_widget' 	=> '</div>',
								'before_title' 	=> '<h4 class="widgettitle">',
								'after_title' 	=> '</h4>',
								)
							);

		}

		register_sidebar( array(
							'name' 			=> 'Footer 1',
							'id' 			=> 'footer-1',
							'description' 	=> __( 'Appears in the footer section of the site, the first column.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		register_sidebar( array(
							'name' 			=> 'Footer 2',
							'id' 			=> 'footer-2',
							'description' 	=> __( 'Appears in the footer section of the site, the second column.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

		if ( $nice_footer_columns == '3' || $nice_footer_columns == '4' ){

			register_sidebar( array(
								'name' 			=> 'Footer 3',
								'id' 			=> 'footer-3',
								'description' 	=> __( 'Appears in the footer section of the site, the third column.', 'nicethemes' ),
								'before_widget' => '<div class="box widget %2$s">',
								'after_widget' 	=> '</div>',
								'before_title' 	=> '<h4 class="widgettitle">',
								'after_title' 	=> '</h4>',
								)
							);

		}

		if ( $nice_footer_columns == '4' ){

			register_sidebar( array(
									'name' 			=> 'Footer 4',
									'id' 			=> 'footer-4',
									'description' 	=> __( 'Appears in the footer section of the site, the fourth column.', 'nicethemes' ),
									'before_widget' => '<div class="box widget %2$s">',
									'after_widget' 	=> '</div>',
									'before_title' 	=> '<h4 class="widgettitle">',
									'after_title' 	=> '</h4>',
									)
								);

		}

		register_sidebar( array(
							'name' 			=> 'Forums (bbPress)',
							'id' 			=> 'bbpress',
							'description' 	=> __( 'Appears in the sidebar of the forum templates.', 'nicethemes' ),
							'before_widget' => '<div class="box widget %2$s">',
							'after_widget' 	=> '</div>',
							'before_title' 	=> '<h4 class="widgettitle">',
							'after_title' 	=> '</h4>',
							)
						);

	}
}

add_action( 'init', 'nice_sidebars_init' );

?>