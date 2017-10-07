<?php
/**
 * zillah child functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 */

/*Load parent css*/
function wpm_enqueue_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri().'/style.css', array(), "4.5.3.0.1");
    /* Zillah automatically includes style child */
}
add_action('wp_enqueue_scripts', 'wpm_enqueue_styles');

/* Setup the child theme */
function child_theme_setup()
{
    /*Change Continue reading text*/
    // override parent theme's 'more' text for excerpts
    remove_filter('excerpt_more', 'zillah_excerpt_more');
    
    /* Add our own thumbnail sizes */
    add_theme_support('post-thumbnails');
    /*              keyword, width, height, cropping strategy
                                            true = hard crop
                                            false = soft crop (what does that mean?)
                                            array('left', 'top') specify origin of crop
    */
    add_image_size('article-large-thumbnail', 425, 425, true);
    add_image_size('article-medium-thumbnail', 275, 275, true);
    add_image_size('article-small-thumbnail', 130, 130, true);
}
add_action('after_setup_theme', 'child_theme_setup');

/*function zillah_child_excerpt_more()
{
    return '...<span class="clearfix clearfix-post"></span><a href="'.esc_url(get_permalink(get_the_ID())).'" class="more-link">'.sprintf(__("Lire l'article %s", 'zillah'), the_title('<span class="screen-reader-text">"', '"</span>', false).' <span class="meta-nav">&rarr;</span>').'</a>';
}
add_filter('excerpt_more', 'zillah_child_excerpt_more');
Ce code ajoutait le lien Lire l'article après chaque excerpt'*/


/*customize excerpt word count length. The global variable $variable_excerpt_length, defined
just before the call to the_excerpt(), defines the number of words we wish to show in the excerpt.
*/

function zillah_child_excerpt_length() {
    global $variable_excerpt_length;
    return $variable_excerpt_length;
}
add_filter( 'excerpt_length', 'zillah_child_excerpt_length', 9999 );


function zillah_child_search_form()
{
    return '<form role="search" method="get" class="search-form" action="'.esc_url(home_url('/')).'">
                <label>
                    <span class="screen-reader-text">'._x('Search for:', 'label').'</span>
                    <input type="search" class="search-field" placeholder="'.esc_attr_x('Rechercher', 'placeholder').'" value="'.get_search_query().'" name="s" />
                </label>
                <input type="submit" class="search-submit" value="'.esc_attr_x('Search', 'submit button').'" />
            </form>';
}
add_filter('get_search_form', 'zillah_child_search_form');

/*Change jetpack subscription comment form*/
function zillah_child_jetpack_blog_sub()
{
    return __('Me notifier par mail des nouveaux articles.', 'jetpack');
}
add_filter('jetpack_subscribe_blog_label', 'zillah_child_jetpack_blog_sub');
function zillah_child_jetpack_comment_sub_blog()
{
    return __('Me notifier par mail des nouveaux commentaires.', 'jetpack');
}
add_filter('jetpack_subscribe_comment_label', 'zillah_child_jetpack_comment_sub_blog');

/**
 * Set up the WordPress core custom header feature.
 *
 * @uses zillah_header_style()
 */
function zillah_child_custom_header_setup() {
    add_theme_support( 'custom-header', apply_filters( 'zillah_custom_header_args', array(
        'default-image'          => '',
        'default-text-color'     => '7fcaad',
        'width'                  => 1700,
        'height'                 => 400,
        'flex-height'            => true,
    ) ) );
}
add_action( 'after_setup_theme', 'zillah_child_custom_header_setup',20 );

/**
 * Create a social media icons sidebar for the header
 */
function zillah_child_widgets_init() {
    register_sidebar( array(
		'name'          => esc_html__( 'Header Sidebar', 'zillah' ),
		'id'            => 'header-sidebar',
		'description'   => esc_html__( 'Dedicated to the social network icons widget.', 'zillah' ),
        'before_widget' => '<div id="social-icons-widget" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );
    register_sidebar( array(
		'name'          => esc_html__( 'Footer Sidebar', 'zillah' ),
		'id'            => 'footer-sidebar',
		'description'   => esc_html__( 'Dedicated to the social network icons widget.', 'zillah' ),
        'before_widget' => '<div id="footer-social-icons-widget" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );
    register_sidebar( array(
		'name'          => esc_html__( 'Instagram widget area', 'zillah' ),
		'id'            => 'instagram-widget-area',
		'description'   => esc_html__( 'Dedicated to the Instagram feed', 'zillah' ),
        'before_widget' => '<div id="instagram-widget-area" class="widget %2$s">',
		'after_widget'  => '</div>'
	) );
}
add_action('widgets_init', 'zillah_child_widgets_init');

/* Add theme customization options */
function zillah_child_customization( $wp_customize ){
    /* ===== Settings ===== */
    /* Header banner image */
    $wp_customize->add_setting('header_banner_image', array (
        'default' => '',
        'transport' => 'refresh'
    ));
    /* Recettes category image */
    $wp_customize->add_setting('recettes_category_image', array (
        'default' => '',
        'transport' => 'refresh'
    ));
    /* Marché category image */
    $wp_customize->add_setting('marche_category_image', array (
        'default' => '',
        'transport' => 'refresh'
    ));
    /* Astuces category image */
    $wp_customize->add_setting('astuces_category_image', array (
        'default' => '',
        'transport' => 'refresh'
    ));
    /* Découverte category image */
    $wp_customize->add_setting('decouverte_category_image', array (
        'default' => '',
        'transport' => 'refresh'
    ));
    
    /* ===== Sections ===== */
    /* Header customization section */
    $wp_customize->add_section('header_customization', array(
        'title' => __('Header customizations', 'zillah'),
        'priority' => 30
    ));
        
    /* ===== Controls ===== */
    /* Landing page customization controls */
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'header_banner_image_control', array(
        'label' => __('Header banner image', 'zillah'),
        'section' => 'header_customization',
        'settings' => 'header_banner_image'
    )));
    /* Recetttes category image customization controls */
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'recettes_category_image_control', array(
        'label' => __('Recettes category image', 'zillah'),
        'description' => __( 'The image size must be a minimum of 500px and a perfect square. All the 4 categories images must be the same size.', 'zillah' ),
        'section' => 'header_customization',
        'settings' => 'recettes_category_image'
    )));
    /* Marché category image customization controls */
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'marche_category_image_control', array(
        'label' => __('Marché category image', 'zillah'),
        'description' => __( 'The image size must be a minimum of 500px and a perfect square. All the 4 categories images must be the same size.', 'zillah' ),
        'section' => 'header_customization',
        'settings' => 'marche_category_image'
    )));
    /* Astuces category image customization controls */
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'astuces_category_image_control', array(
        'label' => __('Astuces category image', 'zillah'),
        'description' => __( 'The image size must be a minimum of 500px and a perfect square. All the 4 categories images must be the same size.', 'zillah' ),
        'section' => 'header_customization',
        'settings' => 'astuces_category_image'
    )));
    /* Découverte category image customization controls */
    $wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'decouverte_category_image_control', array(
        'label' => __('Découverte category image', 'zillah'),
        'description' => __( 'The image size must be a minimum of 500px and a perfect square. All the 4 categories images must be the same size.', 'zillah' ),
        'section' => 'header_customization',
        'settings' => 'decouverte_category_image'
    )));
}
/* Register the customization function */
add_action('customize_register', 'zillah_child_customization');

/* Function which takes care of pagination for custom wp_queries */
if( !function_exists( 'theme_pagination' ) ) {
	
    function theme_pagination() {
	
	global $wp_query, $wp_rewrite;
	$wp_query->query_vars['paged'] > 1 ? $current = $wp_query->query_vars['paged'] : $current = 1;
	
	$pagination = array(
		'base' => @add_query_arg('page','%#%'),
		'format' => '',
		'total' => $wp_query->max_num_pages,
		'current' => $current,
	        'show_all' => false,
	        'end_size'     => 1,
	        'mid_size'     => 2,
		'type' => 'list',
		'next_text' => 'suiv.',
		'prev_text' => 'prev.'
	);
	
	if( $wp_rewrite->using_permalinks() )
		$pagination['base'] = user_trailingslashit( trailingslashit( remove_query_arg( 's', get_pagenum_link( 1 ) ) ) . 'page/%#%/', 'paged' );
	
	if( !empty($wp_query->query_vars['s']) )
		$pagination['add_args'] = array( 's' => str_replace( ' ' , '+', get_query_var( 's' ) ) );
		
	echo str_replace('page/1/','', paginate_links( $pagination ) );
    }	
}