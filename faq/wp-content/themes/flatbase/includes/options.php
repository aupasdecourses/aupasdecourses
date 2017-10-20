<?php
/**
 * Flatbase by NiceThemes.
 *
 * This file contains functions to implement theme options.
 *
 * @see nice_options()
 *
 * @package   Flatbase
 * @author    NiceThemes <hello@nicethemes.com>
 * @license   GPL-2.0+
 * @link      http://nicethemes.com/theme/flatbase/
 * @copyright 2014-2015 NiceThemes
 * @since     1.0.0
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}


if ( ! function_exists( 'nice_options_global' ) ) :
add_action( 'init', 'nice_options_global'
	);
/**
 * make options global.
 *
 * @since 1.0.0
 */
function nice_options_global() {
	global $nice_options;
	$nice_options = get_option( 'nice_options'
	);
}
endif;

if ( ! isset( $content_width ) ) {
	$content_width = 620;
}


if ( ! function_exists( 'nice_options' ) ) :
add_action( 'admin_head', 'nice_options'
	);
/**
 * Set up theme options.
 *
 * @since 1.0.0
 */
function nice_options() {

	$prefix = NICE_PREFIX;

	$nice_options = array();

	/**
	 * General Settings.
	 */

	$nice_options[] = array(
		'name' => __( 'General Settings', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Favicon', 'nicethemes' ),
		'desc' => __( 'Upload a favicon.', 'nicethemes' ),
		'id'   => $prefix . '_favicon',
		'std'  => '',
		'type' => 'upload'
	);

	$nice_options[] = array(
		'name' => __( 'Sidebar Position', 'nicethemes' ),
		'desc' => __( 'Select if you want to show the full content or the excerpt on posts.', 'nicethemes' ),
		'id'   => $prefix . '_sidebar_position',
		'type' => 'select',
		'std'  => 'right',
		'tip'  => '',
		'options' 	=> array( 	'right' => __( 'Sidebar on right side', 'nicethemes' ),
								'left' 	=> __( 'Sidebar on left side', 'nicethemes' )
							)

	);

	$nice_options[] = array(
		'name' => __( 'Display Back To Top Button', 'nicethemes' ),
		'desc' => __( 'Enable if you want the "Back To Top button" to be displayed in the bottom right corner of the site.', 'nicethemes' ),
		'id'   => $prefix . '_back_to_top',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Post Author Box', 'nicethemes' ),
		'desc' => __( 'This will enable the post author box on the single posts page. Edit description in Users > Your Profile.', 'nicethemes' ),
		'id'   => $prefix . '_post_author',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Call to Action Text', 'nicethemes' ),
		'desc' => __( 'Add the text that you would like to appear in the global call to action section.', 'nicethemes' ),
		'id'   => $prefix . '_cta_text',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
	);

	$nice_options[] = array(
		'name' => __( 'Call to Action Button Link URL', 'nicethemes' ),
		'desc' => __( 'Please enter the URL for the call to action section here.', 'nicethemes' ),
		'id'   => $prefix . '_cta_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Call to Action Button Text', 'nicethemes' ),
		'desc' => __( 'If you would like a button to be the link in the global call to action section, please enter the text for it here.', 'nicethemes' ),
		'id'   => $prefix . '_cta_url_text',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Tracking Code', 'nicethemes' ),
		'desc' => __( 'Insert the code if you have one. Google analytics, etc.', 'nicethemes' ),
		'id'   => $prefix . '_tracking_code',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
	);

	/**
	 * Design & Styles
	 */

	$nice_options[] = array(
		'name' => __( 'Design & Styles', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Accent Color', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_accent_color',
		'std'  => '#5bc4be',
		'tip'  => '',
		'type' => 'color'
	);

	$nice_options[] = array(
		'name' => __( 'Background Image', 'nicethemes' ),
		'desc' => __( 'Upload or choose the background image.', 'nicethemes' ),
		'id'   => $prefix . '_background_image',
		'std'  => '',
		'type' => 'upload'
	);

	$nice_options[] = array(
		'name' => __( 'Background Image Repeat', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_background_image_repeat',
		'std'  => 'repeat',
		'type' => 'select',
		'tip'  => '',
		'options'	=> array(
							'no-repeat'	=> 	__( 'No Repeat', 'nicethemes' ),
							'repeat'	=> 	__( 'Repeat', 'nicethemes' ),
							'repeat-x'	=> 	__( 'Repeat horizontally', 'nicethemes' ),
							'repeat-y'	=> 	__( 'Repeat vertically', 'nicethemes' )
						)
	);

	$nice_options[] = array(
		'name' => __( 'Background Image Position', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_background_image_position',
		'std'  => 'left top',
		'type' => 'select',
		'tip'  => '',
		'options' 	=> array(
							'center top'	=> 	__( 'Center Top', 'nicethemes' ),
							'center center'	=> 	__( 'Center Center', 'nicethemes' ),
							'center bottom'	=> 	__( 'Center Bottom', 'nicethemes' ),
							'left top'		=> 	__( 'Left Top', 'nicethemes' ),
							'left center'	=> 	__( 'Left Center', 'nicethemes' ),
							'left bottom'	=> 	__( 'Left Bottom', 'nicethemes' ),
							'right top'		=> 	__( 'Right Top', 'nicethemes' ),
							'right center'	=> 	__( 'Right Center', 'nicethemes' ),
							'right bottom'	=> 	__( 'Right Bottom', 'nicethemes' )
						)
	);

	$nice_options[] = array(
		'name' => __( 'Background Color', 'nicethemes' ),
		'desc' => __( 'Choose the background color.', 'nicethemes' ),
		'id'   => $prefix . '_background_color',
		'std'  => '#f0f0f0',
		'type' => 'color'
	);

	$nice_options[] = array(
		'name' => __( 'Layout Type', 'nicethemes' ),
		'desc' => __( 'Select the layout type.', 'nicethemes' ),
		'id'   => $prefix . '_layout_type',
		'type' => 'select',
		'std'  => 'full',
		'tip'  => '',
		'options' 	=> array(
						'boxed' => __( 'Boxed', 'nicethemes' ),
						'full'  => __( 'Full Width', 'nicethemes' )
					)
	);

	$nice_options[] = array(
		'name' => __( 'Masonry Blog Posts Load Method', 'nicethemes' ),
		'desc' => __( 'Select the method for loading masonry blog posts.', 'nicethemes' ),
		'id'   => $prefix . '_masonry_posts_load_method',
		'type' => 'select',
		'std'  => 'on_scroll',
		'tip'  => '',
		'options' 	=> array(
						'on_scroll' => __( 'On Scroll', 'nicethemes' ),
						'on_button' => __( 'On Clicking Button', 'nicethemes' )
					)
	);

	$nice_options[] = array(
		'name' => __( 'Custom CSS', 'nicethemes' ),
		'desc' => __( 'Quickly add some CSS to your theme by adding it to this block.', 'nicethemes' ),
		'id'   => $prefix . '_custom_css',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
	);

	/**
	 * Header
	 */

	$nice_options[] = array(
		'name' => __( 'Header', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Custom Logo', 'nicethemes' ),
		'desc' => __( 'Upload a custom logo.', 'nicethemes' ),
		'id'   => $prefix . '_logo',
		'std'  => '',
		'type' => 'upload'
	);

	$nice_options[] = array(
		'name' => __( 'Custom Logo (Retina)', 'nicethemes' ),
		'desc' => __( 'Upload a custom logo for retina displays. Upload at exactly 2x the size of your standard logo.', 'nicethemes' ),
		'id'   => $prefix . '_logo_retina',
		'std'  => '',
		'type' => 'upload'
	);

	$nice_options[] = array(
		'name' => __( 'Logo Height', 'nicethemes' ),
		'desc' => __( 'Change the logo height. This setting will standarize the logo height for retina devices.', 'nicethemes' ),
		'id'   => $prefix . '_logo_height',
		'std'  => array( 'range' => 'min', 'value' => '65', 'min' => '10', 'max' => '400', 'unit' => 'px' ),
		'type' => 'slider'
	);


	$nice_options[] = array(
		'name' => __( 'Text Title', 'nicethemes' ),
		'desc' => __( 'Enable if you want Blog Title and Tagline to be text-based. Setup title/tagline in WP -> Settings -> General.', 'nicethemes' ),
		'id'   => $prefix . '_texttitle',
		'std'  => 'false',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Site Title Typography', 'nicethemes' ),
		'desc' => __( 'Change the site title typography. ( Only if Text Title is enabled )', 'nicethemes' ),
		'id'   => $prefix . '_font_site_title',
		'std'  => array( 'size' => '30', 'family' => 'Nunito', 'style' => '','color' => '#fff'),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Header Background Color', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_header_background_color',
		'std'  => '#35a49e',
		'tip'  => '',
		'type' => 'color'
	);

	$nice_options[] = array(
		'name' => __( 'Header background Image', 'nicethemes' ),
		'desc' => __( 'Upload or choose the header background image.', 'nicethemes' ),
		'id'   => $prefix . '_header_background_image',
		'std'  => '',
		'type' => 'upload'
	);

	$nice_options[] = array(
		'name' => __( 'Header background image repeat', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . "_header_background_image_repeat",
		'std'  => 'repeat',
		'type' => 'select',
		'tip'  => '',
		'options'	=> array(
								'no-repeat'	=> 	__( 'No Repeat', 'nicethemes' ),
								'repeat'	=> 	__( 'Repeat', 'nicethemes' ),
								'repeat-x'	=> 	__( 'Repeat horizontally', 'nicethemes' ),
								'repeat-y'	=> 	__( 'Repeat vertically', 'nicethemes' )
								)
	);

	$nice_options[] = array(
		'name' => __( 'Header background image position', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_header_background_image_position',
		'std'  => 'left top',
		'type' => 'select',
		'tip'  => '',
		'options' 	=> array(
							'center top'	=> 	__( 'Center Top', 'nicethemes' ),
							'center center'	=> 	__( 'Center Center', 'nicethemes' ),
							'center bottom'	=> 	__( 'Center Bottom', 'nicethemes' ),
							'left top'		=> 	__( 'Left Top', 'nicethemes' ),
							'left center'	=> 	__( 'Left Center', 'nicethemes' ),
							'left bottom'	=> 	__( 'Left Bottom', 'nicethemes' ),
							'right top'		=> 	__( 'Right Top', 'nicethemes' ),
							'right center'	=> 	__( 'Right Center', 'nicethemes' ),
							'right bottom'	=> 	__( 'Right Bottom', 'nicethemes' )
						)
	);

	/**
	 * Footer
	 */

	$nice_options[] = array(
		'name' => __( 'Footer', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Enable Custom Copyright', 'nicethemes' ),
		'desc' => __( 'Enable if you want to write your own copyright text.', 'nicethemes' ),
		'id'   => $prefix . '_custom_copyright_enable',
		'std'  => 'false',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Custom Copyright Text', 'nicethemes' ),
		'desc' => __( 'Please enter the copyright section text. e.g. All Rights Reserved, Nice Themes.', 'nicethemes' ),
		'id'   => $prefix . '_custom_copyright_text',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Footer Columns', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_footer_columns',
		'std'  => array( 'range' => 'min', 'value' => '3', 'min' => '2', 'max' => '4' ),
		'type' => 'slider'
	);

	/**
	 * Home Options
	 */
	$nice_options[] = array(
		'name' => __( 'Home Options', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Display Live Search', 'nicethemes' ),
		'desc' => __( 'This will enable the live search block on the home page.', 'nicethemes' ),
		'id'   => $prefix . '_livesearch_enable',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Live Search Tagline', 'nicethemes' ),
		'desc' => __( 'Insert the text that will appear above the search bar.', 'nicethemes' ),
		'id'   => $prefix . '_welcome_message',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
	);

	$nice_options[] = array(
		'name' => __( 'Live Search Tagline (extended)', 'nicethemes' ),
		'desc' => __( 'Insert the text that will appear below the Live Search Text..', 'nicethemes' ),
		'id'   => $prefix . '_welcome_message_extended',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
	);

	$nice_options[] = array(
		'name' => __( 'Enable Info Boxes', 'nicethemes' ),
		'desc' => __( 'This will enable the info boxes to be shown in the home page.', 'nicethemes' ),
		'id'   => $prefix . '_infobox_enable',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Info Boxes Order', 'nicethemes' ),
		'desc' => __( 'Select the view order you wish to set for the info boxes items on the home page.', 'nicethemes' ),
		'id'   => $prefix . '_infobox_order',
		'std'  => 'date',
		'type' => 'select',
		'tip'  => '',
							 'options' 	=> array( 'date' => __( 'Date', 'nicethemes' ), 'menu_order' => __( 'Page Order', 'nicethemes' ), 'title' => __( 'Title', 'nicethemes' ), 'rand' => __( 'Random', 'nicethemes' ) ));

	$nice_options[] = array(
		'name' => __( 'Number of Articles per Category', 'nicethemes' ),
		'desc' => __( 'Select the number of articles entries that should appear per category in the home page .(Default is 5)', 'nicethemes' ),
		'id'   => $prefix . '_articles_entries',
		'std'  => array( 'range' => 'min', 'value' => '5', 'min' => '1', 'max' => '15' ),
		'type' => 'slider'
	);

	$nice_options[] = array(
		'name' => __( 'Enable Videos', 'nicethemes' ),
		'desc' => __( 'This will enable the videos section to be shown in the home page.', 'nicethemes' ),
		'id'   => $prefix . '_video_enable',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);


	$nice_options[] = array(
		'name' => __( 'Number of Videos to Show', 'nicethemes' ),
		'desc' => __( 'Select the number of video entries that should appear in the home page .(Default is 5)', 'nicethemes' ),
		'id'   => $prefix . '_video_entries',
		'std'  => array( 'range' => 'min', 'value' => '5', 'min' => '1', 'max' => '10' ),
		'type' => 'slider'
	);

	$nice_options[] = array(
		'name' => __( 'Videos Order', 'nicethemes' ),
		'desc' => __( 'Select the view order you wish to set for the videos items on the home page.', 'nicethemes' ),
		'id'   => $prefix . '_video_order',
		'std'  => 'date',
		'type' => 'select',
		'tip'  => '',
							 'options' 	=> array( 'date' => __( 'Date', 'nicethemes' ), 'menu_order' => __( 'Page Order', 'nicethemes' ), 'title' => __( 'Title', 'nicethemes' ), 'rand' => __( 'Random', 'nicethemes' ) ));

	/**
	 * Typography
	 */
	$nice_options[] = array(
		'name' => __( 'Typography', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Enable Custom Typography', 'nicethemes' ),
		'desc' => __( 'Enable if you want to pick your fonts.', 'nicethemes' ),
		'id'   => $prefix . '_custom_typography',
		'std'  => 'false',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'General Typography', 'nicethemes' ) ,
		'desc' => __( 'Change the general font.', 'nicethemes' ) ,
		'id'   => $prefix . '_font_body',
		'std'  => array( 'size' => '15', 'unit' => 'px', 'family' => 'Lato', 'style' => '', 'color' => '#8B989E' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Navigation', 'nicethemes' ) ,
		'desc' => __( 'Change the navigation font.', 'nicethemes' ),
		'id'   => $prefix . '_font_nav',
		'std'  => array( 'size' => '15', 'unit' => 'px', 'family' => 'Lato', 'style' => '', 'color' => '#eff2f3' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Sub Navigation (Submenus)', 'nicethemes' ) ,
		'desc' => __( 'Change the navigation submenu font.', 'nicethemes' ),
		'id'   => $prefix . '_font_subnav',
		'std'  => array( 'size' => '12', 'unit' => 'px', 'family' => 'Lato', 'style' => '', 'color' => '#FFFFFF' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Headings', 'nicethemes' ) ,
		'desc' => __( 'Change the headings font family.', 'nicethemes' ),
		'id'   => $prefix . '_font_headings',
		'std'  => array( 'family' => 'Nunito', 'style' => '' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Form Inputs', 'nicethemes' ) ,
		'desc' => __( 'Change the buttons font family.', 'nicethemes' ),
		'id'   => $prefix . '_font_inputs',
		'std'  => array( 'family' => 'Lato', 'style' => '' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Buttons', 'nicethemes' ) ,
		'desc' => __( 'Change the buttons font family.', 'nicethemes' ),
		'id'   => $prefix . '_font_buttons',
		'std'  => array( 'family' => 'Nunito', 'style' => '' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Infoboxes Title', 'nicethemes' ) ,
		'desc' => __( 'Change the infoboxes font.', 'nicethemes' ),
		'id'   => $prefix . '_font_infobox_title',
		'std'  => array( 'size' => '21', 'unit' => 'px', 'family' => 'Nunito', 'style' => '', 'color' => '#4B4D4B' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Infoboxes Content', 'nicethemes' ) ,
		'desc' => __( 'Change the infoboxes font.', 'nicethemes' ) ,
		'id'   => $prefix . '_font_infobox_content',
		'std'  => array( 'size' => '15', 'unit' => 'px', 'family' => 'Lato', 'style' => '', 'color' => '#8B989E' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Live Search Tagline', 'nicethemes' ) ,
		'desc' => __( 'Change the live search tagline font.', 'nicethemes' ) ,
		'id'   => $prefix . "_font_welcome_message",
		'std'  => array( 'size' => '32', 'unit' => 'px', 'family' => 'Nunito', 'style' => '', 'color' => '#ffffff' ),
		'type' => 'typography'
	);

	$nice_options[] = array(
		'name' => __( 'Live Search Tagline (extended)', 'nicethemes' ) ,
		'desc' => __( 'Change the extended live search tagline font.', 'nicethemes' ) ,
		'id'   => $prefix . "_font_welcome_message_extended",
		'std'  => array( 'size' => '16', 'unit' => 'px', 'family' => 'Lato', 'style' => '', 'color' => '#dddddd' ),
		'type' => 'typography'
	);


	/**
	 * Knowledge Base
	 */
	$nice_options[] = array(
		'name' => __( 'Knowledge Base', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Display Views', 'nicethemes' ),
		'desc' => __( 'Enable to display the amount of article views below the article title.', 'nicethemes' ),
		'id'   => $prefix . '_views',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Display Likes', 'nicethemes' ),
		'desc' => __( 'Enable to display the amount of likes below the article title.', 'nicethemes' ),
		'id'   => $prefix . '_likes',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Display Reading Time', 'nicethemes' ),
		'desc' => __( 'Enable to display the reading time below the article title.', 'nicethemes' ),
		'id'   => $prefix . '_reading_time',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Display Article Author', 'nicethemes' ),
		'desc' => __( 'This will enable the display of the article author information below the content.', 'nicethemes' ),
		'id'   => $prefix . '_article_author',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'Display Related Articles', 'nicethemes' ),
		'desc' => __( 'This will enable display of the related articles below the content.', 'nicethemes' ),
		'id'   => $prefix . '_related_articles',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);


	/**
	 * Contact Information.
	 */

	$nice_options[] = array(
		'name' => __( 'Contact Information', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Contact Form Email Address', 'nicethemes' ),
		'desc' => __( 'Enter the email address where you\'d like to receive emails from the contact form, or leave blank to use admin email.', 'nicethemes' ),
		'id'   => $prefix . '_email',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
							);

	$nice_options[] = array(
		'name' => __( 'Google Maps Embed Code', 'nicethemes' ),
		'desc' => __( 'W-P-L-O-C-K-E-R-.-C-O-M - Insert the Google Map embed code for the contact template.', 'nicethemes' ),
		'id'   => $prefix . '_google_map',
		'std'  => '',
		'tip'  => '',
		'type' => 'textarea'
							);
	/* Images */
	$nice_options[] = array(
		'name' => __( 'Images', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'WP Resize Cropping', 'nicethemes' ),
		'desc' => __( 'The post thumbnail will be cropped to match the target aspect ratio.', 'nicethemes' ),
		'id'   => $prefix . '_wp_resize_crop',
		'std'  => 'true',
		'tip'  => '',
		'type' => 'checkbox'
	);

	$nice_options[] = array(
		'name' => __( 'JPEG Quality', 'nicethemes' ),
		'desc' => __( 'Change the JPEG compression-level of uploaded images and thumbnails.(Default is 90)', 'nicethemes' ),
		'id'   => $prefix . '_jpeg_quality',
		'std'  => array( 'range' => 'min', 'value' => '90', 'min' => '40', 'max' => '100', 'unit' => '%' ),
		'type' => 'slider'
	);

	/**
	 * Social Media.
	 */

	$nice_options[] = array(
		'name' => __( 'Social Media', 'nicethemes' ),
		'type' => 'heading'
	);

	$nice_options[] = array(
		'name' => __( 'Facebook URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_facebook_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Twitter URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_twitter_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Instagram URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_instagram_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Google+ URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_google_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Dribbble URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_dribbble_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Vimeo URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_vimeo_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Tumblr URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_tumblr_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Flickr URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_flickr_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Youtube URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_youtube_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Linkedin URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_linkedin_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Dropbox URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_dropbox_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Foursquare URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_foursquare_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Pinterest URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_pinterest_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Quora URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_quora_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Skype URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_skype_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'BitBucket URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_bitbucket_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Github URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_github_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'

	);

	$nice_options[] = array(
		'name' => __( 'Stack Exchange URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_stack_exchange_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Stack Overflow URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_stack_overflow_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	$nice_options[] = array(
		'name' => __( 'Trello URL', 'nicethemes' ),
		'desc' => '',
		'id'   => $prefix . '_trello_url',
		'std'  => '',
		'tip'  => '',
		'type' => 'text'
	);

	/**
	 * Let other functions add, remove or modify options.
	 *
	 */
	$nice_options = apply_filters( 'nice_options', $nice_options
	);

	if ( ( get_option( 'nice_template' ) != $nice_options ) ) {
		update_option( 'nice_template', $nice_options
	);
	}
}
endif;