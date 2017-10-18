<?php
/**
 * The header for our theme.
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package zillah
 */

?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?>>

    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
        
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
            <!------------------ BEGIN HEADER ------------------>
            <header>
               <div class="container-fluid" id="header-wrapper" role="banner">
                    <div class="container">
                        <div class="row">
                            <div class="col-xs-8 col-xs-offset-2 col-sm-4 col-sm-offset-4" id="logo-wrapper">
                                <!-- APDC logo -->
                                <a href="https://www.aupasdecourses.com">
                                    <img src="https://www.aupasdecourses.com/faq/wp-content/uploads/2016/09/bitmap.png" alt="Logo Au Pas de Courses"/>
                                </a>
                            </div> <!-- end columns-->
                        </div> <!-- end row -->
                        <div class="row">
                            <div class="col-xs-12 col-md-8 col-md-offset-2">
                                <div class="hd-search">
                                    <?php echo do_shortcode('[wpdreams_ajaxsearchlite]'); ?>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end .container -->
                </div> <!-- end .container-fluid -->
            </header>
            <!------------------ END HEADER ------------------>
        
        <div id="content" class="site-content">
            <div class="container">
