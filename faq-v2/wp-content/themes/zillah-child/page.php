<?php
/**
 * The template for displaying all pages.
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages
 * and that other 'pages' on your WordPress site may use a
 * different template.
 *
 * @link https://codex.wordpress.org/Template_Hierarchy
 */

get_header(); ?>

	</div><!-- .container -->
	
	<?php if (have_posts()) :
    while (have_posts()) : the_post();?>
    
	<div class="container" id="questions-and-answers">
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>"><img class="left-arrow" src="<?php echo esc_url( home_url( '/' ) ); ?>wp-content/themes/zillah-child/img/arrow-left.png">Retour</a>
	    <h1><?php the_title(); ?></h1>
        <?php the_content(); ?>

<?php
    endwhile;
    
    else :
        echo '<p>No content found</p>';
        
    endif;
        
get_footer();
