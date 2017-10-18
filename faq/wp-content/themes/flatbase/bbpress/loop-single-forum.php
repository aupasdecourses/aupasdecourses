<?php

/**
 * Forums Loop - Single Forum
 *
 * @package bbPress
 * @subpackage Theme
 */

?>

<?php $nice_ul_class = ''; ?>

<?php if ( ! is_single() && get_the_content() == '' ) $nice_ul_class = 'no-content'; ?>

<ul id="bbp-forum-<?php bbp_forum_id(); ?>" <?php bbp_forum_class(  bbp_get_forum_id(), array( $nice_ul_class ) ); ?>>

	<li class="bbp-forum-info clearfix">

	<?php if ( ! is_single() ) : ?>

	<div class="bbp-forum-header">

		<?php do_action( 'bbp_theme_before_forum_title' ); ?>
		<h3><a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>"><?php bbp_forum_title(); ?></a></h3>

		<?php do_action( 'bbp_theme_after_forum_title' ); ?>

	</div>



	<?php if ( get_the_content() != '' ) : ?>

		<?php do_action( 'bbp_theme_before_forum_description' ); ?>

		<div class="bbp-forum-content"><?php the_content(); ?></div>

		<?php do_action( 'bbp_theme_after_forum_description' ); ?>


			<?php

			if ( ! bbp_get_forum_subforum_count( bbp_get_forum_id() ) ) :

				echo '<div class="topic-reply-counts">' . "\n";
				echo '<i class="fa fa-list-ul"></i>' . bbp_get_forum_topic_count( bbp_get_forum_id() ) . '<br />' . "\n";
				echo '<i class="fa fa fa-comments-o"></i>' . bbp_get_forum_reply_count( bbp_get_forum_id() ) . "\n";
				echo '</div>';

			endif;

			?>

	<?php endif; ?>

	<?php else : ?>

		<div class="bbp-forum-title-container">
			<a class="bbp-forum-title" href="<?php bbp_forum_permalink(); ?>" title="<?php bbp_forum_title(); ?>"><?php bbp_forum_title(); ?></a>
			<?php the_content(); ?>
		</div>

		<div class="bbp-forum-topic-count">

			<i class="fa fa-list-ul"></i><?php echo bbp_get_forum_topic_count( bbp_get_forum_id() ); ?><br />
			<i class="fa fa-comments-o"></i><?php echo bbp_get_forum_reply_count( bbp_get_forum_id() ); ?>
		</div>

	<?php endif; ?>

		<?php do_action( 'bbp_theme_before_forum_sub_forums' );

			nice_bbp_list_forums( array (
									'before'				=> '<ul class="bbp-forums-list">',
									'after'					=> '</ul>',
									'link_before'			=> '<li class="bbp-forum">',
									'link_after'			=> '</li>',
									'count_before'			=> '<div class="topic-reply-counts"><i class="fa fa-list-ul"></i>',
									'count_after'			=> '</div>',
									'count_sep'				=> '<br /><i class="fa fa-comments-o"></i>',
									'separator'				=> '<div style="clear:both;"></div>',
									'forum_id'				=> '',
									'show_topic_count'		=> true,
									'show_reply_count'		=> true,
									'show_freshness_link'	=> false
									));

		do_action( 'bbp_theme_after_forum_sub_forums' ); ?>

		<?php bbp_forum_row_actions(); ?>
	</li>

</ul><!-- #bbp-forum-<?php bbp_forum_id(); ?> -->