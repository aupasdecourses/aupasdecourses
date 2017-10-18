<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

/* Template Name: Gallery */

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="full-width <?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

				<header>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>

				<div class="entry clearfix">
					<?php the_content( __( 'Continue reading', 'nicethemes' ) . ' &raquo;' ); ?>
				</div>

				<?php nicethemes_gallery(); ?>

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