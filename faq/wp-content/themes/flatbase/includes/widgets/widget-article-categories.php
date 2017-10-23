<?php
  /*---------------------------------------------------------------------------------*/
 /* Blog Author Info 																*/
/*---------------------------------------------------------------------------------*/
class Nice_ArticleCategories extends WP_Widget {

   function __construct() {

		$this->alt_option_name = 'nice_article_categories';

		parent::__construct( 'nice_article_categories', __( '(NiceThemes) Article Categories', 'nicethemes' ), array( 'description' => 'A widget that displays the article categories.', 'classname' => 'widget_nice_article_categories' ) );

		add_action( 'save_post', 	array(&$this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array(&$this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array(&$this, 'flush_widget_cache' ) );

	} // end __construct()

	function widget($args, $instance) {

		$cache = wp_cache_get( 'widget_nice_article_categories', 'widget');

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

		?>
			<?php echo $before_widget; ?>
			<?php if ($title) { echo $before_title . $title . $after_title; } ?>

			<?php $cat_args = array(
						'taxonomy' => 'article-category',
						'orderby' => 'name',
						'order' => 'ASC',
						'hierarchical' => true,
						'parent' => 0,
						'hide_empty' => true,
						'child_of' => 0
					);

					$categories = get_categories( $cat_args );
					$catCounter = 0;
					echo '<ul>';
					foreach( $categories as $category ) {
						echo '<li><div><span>'. $category->count . '</span><a href="' . get_term_link( $category->slug, 'article-category' ) . '" title="' . sprintf( __( 'View all posts in %s', 'nicethemes' ), $category->name ) . '" ' . '>' . $category->name.'</a> </div></li> ';
					}
					echo '</ul>';

					?>

			<?php echo $after_widget; ?>
		<?php

		$cache[$args['widget_id']] = ob_get_flush();
		wp_cache_set( 'widget_nice_article_categories', $cache, 'widget' );

	}

	function update($new_instance, $old_instance) {

		$this->flush_widget_cache();

		$alloptions = wp_cache_get( 'alloptions', 'options' );
		//if ( isset( $alloptions['nice_feedback'] ) )
		//	delete_option( 'nice_feedback' );

		return $new_instance;
	}

	function flush_widget_cache() {
		wp_cache_delete( 'widget_nice_article_categories', 'widget' );
	}

	function form( $instance ) {

		$title = esc_attr( $instance['title'] );
		$number = esc_attr( $instance['number'] );
		?>
		<p>
		   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e( 'Title:','nicethemes' ); ?></label>
		   <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>


		<?php
	}
} // end Nice_BlogAuthor classs

add_action( 'widgets_init', create_function( '', 'return register_widget("Nice_ArticleCategories");' ), 1 );
?>