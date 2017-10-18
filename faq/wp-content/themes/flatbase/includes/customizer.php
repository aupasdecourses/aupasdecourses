<?php

// the customizer only works in WordPress 3.6 or later.
if ( version_compare( $GLOBALS['wp_version'], '3.6', '<' ) )
	return;

/**
 * Front End Customizer
 *
 * WordPress 3.6 Required (for customize_save_after)
 */

/**
 * nice_customizer_save()
 *
 * Action to save the options in the $nice_options array.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_customizer_save' ) ) :

	function nice_customizer_save( $obj ) {

		global $nice_options;

		$nice_options['nice_logo'] = get_option( 'nice_logo' );
		$nice_options['nice_accent_color'] = get_option( 'nice_accent_color' );


		$nice_options['nice_background_color'] = get_option( 'nice_background_color' );
		$nice_options['nice_background_image'] = get_option( 'nice_background_image' );
		$nice_options['nice_background_image_repeat'] = get_option( 'nice_background_image_repeat' );
		$nice_options['nice_background_image_position'] = get_option( 'nice_background_image_position' );
		$nice_options['nice_layout_type'] = get_option( 'nice_layout_type' );

		$nice_options['nice_header_background_image'] = get_option( 'nice_header_background_image' );
		$nice_options['nice_header_background_image_repeat'] = get_option( 'nice_header_background_image_repeat' );
		$nice_options['nice_header_background_image_position'] = get_option( 'nice_header_background_image_position' );
		$nice_options['nice_header_background_color'] = get_option( 'nice_header_background_color' );

		$nice_options['nice_custom_copyright_enable'] = get_option( 'nice_custom_copyright_enable' );
		$nice_options['nice_custom_copyright_text'] = get_option( 'nice_custom_copyright_text' );

		update_option( 'nice_options', $nice_options );

		return true;
	}

endif;

add_action( 'customize_save_after', 'nice_customizer_save' );


/**
 * nice_customizer_register()
 *
 * Add settings to the live customizer.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_customizer_register' ) ) :

function nice_customizer_register( $wp_customize ) {

	// remove the title and tagline section from the live customizer
	$wp_customize->remove_section('title_tagline');

	/*
		Add "Design & Styles" section.
	*/
	$wp_customize->add_section( 'nice_customizer_section_design', array(
		'title'	=> __( 'Design & Styles', 'nicethemes' ),
		'priority'	=> 6
	));

	/* Accent Color*/
	$wp_customize->add_setting(
		'nice_accent_color',
		array(
				'default'		=> '#5bc4be',
				'type'			=> 'option',
				'capability'	=>
				'edit_theme_options'
			)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nice_accent_color',
			array(
				'label'			=> __( 'Accent Color', 'nicethemes' ),
				'section'		=> 'nice_customizer_section_design',
				'settings'		=> 'nice_accent_color'
				)
		)
	);

	/* Background Color */
	$wp_customize->add_setting(
		'nice_background_color',
		array(
			'default'		=> '#f0f0f0',
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nice_background_color',
			array(
				'label'			=> __( 'Background Color', 'nicethemes' ),
				'section'		=> 'nice_customizer_section_design',
				'settings'		=> 'nice_background_color')
		)
	);

	/* Backkground Image */
	$wp_customize->add_setting(
		'nice_background_image',
		array(
			'default'		=> '',
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nice_background_image',
		array(
			'label'			=> __( 'Background Image', 'nicethemes' ),
			'section'		=> 'nice_customizer_section_design',
			'settings'		=> 'nice_background_image',
		)
	) );

	/* Beckground Image Repeat */
	$wp_customize->add_setting(
		'nice_background_image_repeat', array(
			'default'		=> get_option( 'nice_background_image_repeat' ),
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( 'nice_background_image_repeat', array(
		'label'  	=> __( 'Background Image Repeat', 'nicethemes' ),
		'section'	=> 'nice_customizer_section_design',
		'type'		=> 'select',
		'choices'	=> array(
			'no-repeat'	=> 	__( 'No Repeat', 'nicethemes' ),
			'repeat'	=> 	__( 'Repeat', 'nicethemes' ),
			'repeat-x'	=> 	__( 'Repeat horizontally', 'nicethemes' ),
			'repeat-y'	=> 	__( 'Repeat vertically', 'nicethemes' )
			),
	) );

	/* Background Image Position */
	$wp_customize->add_setting(
		'nice_background_image_position', array(
			'default'	=> get_option( 'nice_background_image_position' ),
			'type'		=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( 'nice_background_image_position', array(
		'label'  	=> __( 'Background Image Position', 'nicethemes' ),
		'section'	=> 'nice_customizer_section_design',
		'type'		=> 'select',
		'choices'	=> array(
			'center top'	=> 	__( 'Center Top', 'nicethemes' ),
			'center center'	=> 	__( 'Center Center', 'nicethemes' ),
			'center bottom'	=> 	__( 'Center Bottom', 'nicethemes' ),
			'left top'		=> 	__( 'Left Top', 'nicethemes' ),
			'left center'	=> 	__( 'Left Center', 'nicethemes' ),
			'left bottom'	=> 	__( 'Left Bottom', 'nicethemes' ),
			'right top'		=> 	__( 'Right Top', 'nicethemes' ),
			'right center'	=> 	__( 'Right Center', 'nicethemes' ),
			'right bottom'	=> 	__( 'Right Bottom', 'nicethemes' )
			),
	) );

	/* Layout Type */
	$wp_customize->add_setting(
		'nice_layout_type',
		array(
			'default'		=> 'boxed',
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options'
		)
	);

	$wp_customize->add_control( 'nice_layout_type',
		array(
			'label'  	=> __( 'Layout Type', 'nicethemes'),
			'section'	=> 'nice_customizer_section_design',
			'type'		=> 'select',
			'choices'	=> array(
				'full'	=> __( 'Full Width', 'nicethemes' ),
				'boxed'	=> __( 'Boxed', 'nicethemes' ),
				),
			)
	);

	/*
		Header
	*/
	$wp_customize->add_section( 'nice_customizer_section_header', array(
		'title'		=> __( 'Header', 'nicethemes' ),
		'priority'	=> 7
	));

	/* Logo */
	$wp_customize->add_setting(
		'nice_logo', array(
			'default'		=> get_stylesheet_directory_uri() . '/images/header.jpg',
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nice_logo', array(
		'label'		=> __( 'Logo', 'nicethemes' ),
		'section' 	=> 'nice_customizer_section_header',
		'settings'	=> 'nice_logo',
	) ) );

	/* Header Background Image */
	$wp_customize->add_setting(
		'nice_header_background_image',
		array(
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'nice_header_background_image', array(
		'label'		=> __( 'Header Background Image', 'nicethemes' ),
		'section' 	=> 'nice_customizer_section_header',
		'settings'	=> 'nice_header_background_image',
	) ) );

	/* Header Beckground Image Repeat */
	$wp_customize->add_setting(
		'nice_header_background_image_repeat', array(
			'default'		=> get_option( 'nice_header_background_image_repeat' ),
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( 'nice_header_background_image_repeat', array(
		'label'  	=> __( 'Header Background Image Repeat', 'nicethemes' ),
		'section'	=> 'nice_customizer_section_header',
		'type'		=> 'select',
		'choices'	=> array(
			'no-repeat'	=> 	__( 'No Repeat', 'nicethemes' ),
			'repeat'	=> 	__( 'Repeat', 'nicethemes' ),
			'repeat-x'	=> 	__( 'Repeat horizontally', 'nicethemes' ),
			'repeat-y'	=> 	__( 'Repeat vertically', 'nicethemes' )
			),
	) );

	/* Background Image Position */
	$wp_customize->add_setting(
		'nice_header_background_image_position', array(
			'default'		=> get_option( 'nice_header_background_image_position' ),
			'type'			=> 'option',
			'capability'	=>
			'edit_theme_options',
		)
	);

	$wp_customize->add_control( 'nice_header_background_image_position', array(
	'label'  	=> __( 'Header Background Image Position', 'nicethemes' ),
	'section'	=> 'nice_customizer_section_header',
	'type'		=> 'select',
	'choices'	=> array(
		'center top'	=> 	__( 'Center Top', 'nicethemes' ),
		'center center'	=> 	__( 'Center Center', 'nicethemes' ),
		'center bottom'	=> 	__( 'Center Bottom', 'nicethemes' ),
		'left top'		=> 	__( 'Left Top', 'nicethemes' ),
		'left center'	=> 	__( 'Left Center', 'nicethemes' ),
		'left bottom'	=> 	__( 'Left Bottom', 'nicethemes' ),
		'right top'		=> 	__( 'Right Top', 'nicethemes' ),
		'right center'	=> 	__( 'Right Center', 'nicethemes' ),
		'right bottom'	=> 	__( 'Right Bottom', 'nicethemes' )
		),
	) );

	/* Header Background Color */
	$wp_customize->add_setting(
		'nice_header_background_color', array(
			'default'		=> '#35a49e',
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
		)
	);

	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'nice_header_background_color',
			array( 'label'	=> __( 'Header Background Color', 'nicethemes' ),
				'section'	=> 'nice_customizer_section_header',
				'settings'	=> 'nice_header_background_color')
		)
	);

	/*
		Footer
	*/
	$wp_customize->add_section( 'nice_customizer_section_footer', array(
		'title'		=> __( 'Footer', 'nicethemes' ),
		'priority'	=> 8
	));

	 $wp_customize->add_setting(
		'nice_custom_copyright_enable', array(
			'default'		=> get_option('nice_custom_copyright_enable') ,
			'type'			=> 'option',
			'capability'	=> 'edit_theme_options',
		)
	);


	$wp_customize->add_control( 'nice_custom_copyright_enable', array(
		'label'  	=> __( 'Enable Custom Copyright', 'nicethemes'),
		'section'	=> 'nice_customizer_section_footer',
		'type'		=> 'select',
		'choices'	=> array(
			'true'	=> __( 'Yes', 'nicethemes' ),
			'false'	=> __( 'No', 'nicethemes' ),
			),
	) );

	$wp_customize->add_setting( 'nice_custom_copyright_text', array(
		'default'		=> '',
		'capability'	=> 'edit_theme_options',
		'type'			=> 'option',

	));

	$wp_customize->add_control('nice_custom_copyright_text', array(
		'label'			=> __( 'Custom Copyright Text', 'nicethemes' ),
		'section'		=> 'nice_customizer_section_footer',
		'settings'		=> 'nice_custom_copyright_text',
	));

}

endif;

add_action( 'customize_register', 'nice_customizer_register' );


/**
 * nice_customizer_css()
 *
 * Handle the CSS for the customizer.
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_customizer_css' ) ) :

	function nice_customizer_css(){

		/* Handled with refresh, nothing here yet.*/
		/* <style type="text/css"></style> */
	}

endif;

add_action( 'wp_head', 'nice_customizer_css' );


/**
 * nice_customizer_js()
 *
 * Load the JS lib for the customizer
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( '' ) ) :

	function nice_customizer_js(){

		wp_enqueue_script(	'nicethemes-customizer',
							get_template_directory_uri() . '/includes/js/nice-customizer.js',
							array( 'jquery','customize-preview' ),
							'',
							true
							);
	}

endif;

add_action( 'customize_preview_init', 'nice_customizer_js' );