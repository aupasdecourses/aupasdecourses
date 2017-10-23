<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

/* Template Name: Contact */

$nice_contact_form = apply_filters( 'nice_contact_form', true );

if ( $nice_contact_form ) {
	add_filter( 'nice_load_contact_js', '__return_true', 10 );
}

get_header(); ?>

<!-- BEGIN #content -->
<section id="content" class="<?php echo $post->post_name; ?>">

<?php if ( have_posts() ) : ?>

		<?php while ( have_posts() ) : the_post(); ?>

			<div class="nice-contact-form">

				<header>
					<?php nice_breadcrumbs(); ?>
					<h1 class="entry-title"><?php the_title(); ?></h1>

				</header>

					<?php

						$nice_google_map = get_option( 'nice_google_map' );

						if ( ! empty ( $nice_google_map ) ): ?>
							<div class="nice-contact-map clearfix">
								<?php echo nice_embed( array ( 'embed' => $nice_google_map, 'width' => 960, 'height' => 300) ); ?>
							</div>
						<?php endif; ?>

					<div class="entry">
						<?php the_content( __( 'Continue reading', 'nicethemes' ) . ' &raquo;' ); ?>
					</div>

					<?php if ( $nice_contact_form ) : ?>
						<div id="node"></div>
						<div id="success"><?php _e( 'Thank you for leaving a message.', 'nicethemes' ); ?></div>

						<form name="nice_contact" id="nice_contact" method="post" >
						<p>
							<label class="display-ie8" for="name" form="nice_contact"><?php _e( 'Your Name', 'nicethemes' ); ?><span class="required">*</span></label>
							<input type="text" id="name" name="name" value="" class="required" placeholder="<?php _e( 'Your Name', 'nicethemes'); ?>" title="<?php _e( '* Please enter your Full Name', 'nicethemes'); ?>" />
						</p>
						<p>
							<label class="display-ie8" for="subject" form="nice_contact"><?php _e( 'Subject', 'nicethemes' ); ?></label>
							<input type="text" name="subject" id="subject" value="" placeholder="<?php _e( 'Subject', 'nicethemes'); ?>" title="<?php _e( '* Please enter the subject', 'nicethemes'); ?>" />
						</p>
						<p>
							<label class="display-ie8" for="mail" form="nice_contact"><?php _e( 'Your E-Mail', 'nicethemes' ); ?><span class="required">*</span></label>
							<input type="text" name="mail" id="mail" value="" class="required email" placeholder="<?php _e( 'Your E-Mail', 'nicethemes'); ?>" title="<?php _e( '* Please enter your email', 'nicethemes'); ?>" />
						</p>
						<p>
							<label class="display-ie8" for="message" form="nice_contact"><?php _e( 'Your Message', 'nicethemes' ); ?><span class="required">*</span></label><br />
							<textarea name="message" id="message" class="required" placeholder="<?php _e( 'Your Message', 'nicethemes'); ?>" title="<?php _e( '* Please enter a message', 'nicethemes'); ?>"></textarea>
						</p>
						<p>
						<input type="submit" value="<?php _e( 'Submit', 'nicethemes' ); ?>" />
						</p>
						</form>
					<?php endif; ?>
				</div>

		<?php endwhile; ?>

<?php else : ?>

			<header>
				<h2><?php _e( 'Not Found', 'nicethemes' ); ?></h2>
			</header>
			<p class="center"><?php _e( 'Sorry, but you are looking for something that isn\'t here.', 'nicethemes' ); ?></p>
			<?php get_search_form(); ?>

<?php endif; ?>

		<!-- END #content -->
		</section>

<?php
get_sidebar();
get_footer();