<?php
/**
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 *
 * @package zillah
 */

get_header(); ?>

<main class="container">
    <!-- Article and sidebar section -->
    <div class="row">
        
        <!-- Category header banner -->
        <div class="col-md-12">
           <?php
            global $current_category_name;
            global $current_category_id;
            $current_category_name = get_query_var('category_name');
            $current_category_id = get_query_var('cat');
            $current_category_visible_name = get_category( $current_category_id )->name;
            
            /* Assign a CSS class for each category */
            switch ($current_category_name) {
                case "recettes":
                    $category_wrapper_class = 'category-label-recettes';
                    break;
                case "astuces":
                    $category_wrapper_class = 'category-label-astuces';
                    break;
                case "marche":
                    $category_wrapper_class = 'category-label-marche';
                    break;
                case "decouverte":
                    $category_wrapper_class = 'category-label-decouverte';
                    break;
                case "portrait":
                    $category_wrapper_class = 'category-label-portrait';
                    break;
                case "coup-de-food":
                    $category_wrapper_class = 'category-label-coupdefood';
                    break;
                case "dossier":
                    $category_wrapper_class = 'category-label-dossier';
                    break;
            }

            ?>
            <div class="archive-category-wrapper <?php echo $category_wrapper_class ?>">
                <h1 class="archive-category-title">
                    <?php echo $current_category_visible_name ?>
                </h1>
                <p>
                    <?php
                        echo category_description( $current_category_id );
                    ?>
                </p>
            </div>
                
        </div> <!-- end column -->
        <!-- end Category header banner -->
        
        
        <!-- 8 latest articles -->
        <div class="col-md-6 col-md-offset-1" id="article-column">
           <div class="article-wrapper">
                <!-- Show the 8 latest posts of the category -->
                <?php
                /* Pagination doesn't work with custom WP loops by default, so we use this extra code
                to make it work.*/
                $temp = $wp_query; /* assign $wp_query to another temporary variable while we execute this loop*/
               
                /*category_name is a list of the category slugs*/
                /* the $paged variable is essential to make the pagination work */
                $args = array (
                    'category_name' => $current_category_name,
                    'posts_per_page' => '8',
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'paged' => $paged 
                );

                $wp_query = new WP_Query( $args );

                if($wp_query->have_posts()) :
                    
                    while ($wp_query->have_posts()) : $wp_query->the_post();
                        echo '<a class="post-wrapper" href="'.get_permalink().'">';

                            the_post_thumbnail('article-medium-thumbnail');

               
                            /* Assign a CSS class for each category */
                            switch ($current_category_name) {
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
                                case "portrait":
                                    $span_class = 'category-label-portrait';
                                    break;
                                case "coup-de-food":
                                    $span_class = 'category-label-coupdefood';
                                    break;
                                case "dossier":
                                    $span_class = 'category-label-dossier';
                                    break;
                            }
                            
                            echo '<p class="category-wrapper">
                                    <span class="category-box '.$span_class.'">'.$current_category_name.'</span>
                                </p>';
                            
                            echo '<h2 class="post-title">'.get_the_title().'</h2>';
                            
                            global $variable_excerpt_length;
                            $variable_excerpt_length='12';
                            echo '<p class="post-excerpt">'.get_the_excerpt().'</p>';
                        echo '</a>';               
                    endwhile;
                    ?>
               
                    </div> <!-- end .article-wrapper -->
                    
                    <?php
                    theme_pagination(); /* insert the pagination links here */
            
                    /* reset the $wp_temp variable back to the original which was stored in $temp */
                    $wp_query = null;
                    $wp_query = $temp;
                    
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();
                ?>
                
            
        </div> <!-- end main article column -->
        <!-- 8 latest articles -->
        
        
        
        <div class="col-md-3 col-md-offset-1" id="aside-column">
            <?php include("aside.php"); ?>
        </div>
    </div><!-- end .row -->
    
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
