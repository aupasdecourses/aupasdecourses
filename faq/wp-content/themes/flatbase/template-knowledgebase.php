<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}
/* Template Name: Knowledgebase */

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="full-width <?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

				<header>
					<h1 class="entry-title"><?php the_title(); ?></h1>
				</header>

				<section id="knowledgebase" class="articles-grid three-col clearfix">
						<?php
						if ( isset( $nice_options['nice_articles_entries'] ) ) $number_articles = $nice_options['nice_articles_entries'];
						else $number_articles = 5;
						?>

						<?php nicethemes_knowledgebase( array( 'columns' => 3, 'numberposts' => $number_articles ) ); ?>
				</section>

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