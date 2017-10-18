<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="<?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<header>
			<h1 class="archive-header"><span class="cat"><?php echo single_cat_title(); ?></span></h1>
		</header>

		<?php while (have_posts()) : the_post(); ?>

				<!-- BEGIN .post -->
				<article class="post clearfix">

					<header>
						<h2><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php printf( __( 'Permanent Link to %s', 'nicethemes' ), get_the_title() ); ?>"><?php the_title(); ?></a></h2>
					</header>


					<div class="post-content">
							<?php nice_excerpt(); ?>
					</div>

				<!-- END .post -->
				</article>

		<?php endwhile; ?>

		<?php nice_pagenavi(); ?>

<?php else : ?>

			<?php _e( 'Sorry, no posts matched your criteria.', 'nicethemes' ); ?>

<?php endif; ?>

<!-- END #content -->
</section>

<!-- BEGIN #sidebar -->
<aside id="sidebar" role="complementary">
	<?php dynamic_sidebar( 'knowledgebase' ); ?>
<!-- END #sidebar -->
</aside>

<?php get_footer();