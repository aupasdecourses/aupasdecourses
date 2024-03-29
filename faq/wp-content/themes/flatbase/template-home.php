<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}
/* Template Name: Home */

get_header();

$nice_infobox_enable = get_option( 'nice_infobox_enable' );
$nice_infobox_order = get_option( 'nice_infobox_order' );

if ( $nice_infobox_order == '' )
	$nice_infobox_order = 'date';

if ( $nice_infobox_enable == 'true' ) :

	nicethemes_infoboxes( array(	'orderby'		=> $nice_infobox_order,
							'numberposts'	=> 3,
							'before' 		=> '<section id="infoboxes" class="infoboxes home-block clearfix"><div class="col-full">',
							'after'			=> '</div></section><!--/.#infobox-->' )
					);

endif;

if ( isset( $nice_options['nice_articles_entries'] ) ) $number_articles = $nice_options['nice_articles_entries'];
else $number_articles = 5;


nicethemes_knowledgebase( array(	'columns'		=> 3,
							'numberposts'	=> $number_articles,
							'before'		=> '<section id="knowledgebase" class="home-block clearfix"><div class="col-full">',
							'after'			=> '</div></section>' ) );


$nice_video_enable = get_option( 'nice_video_enable' );
$nice_video_order = get_option( 'nice_video_order' );

if ( $nice_video_order == '' )
	$nice_video_order = 'date';

if ( $nice_video_enable == 'true' ) : ?>



	<?php

		if ( isset( $nice_options['nice_video_entries'] ) ) $number_videos = $nice_options['nice_video_entries'];
		else $number_videos = 5;

		nice_home_videos( array( 'numberposts' => $number_videos, 'orderby' => $nice_video_order,
									'before'	=> '<section id="home-videos" class="videos home-block clearfix"><div class="col-full">',
									'after'		=> '</div></section>'
									 ) );

	?>


<?php endif; ?>


<?php $nice_footer_columns = ( ! empty( $nice_options['nice_footer_columns'] ) ) ? $nice_options['nice_footer_columns'] : '3'; ?>

<?php $class = ' columns-' . esc_attr( intval( $nice_footer_columns ) ); ?>

<?php if ( 	is_active_sidebar( 'pre-footer-1' ) ||
			is_active_sidebar( 'pre-footer-2' ) ||
			is_active_sidebar( 'pre-footer-3' ) ) : ?>

	<section id="pre-footer-widgets" class="pre-footer-widgets home-block clearfix">

		<div class="col-full">

			<div class="grid">

				<div class="widget-section odd first  <?php echo $class; ?>">
					<?php dynamic_sidebar( 'pre-footer-1' ); ?>
				</div>

				<div class="widget-section even  <?php echo $class; ?>">
					<?php dynamic_sidebar( 'pre-footer-2' ); ?>
				</div>

				<?php if ( $nice_footer_columns == '3' || $nice_footer_columns == '4' ) : ?>
				<?php if ( $nice_footer_columns == '3' ) $class .= ' last'; ?>
				<div class="widget-section odd  <?php echo $class; ?>">
					<?php dynamic_sidebar( 'pre-footer-3' ); ?>
				</div>
				<?php endif; ?>
				<?php if ( $nice_footer_columns == '4' ) : ?>
				<div class="widget-section odd <?php echo $class; ?> last">
					<?php dynamic_sidebar( 'pre-footer-4' ); ?>
				</div>
				<?php endif; ?>
			</div>
		</div>

	<!-- END #home-widgets -->
	</section>

<?php endif; ?>

<?php get_footer();