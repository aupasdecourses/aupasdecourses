<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="<?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

				<article>

					<?php
					$nice_breadcrumbs_args = array(
							// Modify default BBPress Breadcrumbs
							'before'	=> '<div class="breadcrumb breadcrumbs nice-breadcrumb"><div class="breadcrumb-trail">',
							'after'		=> '</div></div>',
							'sep'		=> '<span class="sep">/</span>'
					);
					bbp_breadcrumb( $nice_breadcrumbs_args ); ?>

					<div class="entry clearfix">

						<?php the_content( __( 'Continue reading', 'nicethemes' ) . ' &raquo;' ); ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'nicethemes' ), 'after' => '</div>' ) ); ?>
					</div>

				</article>


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
	<?php dynamic_sidebar( 'bbpress' ); ?>
<!-- END #sidebar -->
</aside>

<?php get_footer(); ?>