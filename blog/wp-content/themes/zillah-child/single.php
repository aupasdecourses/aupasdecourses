<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */
get_header(); ?>

<main class="container">
    <div class="row" id="storefront-blocks"> <!-- Storefront blocks -->
        <div class="col-xs-3 col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-3 col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-3 col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-3 col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-2 display-none-xs col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-2 display-none-xs col-sm-2 col-md-1 no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
        <div class="col-xs-1 display-none-sm no-padding"><div class="block"></div></div>
    </div> <!-- end .row -->
    
    <div class="row">
        <div class="col-sm-12">
            <div class="flex-introduction-article">
            <!-- Article information and content -->
            <?php
            if (have_posts()) :
            while (have_posts()) : the_post(); ?>
                <div class="introduction-article-wrapper"> <!-- wrapper around title, category, publish date -->
                    <div> <!-- second wrapper around title, category and publish date but not display flex -->
                        <div id="single-post-category-label">
                        <?php
                        $single_post_categories = get_the_category();
                        if ($single_post_categories) { /*if there is content in the single_post_categories array*/
                            foreach ($single_post_categories as $category) { /*for each item in the $single_post_categories array, identify the current item as the variable $category*/
                                
                                global $current_category_name;
                                $current_category_name = $category->cat_name;
                                global $current_category_link;
                                $current_category_link = get_category_link($category->term_id);
                                
                                /* Assign a CSS class for each category */
                                switch ($category->slug) {
                                    case "recettes":
                                        $span_class = 'category-label-recettes';
                                        $border_category_class = 'category-border-recettes';
                                        break;
                                    case "astuces":
                                        $span_class = 'category-label-astuces';
                                        $border_category_class = 'category-border-astuces';
                                        break;
                                    case "marche":
                                        $span_class = 'category-label-marche';
                                        $border_category_class = 'category-border-marche';
                                        break;
                                    case "decouverte":
                                        $span_class = 'category-label-decouverte';
                                        $border_category_class = 'category-border-decouverte';
                                        break;
                                    case "portrait":
                                        $span_class = 'category-label-portrait';
                                        $border_category_class = 'category-border-portrait';
                                        break;
                                    case "coup-de-food":
                                        $span_class = 'category-label-coupdefood';
                                        $border_category_class = 'category-border-coupdefood';
                                        break;
                                    case "dossier":
                                        $span_class = 'category-label-dossier';
                                        $border_category_class = 'category-border-dossier';
                                        break;
                                } /* end switch */

                                $output .= '<a href="' . get_category_link($category->term_id) . '">
                                            <span class="category-box-article ' . $span_class . '">
                                            ' . $category->cat_name . '
                                            </span></a>' . $separator;

                            } /* end foreach() */
                            echo trim($output, $separator); /*trim off the last , at the end of the list*/
                            ?>
                        </div> <!-- end #single-post-category-label -->

                        <?php
                        /* Capture the current category slug to be used for the 6 random posts */
                        global $current_category_slug;
                        $current_category_slug = $category->slug;
                        } /* end IF */?>


                        <div class="border <?php echo $border_category_class ?>">
                            <?php
                            echo '<h1 class="article-title">'.get_the_title().'</h1>';
                            echo '<span class="publication-date">article publié le ';
                            echo the_time('j/m/Y').'</span>';
                            ?>
                        </div>
                    </div> <!-- second wrapper around title, category and publish date but not display flex -->
                </div> <!-- end .introduction-article-wrapper -->

                <div id="post-thumbnail-wrapper">
                    <?php the_post_thumbnail('article-large-thumbnail'); ?>
                </div>
            </div> <!-- end .flex-introduction-article -->
        </div><!-- end column -->
    </div> <!-- end .row -->
    
    <div class="row">
        <div class="col-xs-10">
            <div class="article-content">
                <?php
                the_content(); ?>
            </div>
        </div>
        
        <div class="col-xs-2">
            <div id="article-sharing-icon-wrapper">
                <div class="article-sharing-icon">
                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo get_the_permalink() ?>" class="genericon genericon-facebook" target="_blank"></a>
                </div>
                <div class="article-sharing-icon">
                    <a href="https://twitter.com/home?status=<?php echo get_the_permalink() ?>" class="genericon genericon-twitter" target="_blank"></a>
                </div>
            </div>
        </div>
    </div>
    
    <?php
    endwhile;

    else :
        echo "<p>Aucun article n'a été trouvé</p>";

    endif;
    ?>
    <!-- End article information and content -->
    
            
    <!-- Fruit banner -->
    <img class="fruit-banner" src="https://pmainguet.dev.aupasdecourses.com/blog/wp-content/themes/zillah-child/img/fruit-banner.png" alt="Bannière de fruits"/>
    <!-- End fruit banner -->

   
    <!-- 6 random articles from the same category -->
    <div class="row">
        <div class="col-xs-12">
            <div class="six-random-articles-wrapper">
                <?php
                
                global $current_category_slug;
                
                $args = array (
                    'category_name' => $current_category_slug,
                    'posts_per_page' => '6',
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'orderby' => 'rand'
                );

                $six_random_posts = new WP_Query( $args );
                
                if($six_random_posts->have_posts()) :
                    while ($six_random_posts->have_posts()) : $six_random_posts->the_post();
                        
                        echo '<a class="six-random-articles-single-wrapper" href="'.get_permalink().'">';
                        
                            the_post_thumbnail('article-small-thumbnail');

                            echo '<div class="category-and-title-wrapper">';
                                $categories = get_the_category();
                                $separator = ", ";
                                $output = '';
                                if ($categories) { /*if there is content in the categories array*/
                                    foreach ($categories as $category) { /*for each item in the $categories array, identify the current item as the variable $category*/
                                        /* Assign a CSS class for each category */
                                        switch ($category->slug) {
                                            case "recettes":
                                                $span_class = 'category-text-recettes';
                                                break;
                                            case "astuces":
                                                $span_class = 'category-text-astuces';
                                                break;
                                            case "marche":
                                                $span_class = 'category-text-marche';
                                                break;
                                            case "decouverte":
                                                $span_class = 'category-text-decouverte';
                                                break;
                                            case "coup-de-food":
                                                $span_class = 'category-text-coupdefood';
                                                break;
                                            case "portrait":
                                                $span_class = 'category-text-portrait';
                                                break;
                                        }
                                        /*.= allows us to add onto a variable instead of overwriting it*/
                                        $output .= '<span class="'.$span_class.'">'.$category->cat_name.'</span>' . $separator;
                                    }

                                    echo trim($output, $separator); /*trim off the last , at the end of the list*/
                                }
                                ?> 
                                <div>
                                    <?php
                                    echo '<h4 class="post-title">'.get_the_title().'</h4>';
                                    ?>
                                </div>
                            </div> <!-- end .category-and-title-wrapper -->
                        </a><!-- end .six-random-articles-single-wrapper -->
                    <?php
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();
                ?>
            </div> <!-- end .six-random-articles-wrapper -->
        </div> <!-- end columns 6 random articles -->
        <div class="col-xs-12">
            <div class="button-container" id="single-article-category-archives">
                <a href="<?php echo $current_category_link ?>">
                    <button id="all-articles">Tous les articles <span><?php echo $current_category_name; ?></span></button>
                </a>
            </div>
        </div>
    </div> <!-- end .row 6 random articles -->
    
    
    <!-- Instagram Feed Photos -->
    <div class="row">
        <div class="col-md-12">
            <h2 id="instagram-title" class="text-center">Sur Instagram</h2>
            <?php echo wdi_feed(array('id'=>'1')); ?>
        </div> <!-- end column -->
    </div> <!-- end .row Instagram feed -->
    
</main>

<?php
get_footer();
