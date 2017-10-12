<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package zillah
 */

get_header(); 

/* Declare global variable which will hold the list of post IDs which are displayed
to avoid showing them in the 6 random articles section */
global $displayed_articles;
$displayed_articles = array();
?>

<main class="container">
    <!-- Article and sidebar section -->
   <div class="row">
        <div class="col-md-6 col-md-offset-1" id="article-column">
           <div class="article-wrapper">
                <!-- Show the 4 latest posts -->
                <?php
                /*category_name is a list of the category slugs*/
                $args = array (
                    'category_name' => 'recettes, astuces, marche, decouverte',
                    'posts_per_page' => '4',
                    'post_type' => 'post',
                    'post_status' => 'publish'
                );

                $four_latest_posts = new WP_Query( $args );

                if($four_latest_posts->have_posts()) :
                    while ($four_latest_posts->have_posts()) : $four_latest_posts->the_post();
                        
                        /* Add the post ID to a list of posts to not show below in the 6 random articles section */
                        global $displayed_articles;
                        $displayed_articles[] = get_the_ID();/*this adds the current post ID to the array*/
                        
                        echo '<a class="post-wrapper" href="'.get_permalink().'">';

                            the_post_thumbnail('article-medium-thumbnail');

                            $categories = get_the_category();
                            $separator = ", ";
                            $output = '';
                            if ($categories) { /*if there is content in the categories array*/
                                /* Show only the first category assigned because the mockup design
                                does not include an option for showing two or more category label boxes */
                                /* Assign a CSS class for each category */
                                switch ($categories[0]->slug) {
                                    case "recettes":
                                        $span_class = 'category-label-recettes';
                                        break;
                                    case "astuces":
                                        $span_class = 'category-label-astuces';
                                        break;
                                    case "marche":
                                        $span_class = 'category-label-marche';
                                        break;
                                    case "decouverte":
                                        $span_class = 'category-label-decouverte';
                                        break;
                                }
                                
                                echo '<p class="category-wrapper"><span class="category-box '.$span_class.'">' . $categories[0]->cat_name . '</span></p>';
                            }

                            echo '<h2 class="post-title">'.get_the_title().'</h2>';

                            global $variable_excerpt_length;
                            $variable_excerpt_length='12';
                            echo '<p class="post-excerpt">'.get_the_excerpt().'</p>';
                        echo '</a>';
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();
                ?>
                
                <div class="button-container">
                    <a href="https://www.aupasdecourses.com/blog/archives/">
                        <button id="all-articles">Voir tous les articles</button>
                    </a>
                </div>
            </div> <!-- end .article-wrapper -->
            
        </div> <!-- end main article column -->
        <div class="col-md-3 col-md-offset-1" id="aside-column">
            <?php include("aside.php"); ?>
        </div>
        
    </div><!-- end .row -->
    
    <!-- Coup de Food banner -->
    <div class="row">
        
        <?php
            /*category_name is a list of the category slugs*/
            $args = array (
                'category_name' => 'coup-de-food',
                'posts_per_page' => '1',
                'post_type' => 'post',
                'post_status' => 'publish'
            );

            $latest_food_post = new WP_Query( $args );
                
                if($latest_food_post->have_posts()) :
                    while ($latest_food_post->have_posts()) : $latest_food_post->the_post();
                        
                        /* Add the post ID to a list of posts to not show below in the 6 random articles section */
                        global $displayed_articles;
                        $displayed_articles[] = get_the_ID();/*this adds the current post ID to the array*/
                        
                        echo '<a href="'.get_permalink().'">';
                        echo '<div class="col-xs-12">';
                        echo '<div id="coup-de-food-banner">';
                        echo '<h2 id="coup-de-food-section-title">Coup de Food</h2>';
        
                        echo '<div id="coup-de-food-title-excerpt">';
            
                        echo '<h2 class="post-title">'.get_the_title().'</h2>';
                        
                        global $variable_excerpt_length;
                        $variable_excerpt_length='10';
                        echo '<p class="post-excerpt">'.get_the_excerpt().'</p>';
                        
                        echo '</div>';
            
                        echo '<div id="coup-de-food-image">';
                            the_post_thumbnail('article-large-thumbnail');
                        echo '</div>';
                        
                        echo '</div> <!-- end #coup-de-food-banner -->';
                        echo '</div> <!-- end col-xs-12 -->';
                        echo '</a>';
                        
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;
                
            wp_reset_query();
            
        ?>
    </div> <!-- end .row coup-de-food -->
    
    <!-- 6 random articles -->
    <div class="row">
        <div class="col-xs-12">
            <div class="six-random-articles-wrapper">
                <?php
                
                /* Do not display articles aready displayed above */
                global $displayed_articles;
                echo '<!-- displayed_articles = ';
                print_r ($displayed_articles);
                echo '-->';
                
                $args = array (
                    'category_name' => 'recettes, astuces, marche, decouverte, coup-de-food, portrait',
                    'posts_per_page' => '6',
                    'post_type' => 'post',
                    'post__not_in' => $displayed_articles,
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
                                        /* Show only the first category assigned because the mockup design
                                        does not include an option for showing two or more category label boxes */
                                        /* Assign a CSS class for each category */
                                        switch ($categories[0]->slug) {
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
                                        
                                        echo '<span class="'.$span_class.'">'.$categories[0]->cat_name.'</span>';
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
    </div> <!-- end .row 6 random articles -->

    <!-- Section "Dossier" -->
    <div class="row">
        <div class="col-xs-12">
            <?php
            /*category_name is a list of the category slugs*/
            $args = array (
                'category_name' => 'dossier',
                'posts_per_page' => '1',
                'post_type' => 'post',
                'post_status' => 'publish'
            );

            $latest_dossier_post = new WP_Query( $args );

            if($latest_dossier_post->have_posts()) :
                while ($latest_dossier_post->have_posts()) : $latest_dossier_post->the_post();

                    echo '<a href="'.get_permalink().'">';

                        echo '<div id="dossier-content-wrapper" style="background-image:url('. get_the_post_thumbnail_url().');background-size:cover;background-repeat:no-repeat;background-position:center center;">';
                            echo '<div id="dossier-text">';
                                echo '<h2>Dossier</h2>';
                                echo '<h2 id="dossier-title">'.get_the_title().'</h2>';
                                echo '<button class="ghost-button">Découvrir</button>';
                            echo '</div> <!-- end #dossier-text -->';
                        echo '</div> <!-- end #dossier-content-wrapper -->';
                    echo '</a>';
                endwhile;
            else :
                echo "<p>Aucun article n'a été trouvé</p>";
            endif;

            wp_reset_query();
            ?>

            
        </div> <!-- end column -->
    </div> <!-- end .row Section "Dossier" -->
    

    <!-- Instagram Feed Photos -->
    <div class="row">
        <div class="col-xs-12">
            <h2 id="instagram-title" class="text-center">Sur Instagram</h2>
            <?php echo wdi_feed(array('id'=>'1')); ?>
        </div> <!-- end column -->
    </div> <!-- end .row Instagram feed -->
</main> <!-- end main .container -->

<?php
get_footer();
