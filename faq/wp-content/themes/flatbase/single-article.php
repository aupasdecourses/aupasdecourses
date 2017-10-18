<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); ?>

<!-- BEGIN #content -->
<section id="content">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<article <?php post_class(); ?>>

			<?php // add pageview ?>
			<?php nice_pageviews(); ?>

			<header>
					<?php nice_breadcrumbs( array( 'singular_article_taxonomy' => 'article-category' ) ); ?>

					<h1 class="entry-title"><?php the_title(); ?></h1>

					<?php nice_article_meta(); ?>

			</header>

			<div class="entry">

				<?php

				$embed = get_post_meta( get_the_ID(), 'embed', true );
				$video_class = '';

				if ( $embed <> '' ){
					echo nice_embed( array ( 'id' => get_the_ID() ) );
					$video_class = ' has-video';
				}
				elseif ( has_post_thumbnail() ) { ?>

					<figure class="featured-image">
						<?php nice_image( array( 'width' => 730, 'height' => 338, 'class' => 'wp-post-image' ) ); ?>
					</figure>

				<?php } ?>

					<div class="post-content<?php echo $video_class; ?>">
						<?php the_content(); ?>
					</div>

					<footer class="entry-meta">

						<span class="tag-links">
							<?php echo get_the_term_list( get_the_ID(), 'article-tag', '<i class="fa fa-tags"></i>', '', '' ); ?>
						</span>

						<span class="category-links">
							<?php echo get_the_term_list( get_the_ID(), 'article-category', '<i class="fa fa-archive"></i>', '', '' ); ?>
						</span>

					</footer>

			</div>

		</article>


		<?php

			if ( nice_bool ( get_option( 'nice_article_author' ) ) )
				nice_post_author();

			if ( nice_bool ( get_option( 'nice_related_articles' ) ) )
				nice_related_posts( array( 'post_type' => 'article', 'taxonomy' => 'article-category', 'posts_per_page' => 4, 'title' => __( 'Related Articles', 'nicethemes' ) ) );

			comments_template( '', true );

		?>

	<?php endwhile; ?>

<!-- END #content -->
</section>

<!-- BEGIN #sidebar -->
<aside id="sidebar" role="complementary">
	<?php dynamic_sidebar( 'knowledgebase' ); ?>
<!-- END #sidebar -->
</aside>

<?php get_footer();