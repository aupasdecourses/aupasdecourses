<?php

get_header(); /* insert the Wordpress header code from header.php */

    if(have_posts()) : ?>
        <h2>Search results for: <?php the_search_query(); ?></h2>

        <?php
        while (have_posts()) : the_post();
            get_template_part('content', get_post_format()); /*insert the code contained in content[-*].php*/
        endwhile;

    else :
        echo '<p>Aucun résultat trouvé</p>';
    endif;

get_footer(); /* insert the Wordpress footer code from footer.php */
?>