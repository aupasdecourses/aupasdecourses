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
        
        <!-- Mailchimp signup form -->
        <link href="//cdn-images.mailchimp.com/embedcode/classic-10_7.css" rel="stylesheet" type="text/css">
        <style>
            #newsletter-form{
                margin:60px auto 0 auto;
                max-width:425px;
            }
            #mc_embed_signup{
                background:#fff;
                clear:left;
                margin:0px auto 25px auto;
                text-align: center;
                color:#00595e;
                background-color:#f6f6f6;
                padding:20px;
            }
            #mc_embed_signup form{
                text-align:center;
            }
            h2#mc_title{
                padding-bottom:20px;
                margin:0;
                font-size:28px;
            }
            #mc-embedded-subscribe-form{
                border:1px solid #00595e;
                padding:20px 10px;
            }
            input[type="email"]{
                color:#444;
                padding:10px;
                width:100%
            }
            #newsletter-form input[type="submit"]{
                text-transform:none;
            }
            #mc_embed_signup input{
                border-radius:0px;
            }
            #mc_embed_signup .button{
                background-color:white;
                -webkit-transition: all .3s ease;
                transition:all .3s ease;
                border:2px solid #00595e;
                color:#00595e;
                padding:10px 20px;
                margin-top:20px;
                height:auto;
                border-radius:0px;
                font-family:'OpenSans-SemiBold', sans-serif !important;
            }
            #mc_embed_signup .button:hover{
                background-color:#00595e;
                color:white;
            }
        </style>    
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
       <?php if ( function_exists( 'gtm4wp_the_gtm_tag' ) ) { gtm4wp_the_gtm_tag(); } ?>
        <div id="page" class="site">
            <a class="skip-link screen-reader-text" href="#main">
                <?php esc_html_e( 'Skip to content', 'zillah' ); ?>
            </a>
            <!------------------ BEGIN HEADER ------------------>
            <header>
                <!-- Top Menu -->
                <div id="top-menu-fixed-wrapper">
                    <div id="top-menu-container">
                        <div id="top-menu-flex-parent">
                            <div class="top-menu-element-apdc-logo">
                                <!-- APDC logo -->    
                                <a href="https://www.aupasdecourses.com">
                                    <img id="logo-apdc" src="https://www.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/logo-APDC.png" alt="Logo Au Pas de Courses">
                                </a>
                            </div><!--
                            --><div class="top-menu-element-menu">
                                <!-- Main desktop menu -->
                                <nav id="main-menu" role="navigation">
                                    <?php
                                        wp_nav_menu(
                                            array(
                                                'theme_location' => 'primary',
                                                'menu_id' => 'primary-menu'
                                            )
                                        );
                                    ?>
                                </nav>
                                
                                <!-- Main mobile menu -->
                                <input type="checkbox" id="menuToggle"/> <!-- sibling of #sliding-menu -->
                                <div class="menu-container"><!--
                                 --><label for="menuToggle" id="menu-label-bars">
                                        <img src="https://www.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/menu-bars.png" alt="Ouvrir menu">
                                 </label><!--
                                 --><label for="menuToggle" id="menu-label-x">
                                        <img src="https://www.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/menu-x.png" alt="Fermer menu">
                                 </label>
                                </div>
                                
                                <nav id="sliding-menu" role="navigation">
                                   <div id="blog-logo-wrapper-responsive-menu">
                                        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="https://www.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/logo-blog.png" alt="Logo du Blog d'AuPasDeCourses"/></a>
                                    </div>
                                   
                                    <?php
                                        wp_nav_menu(
                                            array(
                                                'theme_location' => 'primary',
                                                'menu_id' => 'primary-menu'
                                            )
                                        );
                                    ?>
                                    <div class="top-menu-element-social-responsive">
                                        <!-- Header sidebar for social network icons -->
                                        <?php dynamic_sidebar( 'header-sidebar' ); ?>
                                    </div>
                                </nav>
                                <!-- End mobile menu -->
                                
                            </div><!--
                            --><div class="top-menu-element-social">
                                <!-- Header sidebar for social network icons -->
                                <?php dynamic_sidebar( 'header-sidebar' ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="container-fluid" id="header-wrapper" role="banner" style="background-image:url('<?php echo get_theme_mod('header_banner_image', ''); ?>')">
                    
                    <div class="container">
                        
                        <!-- LOGO BLOG -->
                        <div class="row">
                            <div id="blog-logo-wrapper">
                                <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img src="https://www.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/logo-blog.png" alt="Logo du Blog d'AuPasDeCourses"/></a>
                            </div>
                        </div>
                        <div class="row">
                            <div>
                                <div id="header-categories-wrapper">
                                    <div class="header-single-category-wrapper">
                                        <a href="https://www.aupasdecourses.com/blog/category/recettes/">
                                            <img src="<?php echo get_theme_mod('recettes_category_image', ''); ?>" alt="Cagégorie Recettes"/>
                                            <div id="header-single-category-recettes">Recettes</div>
                                        </a>
                                    </div>
                                    <div class="header-single-category-wrapper">
                                        <a href="https://www.aupasdecourses.com/blog/category/marche/">
                                            <img src="<?php echo get_theme_mod('marche_category_image', ''); ?>" alt="Cagégorie Marché"/>
                                            <div id="header-single-category-marche">Marché</div>
                                        </a>
                                    </div>
                                    <div class="header-single-category-wrapper">
                                        <a href="https://www.aupasdecourses.com/blog/category/astuces/">
                                            <img src="<?php echo get_theme_mod('astuces_category_image', ''); ?>" alt="Cagégorie Astuces"/>
                                            <div id="header-single-category-astuces">Astuces</div>
                                        </a>
                                    </div>
                                    <div class="header-single-category-wrapper">
                                        <a href="https://www.aupasdecourses.com/blog/category/decouverte/">
                                            <img src="<?php echo get_theme_mod('decouverte_category_image', ''); ?>" alt="Cagégorie Découverte"/>
                                            <div id="header-single-category-decouverte">Découverte</div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div> <!-- end .container -->
                </div> <!-- end .container-fluid -->
                
                
            </header>
            <!------------------ END HEADER ------------------>
            
        </div>

        <?php zillah_slider(); ?>


        <div id="content" class="site-content">
            <div class="container">
