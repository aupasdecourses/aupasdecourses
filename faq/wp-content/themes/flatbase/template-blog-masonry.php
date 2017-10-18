<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

/* Template Name: Blog - Masonry */

add_filter( 'nice_load_flexslider_js', '__return_true', 10 );
add_filter( 'nice_load_more_posts_loader_js', '__return_true', 10 );

get_header();

global $nice_options;

if ( get_query_var( 'paged' ) ) $paged = get_query_var( 'paged' ); elseif ( get_query_var( 'page' ) ) $paged = get_query_var( 'page' ); else $paged = 1; ?>

<?php query_posts( array( 'post_type' => 'post', 'paged' => $paged ) ); ?>

<!-- BEGIN #content -->
<section id="content" class="full-width blog-masonry <?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<div id="masonry-grid" class="grid clearfix">

		<?php

				$columns = 3;
				$loop = 0;

				while ( have_posts() ) : the_post();

				$loop++;
				?>
				<div id="post-<?php the_ID(); ?>" class="masonry-item isotope-item columns-<?php echo $columns; ?> <?php if ( $loop % $columns == 0) echo 'last'; if ( ( $loop - 1 ) % $columns == 0 ) echo 'first'; ?>">

					<!-- BEGIN .post -->
					<article class="post clearfix">

						<?php if ( has_post_thumbnail() ) :	?>
							<figure class="featured-image view view-more">
									<a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permanent Link to %s', 'nicethemes' ), get_the_title() ); ?>">
										<?php nice_image( array( 'width' => 580, 'height' => 405, 'class' => 'wp-post-image' ) ); ?>
									</a>
							</figure>
						<?php endif; ?>

						<header>
							<h2 class="post-title">
								<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( __( 'Permanent Link to %s', 'nicethemes' ), get_the_title() ); ?>"><?php the_title(); ?></a>
							</h2>
							<?php nice_post_meta_masonry(); ?>
						</header>

						<div class="entry">

							<div class="post-content">
								<?php the_excerpt(); ?>

								<a class="readmore" href="<?php echo get_permalink( get_the_ID() ); ?>" title="<?php printf( __( 'Permanent Link to %s', 'nicethemes' ), get_the_title() ); ?>"> <?php _e( 'Read More', 'nicethemes' ); ?></a>
							</div>

						</div>

				<!-- END .post -->
				</article>

				</div>

		<?php endwhile; ?>

		</div>

<?php else : ?>

			<?php _e( 'Sorry, no posts matched your criteria.', 'nicethemes' ); ?>

<?php endif; ?>

<?php if ( isset( $nice_options['nice_masonry_posts_load_method'] ) && $nice_options['nice_masonry_posts_load_method'] == 'on_button'  ) { ?>
	<a href="#" id="posts-ajax-loader-button" title="<?php _e( 'Load More..', 'nicethemes' ); ?>"><?php _e( 'Load More..', 'nicethemes' ); ?></a>
<?php } ?>

<div id="posts-ajax-loader"></div>

<!-- END #content -->
</section>

<?php get_footer();