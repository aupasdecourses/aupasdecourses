<?php
/**
 * The template for displaying all single posts.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 */
global $wp_customize;
$zillah_sidebar_show = get_theme_mod('zillah_sidebar_show', false);

get_header(); ?>

	<div class="content-wrap">

		<div id="primary" class="content-area content-area-arch<?php echo $zillah_sidebar_show !== false ? ' content-area-with-sidebar' : ''; ?>">
			<main id="main" class="site-main" role="main">

			<?php
            while (have_posts()) : the_post();

                get_template_part('template-parts/content', 'single');

                the_post_navigation();

                // If comments are open or we have at least one comment, load up the comment template.
                if (comments_open() || get_comments_number()) :
                    echo '<div class="comments-area-wrap">';
                    comments_template();
                    echo '</div>';
                endif;

            endwhile; // End of the loop.
            ?>

			</main><!-- #main -->
		</div><!-- #primary -->
<?php
            if ($zillah_sidebar_show !== false || ($zillah_sidebar_show === false && is_customize_preview())) {
                get_sidebar();
            }
        ?>
	</div><!-- .content-wrap -->

<?php
get_footer();
