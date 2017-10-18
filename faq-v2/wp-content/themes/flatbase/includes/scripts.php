<?php

if ( ! is_admin() ):

	// he's watching the theme, let's load js and css scripts :)
	add_action( 'wp_print_scripts', 'nice_scripts_js' );

endif;

/**
 * nice_scripts_css()
 *
 * register css styles and enqueue them
 *
 * @since 1.0
 *
 * @uses wp_register_style, wp_enqueue_style
 *
 */

if ( ! function_exists( 'nice_scripts_css' ) ) :

	function nice_scripts_css() {

		global $wp_styles;

		wp_register_style( 'nice-style', get_stylesheet_uri() );
		wp_register_style( 'nice-fancybox', get_template_directory_uri() . '/includes/css/jquery.fancybox.css' );
		wp_register_style( 'nice-fontawesome', get_template_directory_uri() . '/includes/css/font-awesome.min.css' );
		wp_register_style( 'nice-bbpress', get_template_directory_uri() . '/bbpress/bbpress.css' );

		wp_enqueue_style( 'nice-style' );
		wp_enqueue_style( 'nice-fancybox' );
		wp_enqueue_style( 'nice-fontawesome' );
		wp_enqueue_style( 'nice-bbpress' );

		wp_enqueue_style( 'nice-fontawesome-ie7', get_stylesheet_directory_uri() . '/includes/css/font-awesome-ie7.min.css', array( 'nice-style' )  );
		$wp_styles->add_data( 'nice-fontawesome-ie7', 'conditional', 'IE 7' );

	} // end nice_scripts_css

endif;

add_action( 'wp_enqueue_scripts', 'nice_scripts_css', 1 );

/**
 * nice_ie_head_scripts()
 *
 * Add IE conditionals to header (scripts)
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_ie_head_scripts' ) ) :

	function nice_ie_head_scripts() {
		global $is_IE;

		if ( $is_IE ) {
			echo '<!--[if lt IE 9]>' . "\n";
			echo '<script src="' . get_template_directory_uri() . '/includes/js/html5.js" type="text/javascript"></script>' . "\n";
			echo '<script src="' . get_template_directory_uri() . '/includes/js/respond-IE.js" type="text/javascript"></script>' . "\n";
			echo '<![endif]-->' . "\n";
		}
	}

endif;

add_action( 'wp_head', 'nice_ie_head_scripts' );

/**
 * nice_scripts_js()
 *
 * register js scripts and enqueue them
 *
 * @since 1.0
 *
 * @uses wp_register_script, wp_enqueue_script
 *
 */

if ( ! function_exists( 'nice_scripts_js' ) ) :

	function nice_scripts_js() {

		global $nice_options, $wp_scripts;

		wp_register_script( 'nice-contact-validation', 'http://ajax.microsoft.com/ajax/jquery.validate/1.9/jquery.validate.min.js', array('jquery') );
		wp_register_script( 'nice-fancybox-js', get_template_directory_uri() . '/includes/js/jquery.fancybox.js', array('jquery') );
		wp_register_script( 'nice-general', get_template_directory_uri() . '/includes/js/general.js', array( 'jquery' ) );
		wp_register_script( 'nice-superfish', get_template_directory_uri() . '/includes/js/superfish.js', array( 'jquery' ) );
		wp_register_script( 'nice-imagesloaded', get_template_directory_uri() . '/includes/js/imagesloaded.min.js', array( 'jquery' ) );

		if ( get_bloginfo( 'version' ) < 3.5 )
			wp_register_script( 'jquery-masonry', get_template_directory_uri() . '/includes/js/jquery.masonry.min.js', array( 'jquery' ) );

		wp_enqueue_script( 'nice-general' );
		wp_enqueue_script( 'nice-fancybox-js' );
		wp_enqueue_script( 'nice-superfish' );
		wp_enqueue_script( 'jquery-masonry' );
		wp_enqueue_script( 'nice-imagesloaded' );

		// Localize scripts
		wp_localize_script( 'nice-general', 'php_data', array( 'admin_ajax_url' => admin_url() . 'admin-ajax.php', 'play_nice_nonce' => wp_create_nonce( 'play-nice' ) ) );

		$load_contact_js = false;

		$load_contact_js = apply_filters( 'nice_load_contact_js', $load_contact_js );

		if ( $load_contact_js ) {
			wp_enqueue_script( 'nice-contact-validation' );
			add_action( 'wp_head', 'nice_contact_js', 10 );
		}

		$load_more_posts_loader_js = false;

		$load_more_posts_loader_js = apply_filters( 'nice_load_more_posts_loader_js', $load_more_posts_loader_js );
		if ( $load_more_posts_loader_js ) {
			add_action( 'wp_footer', 'nice_more_posts_loader_js', 10 );
		}

		add_action( 'wp_head', 'nicethemes_likes_js', 10 );

		wp_register_script( 'nice-scrollto-js', get_template_directory_uri() . '/includes/js/jquery.scrollTo-min.js', array( 'jquery', 'jquery-ui-core' ) );
		wp_enqueue_script('nice-scrollto-js');
		wp_register_script( 'nice-localscroll-js', get_template_directory_uri() . '/includes/js/jquery.localscroll-min.js', array( 'jquery', 'jquery-ui-core' ) );
		wp_enqueue_script('nice-localscroll-js');

		wp_register_script( 'nice-livesearch-js', get_template_directory_uri() . '/includes/js/jquery.livesearch.js', array( 'jquery' ) );
		wp_enqueue_script( 'nice-livesearch-js');
		add_action( 'wp_head', 'nice_livesearch_js', 10 );

		do_action( 'nice_scripts_js' );

	} // end nice_scripts_js

endif;

/**
 * nice_livesearch_js()
 *
 * initialize the LiveSearch JavaScript
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_livesearch_js' ) ):

	function nice_livesearch_js() {
		// being here we can set admin-ajax.php for WP multisite
		?>

		<script type="text/javascript">
		//<![CDATA[
			jQuery(document).ready(function() {
				jQuery('#live-search #s').liveSearch({url: '<?php echo home_url(); ?>/?ajax=true&livesearch=true&s='});
			});
		//]]>
		</script>

		<?php

	}

endif;

/**
 * nice_contact_js()
 *
 * print js for contact form
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_contact_js' ) ):

	function nice_contact_js()
	{
		// being here we can set admin-ajax.php for WP multisite
		?>
		<script type="text/javascript">
		//<![CDATA[
		jQuery(document).ready(function($) {

			jQuery('#nice_contact').validate({

				messages: {
						"name":{	"required":		"<?php echo esc_js( 'Please enter your name.', 'nicethemes' ); ?>"},
						"mail":{	"required":		"<?php echo esc_js( 'Please enter your email address.', 'nicethemes' ); ?>"},
						"message":{	"required":		"<?php echo esc_js( 'Please enter a message.', 'nicethemes' ); ?>"}
				},

				submitHandler: function(form) {

					var str = jQuery('#nice_contact').serialize();

					jQuery.ajax({
						type: "POST",
						url: "<?php echo admin_url();?>admin-ajax.php",
						data: 'action=nice_contact_form&nonce=<?php echo wp_create_nonce("play-nice");?>&' + str,
						success: function(msg) {
							jQuery("#node").ajaxComplete(function(event, request, settings){
									if ( msg == 'sent' ) {
										jQuery(".nice-contact-form #node").hide();
										jQuery(".nice-contact-form #success").fadeIn("slow");
										jQuery("#nice_contact input[type=text], #nice_contact textarea").val("");
									} else {
										result = msg;
										jQuery(".nice-contact-form #node").html(result);
										jQuery(".nice-contact-form #node").fadeIn("slow");
									}
							});
						}
					});
					return false;
					form.submit();
				}
			});
		});
		//]]>
		</script>
		<?php

	}
endif;


/**
 * nicethemes_likes_js()
 *
 * print js for likes form
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nicethemes_likes_js' ) ) :

	function nicethemes_likes_js() {
?>

		<script type="text/javascript">
			/* <![CDATA[ */
			jQuery(document).ready(function($) {

				nicethemes_likes_handler();

			});
			/* ]]> */
		</script>

<?php

	}

endif;


/**
 * nice_more_posts_loader_js()
 *
 * print js for masonry blog more posts loader
 *
 * @since 1.0.0
 *
 */
if ( ! function_exists( 'nice_more_posts_loader_js' ) ) :

	function nice_more_posts_loader_js() {

	global $nice_options;
?>

	<script type="text/javascript">
		/* <![CDATA[ */

		jQuery(document).ready(function($) {

			/* masonry script */
			var $masonry_container = jQuery('#masonry-grid');
			var $masonry_item_width = jQuery('.blog-masonry .grid .masonry-item').outerWidth();
			var $masonry_item_margin = jQuery('.blog-masonry .grid .masonry-item').css('marginRight');

			// initialize Masonry after all images have loaded
			$masonry_container.imagesLoaded( function() {
				$masonry_container.masonry( {
						columnWidth: $masonry_item_width,
						itemSelector: '.blog-masonry .grid .masonry-item',
						gutterWidth: parseInt( $masonry_item_margin ),
						isResizable: true
				});
			});

			jQuery(window).resize(function(){
				var $masonry_item_cur_width = jQuery('.blog-masonry .grid .masonry-item').outerWidth();
				$masonry_item_margin = jQuery('.blog-masonry .grid .masonry-item').css('marginRight');
					$masonry_container.masonry( 'option', {
						columnWidth: $masonry_item_cur_width,
						gutterWidth: parseInt( $masonry_item_margin )
					});
					jQuery($masonry_container).masonry('reload');
			});


				var page = 1;
				var loading = false;
				var $window = jQuery(window);
				var $content = jQuery("#masonry-grid");
				var load_posts = function(){
				jQuery.ajax({
						type: "POST",
						url: "<?php echo admin_url();?>admin-ajax.php",
						data: 'action=nice_more_posts_loader&nonce=<?php echo wp_create_nonce( 'play-nice' );?>&pageNumber=' + page,
							beforeSend : function(){
								if ( page != 1 ) {
									jQuery("#posts-ajax-loader-button").css('visibility', 'hidden');
									jQuery("#posts-ajax-loader").show();
								}
							},
							success	: function(data){
								$data = jQuery(data);
								if( ! $data.hasClass( 'no-more-posts' ) ){
									$data.hide();
									jQuery($masonry_container).append($data).imagesLoaded( function() {
										//jQuery($masonry_container).masonry('reloadItems');
										$masonry_container.masonry( 'appended', $data );

										var $masonry_item_cur_width = jQuery('.blog-masonry .grid .masonry-item').outerWidth();
										$masonry_item_margin = jQuery('.blog-masonry .grid .first').css('marginRight');
										$masonry_container.masonry( 'option', {
											columnWidth: $masonry_item_cur_width,
											gutterWidth: parseInt( $masonry_item_margin )
										});
										jQuery($masonry_container).masonry('reload');


										$data.fadeIn();
										jQuery("#posts-ajax-loader").hide();
										jQuery("#posts-ajax-loader-button").css('visibility', 'visible');
										loading = false;
									});

								} else {
									jQuery("#posts-ajax-loader").hide();
									jQuery("#posts-ajax-loader-button").hide();
									jQuery("#content").append(data);
								}
							},
							error	 : function(jqXHR, textStatus, errorThrown) {
								jQuery("#posts-ajax-loader").hide();
								jQuery("#posts-ajax-loader-button").css('visibility', 'visible');
								console.log(jqXHR + " :: " + textStatus + " :: " + errorThrown);
							}
					});
				}

				function load_masonry_blog_posts() {
					if ( ! loading ) {
						loading = true;
						page++;
						load_posts();
					}
				}

				<?php if ( ! isset( $nice_options['nice_masonry_posts_load_method'] ) || $nice_options['nice_masonry_posts_load_method'] == 'on_scroll'  ) { ?>
					$window.scroll(function() {
						var content_offset = $content.offset();
						if (  $window.scrollTop() + $window.height()  > ($content.scrollTop() + $content.height() + content_offset.top)) {
							load_masonry_blog_posts();
						}
					});
				<?php } else { ?>

					jQuery("#posts-ajax-loader-button").click(function() {
						load_masonry_blog_posts();
						return false;
					});

				<?php } ?>

			jQuery($masonry_container).masonry();

		});
		/* ]]> */
	</script>
<?php

	}

endif;

?>