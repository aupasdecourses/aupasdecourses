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
    wp_enqueue_style( 'child-style', get_stylesheet_directory_uri() . '/style.css', array( 'parent-style' ), "4.5.3.0.2");
}
add_action('wp_enqueue_scripts', 'wpm_enqueue_styles');

/*Change Continue reading text*/
function child_theme_setup()
{
    // override parent theme's 'more' text for excerpts
    remove_filter('excerpt_more', 'zillah_excerpt_more');
}
add_action('after_setup_theme', 'child_theme_setup');

function zillah_child_excerpt_more()
{
    return '...<span class="clearfix clearfix-post"></span><a href="'.esc_url(get_permalink(get_the_ID())).'" class="more-link">'.sprintf(__("Lire l'article %s", 'zillah'), the_title('<span class="screen-reader-text">"', '"</span>', false).' <span class="meta-nav">&rarr;</span>').'</a>';
}
add_filter('excerpt_more', 'zillah_child_excerpt_more');

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