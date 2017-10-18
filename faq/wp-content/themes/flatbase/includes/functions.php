<?php
/**
 * Flatbase by NiceThemes.
 *
 * This file contains generic functions for this theme.
 *
 * @package   Flatbase
 * @author    NiceThemes <hello@nicethemes.com>
 * @license   GPL-2.0+
 * @link      http://nicethemes.com/theme/flatbase/
 * @copyright 2014-2015 NiceThemes
 * @since     1.0.0
 */
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}



if ( ! function_exists( 'nice_pagenavi' ) ) :
/**
 * nice_pagenavi()
 *
 * If wp_pagenavi exists, it shows it.
 * else, it shows the < previous | next > links.
 *
 * @since 1.0.0
 *
 */
function nice_pagenavi() {

	if ( function_exists( 'wp_pagenavi' ) ) {

		wp_pagenavi();

	} else { ?>

		<?php if ( get_next_posts_link() || get_previous_posts_link() ) { ?>

			<nav class="nav-entries">
				<div class="nav-prev fl"><?php next_posts_link( ''. __( 'Older posts', 'nicethemes' ) . '' ); ?></div>
				<div class="nav-next fr"><?php previous_posts_link( ''. __( 'Newer posts', 'nicethemes' ) . '' ); ?></div>
				<div class="fix"></div>
			</nav>

		<?php } ?>

	<?php }
}
endif;


if ( ! function_exists( 'nice_post_meta' ) ) :
/**
 * nice_post_meta()
 *
 * Post metadata, nicely displayed.
 *
 * @since 1.0.0
 *
 */
function nice_post_meta() { ?>
	<p class="post-meta">
		<span class="post-author"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
		<span class="post-date"><i class="fa fa-clock-o"></i> <?php the_time( get_option( 'date_format' ) ); ?></span>
		<span class="post-comments"><i class="fa fa-comments-o"></i> <?php comments_popup_link(__( 'No Comments', 'nicethemes' ), __( '1 Comment', 'nicethemes' ), __( '% Comments', 'nicethemes' ) ); ?></span>
		<?php edit_post_link( __( 'Edit', 'nicethemes' ), '<span class="small"><i class="fa fa-pencil"></i>', '</span>' ); ?>
	</p>
<?php
}
endif;



if ( ! function_exists( 'nice_post_meta_masonry' ) ) :
/**
 * nice_post_meta_masonry()
 *
 * Post metadata for the masonry template, nicely displayed.
 *
 * @since 1.0.0
 *
 */

function nice_post_meta_masonry() { ?>
<p class="post-meta">
	<span class="post-author"><i class="fa fa-user"></i> <?php the_author_posts_link(); ?></span>
	<span class="post-date"><i class="fa fa-clock-o"></i><?php the_time( 'M j, Y' ); ?></span>
	<span class="post-comments"><i class="fa fa-comments-o"></i> <?php comments_popup_link( '0', '1', '%' ); ?></span>
	<?php edit_post_link( __( 'Edit', 'nicethemes' ), '<span class="edit"><i class="fa fa-pencil"></i>', '</span>' ); ?>
</p>
<?php
}
endif;

if ( ! function_exists( 'nice_article_meta' ) ) :
/**
 * nice_article_meta()
 *
 * Articles metadata, nicely displayed.
 *
 * @since 1.0.0
 *
 */
function nice_article_meta() {

global $nice_options; ?>

	<div class="entry-meta">

		<?php if ( isset( $nice_options['nice_views'] ) && nice_bool( $nice_options['nice_views'] ) ) : ?>
		<span class="nice-views">
			<?php $pageviews = nice_pageviews_count(); ?>
			<i class="fa fa-bullseye"></i><?php printf( _n( '1 view', '%s views', $pageviews, 'nicethemes' ), $pageviews ); ?>
		</span>
		<?php endif; ?>

		<?php if ( isset( $nice_options['nice_reading_time'] ) && nice_bool( $nice_options['nice_reading_time'] ) ) : ?>
		<span class="nice-reading-time">
			<?php nicethemes_reading_time( array( 'before' => '<i class="fa fa-bookmark"></i>' ) ); ?>
		</span>
		<?php endif; ?>

		<?php if ( isset( $nice_options['nice_likes'] ) && nice_bool( $nice_options['nice_likes'] ) ) : ?>
		<a class="nice-like<?php if ( ! nicethemes_likes_can( get_the_ID() ) ) echo ' liked';  ?>" data-id="<?php the_ID(); ?>" href="#" title="<?php _e( 'Like this', 'nicethemes' ); ?>">
			<i class="fa fa-heart"></i>
			<span class="like-count">
				<?php echo nicethemes_likes_count(); ?>
			</span>
		</a>
		<?php endif; ?>

		<?php edit_post_link( __( 'Edit', 'nicethemes' ), '<span class="edit-link"><span class="edit"><i class="fa fa-pencil"></i>', '</span></span>' ); ?>

	</div>
<?php
}
endif;



if ( ! function_exists( 'nice_post_author' ) ) :
/**
 * nice_post_author()
 *
 * Post author info, nicely displayed.
 *
 * @since 1.0.0
 *
 */
function nice_post_author(){

	global $post, $nice_options;

	if ( isset( $nice_options["nice_post_author"] ) && $nice_options["nice_post_author"] == "true" ) { ?>

		<div id="post-author">
			<div class="profile-image thumb"><?php echo get_avatar( get_the_author_meta( 'ID' ), '70' ); ?></div>
				<div class="profile-content">
					<h4 class="title"><?php printf( esc_attr__( 'About %s', 'nicethemes' ), get_the_author() ); ?></h4>
					<?php the_author_meta( 'description' ); ?>
					<div class="profile-link">
						<a href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>">
							<?php printf( __( 'View all posts by %s <span class="meta-nav">&rarr;</span>', 'nicethemes' ), get_the_author() ); ?>
						</a>
					</div><!-- #profile-link	-->
			</div><!-- .post-entries -->
			<div class="fix"></div>
		</div><!-- #post-author -->
<?php
	}
}
endif;



if ( ! function_exists( 'nice_related_posts' ) ) :
/**
 * nice_related_posts()
 *
 * Echoes a list of the related posts/cpt by taxonomy
 *
 * @since 1.0.0
 *
 */
function nice_related_posts( $args = array() ) {

	$defaults = apply_filters( 'nice_related_posts_args', array(
							'post_type'			=> 'post',
							'title'				=> __( 'Related Posts', 'nicethemes' ),
							'taxonomy'			=> 'category',
							'before_title'		=> '<h3>',
							'after_title'		=> '</h3>',
							'before'			=> '<section id="related-posts" class="clearfix">',
							'after'				=> '</section>',
							'posts_per_page'	=> 5,
							'ignore_sticky'		=> 1,
							'icon_article'		=> '<i class="fa fa-file-o"></i>',
							'icon_video'		=> '<i class="fa fa-youtube-play"></i>',
							'echo'				=> true)
			);

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nice_related_posts_args', $args );

	do_action( 'nice_related_posts_before', $args );

	$output = '';

	$categories = get_the_terms( get_the_ID(), $args['taxonomy'] );

	if ( $categories ) {



		$category_ids = array();

		foreach( $categories as $individual_category ) $category_ids[] = $individual_category->term_id;

		$query_args = array(
					'post_type'		=> $args['post_type'],
					'tax_query'		=> array(
										array(
												'taxonomy' 	=> $args['taxonomy'] ,
												'field' 	=> 'id',
												'operator' 	=> 'IN',
												'terms' 	=> $category_ids
											  )
										),
					'post__not_in'			=> array( get_the_ID() ),
					'posts_per_page'		=> $args['posts_per_page'] , // Number of related posts that will be shown.
					'ignore_sticky_posts'	=> $args['ignore_sticky']
					);

		$nice_query = new wp_query( $query_args );

		if ( $nice_query->have_posts() ) {

			$output .= $args['before'];
			$output .= $args['before_title'] . $args['title'] . $args['after_title'];
			$output .= '<ul class="clearfix">' . "\n";

			while( $nice_query->have_posts() ) : $nice_query->the_post();

				if ( has_post_format( 'video' ) ) {
					$li_class = 'format-video';
					$nice_icon = $args['icon_video'];
				} else {
					$li_class = 'format-article';
					$nice_icon = $args['icon_article'];
				}

				$output .= '<li class="' . $li_class . '">';
				$output .= '<a href="' . get_permalink() . '" rel="bookmark" title="' . get_the_title() . '">';
				$output .=  $nice_icon . get_the_title();
				$output .= '</a>';
				$output .= '</li>';

			endwhile;

			$output .= '</ul>' . "\n";
			$output .= $args['after'];

		}
	}

	wp_reset_query();

	// Allow child themes/plugins to filter here.
	$output = apply_filters( 'nice_related_posts_html', $output, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nice_related_posts_after', $args );

}

endif;


if ( ! function_exists( 'nice_contact_ajax' ) ) :
/**
 * nice_contact_ajax()
 *
 * Handles the ajax call from the contact form
 *
 * @since 1.0.0
 *
 */
function nice_contact_ajax() {

	global $nice_options;

	check_ajax_referer( 'play-nice', 'nonce' );

	if ( ! empty( $_POST ) ) {

		$admin_email = get_option( 'nice_email' );

		if ( trim( $admin_email ) == '' )
			$admin_email = get_bloginfo( 'admin_email' );

		$name = 	$_POST['name'];
		$subject = 	$_POST['subject'];
		$mail = 	$_POST['mail'];
		$msg = 		$_POST['message'];

		$error = "";

		if ( ! $name ) {
			$error .= __( 'Please tell us your name','nicethemes' ) . "<br />";
		}
		if ( ! $mail ) {
			$error .= __( 'Please tell us your E-Mail address','nicethemes' ) . "<br />";
		}
		if( ! $msg ) {
			$error .= __( 'Please add a message','nicethemes' );
		}

		if( empty( $error ) ) {

			$mail_subject = '[' . get_bloginfo( 'name' ) . '] ' . __( 'New contact form received','nicethemes' );

			$body = __( 'Name: ', 'nicethemes' ) . "$name \n\n";
			if( !empty( $subject ) )
				$body .= __( 'Subject: ', 'nicethemes' ) ."$subject\n\n";

			$body .= __( 'Email: ', 'nicethemes') ."$mail \n\n" . __( 'Comments: ', 'nicethemes' )  ."$msg";

			$headers[] = __( 'From: ', 'nicethemes' ) . $name . ' <' . $mail . '>';
			$headers[] = __( 'Reply-To: ', 'nicethemes' ) . $mail ;
			$headers[] = "X-Mailer: PHP/" . phpversion();

			if ( $sent = wp_mail( $admin_email, $mail_subject, $body, $headers ) ) {
				_e( 'Thank you for leaving a message.', 'nicethemes' );
			} else {
				_e( 'There has been an error, please try again.', 'nicethemes' );
			}

		} else {
			echo $error;
		}
	}
	die();
}

endif;

	add_action( 'wp_ajax_nopriv_nice_contact_form', 'nice_contact_ajax' );
	add_action( 'wp_ajax_nice_contact_form', 'nice_contact_ajax' );



if ( ! function_exists( 'nicethemes_likes_ajax' ) ) :
/**
 * nicethemes_likes_ajax()
 *
 * Handles the ajax request for the like functionality
 *
 * @since 1.0.0
 *
 */

function nicethemes_likes_ajax() {

	check_ajax_referer( 'play-nice', 'nonce' );

	if ( ! empty( $_POST ) && ! empty( $_POST['id'] ) ) {

		if ( nicethemes_likes_can( $_POST['id'] ) ){

			$count_key = '_like_count';
			$count = nicethemes_likes_count( $_POST['id'] );
			if ( $count == '' ){
				delete_post_meta( $_POST['id'], $count_key );
				add_post_meta( $_POST['id'], $count_key, '1' );
				$count = 1;
			} else {
				$count++;
				update_post_meta( $_POST['id'], $count_key, $count );
			}


			$ip_list = get_post_meta( $_POST['id'], '_like_ip', true );

			$user_ip = nice_user_ip();

			if ( ( count( $ip_list ) != 0 ) && ( is_array( $ip_list ) ) ) {
				if ( ! in_array( $user_ip, $ip_list ) ) {
					$ip_list[] = $user_ip;
				}
				update_post_meta( $_POST['id'], '_like_ip', $ip_list );
			} else {
				$ip_list = array();
				$ip_list[] = $user_ip;
				update_post_meta( $_POST['id'], '_like_ip', $ip_list );
			}

			echo $count;

		}
	}

	die();

}

endif;

	add_action( 'wp_ajax_nopriv_nicethemes_likes_add', 'nicethemes_likes_ajax' );
	add_action( 'wp_ajax_nicethemes_likes_add', 'nicethemes_likes_ajax' );


if ( ! function_exists( 'nice_pageviews_count' ) ) :
/**
 * nice_pageviews_count()
 *
 * Handles the pageview count
 * returns the number of pageviews
 *
 * @since 1.0.0
 *
 */

function nice_pageviews_count() {

	$post_ID = get_the_ID();

	$count_key = '_pageview_count';
	$count = get_post_meta( $post_ID, $count_key, true );
	if ( $count == '' ) return 0;

	return $count;
}

endif;


if ( ! function_exists( 'nice_pageviews' ) ) :
/**
 * nice_pageviews()
 *
 * Handles the pageview count
 * returns the number of pageviews
 *
 * @since 1.0.0
 *
 */

function nice_pageviews() {

	$count = nice_pageviews_count();
	$count++;
	update_post_meta( get_the_ID(), '_pageview_count', $count );

	return $count;
}

endif;


if ( ! function_exists( 'nicethemes_likes_count' ) ) :
/**
 * nicethemes_likes_count()
 *
 * Returns the number of likes for a certain post/page/cpt
 *
 * @since 1.0.0
 *
 */
function nicethemes_likes_count( $id = 0 ){

	if ( ! $id ) $id = get_the_ID();
	$count_key = '_like_count';
	$likes = get_post_meta( $id, $count_key, true );
	if ( $likes == '' ) return 0;

	return $likes;
}

endif;


if ( ! function_exists( 'nicethemes_likes_can' ) ) :
/**
 * nicethemes_likes_can()
 *
 * Returns a boolean determining if the current IP
 * already liked the content or not
 *
 * @since 1.0.0
 *
 */
function nicethemes_likes_can( $id = 0 ) {

	if ( ! $id ) return false;

	$ip_list = get_post_meta( $id, '_like_ip', true );

	if ( ( $ip_list == '' ) || ( is_array( $ip_list ) && ! in_array( nice_user_ip(), $ip_list ) ) ){
		return true;
	}

	return false;

}

endif;


if ( ! function_exists( 'nicethemes_reading_time' ) ) :
/**
 * nicethemes_reading_time()
 *
 * Echoes the estimated time to read by the amount of
 * text of the post/page/cpt
 *
 * @since 1.0.0
 *
 */

function nicethemes_reading_time( $args = array() ) {

	$defaults = apply_filters( 'nicethemes_reading_time_default_args', array(
							'words_per_minute'	=> 300,
							'display_seconds'	=> true,
							'echo'				=> true,
							'before'			=> '',
							'after'				=> '')
			);

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nicethemes_reading_time_args', $args );

	do_action( 'nicethemes_reading_time_before', $args );

	$output = '';

	$output .= $args['before'];

	$content = get_the_content();
	$num_words = str_word_count( strip_tags( $content ) );

	$minutes = floor( $num_words / $args['words_per_minute'] );
	$seconds = floor( $num_words % $args['words_per_minute'] / ( $args['words_per_minute'] / 60 ) );
	$estimated_time = '';
	if ( ! $args['display_seconds'] ) {
		if( $seconds >= 30 ) {
			$minutes = $minutes + 1;
		}
		$estimated_time = $estimated_time.' '. sprintf( _n( '1 min read', '%s min read', $minutes, 'nicethemes' ), $minutes );
	} else {
		$estimated_time = $estimated_time . ' '. sprintf( _n( '1 min ', '%s min ', $minutes, 'nicethemes' ), $minutes ) . ', ' . sprintf( _n( '1 sec read', '%s sec read', $seconds, 'nicethemes' ), $seconds );
	}

	if ( $minutes < 1 ) {
		$estimated_time = __( 'Less than a minute', 'nicethemes' );
	}

	$output .= $estimated_time;

	$output .= $args['after'];

	// Allow child themes/plugins to filter here.
	$output = apply_filters( 'nicethemes_reading_time_html', $output, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nicethemes_reading_time_after', $args );

}

endif;


if ( ! function_exists( 'nice_opengraph_for_posts' ) ) :
/**
 * nice_opengraph_for_posts()
 *
 * Print the Facebook opengraph tags.
 *
 * @since 1.0.0
 *
 */

function nice_opengraph_for_posts() {

	if ( is_singular() && apply_filters( 'nice_opengraph_enable', true ) ) {
		global $post;
		setup_postdata( $post );
		$output  = '<meta property="og:type" content="article" />' . "\n";
		$output .= '<meta property="og:title" content="' . esc_attr( get_the_title() ) . '" />' . "\n";
		$output .= '<meta property="og:url" content="' . get_permalink() . '" />' . "\n";
		$output .= '<meta property="og:description" content="' . esc_attr( get_the_excerpt() ) . '" />' . "\n";
		if ( has_post_thumbnail() ) {
			$imgsrc = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
			$output .= '<meta property="og:image" content="' . $imgsrc[0] . '" />' . "\n";
		}
		echo $output;
	}

}

endif;

add_action( 'wp_head', 'nice_opengraph_for_posts' );

/**
 * nice_masonry_blog_ajax()
 *
 * Ajax function for the masonry blog
 *
 * @since 1.0.0
 *
 */

function nice_masonry_blog_ajax( $args = array() ) {

	check_ajax_referer( 'play-nice', 'nonce' );

		if ( ! empty( $_POST ) ) {

			$page = ( isset( $_POST['pageNumber'] ) ) ? $_POST['pageNumber'] : 0;

			$output = '';
			$columns = 3;
			$loop = 0;

			$query_args = array(
							'posts_per_page' => get_option('posts_per_page'),
							'paged'			 => $page
						);

			// The Query
			$query = new WP_Query( $query_args );

			// The Loop
			if ( $query->have_posts() ) {
					while ( $query->have_posts() ) {
							$query->the_post();
								$loop++;

					$class = '';
					if ( $loop % $columns == 0 )
						$class = 'last';
					if ( ( $loop - 1 ) % $columns == 0 )
						$class = 'first';
							$output .= '<div id="post-' . get_the_ID() . '" class="masonry-item isotope-item columns-' . $columns . ' ' . $class . '">';
							$output .= '<!-- BEGIN .post -->';
							$output .= '<article class="post clearfix">';
							if ( has_post_thumbnail() ) :
								$output .= '<figure class="featured-image view view-more">';
								$output .= '<a href="' . get_permalink() .'" >';
								$output .= nice_image( array( 'width' => 580, 'height' => 405, 'class' => 'wp-post-image', 'echo' => false ) );
								$output .= '</a>';
								$output .= '</figure>';
							endif;

							$output .= '<header>';
							$output .= '<h2 class="post-title">';
							$output .= '<a href="' . get_permalink() . '" >' . get_the_title() . '</a>';
							$output .= '</h2>';
							ob_start();
				nice_post_meta_masonry();
							$post_meta_masonry = ob_get_contents();
							ob_end_clean();
							$output .= $post_meta_masonry;
				$output .= '</header>';
							$output .= '<div class="entry">';
							$output .= '<div class="post-content">';
								$nice_excerpt = substr( get_the_excerpt(), 0, 400 ); //truncate excerpt according to $len
								if ( strlen( $nice_excerpt ) < strlen( get_the_excerpt() ) ) {
										$nice_excerpt = $nice_excerpt . "...";
								}
							$output .= '<p>' . $nice_excerpt . '</p>';
							$output .= '<a class="readmore" href="' . get_permalink() . '" >' . __( 'Read More', 'nicethemes' ) . '</a>';
							$output .= '</div>';
							$output .= '</div>';
							$output .= '<!-- END .post -->';
							$output .= '</article>';
							$output .= '</div>';
					}

			} else {
					$output .= '<div class="no-more-posts clearfix"><span>' . __( 'No More Posts Found.', 'nicethemes' ) . '</span></div>';
			}

			 echo $output;

			/* Restore original Post Data */
			wp_reset_postdata();

		}
	die();
}

	add_action( 'wp_ajax_nopriv_nice_more_posts_loader', 'nice_masonry_blog_ajax' );
	add_action( 'wp_ajax_nice_more_posts_loader', 'nice_masonry_blog_ajax');



if ( ! function_exists( 'nicethemes_knowledgebase' ) ) :
/**
 * nicethemes_knowledgebase()
 *
 * Create a list of articles, by category, within a grid.
 *
 * @since 1.0.0
 *
 */
function nicethemes_knowledgebase( $args = array() ) {

	global $post;

	$defaults = apply_filters( 'nicethemes_knowledgebase_default_args', array(
						'columns'       => 2,
						'numberposts'   => 5,
						'orderby'       => 'menu_order',
						'order'         => 'ASC',
						'echo'          => true,
						'title'         => '',
						'before'        => '',
						'after'         => '',
						'before_title'  => '<h3>',
						'after_title'   => '</h3>',
						'category'      => 0,
						'hide_empty'    => true,
						'exclude'       => '',
						'include'       => '',
						'icon_article'  => '<i class="fa fa-file-o"></i>',
						'icon_video'    => '<i class="fa fa-youtube-play"></i>')
						);

	$args = wp_parse_args( $args, $defaults );

	$cat_args = array(
					'taxonomy' 		=> 'article-category',
					'orderby'		=> 'menu_order',
					'order' 		=> 'ASC',
					'hierarchical' 	=> true,
					'parent' 		=> $args['category'],
					'hide_empty' 	=> $args['hide_empty'],
					'child_of' 		=> $args['category'],
					'exclude'		=> $args['exclude'],
					'include'		=> $args['include']
				);

	$categories = get_categories( $cat_args );
	$loop = 0;

	$output = '';

	$output .= $args['before'];

	if ( $categories ) :
	$output .= '<div class="nice-knowledgebase grid clearfix">';

	// foreach categories
	foreach ( $categories as $category ) :

		$loop++;

		$class = '';

		// open the row &  set the column class if it's the first or the last one :)
		if ( ( $loop - 1 ) % $args['columns'] == 0 ) {
			$class = 'first';
			$output .= '<div class="row clearfix">';
		}
		elseif ( $loop % $args['columns'] == 0 ) {
			$class = 'last';
		}

		$output .= '<div class="columns-' . $args['columns'] . ' '. $class .'">';

		$output .= '<header>';
		$output .= $args['before_title'];
		if ( apply_filters( 'nicethemes_knowledgebase_enable_category_link', true ) ) {
			$output .= '<a href="' .  get_term_link( intval( $category->term_id ), 'article-category' ) . '"  ' . '>';
		}
		$output .= $category->name;
		if ( apply_filters( 'nicethemes_knowledgebase_enable_category_link', true ) ) {
			$output .= '</a>';
		}
		$output .= '<span class="cat-count">(' . $category->count . ')</span>';
		$output .= $args['after_title'];
		$output .= '</header>' . "\n\n";

		// Sub category
		$sub_category = get_category( $category );

		$subcat_args = array(
								'orderby' 	=> 'menu_order',
								'order' 	=> 'ASC',
								'child_of'	=> $sub_category->cat_ID,
								'parent'	=> $sub_category->cat_ID
		);

		$sub_categories = get_categories( $subcat_args );

		foreach ( $sub_categories as $sub_category ) {
			$output .= '<ul class="sub-categories">' . "\n";
			$output .= '<li>' . "\n";
			$output .= '<header>' . "\n";
			$output .= '<h4><a href="' . get_category_link( $sub_category->term_id ) . '" title="' . sprintf( esc_attr__( 'View all articles in %s', 'nicethemes' ), $sub_category->name ) . '" >' . esc_html( $sub_category->name ) . '</a></h4></header>' . "\n\n";
			$output .= '</li>' . "\n";
			$output .= '</ul>' . "\n\n";
		}

		$cat_post_num = $args['numberposts'];

		$sub_category_num = count( $sub_categories );

		if ( $sub_category_num != 0 ) {
			$cat_post_num_smart = $cat_post_num - $sub_category_num;
		} else {
			$cat_post_num_smart = $cat_post_num;
		}


		$cat_post_args = array(
								'numberposts'  => $cat_post_num_smart,
								'post_type'    => 'article',
								'orderby'      => $args['orderby'],
								'order'        => $args['order']
								);

		$cat_post_args['tax_query'] = array(
										array(
												'taxonomy'  => 'article-category',
												'field'     => 'id',
												'operator'  => 'IN',
												'terms'     => $category->term_id
												)
											);

		$cat_posts = get_posts( $cat_post_args );

		$output .= '<ul class="category-posts">';

		foreach ( $cat_posts as $post ) : setup_postdata( $post );

			$format = get_post_format();
			if ( $format === false ) { $article_icon = $args["icon_article"]; }
			elseif( $format == 'video') { $article_icon = $args["icon_video"]; }

			$output .= '<li>' . $article_icon . ' <a href="' . get_permalink() . '" title="' . sprintf( __( 'Permanent Link to %s', 'nicethemes' ), get_the_title() ) .'">' . get_the_title() . '</a></li>';

		endforeach;

		$output .= '</ul>';
		$output .= '</div>'; // close column

		// close the row div
		if ( ( $loop  % $args['columns'] == 0 ) && ( $loop != 1 ) ) $output .= '</div>';

	endforeach; // end foreach

	if ( ( $loop  % $args['columns'] != 0 ) ) $output .= '</div>';

	$output .= '</div>';

	endif;

	$output .= $args['after'];

	wp_reset_postdata();

	$output = apply_filters( 'nicethemes_knowledgebase_html', $output, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nicethemes_knowledgebase_after', $args );

}

endif;


if ( ! function_exists( 'nice_attachments_from_gallery' ) ) :
/**
 * nice_attachments_from_gallery()
 *
 * Returns ids of attachments from gallery
 *
 * @since 1.0.0
 *
 */
function nice_attachments_from_gallery() {

	global $post;
	$attachment_ids = array();
	$pattern = get_shortcode_regex();
	$ids = array();

	if ( preg_match_all( '/' . $pattern . '/s', $post->post_content, $matches ) ) {   //finds the "gallery" shortcode and puts the image ids in an associative array at $matches[3]
		$count = count( $matches[3] );      //in case there is more than one gallery in the post.
		for ( $i = 0; $i < $count; $i++ ){
			$atts = shortcode_parse_atts( $matches[3][ $i ] );
			if ( isset( $atts['ids'] ) ){
			$attachment_ids = explode( ',', $atts['ids'] );
			$ids = array_merge( $ids, $attachment_ids );
			}
		}
	}

	if ( ! empty( $ids ) )
		$ids = array_flip( $ids );

return $ids;

}

endif;


if ( ! function_exists( 'nice_content_without_gallery' ) ) :
add_filter( 'the_content', 'nice_content_without_gallery' );
/**
 * nice_content_without_gallery()
 *
 * Removes gallery shortcodes from content and returns the content
 *
 * @since 1.0.0
 *
 */
function nice_content_without_gallery( $content ) {
	global $post;

	if ( is_page_template( 'template-gallery.php' ) || has_post_format( 'gallery', get_the_ID() ) )
		$content = preg_replace( '/\[gallery[^\]]*\]/', '',  $content );

	return $content;
}

endif;


if ( ! function_exists( 'add_query_vars_filter' ) ) :
/**
 * add_query_vars_filter()
 *
 * Add query vars for the livesearch functionality.
 * This way, pages and posts won't be included in the search results.
 *
 * @since 1.0.0
 *
 */

function add_query_vars_filter( $vars ) {

	global $wp_query;

	$vars[] = 'ajax';
	$vars[] = 'livesearch';

	return $vars;

}

endif;

add_filter( 'query_vars', 'add_query_vars_filter' );


if ( ! function_exists( 'nice_clean_live_search' ) ) :
/**
 * nice_clean_live_search()
 *
 * Exclude pages and posts from the livesearch functionality
 *
 * @since 1.0.0
 *
 */
function nice_clean_live_search( $query ) {

	if ( ! is_admin() && $query->is_main_query() ) {
		if ( $query->is_search ) {
			if ( get_query_var('ajax') == true ){
				$post_type = apply_filters( 'nice_live_search_post_type', array( 'article', 'faq' ) );
				$query->set('post_type', $post_type );
			}
		}
	}
}

endif;

add_action( 'pre_get_posts', 'nice_clean_live_search' );


if ( ! function_exists( 'nicethemes_infoboxes' ) ) :
/**
 * nicethemes_infoboxes()
 *
 * Create a list of articles, by category, within a grid.
 *
 * @since 1.0.0
 *
 */
function nicethemes_infoboxes( $args = array() ) {

	$defaults = apply_filters( 'nicethemes_infoboxes_default_args', array(
						'columns'		=> 3,
						'rows'			=> true,
						'numberposts'	=> 10,
						'orderby'		=> 'menu_order',
						'echo'			=> true,
						'order'			=> 'ASC',
						'height'		=> 480,
						'width'			=> 480,
						'before'		=> '',
						'after'			=> '',
						'before_title'	=> '',
						'after_title'	=> '')
		);


	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nicethemes_infoboxes_args', $args );

	do_action( 'nicethemes_infoboxes_before', $args );

	$output = '';

	$query = new WP_Query( array(
				'post_type'      => 'infobox',
				'orderby'        => $args['orderby'],
				//'posts_per_page' => $args['numberposts'],
				'order'          => $args['order']
			));

	if ( $query->have_posts() ) :

		$output .= $args['before'] . "\n";

		$tpl = '<div class="%%CLASS%%"><div class="thumb">%%IMAGE%%</div><div class="infobox-title">%%TITLE%%</div><div class="infobox-content">%%CONTENT%%</div>%%READMORE%%</div>';
		$tpl = apply_filters( 'nicethemes_infoboxes_item_template', $tpl, $args );

		$loop = 0;

		$output .= '<div class="nice-infoboxes grid">' . "\n";

		while ( $query->have_posts() ) : $query->the_post();

			$loop++;

			$template = $tpl;

			// get the custom fields
			$infobox_readmore = get_post_meta ( get_the_ID(), 'infobox_readmore', true );
			$infobox_readmore_anchor = get_post_meta ( get_the_ID(), 'infobox_readmore_text', true );
			$infobox_readmore_window = get_post_meta ( get_the_ID(), 'infobox_readmore_window', true );

			$infobox_url_target = '';
			if ( $infobox_readmore_window == true ) $infobox_url_target = 'target="_blank"';

			$class = 'item post-' . get_the_ID() . ' columns-' . esc_attr( intval( $args['columns'] ) );
			if ( $loop % $args['columns'] == 0 ) $class .= ' last';
			if ( ( $loop - 1 ) % $args['columns'] == 0 ){
				$class .= ' first';
				if ( $args['rows'] ) $output .= '<div class="row">' . "\n";
			}

			$template = str_replace( '%%CLASS%%', $class, $template );

			/* The Image */
			$image = '';

			if ( ( function_exists( 'has_post_thumbnail' ) ) && ( has_post_thumbnail() ) ) :

				$image_size = apply_filters( 'nicethemes_infoboxes_image_size', array( $args['width'], $args['height'] ) );

				if ( $infobox_readmore <> '' ) :
					$image .= '<a href="' . $infobox_readmore . ' " rel="bookmark"  ' . $infobox_url_target . '>';
				endif;

				if ( function_exists( 'nice_image' ) ){
					$image .= nice_image( array ( 'echo' => 'false', 'key' =>'infobox-image', 'width' => $args['width'], 'height' => $args['height'] ) );
				} else {
					$image .= get_the_post_thumbnail( get_the_ID() , $image_size );
				}

				if ( $infobox_readmore <> '' ) : $image .= '</a>' ; endif;

			endif;

			$template = str_replace( '%%IMAGE%%', $image, $template );


			/* Title */

			$title = $args['before_title'];

			if ( $infobox_readmore <> '' ) {
				$title .= '<a href="' . $infobox_readmore . '" rel="bookmark" ' . $infobox_url_target . '>' . get_the_title() . '</a>';
			} else {
				$title .= get_the_title();
			}

			$title .= $args['after_title'];

			$template = str_replace( '%%TITLE%%', $title, $template );


			/* Content */

			if ( '' != get_the_excerpt() ) {
				$content = get_the_excerpt();
			} else {
				$content = get_the_content();
			}

			$content = apply_filters( 'nicethemes_infoboxes_content', $content, $query->post );
			$template = str_replace( '%%CONTENT%%', $content, $template );

			/* Read more Link */

			$readmore = '';

			if ( $infobox_readmore <> '' ) :

				$readmore .= '<a href="' . $infobox_readmore . '">';

				if ( $infobox_readmore_anchor <> '' ) {
					$readmore .= $infobox_readmore_anchor;
				} 
				// else {
					// $readmore .= __( 'Cliquez ici', 'nicethemes' );
				// }

			$readmore .= '</a>';

			endif;

			$template = str_replace( '%%READMORE%%', $readmore, $template );

			$template = apply_filters( 'nicethemes_infoboxes_template', $template, $query->post );

			$output .= $template;
			if ( $loop % $args['columns'] == 0 ){
				if ( $args['rows'] ) $output .= '</div><!--close row-->' . "\n";
			}

		endwhile;

		// close grid div
		$output .= '</div><!--/.infoboxes .grid -->' . "\n";

		$output .= $args['after'] . "\n";

	endif;

	wp_reset_postdata();

	// Allow child themes/plugins to filter here.
	$output = apply_filters( 'nicethemes_infoboxes_html', $output, $query, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nicethemes_infoboxes_after', $args );

}

endif;




if ( ! function_exists( 'nice_home_videos' ) ) :
/**
 * nice_home_videos()
 *
 * Create a list of articles with a video post format
 *
 * @since 1.0.0
 *
 */

function nice_home_videos( $args = array() ) {

	$defaults = apply_filters( 'nice_home_videos_default_args', array(
						'columns'		=> 3,
						'numberposts'	=> 5,
						'orderby'		=> 'menu_order',
						'echo'			=> true,
						'title'			=> __( 'Video Library', 'nicethemes' ),
						'before_title'	=> '<h2>',
						'after_title'	=> '</h2>',
						'before'		=> '',
						'after'			=> ''
						)
						);

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nice_home_videos_args', $args );

	do_action( 'nice_home_videos_before', $args );

	$video_posts_args = array(
								'posts_per_page'	=> $args['numberposts'],
								'orderby'		=> $args['orderby'],
								'post_type'		=> 'article',
								'order'			=> 'ASC',
								'tax_query' => array(
													array(
														'taxonomy' => 'post_format',
														'field' => 'slug',
														'terms' => array( 'post-format-video' ),
														))
								);

	$query = new WP_Query( $video_posts_args );

	$v = 0;
	$output = '';

	if ( $query->have_posts() ) :

		$output .= $args['before'];

		while ( $query->have_posts() ) : $query->the_post();

			$v++;

			if ( $v == 1 ){
				$embed = get_post_meta( get_the_ID(), 'embed', true );
				if ( $embed <> '' ) {
					$output .= '<div id="" class="video-content entry">';
					$output .=  nice_embed( array ( 'id' => get_the_ID(), 'echo' => false, 'width' => 960, 'height' => 540 ) );
					$output .=  '</div>';
				}
			}

			if ( $v == 1 ) {

				$output .= '<div id="" class="video-list">';
				$output .= $args['before_title'] . $args['title'] . $args['after_title'];
				$output .= '<ul>';
			}

			$output .= '<li><i class="fa fa-youtube-play"></i> <a href="' . get_permalink() .'">' . get_the_title() . '</a></li>';

		endwhile;

		if ( $v > 0 ) $output .= '</ul></div>';

		$output .= $args['after'];

	endif;

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nice_home_videos_after', $args );

}

endif;


if ( ! function_exists( 'nicethemes_gallery' ) ) :
/**
 * nicethemes_gallery()
 *
 * Create a list of articles, by category, within a grid.
 *
 * @since 1.0.0
 *
 */

function nicethemes_gallery( $args = array() ) {

	global $post;

	$defaults = apply_filters( 'nicethemes_gallery_default_args', array(
						'ids'			=> null,
						'columns'		=> 3,
						'rows'			=> false,
						'numberposts'	=> -1,
						'orderby'		=> 'menu_order',
						'echo'			=> true,
						'order'			=> 'ASC',
						'width'			=> 480,
						'height'		=> 480,
						'before'		=> '',
						'after'			=> '')
		);


	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nicethemes_gallery_args', $args );

	do_action( 'nicethemes_gallery_before', $args );

	$output = '';

	if ( ! empty ( $args['ids'] ) ) {

		// we get the ids parameter containing the list of images
		// Set the list in an array
		$ids = array();
		$attachment_ids = explode( ',', $args['ids'] );
		$ids = array_merge( $ids, $attachment_ids );
		$attachments = array_flip( $ids );

	} else {

		// get the images from the media uploaded to the page/post/cpt
		$attachments = get_children( array(
											'post_parent'		=> get_the_ID(),
											'post_type'			=> 'attachment',
											'post_mime_type'	=> 'image',
											'order'				=> $args['order'],
											'numberposts'		=> $args['numberposts'],
											'orderby'			=> $args['orderby']
											)
					);

		if ( empty( $attachments ) ) {
			// if the gallery shortcode is used, we get the images from that
			$attachments = nice_attachments_from_gallery();
		}

	}

	if ( ! empty( $attachments ) && ( count( $attachments ) > 1 ) ) :

		// begin parsing the images, creating the gallery
		$output .= $args['before'] . "\n";

		// The template for each gallery item
		$tpl = '<div class="%%CLASS%%"><figure class="thumb">%%IMAGE%%</figure></div>';
		$tpl = apply_filters( 'nicethemes_gallery_item_template', $tpl, $args );

		$loop = 0;

		$output .= '<div class="nice-gallery grid">' . "\n";

		foreach ( $attachments as $att_id => $attachment ) : $loop++;

			$template = $tpl;

			$class = 'item columns-' . esc_attr( intval( $args['columns'] ) );
			if ( $loop % $args['columns'] == 0 ) {
				$class .= ' last';
			}

			if ( ( $loop - 1 ) % $args['columns'] == 0 ) {
				$class .= ' first';
				if ( $args['rows'] ) $output .= '<div class="row">' . "\n";
			}

			$template = str_replace( '%%CLASS%%', $class, $template );

			$image = '<a class="fancybox" rel="group" href="' . wp_get_attachment_url( $att_id ) . '" title="' . get_the_title( $att_id ) . '">';

			$image_size = apply_filters( 'nicethemes_gallery_image_size', array( $args['width'], $args['height'] ) );

			if ( function_exists( 'nice_image' ) ){
				$image .= nice_image( array ( 'width' => $args['width'], 'height' => $args['height'], 'id' =>  $att_id, 'echo' => false ) );
			} else {
				$image .= get_the_post_thumbnail( $att_id , $image_size );
			}

			$image .= '<div class="mask"></div></a>';

			$template = str_replace( '%%IMAGE%%', $image, $template );

			// $post ??
			$template = apply_filters( 'nicethemes_gallery_template', $template, $post );

			$output .= $template;

			if ( ( $loop % $args['columns'] == 0 ) && $args['rows'] ) {
				$output .= '</div>';
			}

		endforeach;

		if ( ( $loop  % $args['columns'] != 0 ) && $args['rows'] ) $output .= '</div>';

		$output .= '</div>';

		$output .= $args['after'] . "\n";

	else :

		$output .= __( 'There are no images for this gallery', 'nicethemes' );

	endif;

	$output = apply_filters( 'nicethemes_gallery_html', $output, $attachments, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nicethemes_gallery_after', $args );

}

endif;


if ( ! function_exists( 'nice_footer_widgets' ) ) :
/**
 * nice_footer_widgets()
 *
 * Echo the footer widgets.
 *
 * @since 1.0.0
 *
 */
function nice_footer_widgets( $args = array() ){

	global $nice_options;

	$defaults = apply_filters( 'nice_footer_widgets_default_args', array(
							'echo'			=> true,
							'columns'		=> '4',
							'before'		=> '<!-- BEGIN #footer-widget --><div id="footer-widgets" class="col-full">',
							'after'			=> '</div><!-- /#footer-widgets -->')
			);

	$args = wp_parse_args( $args, $defaults );

	$args = apply_filters( 'nice_footer_widgets_args', $args );

	do_action( 'nice_footer_widgets_before', $args );

	$output = '';

	$nice_footer_columns = ( ! empty( $nice_options['nice_footer_columns'] ) ) ? $nice_options['nice_footer_columns'] : $args['columns'];


	$class = ' columns-' . esc_attr( intval( $nice_footer_columns ) );

	if ( 	is_active_sidebar( 'footer-1' ) ||
			is_active_sidebar( 'footer-2' ) ||
			is_active_sidebar( 'footer-3' ) ||
			is_active_sidebar( 'footer-4' ) ) : ?>

		<?php echo $args['before']; ?>

			<div class="grid footer-grid">
				<div class="widget-section first <?php echo $class; ?>">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
				<div class="widget-section even <?php echo $class; ?>">
					<?php dynamic_sidebar( 'footer-2' ); ?>
				</div>
				<?php if ( $nice_footer_columns == '3' || $nice_footer_columns == '4' ) : ?>
				<?php if ( $nice_footer_columns == '3' ) $class .= ' last'; ?>
				<div class="widget-section odd <?php echo $class; ?>">
					<?php dynamic_sidebar( 'footer-3' ); ?>
				</div>
				<?php endif; ?>
				<?php if ( $nice_footer_columns == '4' ) : ?>
				<div class="widget-section odd <?php echo $class; ?> last">
					<?php dynamic_sidebar( 'footer-4' ); ?>
				</div>
				<?php endif; ?>
			</div>

		<?php echo $args['after']; ?>

		<?php endif;

	$output = apply_filters( 'nice_footer_widgets_html', $output, $args );

	if ( $args['echo'] == true ) echo $output;
	else return $output;

	do_action( 'nice_footer_widgets_after', $args );

}

endif;
