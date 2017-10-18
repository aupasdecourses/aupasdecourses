<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'Vous n\'avez pas la permission d\'accéder à cette page' );
}

global $nice_options;
?>
<!DOCTYPE html>
<!--[if IE 7]>	<html class="ie ie7" <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">	<![endif]-->
<!--[if IE 8]>	<html class="ie ie8" <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">	<![endif]-->
<!--[if IE 9]>	<html class="ie ie9" <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">	<![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html <?php language_attributes(); ?> prefix="og: http://ogp.me/ns#">
<!--<![endif]-->
<head>

	<meta charset="<?php bloginfo( 'charset' ); ?>" />
	<title><?php wp_title( '&laquo;', true, 'right' ); ?> <?php bloginfo( 'name' ); ?></title>

	<!-- Pingback -->
	<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />

	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />

	<?php wp_head(); ?>

</head>

<body <?php body_class(); ?>>

<!-- BEGIN #wrapper -->
<div id="wrapper">

	<!-- BEGIN #header -->
	<header id="header" class="clearfix">

		<!-- BEGIN #top -->
		
		<div id="top" class="col-full">

			<!-- BEGIN #logo -->
			<?php nice_logo( array( 'before'	=> '<div id="logo" class="fl">',
									'after'		=> '</div>'
						 ) ); ?>
			
			<!-- END #logo -->


			<!-- BEGIN #navigation -->
			<nav id="navigation">

			<?php $defaults = array(
								'menu'				=> '',
								'container'			=> 'div',
								'container_class'	=> '',
								'container_id'		=> '',
								'menu_class'		=> 'nav fl clearfix',
								'menu_id'			=> 'main-nav',
								'echo'				=> true,
								'fallback_cb'		=> '',
								'before'			=> '',
								'after'				=> '',
								'link_before'		=> '',
								'link_after'		=> '',
								'depth'				=> 0,
								'walker'			=> new Nice_Walker_Nav_Menu(),
								'theme_location'	=> 'navigation-menu' );
			?>

			<?php wp_nav_menu( $defaults ); ?>

			<!-- END #navigation -->
			</nav>

		<!-- END #top -->
		</div>

	<?php

	$nice_livesearch_enable = get_option( 'nice_livesearch_enable' );

	if (  nice_bool( $nice_livesearch_enable )   ) : ?>
	<!-- #live-search -->
	<section id="live-search" class="clearfix">
		<div class="container col-full">

			<?php

			$nice_welcome_message = get_option( 'nice_welcome_message' );
			$nice_welcome_message_extended = get_option( 'nice_welcome_message_extended' );

			if ( ( ( $nice_welcome_message != '' ) || ( $nice_welcome_message_extended != '' ) ) && is_front_page() ) : ?>

					<!-- BEGIN .welcome-message -->
					<section class="welcome-message clearfix">

							<div class="col-full">

									<?php if ( $nice_welcome_message != '' ) : ?>
										<header>
											<h2><?php echo stripslashes( htmlspecialchars_decode( nl2br( $nice_welcome_message ) ) ); ?></h2>
										</header>
									<?php endif ;?>

									<?php if ( $nice_welcome_message_extended != '' ) : ?>
										<p><?php echo stripslashes( htmlspecialchars_decode( nl2br( $nice_welcome_message_extended ) ) ); ?></p>
								<?php endif ;?>

							</div>

					<!-- END .welcome-message -->
					</section>

			<?php endif; ?>

			<div id="search-wrap">
				<form role="search" method="get" id="searchform" class="clearfix" action="<?php echo home_url( '/' ); ?>" autocomplete="off">
					<div class="input">
					<label for="s"><?php _e( 'Comment pouvons nous vous aider ?', 'nicethemes' ); ?></label>
					<input type="text" name="s" id="s" />
					<input type="submit" id="searchsubmit" value="&#xf002;" />
					</div>
				</form>
			</div>
		</div>
	</section>
	<!-- /#live-search -->

<?php endif; ?>

	<!-- END #header -->
	</header>

<?php if ( ! is_page_template( 'template-home.php' ) ) : ?>
<!-- BEGIN #container -->
<div id="container" class="clearfix">
<?php endif; ?>
