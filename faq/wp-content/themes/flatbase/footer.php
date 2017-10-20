<?php
// File Security Check
if ( ! empty( $_SERVER['SCRIPT_FILENAME'] ) && basename( __FILE__ ) == basename( $_SERVER['SCRIPT_FILENAME'] ) ) {
	die ( 'You do not have sufficient permissions to access this page!' );
}

global $nice_options;

?>
	<?php if ( ! is_page_template( 'template-home.php' ) ) : ?>
		<!-- END #container -->
		</div>
	<?php endif; ?>

	<?php
	$nice_cta_text = get_option( 'nice_cta_text' );
	$nice_cta_url = get_option( 'nice_cta_url' );
	$nice_cta_url_text = get_option( 'nice_cta_url_text' );

	if ( $nice_cta_text != '' || $nice_cta_url_text != '' ) : ?>

		<!-- BEGIN #call-to-action .home-cta-block -->
		<section id="call-to-action" class="home-cta-block clearfix <?php if ( $nice_cta_url_text != '' ) echo 'has-cta-button'; ?>">

			<div class="col-full">
				<div class="cta-wrapper">
					<?php if ( $nice_cta_text != '' ) : ?>
						<div class="cta-text"><?php echo $nice_cta_text; ?></div>
					<?php endif; ?>

					<?php if ( $nice_cta_url_text != '' ) : ?>
						<span class="cta-button-wrapper">
							<a class="cta-button" href="<?php echo $nice_cta_url; ?>" title="<?php echo $nice_cta_url_text; ?>"><?php echo $nice_cta_url_text; ?></a>
						</span>
					<?php endif; ?>
				</div>
			</div>

		</section>

	<?php endif; ?>

	<!-- BEGIN #footer -->
	<footer id="footer">

		<?php nice_footer_widgets(); ?>

		<div id="extended-footer">
			<div class="col-full">

				<?php nice_copyright(); ?>

				<nav id="footer-navigation">
					<?php

						$defaults = array(
									'menu'				=> '',
									'container'			=> 'div',
									'container_class'	=> '',
									'container_id'		=> '',
									'menu_class'		=> 'nav fl sf-js-enabled clearfix',
									'menu_id'			=> 'footer-nav',
									'echo'				=> true,
									'fallback_cb'		=> '',
									'before'			=> '',
									'after'				=> '',
									'link_before'		=> '',
									'link_after'		=> '',
									'depth'				=> 0,
									'walker'			=> '',
									'theme_location'	=> 'footer-menu' );

						wp_nav_menu( $defaults );

					?>
				</nav>
			</div>

		</div>
		
		<!--Start of Tawk.to Script-->
		<script type="text/javascript">
		var Tawk_API=Tawk_API||{}, Tawk_LoadStart=new Date();
		(function(){
		var s1=document.createElement("script"),s0=document.getElementsByTagName("script")[0];
		s1.async=true;
		s1.src='https://embed.tawk.to/562df430443b264065486ed0/default';
		s1.charset='UTF-8';
		s1.setAttribute('crossorigin','*');
		s0.parentNode.insertBefore(s1,s0);
		})();
		</script>
		<!--End of Tawk.to Script-->
	<!-- END #footer -->
	</footer>

<!-- END #wrapper -->
</div>

<?php wp_footer(); ?>
</body>
</html>
