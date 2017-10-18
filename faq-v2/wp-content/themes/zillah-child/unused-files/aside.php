    <aside>
        <div class="article-wrapper-aside">
                <!-- Show the latest portrait post -->
                <?php
                /*category_name is a list of the category slugs*/
                $args = array (
                    'category_name' => 'portrait',
                    'posts_per_page' => '1',
                    'post_type' => 'post',
                    'post_status' => 'publish'
                );

                $latest_portrait_post = new WP_Query( $args );

                if($latest_portrait_post->have_posts()) :
                    while ($latest_portrait_post->have_posts()) : $latest_portrait_post->the_post();
            
                        /* Add the post ID to a list of posts to not show below in the 6 random articles section */
                        global $displayed_articles;
                        $displayed_articles[] = get_the_ID();/*this adds the current post ID to the array*/
                        echo '<!-- displayed_articles = ';
                        print_r ($displayed_articles);
                        echo '-->';
            
                        echo '<a class="post-wrapper-aside" href="'.get_permalink().'">';

                            the_post_thumbnail('article-large-thumbnail');
                            ?>
                            <div class="background-article-portrait">
                                <?php
                                echo '<h2 class="post-title">'.get_the_title().'</h2>';

                                global $variable_excerpt_length;
                                $variable_excerpt_length='5';
                                echo '<p class="post-excerpt">'.get_the_excerpt().'</p>';
                                ?>
                            </div>
                            
                            <?php
                            $categories = get_the_category();
                            $separator = ", ";
                            $output = '';
                            if ($categories) { /*if there is content in the categories array*/
                                foreach ($categories as $category) { /*for each item in the $categories array, identify the current item as the variable $category*/
                                    
                                    /*.= allows us to add onto a variable instead of overwriting it*/
                                    $output .=
                                        '<p class="category-wrapper">
                                            <span class="category-box category-label-portrait">
                                            ' . $category->cat_name . '
                                            </span>
                                        </p>' . $separator;
                                }

                                echo trim($output, $separator); /*trim off the last , at the end of the list*/
                            }

                            
                        echo '</a>';
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();
                ?>
            </div> <!-- end .article-wrapper -->
            
            
            
            <div class="article-wrapper-aside" id="random-article-wrapper-aside">
                <!-- Show a random post -->
                <?php
                
                global $current_category_id;
                /*category_name is a list of the category slugs*/
                if (is_home()){
                    $args = array (
                        'category_name' => 'recettes, astuces, marche, decouverte, coup-de-food',
                        'posts_per_page' => '1',
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'orderby' => 'rand'
                    );
                }
                if (is_category()){
                    $args = array (
                        'category_name' => 'recettes, astuces, marche, decouverte, coup-de-food',
                        'category__not_in' => $current_category_id,
                        'posts_per_page' => '1',
                        'post_type' => 'post',
                        'post_status' => 'publish',
                        'orderby' => 'rand'
                    );
                }

                $random_post = new WP_Query( $args );

                if($random_post->have_posts()) :
                    while ($random_post->have_posts()) : $random_post->the_post();
                        /* Add the post ID to a list of posts to not show below in the 6 random articles section */
                        global $displayed_articles;
                        $displayed_articles[] = get_the_ID();/*this adds the current post ID to the array*/
                        echo '<!-- displayed_articles = ';
                        print_r ($displayed_articles);
                        echo '-->';
                
                        echo '<a class="post-wrapper-aside" href="'.get_permalink().'">';
                            
                            the_post_thumbnail('article-large-thumbnail');
                            
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
                                    case "coup-de-food":
                                        $span_class = 'category-label-coupdefood';
                                        break;
                                }
                                
                                echo '<span class="category-box-random '.$span_class.'">'.$categories[0]->cat_name.'</span>';
                                
                            }
                            ?> 
                            <div class="title-random-post">
                                <?php
                                echo '<h2 class="post-title" id="random-post-title">'.get_the_title().'</h2>';
                                ?>
                            </div>
                            <?php
                            
                            
                            ?><button class="ghost-button" id="button-random-post">Découvrir</button> <?php
                            
                        echo '</a>';
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();
                ?>
            </div> <!-- end .article-wrapper -->
            
            
            <!-- Newsletter subscription form -->
            <div id="newsletter-form">
                <!-- Begin MailChimp Signup Form -->
                <div id="mc_embed_signup">
                <form action="//aupasdecourses.us10.list-manage.com/subscribe/post?u=813feff892b5d2dac949b8ad4&amp;id=dbcdbd8580" method="post" id="mc-embedded-subscribe-form" name="mc-embedded-subscribe-form" class="validate" target="_blank" novalidate>
                    <div id="mc_embed_signup_scroll">
                    <h2 id="mc_title">Recevez nos nouvelles fraîches&nbsp;!</h2>
                <div class="mc-field-group">
                    <input type="email" value="" name="EMAIL" class="required email" placeholder="Votre email" id="mce-EMAIL">
                </div>
                    <div id="mce-responses" class="clear">
                        <div class="response" id="mce-error-response" style="display:none"></div>
                        <div class="response" id="mce-success-response" style="display:none"></div>
                    </div>    <!-- real people should not fill this in and expect good things - do not remove this or risk form bot signups-->
                    <div style="position: absolute; left: -5000px;" aria-hidden="true"><input type="text" name="b_813feff892b5d2dac949b8ad4_dbcdbd8580" tabindex="-1" value=""></div>
                    <div class="clear"><input type="submit" value="S'inscrire" name="subscribe" id="mc-embedded-subscribe" class="button"></div>
                    </div>
                </form>
                </div>
                <!--End mc_embed_signup-->
            </div>
        
        
            <!-- Vos articles préférés -->
            <?php
            /* Only on the category archive pages we show 4 random articles from the current category */
            /* Use the global variable $current_category_slug which we defined in archive.php which contains the current category slug */
            global $current_category_name;

            if (is_category()){
                $args = array (
                    'category_name' => $current_category_name,
                    'posts_per_page' => '4',
                    'post_type' => 'post',
                    'post_status' => 'publish',
                    'orderby' => 'rand'
                );
                ?>
                <h3 class="title-favorite-articles">Vos articles préférés</h3>
                <?php
                echo '<div class="four-random-articles-wrapper">';

                $four_random_posts = new WP_Query( $args );

                if($four_random_posts->have_posts()) :
                    while ($four_random_posts->have_posts()) : $four_random_posts->the_post();

                        echo '<a class="four-random-articles-single-wrapper" href="'.get_permalink().'">';

                                the_post_thumbnail('article-small-thumbnail');

                                echo '<div class="category-and-title-wrapper">';
                                    
                                /* Assign a CSS class for each category */
                                switch ($current_category_name) {
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

                                echo '<span class="'.$span_class.'">'.$current_category_name.'</span>';
                                    
                                ?> 
                                <div>
                                    <?php
                                    echo '<h4 class="post-title">'.get_the_title().'</h4>';
                                    ?>
                                </div>
                            </div> <!-- end .category-and-title-wrapper -->
                        </a><!-- end .four-random-articles-single-wrapper -->
                    <?php
                    endwhile;
                else :
                    echo "<p>Aucun article n'a été trouvé</p>";
                endif;

                wp_reset_query();

                echo '</div> <!-- end .four-random-articles-wrapper -->';
            }
            ?>
        <!-- end Vos articles préférés -->
    </aside>