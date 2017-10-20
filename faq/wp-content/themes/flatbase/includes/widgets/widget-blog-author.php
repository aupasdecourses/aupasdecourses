<?php
  /*---------------------------------------------------------------------------------*/
 /* Blog Author Info 																*/
/*---------------------------------------------------------------------------------*/
class Nice_BlogAuthor extends WP_Widget {

	function __construct() {

		$this->alt_option_name = 'nice_blogauthor';

		parent::__construct('nice_blogauthor', __( '(NiceThemes) Blog Author', 'nicethemes' ), array( 'description' => __( 'This widget includes Blog Author Info.', 'nicethemes' ) ));

		add_action( 'save_post', array(&$this, 'flush_widget_cache') );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache') );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache') );

   } // end __construct()

	function widget($args, $instance) {

		$cache = wp_cache_get('widget_nice_blogauthor', 'widget');

		if ( !is_array($cache) )
			$cache = array();

		if ( ! isset( $args['widget_id'] ) )
			$args['widget_id'] = $this->id;

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return;
		}

		ob_start();

		extract( $args );
		$title = $instance['title'];
		$bio = $instance['bio'];
		$twitter = $instance['twitter'];
		$custom_email = $instance['custom_email'];
		$avatar_size = $instance['avatar_size']; if ( !$avatar_size ) $avatar_size = 50;
		$avatar_align = $instance['avatar_align']; if ( !$avatar_align ) $avatar_align = 'left';
		$read_more_text = $instance['read_more_text'];
		$read_more_url = $instance['read_more_url'];
		$page = $instance['page'];
		if ( ( $page == "home" && is_home() ) || ( $page == "single" && is_single() ) || $page == "all" ) {
		?>
			<?php echo $before_widget; ?>
			<?php if ($title) { echo $before_title . $title . $after_title; } ?>

			<span class="<?php echo $avatar_align; ?>"><div class="thumb"><?php if ( $custom_email ) echo get_avatar( $custom_email, $size = $avatar_size ); ?></div></span>
			<p><?php echo $bio; ?></p>
			<?php if ( $read_more_url ) echo '<p><a href="' . $read_more_url . '">' . $read_more_text . '</a></p>'; ?>
			<?php if ( $twitter ) echo '<p><a href="http://twitter.com/' . $twitter . '" class="twitter">' . __('Follow me on twitter', 'nicethemes') . '</a></p>'; ?>
			<div class="fix"></div>
			<?php echo $after_widget; ?>
		<?php
		}

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set('widget_nice_blogauthor', $cache, 'widget');

	}

	function update($new_instance, $old_instance) {

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		if ( isset($alloptions['nice_feedback']) )
			delete_option('nice_feedback');

		return $new_instance;
	}

	function flush_widget_cache() {
		wp_cache_delete('widget_nice_blogauthor', 'widget');
	}

	function form($instance) {

		$title = esc_attr($instance['title']);
		$bio = esc_attr($instance['bio']);
		$twitter = esc_attr($instance['twitter']);
		$custom_email = esc_attr($instance['custom_email']);
		$avatar_size = esc_attr($instance['avatar_size']);
		$avatar_align = esc_attr($instance['avatar_align']);
		$read_more_text = esc_attr($instance['read_more_text']);
		$read_more_url = esc_attr($instance['read_more_url']);
		$page = esc_attr($instance['page']);
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('bio'); ?>"><?php _e('Bio:','nicethemes'); ?></label>
			<textarea name="<?php echo $this->get_field_name('bio'); ?>" class="widefat" id="<?php echo $this->get_field_id('bio'); ?>"><?php echo $bio; ?></textarea>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('twitter'); ?>"><?php _e('<a href="http://twitter.com/">Twitter</a> Username:','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('twitter'); ?>"  value="<?php echo $twitter; ?>" class="widefat" id="<?php echo $this->get_field_id('twitter'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('custom_email'); ?>"><?php _e('<a href="http://www.gravatar.com/">Gravatar</a> E-mail:','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('custom_email'); ?>"  value="<?php echo $custom_email; ?>" class="widefat" id="<?php echo $this->get_field_id('custom_email'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('avatar_size'); ?>"><?php _e('Gravatar Size:','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('avatar_size'); ?>"  value="<?php echo $avatar_size; ?>" class="widefat" id="<?php echo $this->get_field_id('avatar_size'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('avatar_align'); ?>"><?php _e('Gravatar Alignment:','nicethemes'); ?></label>
			<select name="<?php echo $this->get_field_name('avatar_align'); ?>" class="widefat" id="<?php echo $this->get_field_id('avatar_align'); ?>">
				<option value="left" <?php if($avatar_align == "left"){ echo "selected='selected'";} ?>><?php _e('Left', 'nicethemes'); ?></option>
				<option value="right" <?php if($avatar_align == "right"){ echo "selected='selected'";} ?>><?php _e('Right', 'nicethemes'); ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('read_more_text'); ?>"><?php _e('Read More Text (optional):','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('read_more_text'); ?>"  value="<?php echo $read_more_text; ?>" class="widefat" id="<?php echo $this->get_field_id('read_more_text'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('read_more_url'); ?>"><?php _e('Read More URL (optional):','nicethemes'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('read_more_url'); ?>"  value="<?php echo $read_more_url; ?>" class="widefat" id="<?php echo $this->get_field_id('read_more_url'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('page'); ?>"><?php _e('Visible Pages:','nicethemes'); ?></label>
			<select name="<?php echo $this->get_field_name('page'); ?>" class="widefat" id="<?php echo $this->get_field_id('page'); ?>">
				<option value="all" <?php if($page == "all"){ echo "selected='selected'";} ?>><?php _e('All', 'nicethemes'); ?></option>
				<option value="home" <?php if($page == "home"){ echo "selected='selected'";} ?>><?php _e('Home only', 'nicethemes'); ?></option>
				<option value="single" <?php if($page == "single"){ echo "selected='selected'";} ?>><?php _e('Single only', 'nicethemes'); ?></option>
			</select>
		</p>
		<?php
	}
} // end Nice_BlogAuthor classs

add_action( 'widgets_init', create_function( '', 'return register_widget("Nice_BlogAuthor");' ), 1 );
?>