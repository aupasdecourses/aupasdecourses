<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

/* Template Name: Faq (Accordion) */

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

						$loop = 0;

						while ( $faq_query->have_posts() ) : $faq_query->the_post();
						?>

						<article id="faq-<?php the_ID(); ?>" class="faq clearfix">

							<header>
								<span id="faq-<?php echo get_the_ID(); ?>" class="faq-title">
									<a href="#faq-title-<?php echo get_the_ID(); ?>">
										<?php the_title(); ?>
									</a>
								</span>
							</header>

							<div class="entry-content">
								<?php the_content(); ?>
							</div>

						</article>

						<?php
						endwhile;

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