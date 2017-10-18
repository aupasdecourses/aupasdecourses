<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="full-width">

	<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>

		<article <?php post_class(); ?>>

			<header>
					<h1 class="entry-title"><?php the_title(); ?></h1>
			</header>

			<div class="entry">

				<div class="post-content<?php echo $video_class; ?>">
					<?php the_content( __( 'Continue reading', 'nicethemes' ) . ' &raquo;' ); ?>
				</div>

			</div>

		</article>

		<?php nice_pagenavi(); ?>

		<?php comments_template( '', true ); ?>

	<?php endwhile; ?>

<!-- END #content -->
</section>

<?php get_footer();