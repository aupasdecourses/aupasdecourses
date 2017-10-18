<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

/* Template Name: Faq (Scroll) */

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="<?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

				<header>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>

				<?php nice_breadcrumbs(); ?>

				<div class="entry clearfix">

					<?php the_content(); ?>

					<?php if ( get_query_var( 'paged' ) ) $paged = get_query_var( 'paged' ); elseif ( get_query_var( 'page' ) ) $paged = get_query_var( 'page' ); else $paged = 1; ?>

					<?php

					$args = array(
							'post_type'			=> 'faq',
							'posts_per_page'	=> '-1',
							'orderby'			=> 'menu_order',
							'order'				=> apply_filters( 'nice_faq_order', 'ASC' ),
							'paged'				=> $paged
						);
					$faq_query = new WP_Query( $args );

					if ( $faq_query->have_posts() ) :

						$questions = '<ul class="faq-questions">' . "\n";

						$full = '';

						while ( $faq_query->have_posts() ) : $faq_query->the_post();

							$questions .= '<li><a href="#faq-' . get_the_ID() . '">' . get_the_title() . '</a></li>' . "\n";

							$full .= '<article class="faq-entry">' . "\n";
							$full .= '<header><h3><a name="faq-' . get_the_ID() . '">' . get_the_title() . '</a></h3></header>' . "\n";
							$full .= '<div class="entry-content">' . "\n";
							$full .= get_the_content() . "\n";
							$full .= '</div>' . "\n";
							$full .= '</article>' . "\n";

						?>

						<?php
						endwhile;

						$questions .= '</ul>';

						echo $questions;

						echo $full;

					endif;

					?>
				</div>


		<?php endwhile; ?>

<?php else : ?>

		<header>
			<h2><?php _e( 'Not Found', 'nicethemes' ); ?></h2>
		</header>
		<p><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'nicethemes' ); ?></p>

		<?php get_search_form(); ?>

<?php endif; ?>

<!-- END #content -->
</section>

<!-- BEGIN #sidebar -->
<aside id="sidebar" role="complementary">
	<?php dynamic_sidebar( 'faq' ); ?>
<!-- END #sidebar -->
</aside>

<?php get_footer();