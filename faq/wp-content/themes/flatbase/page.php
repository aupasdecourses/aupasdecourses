<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); ?>

<!-- BEGIN #sidebar -->
<aside id="sidebar" role="complementary">
	<?php dynamic_sidebar( 'page' ); ?>
<!-- END #sidebar -->
</aside>


<!-- BEGIN #content -->
<section id="content" class="<?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

				<article>

					<header>
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<?php nice_breadcrumbs(); ?>
					</header>

					<div class="entry clearfix">

						<?php the_content( __( 'Continue reading', 'nicethemes' ) . ' &raquo;' ); ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'nicethemes' ), 'after' => '</div>' ) ); ?>
					</div>

				</article>

				<?php comments_template( '', true ); ?>

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


<?php get_footer();
