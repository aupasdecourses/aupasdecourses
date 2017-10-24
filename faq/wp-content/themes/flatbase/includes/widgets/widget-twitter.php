<?php
  /*---------------------------------------------------------------------------------*/
 /* Twitter widget 																	*/
/*---------------------------------------------------------------------------------*/
class Nice_Twitter extends WP_Widget {

	/**
	* How often the tweets are refreshed (in seconds). Defualt is five minutes.
	*
	* @since 1.0
	*/
	public static $refresh = 300;

	function __construct() {

		parent::__construct( false, __( '(NiceThemes) Twitter', 'nicethemes' ), array( 'description' => __( 'Add your Twitter feed with this widget.', 'nicethemes' ) ) );

	} // end __construct

	function widget( $args, $instance ) {

		extract( $args );

		/** Get the tweets */
		$tweets = $this->nice_get_tweets( $instance );

		/** error if there are no tweets */
		if ( ! $tweets ) {
			if ( current_user_can( 'edit_plugins' ) )
				echo '<p class="error">' . __( 'No tweets found. Please make sure your twitter widget settings are correct.', 'nicethemes' ) . '</p>';
			return;
		}

		echo $before_widget;

		$limit = $instance['limit'];
		if ( ! $limit ) {
			$limit = 1;
		}

		if ( $limit > 1 ) {
			$twclass = 'tweet-list';
		} else {
			$twclass = 'one-tweet';
		}

		$username = $instance['username'];
		$unique_id = $args['widget_id'];
		$title = $instance['title'];

		if ( $title ) {
			echo $before_title . $title . $after_title;
		} ?>

		<div class="back clearfix">

		<ul id="twitter_update_list_<?php echo $unique_id; ?>" class="<?php echo $twclass; ?>">

			<?php /** Print the tweets */
				foreach ( $tweets as $tweet ) {

						/** Set the date and time format */
						$datetime_format = apply_filters( 'nice_twitter_datetime_format', "l M j \- g:ia" );

						/** Get the date and time posted as a nice string */
						$posted_since = apply_filters( 'nice_twitter_posted_since', date_i18n( $datetime_format , strtotime( $tweet->created_at ) ) );

						/** Filter for linking dates to the tweet itself */
						$link_date = true; //apply_filters( 'nice_twitter_link_date_to_tweet', __return_false() );

						if ( $link_date )
							$posted_since = "<a href=\"https://twitter.com/{$tweet->user->screen_name}/status/{$tweet->id_str}\">{$posted_since}</a>";

						/** Print tweet */
						echo "<li>{$this->nice_format_tweet( $tweet->text )}<br /><small class=\"muted\">{$posted_since}</small></li>";

				}
			?>

		</ul>
		<p class="tw-follow"><?php _e( 'Follow', 'nicethemes' ); ?> <a href="http://twitter.com/<?php echo ( $username ); ?>" target="_blank">@<?php echo esc_html( $username ); ?></a> <?php _e( 'on Twitter', 'nicethemes' ); ?></p>
		</div>
		<?php
			echo $after_widget;
	}

	function update( $new_instance, $old_instance ) {

		/** Delete tweets from transient. */
		if ( is_multisite() ) {
			set_site_transient( 'nice_twitter_tweets', '' );
		} else {
			set_transient( 'nice_twitter_tweets', '' );
		}

		return $new_instance;
	}

	function form( $instance) {

		$title = esc_attr( $instance['title'] );
		$consumer_key = esc_attr( $instance['consumer_key'] );
		$consumer_secret = esc_attr( $instance['consumer_secret'] );
		$access_token = esc_attr( $instance['access_token'] );
		$access_token_secret = esc_attr( $instance['access_token_secret'] );
		$include_retweets = esc_attr( $instance['include_retweets'] );
		$exclude_replies = esc_attr( $instance['exclude_replies'] );
		$username = esc_attr( $instance['username'] );
		$limit = esc_attr( $instance['limit'] );
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title', 'nicethemes' ); ?>&nbsp;<small>(<?php _e( 'optional', 'nicethemes' ); ?>)</small></label>
			<input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_key' ); ?>"><?php _e( 'Consumer Key', 'nicethemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'consumer_key' ); ?>"  value="<?php echo $consumer_key; ?>" class="widefat" id="<?php echo $this->get_field_id( 'consumer_key' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'consumer_secret' ); ?>"><?php _e( 'Consumer Secret', 'nicethemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'consumer_secret' ); ?>"  value="<?php echo $consumer_secret; ?>" class="widefat" id="<?php echo $this->get_field_id( 'consumer_secret' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token' ); ?>"><?php _e( 'Access Token', 'nicethemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name( 'access_token' ); ?>"  value="<?php echo $access_token; ?>" class="widefat" id="<?php echo $this->get_field_id( 'access_token' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'access_token_secret' ); ?>"><?php _e('Access Token Secret', 'nicethemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('access_token_secret'); ?>"  value="<?php echo $access_token_secret; ?>" class="widefat" id="<?php echo $this->get_field_id( 'access_token_secret' ); ?>" />
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'include_retweets' ); ?>" name="<?php echo $this->get_field_name( 'include_retweets' ); ?>" type="checkbox" value="true" <?php checked( $include_retweets, 'true' ); ?> />
			<label for="<?php echo $this->get_field_id( 'include_retweets' ); ?>">&nbsp;<?php _e( 'Include Retweets', 'nicethemes' ); ?></label>
		</p>
		<p>
			<input id="<?php echo $this->get_field_id( 'exclude_replies' ); ?>" name="<?php echo $this->get_field_name( 'exclude_replies' ); ?>" type="checkbox" value="true" <?php checked( $exclude_replies, 'true' ); ?> />
			<label for="<?php echo $this->get_field_id( 'exclude_replies' ); ?>">&nbsp;<?php _e( 'Exclude Replies', 'nicethemes' ); ?></label>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'username' ); ?>"><?php _e( 'Username', 'nicethemes' ); ?>&nbsp;<small>(<?php _e( 'without @', 'nicethemes' );?>)</small></label>
			<input type="text" name="<?php echo $this->get_field_name( 'username' ); ?>"  value="<?php echo $username; ?>" class="widefat" id="<?php echo $this->get_field_id( 'username' ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e( 'Number of tweets to show:', 'nicethemes' ); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('limit'); ?>"  value="<?php echo $limit; ?>" class="" size="3" id="<?php echo $this->get_field_id('limit'); ?>" /><br />
			<small>(<?php _e( 'by default, 1', 'nicethemes' );?>)</small>
		</p>
		<?php
	}

	/**
	 * Gets the tweets
	 *
	 * @since 1.0
	 */
	public function nice_get_tweets( $instance ) {

		$include_retweets = ( isset( $instance['include_retweets'] ) ) ? true : false;
		$exclude_replies = ( isset( $instance['exclude_replies'] ) ) ? true : false;

		if ( ! isset( $instance['limit'] ) || ( $instance['limit'] < 1 ) ) $limit = 1;

		/** Merge arugments with defaults */
		$args = apply_filters( 'nice_twitter_args', array(
			'screen_name'		=> $instance['username'],
			'count'				=> $instance['limit'],
			'include_rts'		=> $include_retweets,
			'exclude_replies'	=> $exclude_replies
		) );

		/** Get tweets from transient. False if it has expired */
		if ( is_multisite() ){
			$tweets = get_site_transient( 'nice_twitter_tweets' );
		} else {
			$tweets = get_transient( 'nice_twitter_tweets' );
		}

		if ( $tweets === false || $tweets === '' ) {

			/** Require the twitter auth class */
			if ( ! class_exists( 'TwitterOAuth' ) )
				require_once 'widget-twitter/twitteroauth/twitteroauth.php';

			if ( ( isset( $instance['consumer_key'] ) ) &&
				 ( isset( $instance['consumer_key'] ) ) &&
				 ( isset( $instance['consumer_key'] ) ) &&
				 ( isset( $instance['consumer_key'] ) )
				) {
				/** Get Twitter connection */
				$twitterConnection = new TwitterOAuth(
					$instance['consumer_key'],
					$instance['consumer_secret'],
					$instance['access_token'],
					$instance['access_token_secret']
				);

				/** Get tweets */
				$tweets = $twitterConnection->get(
					'statuses/user_timeline',
					$args
				);
			}

			/** Bail if failed */
			if ( ! $tweets || isset( $tweets->errors ) )
				return false;

			/** Set tweets */
			if ( is_multisite() ) {
				set_site_transient( 'nice_twitter_tweets', $tweets, apply_filters( 'nice_twitter_refresh_timeout', self::$refresh ) );
			} else {
				set_transient( 'nice_twitter_tweets', $tweets, apply_filters( 'nice_twitter_refresh_timeout', self::$refresh ) );
			}
		}

		/** Return tweets */
		return $tweets;

	}

	/**
	 * Formats tweet text to add URLs and hashtags
	 *
	 * @since 1.0
	 */
	public function nice_format_tweet( $text ) {
		$text = preg_replace( "#(^|[\n ])([\w]+?://[\w]+[^ \"\n\r\t< ]*)#", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text );
		$text = preg_replace( "#(^|[\n ])((www|ftp)\.[^ \"\t\n\r< ]*)#", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text );
		$text = preg_replace( "/@(\w+)/", "<a href=\"http://www.twitter.com/\\1\" target=\"_blank\">@\\1</a>", $text );
		$text = preg_replace( "/#(\w+)/", "<a href=\"http://twitter.com/search?q=%23\\1&src=hash\" target=\"_blank\">#\\1</a>", $text );
		return $text;
	}
}

register_widget( 'Nice_Twitter' );
?>