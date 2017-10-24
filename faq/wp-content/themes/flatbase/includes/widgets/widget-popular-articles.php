<?php
  /*---------------------------------------------------------------------------------*/
 /*	Popular Articles																*/
/*---------------------------------------------------------------------------------*/
class Nice_PopluarArticles extends WP_Widget {

   function __construct() {

	   	$this->alt_option_name = 'nice_popular_articles';

		parent::__construct( 'nice_popular_articles', __( '(NiceThemes) Popular Articles', 'nicethemes' ), array( 'description' => __( 'A widget that displays your most popular articles.', 'nicethemes' ), 'classname' => 'nice_popular_articles' ) );

		add_action( 'save_post', 	array( &$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );

   } // end __construct()

   function widget( $args, $instance ) {

   		$cache = wp_cache_get( 'widget_nice_popular_articles', 'widget');

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
		$number = $instance['number'];
		$orderby = $instance['orderby'];
		if ( isset( $instance['hideviews'] ) ) $hideviews = $instance['hideviews'];
		if ( isset( $instance['hidelikes'] ) ) $hidelikes = $instance['hidelikes'];

		if ( ! isset( $orderby ) || $orderby == 'views' ){
			$meta_key = '_pageview_count';
		}else{
			$meta_key = '_like_count';
		}


		if ( ! $number ) $number = 5;

		?>
			<?php echo $before_widget; ?>
			<?php if ( $title ) { echo $before_title . $title . $after_title; } ?>

			<ul class="clearfix">

					<?php
					$query = new WP_Query();
					$query->query( array (	'post_type' 		=> 'article',
											'posts_per_page' 	=> $number,
											'caller_get_posts' 	=> 1,
											'meta_key' 			=> $meta_key,
											'orderby'			=> 'meta_value_num',
											'order' 			=> 'DESC'

										)
									);
					?>
					<?php if ( $query->have_posts() ) : while ( $query->have_posts() ) : $query->the_post(); ?>
					<li class="clearfix <?php if ( has_post_format( 'video' )) { ?>format-video<?php } else { ?>format-standard<?php } ?>">
						<a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a>

							<?php if ( ( isset( $hidelikes) || isset( $hideviews ) ) || ( ! isset( $hidelikes) && ! isset( $hideviews ) ) ) : ?>
							<span class="meta">
								<?php if ( ! isset( $hideviews ) || ! $hideviews ) : ?>
								<span class="nice-views"><i class="fa fa-bullseye"></i> <?php echo get_post_meta( get_the_ID(), '_pageview_count', true ); ?> </span>
								<?php endif; ?>

								<?php if ( ! isset( $hidelikes ) || ! $hidelikes ) : ?>
								<span class="nice-likes"><i class="fa fa-heart"></i> <span class="like-count"><?php echo nicethemes_likes_count(); ?></span></span>
								<?php endif; ?>
							</span>
							<?php endif; ?>
					</li>
					<?php endwhile; endif; ?>

					<?php wp_reset_query(); ?>

				</ul>


			<?php echo $after_widget; ?>
		<?php

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_nice_popular_articles', $cache, 'widget' );

	}

   	function update( $new_instance, $old_instance ) {

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );


		return $new_instance;
   	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_nice_popular_articles', 'widget' );
	}

   	function form( $instance ) {

		$title = esc_attr( $instance['title'] );
		$number = esc_attr( $instance['number'] );
		$orderby = esc_attr( $instance['orderby'] );
		if ( isset( $instance['hideviews'] ) ) $hideviews = esc_attr( $instance['hideviews'] );
		else $hideviews = '';
		if ( isset( $instance['hidelikes'] ) ) $hidelikes = esc_attr( $instance['hidelikes'] );
		else $hidelikes = '';
		?>
		<p>
		   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:', 'nicethemes' ); ?></label>
		   <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>

		<p>
		   <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e( 'Number:', 'nicethemes' ); ?></label>
		   <input type="text" name="<?php echo $this->get_field_name('number'); ?>"  value="<?php echo $number; ?>" class="widefat" id="<?php echo $this->get_field_id('number'); ?>" />
		</p>

		<p>
			<label for="<?php echo $this->get_field_id('orderby'); ?>"><?php _e( 'Order By:', 'nicethemes' ); ?></label>
			<select name="<?php echo $this->get_field_name('orderby'); ?>" class="widefat" id="<?php echo $this->get_field_id('orderby'); ?>">
				<option value="views" <?php if ( isset( $type ) && $type == "views" ) { echo "selected='selected'"; } ?>><?php _e( 'Views', 'nicethemes' ); ?></option>
				<option value="likes" <?php if ( isset( $type ) && $type == "likes" ) { echo "selected='selected'"; } ?>><?php _e( 'Likes', 'nicethemes' ); ?></option>
			</select>
		</p>

		<p>
			<input id="<?php echo $this->get_field_id( 'hideviews' ); ?>" name="<?php echo $this->get_field_name( 'hideviews' ); ?>" type="checkbox" <?php checked( $hideviews, 'on' ); ?>><?php _e( 'Hide Views', 'nicethemes' ); ?></input>
		</p>

		<p>
		<input id="<?php echo $this->get_field_id( 'hidelikes' ); ?>" name="<?php echo $this->get_field_name( 'hidelikes' ); ?>" type="checkbox" <?php checked( $hidelikes, 'on' ); ?>><?php _e( 'Hide Likes', 'nicethemes' ); ?></input>
		</p>

		<?php
	}
} // end Nice_BlogAuthor classs

add_action( 'widgets_init', create_function( '', 'return register_widget("Nice_PopluarArticles");' ), 1 );
?>