<?php
/*
	Shortcodes.php

*/


/**
 * nicethemes_knowledgebase_shortcode()
 *
 * Desc
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nicethemes_knowledgebase_shortcode' ) ) :

	function nicethemes_knowledgebase_shortcode( $atts ) {

		extract( shortcode_atts( array(
									'columns'		=> '3',
									'category'		=> '0',
									'exclude'		=> '',
									'include'		=> ''
									), $atts ) );

		$html = nicethemes_knowledgebase( array( 'echo' => false,
											'columns'	=> $columns,
											'category'	=> $category,
											'exclude'	=> $exclude,
											'include'	=> $include ) );
		return  $html;
	}

endif;

add_shortcode( 'nicethemes_knowledgebase', 'nicethemes_knowledgebase_shortcode' );

/**
 * nicethemes_infoboxes_shortcode()
 *
 * desc.
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nicethemes_infoboxes_shortcode' ) ) :

	function nicethemes_infoboxes_shortcode( $atts ) {

		extract( shortcode_atts( array(
			'columns'		=> '3',
			'rows'			=> false,
			'numberposts'	=> '3'
		), $atts ) );

		$html = nicethemes_infoboxes( array( 'echo' 			=> false,
										'columns'		=> $columns,
										'rows'			=> $rows,
										'numberposts'	=> $numberposts ) );
		return  $html;
	}

endif;

add_shortcode( 'nicethemes_infoboxes', 'nicethemes_infoboxes_shortcode' );


/**
 * nicethemes_gallery_shortcode()
 *
 * desc
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nicethemes_gallery_shortcode' ) ) :

	function nicethemes_gallery_shortcode( $atts ) {

		extract( shortcode_atts( array(
				'columns' 	=> '4',
				'rows'		=> false,
				'ids'		=> null
		), $atts ) );

		$html = nicethemes_gallery( array( 'echo' 			=> false,
										'columns'		=> $columns,
										'rows'			=> $rows,
										'ids'	=> $ids ) );

		return  $html;
	}

endif;

add_shortcode( 'nicethemes_gallery', 'nicethemes_gallery_shortcode' );


?>