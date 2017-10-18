<?php

  /*---------------------------------------------------------------------------------*/
 /* Social widget 																	*/
/*---------------------------------------------------------------------------------*/
class Nice_Social extends WP_Widget {

   function __construct() {
	   $widget_ops = array( 'description' => __( 'Add your social links with this widget. (Note: To set the social links you have to do it on the theme options panel.)', 'nicethemes' ) );
	   parent::__construct( false, __( '(NiceThemes) Social Widget', 'nicethemes' ), $widget_ops);
   }

   function widget( $args, $instance) {
	extract( $args );
   	$title = $instance['title'];
	$unique_id = $args['widget_id'];
	global $nice_options;
	?>
		<?php echo $before_widget; ?>
		<?php if ( $title ) echo $before_title . $title . $after_title; ?>
		<?php

				$social_items = '';

				if ( $nice_options["nice_facebook_url"] <> '' )
					$social_items .= '<li id="facebook"><a href="' . esc_url( $nice_options["nice_facebook_url"] ) . '"><i class="fa fa-facebook"></i></a></li>';

				if ( $nice_options["nice_twitter_url"] <> '' )
					$social_items .= '<li id="twitter"><a href="' . esc_url( $nice_options["nice_twitter_url"] ) . '"><i class="fa fa-twitter"></i></a></li>';

				if ( $nice_options["nice_instagram_url"] <> '' )
					$social_items .= '<li id="instagram"><a href="' . esc_url( $nice_options["nice_instagram_url"] ) . '"><i class="fa fa-instagram"></i></a></li>';

				if ( $nice_options["nice_google_url"] <> '' )
					$social_items .= '<li id="google"><a href="' . esc_url( $nice_options["nice_google_url"] ) . '"><i class="fa fa-google-plus"></i></a></li>';

				if ( $nice_options["nice_dribbble_url"] <> '' )
					$social_items .= '<li id="dribbble"><a href="' . esc_url( $nice_options["nice_dribbble_url"] ) . '"><i class="fa fa-dribbble"></i></a></li>';

				if ( $nice_options["nice_vimeo_url"] <> '' )
					$social_items .= '<li id="vimeo"><a href="' . esc_url( $nice_options["nice_vimeo_url"] ) . '"><i class="fa fa-vimeo-square"></i></a></li>';

				if ( $nice_options["nice_tumblr_url"] <> '' )
					$social_items .= '<li id="tumblr"><a href="' . esc_url( $nice_options["nice_tumblr_url"] ) . '"><i class="fa fa-tumblr"></i></a></li>';

				if ( $nice_options["nice_flickr_url"] <> '' )
					$social_items .= '<li id="flickr"><a href="' . esc_url( $nice_options["nice_flickr_url"] ) . '"><i class="fa fa-flickr"></i></a></li>';

				if ( $nice_options["nice_youtube_url"] <> '' )
					$social_items .= '<li id="youtube"><a href="' . esc_url( $nice_options["nice_youtube_url"] ) . '"><i class="fa fa-youtube-play"></i></a></li>';

				if ( $nice_options["nice_linkedin_url"] <> '' )
					$social_items .= '<li id="linkedin"><a href="' . esc_url( $nice_options["nice_linkedin_url"] ) . '"><i class="fa fa-linkedin"></i></a></li>';

				if ( $nice_options["nice_dropbox_url"] <> '' )
					$social_items .= '<li id="dropbox"><a href="' . esc_url( $nice_options["nice_dropbox_url"] ) . '"><i class="fa fa-dropbox"></i></a></li>';

				if ( $nice_options["nice_foursquare_url"] <> '' )
					$social_items .= '<li id="foursquare"><a href="' . esc_url( $nice_options["nice_foursquare_url"] ) . '"><i class="fa fa-foursquare"></i></a></li>';

				if ( $nice_options["nice_pinterest_url"] <> '' )
					$social_items .= '<li id="pinterest"><a href="' . esc_url( $nice_options["nice_pinterest_url"] ) . '"><i class="fa fa-pinterest"></i></a></li>';

				if ( $nice_options["nice_skype_url"] <> '' )
					$social_items .= '<li id="skype"><a href="' . esc_url( $nice_options["nice_skype_url"] ) . '"><i class="fa fa-skype"></i></a></li>';

				if ( $nice_options["nice_bitbucket_url"] <> '' )
					$social_items .= '<li id="bitbucket"><a href="' . esc_url( $nice_options["nice_bitbucket_url"] ) . '"><i class="fa fa-bitbucket"></i></a></li>';

				if ( $nice_options["nice_github_url"] <> '' )
					$social_items .= '<li id="github"><a href="' . esc_url( $nice_options["nice_github_url"] ) . '"><i class="fa fa-github"></i></a></li>';

				if ( $nice_options["nice_stack_exchange_url"] <> '' )
					$social_items .= '<li id="stack-exchange"><a href="' . esc_url( $nice_options["nice_skype_url"] ) . '"><i class="fa fa-stack-exchange"></i></a></li>';

				if ( $nice_options["nice_stack_overflow_url"] <> '' )
					$social_items .= '<li id="stack-overflow"><a href="' . esc_url( $nice_options["nice_skype_url"] ) . '"><i class="fa fa-stack-overflow"></i></a></li>';

				if ( $nice_options["nice_trello_url"] <> '' )
					$social_items .= '<li id="trello"><a href="' . esc_url( $nice_options["nice_trello_url"] ) . '"><i class="fa fa-trello"></i></a></li>';


				if ( ! empty ( $social_items ) ) :
				?>

				<div class="social-links clearfix">

					<ul id="social">
						<?php echo $social_items; ?>
					</ul>

				</div>
				<?php endif; ?>

			<?php echo $after_widget; ?>


	<?php
   }

   function update( $new_instance, $old_instance) {
	   return $new_instance;
   }

   function form( $instance) {

	   $title = esc_attr( $instance['title']);
	   ?>
	   <p>
	   	   <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title (optional):', 'nicethemes' ); ?></label>
		   <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
	   </p>
	  <?php
   }

}
register_widget('Nice_Social');
?>