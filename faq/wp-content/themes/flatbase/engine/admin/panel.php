<?php
/**
 * Table of Contents (panel.php)
 *
 *	- nice_admin_menu()
 *	- nice_admin_head()
 *	- nice_admin()
 *	- nicethemes()
 *	- nice_ajax_save_options()
 *
 */

// Action for backend menu.
add_action( 'admin_menu', 'nice_admin_menu' );

/**
 * nice_admin_menu()
 *
 * Create admin menu for nicethemes
 *
 * @since 1.0.0
 *
 */

function nice_admin_menu(){

	add_object_page( __( 'Theme Options', 'nicethemes' ), 'Nice Themes', 'manage_options', 'nicethemes', 'nicethemes', nice_admin_menu_icon() );

	// Theme Options.
	$niceadmin = add_submenu_page( 'nicethemes', __( 'Theme Options', 'nicethemes' ), __( 'Theme Options', 'nicethemes' ), 'manage_options', 'nicethemes', 'nicethemes' );

	// Updates.
	$niceadmin = add_submenu_page( 'nicethemes', __( 'NiceThemes Updates', 'nicethemes' ), __( 'Updates','nicethemes' ), 'manage_options', 'niceupdates', 'niceupdates' );

	// Support.
	$niceadmin = add_submenu_page( 'nicethemes', __( 'Support Forums', 'nicethemes'), __( 'Support', 'nicethemes' ), 'manage_options', 'nicethemes-support', 'nicethemes_support_page');

	// More Themes. - temporary commented, as requested by Envato, te monopolic WordPress Marketplace.
	//$niceadmin = add_submenu_page( 'nicethemes', __( 'More Themes', 'nicethemes' ), __( 'More Themes', 'nicethemes' ), 'manage_options', 'nicethemes-themes', 'nicethemes_themes_page' );


	if ( is_admin_niceframework() ) {

		wp_enqueue_style( 'admin-style', NICE_TPL_DIR . '/engine/admin/admin-style.css' );
		wp_register_style( 'nice-datepicker', get_template_directory_uri() . '/engine/admin/css/datepicker.css' );
		wp_register_style( 'nice-ui-slider', get_template_directory_uri() . '/engine/admin/css/ui-slider.css' );
		wp_register_script( 'nice-typography-preview', get_template_directory_uri() . '/engine/admin/js/nice-typography-preview.js', array( 'jquery' ), '1.0.0', true );
		wp_register_script( 'nice-general', get_template_directory_uri() . '/engine/admin/js/general.js' );


		add_action( 'admin_head', 'nice_admin_head' );
		wp_enqueue_style( 'nice-datepicker' );
		wp_enqueue_style( 'nice-ui-slider' );
		wp_enqueue_script( 'jquery-ui-core' );
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_script( 'jquery-ui-slider' );
		wp_enqueue_script( 'nice-typography-preview' );
		wp_enqueue_script( 'nice-general' );

		//if ( isset( $_GET ) && ( isset( $_GET['activated'] ) && $_GET['activated'] == true ) ) $first_time = 'true';
		//else $first_time = 'false';

		// Had to set this because of Envato, the monopolic WordPress marketplace.
		$first_time = false;

		$data = array( 'activated' => $first_time );
		wp_localize_script( 'nice-general', 'php_data', $data );

	}

	if ( is_admin() ) {

		wp_register_style( 'nice-admin-menu', get_template_directory_uri() . '/engine/admin/css/admin-menu.css' );
		wp_enqueue_style( 'nice-admin-menu' );

		wp_register_style( 'nice-admin-font', get_template_directory_uri() . '/engine/admin/css/niceadmin-font.css' );
		wp_enqueue_style( 'nice-admin-font' );

	}
}


/**
 * nice_admin_head()
 *
 * include all the js and css for the admin section
 *
 */

function nice_admin_head() { ?>

<link rel="stylesheet" media="screen" type="text/css" href="<?php echo NICE_TPL_DIR; ?>/engine/admin/css/colorpicker.css" />
<script type="text/javascript" src="<?php echo NICE_TPL_DIR; ?>/engine/admin/js/colorpicker.js"></script>


<script type="text/javascript" language="javascript">
		//<![CDATA[
		jQuery(document).ready(function(){

			// nice help buttons
			jQuery('.nice-help-button').hover(
				function(){jQuery(this).parent().children().children( '.explain' ).fadeIn( 'slow' );return false;},
				function(){jQuery(this).parent().children().children( '.explain' ).fadeOut( 'slow' );}
			);

			if (jQuery('#nice-header').length == 0 ){}
			else{
				// sticky header & nav
				var topHeight = jQuery( '#nice-header' ).offset().top - jQuery( '#wpadminbar' ).height();

				jQuery(window).scroll( function() {
					if (jQuery(document).scrollTop() > topHeight) {
						jQuery( '#nice-header' ).css({position: 'fixed', marginTop: -topHeight});
						jQuery( '#nice-nav' ).css({position: 'fixed', marginTop: -topHeight});
					} else {
						jQuery( '#nice-header' ).css({position: 'absolute', marginTop: 0});
						jQuery( '#nice-nav' ).css({position: 'absolute', marginTop: 0});
					}

				});
			}

			//Color Picker
			<?php $nice_options = get_option( 'nice_template' );

			foreach ( $nice_options as $option ) {

				if ( $option['type'] == 'color' ) {

					$option_id = $option['id'];
					$color = get_option( $option_id );
					if ( $color == '' ) $color = $option['std'];
					?>
					jQuery( '#<?php echo $option_id; ?>_picker' ).children( 'div' ).css( 'backgroundColor', '<?php echo esc_js( $color ); ?>' );
					jQuery( '#<?php echo $option_id; ?>_picker' ).ColorPicker({
						color: '<?php echo $color; ?>',
						onChange: function (hsb, hex, rgb) {
							jQuery( '#<?php echo $option_id; ?>_picker' ).children( 'div' ).css( 'backgroundColor', '#' + hex );
							jQuery( '#<?php echo $option_id; ?>_picker' ).next( 'input' ).attr( 'value','#' + hex );

						},
						onShow: function (colpkr) {	jQuery(colpkr).fadeIn(600); return false;	},
						onHide: function (colpkr) {	jQuery(colpkr).fadeOut(600);return false;	}

					});
				<?php } elseif ( $option['type'] == 'typography' ) { ?>

					// typography

					<?php
						$option_id = $option['id'];
						$db = get_option( $option['id'] );
						$std = $option['std'];

						if ( ! is_array( $db ) || empty( $db ) ) {
							$std = $option['std'];
						}

						// if there's a standard color we call the color picker, else we avoid the code
						if ( isset( $std['color'] ) ) :

							$font_color = $std['color'];
							if ( $db['color'] != '' ) { $font_color = $db['color']; }

					?>
							 jQuery( '#<?php echo $option_id; ?>_color_picker' ).children( 'div' ).css( 'backgroundColor', '<?php echo esc_js( $font_color ); ?>' );
							 jQuery( '#<?php echo $option_id; ?>_color_picker' ).ColorPicker({
								color: '<?php echo esc_js( $font_color ); ?>',
								onChange: function (hsb, hex, rgb) {
									jQuery( '#<?php echo $option_id; ?>_color_picker' ).children( 'div' ).css( 'backgroundColor', '#' + hex );
									jQuery( '#<?php echo $option_id; ?>_color_picker' ).next( 'input' ).attr( 'value','#' + hex );

								},
								onShow: function (colpkr) {	jQuery(colpkr).fadeIn(600); return false;	},
								onHide: function (colpkr) {	jQuery(colpkr).fadeOut(600);return false;	}

							});
					<?php endif; ?>

				<?php } elseif ( $option['type'] == 'slider' ) { ?>

						<?php
						$option_id = $option['id'];
						$db = get_option( $option['id'] );
						$std = $option['std'];
						$std_value = $option['std']['value'];

						if ( ! is_array( $db ) || empty( $db ) ) {
							$std = $std_value;
						}

						if ( $db == '' ) { $value = $std_value; }
						else{ $value = $db; }

						$range	= isset( $option['std']['range'] ) ? $option['std']['range'] : NULL;
						$min	= isset( $option['std']['min'] ) ? $option['std']['min'] : NULL;
						$max	= isset( $option['std']['max'] ) ? $option['std']['max'] : NULL;
						$step	= isset( $option['std']['step'] ) ? $option['std']['step'] : NULL;
						$unit	= isset( $option['std']['unit'] ) ? $option['std']['unit'] : NULL;

						?>

						jQuery(function() {
							jQuery( '#<?php echo $option_id; ?>_slider' ).slider({
								<?php if ( isset( $range ) ) : ?>	range: '<?php echo esc_js( $range ); ?>',<?php endif; ?>
								<?php if ( isset( $value ) ) : ?>	value: <?php echo esc_js( $value ); ?>,<?php endif; ?>
								<?php if ( isset( $min ) ) : ?> 	min: <?php echo esc_js( $min ); ?>,<?php endif; ?>
								<?php if ( isset( $max ) ) : ?>		max: <?php echo esc_js( $max ); ?>,<?php endif; ?>
								<?php if ( isset( $step ) ) : ?>	step: <?php echo esc_js( $step ); ?>,<?php endif; ?>
								slide: function( event, ui ) {
									jQuery( '#<?php echo $option_id; ?>' ).val( ui.value + '<?php echo esc_js($unit); ?>' );
								}
							});

						jQuery( '#<?php echo $option_id; ?>' ).val( jQuery( '#<?php echo $option_id; ?>_slider' ).slider( "value" ) + '<?php echo esc_js($unit); ?>' );
						});
				<?php } ?>
			  <?php } ?>

			  // DATE Pickers
			  if ( jQuery( '.nice-date' ).length ) {
			 		jQuery( '.nice-date' ).each(function () {
			 			var buttonImageURL = jQuery( this ).parent().find( 'input[name=datepicker-image]' ).val();
			 			jQuery( this ).next( 'input[name=datepicker-image]' ).remove();

						jQuery( '#' + jQuery( this ).attr( 'id' ) ).datepicker( { showOn: 'button', buttonImage: buttonImageURL, buttonImageOnly: true, showAnim: 'slideDown' } );
					});
				}

			  jQuery( '#niceform' ).submit(function(){

					function newValues() {
					  var serializedValues = jQuery('#niceform').serialize();
					  return serializedValues;
					}
					jQuery(':checkbox, :radio').click(newValues);
					jQuery('select').change(newValues);
					jQuery( '.nice-icon-loading' ).fadeIn();
					var serializedReturn = newValues();

					var ajax_url = '<?php echo esc_js( admin_url( 'admin-ajax.php' ) ); ?>';

					var data = {
						<?php if( isset ( $_REQUEST['page'] ) && $_REQUEST['page'] == 'nicethemes' ){ ?>
						type: 'options',
						<?php } ?>

						action: 'nice_post_action',
						data: serializedReturn
					};

					jQuery.post(ajax_url, data, function(response) {
						var success = jQuery( '#nice-popup-save' );
						var loading = jQuery( '.nice-icon-loading' );
						loading.fadeOut();
						success.fadeIn();
						window.setTimeout(function(){
							success.fadeOut();
						}, 2500);
					});

					return false;

				});

		});
		//]]>
		</script>

		<?php //AJAX Upload ?>

		<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function(){

				jQuery( '.group' ).hide();
				jQuery( '.group:first' ).fadeIn();

				jQuery( '.group .collapsed' ).each(function(){
					jQuery(this).find( 'input:checked' ).parent().parent().parent().nextAll().each(
						function(){
							if (jQuery(this).hasClass( 'last'  ) ) {
								jQuery(this).removeClass( 'hidden' );
								return false;
							}
							jQuery(this).filter( '.hidden' ).removeClass( 'hidden' );
						});
					});

				jQuery( '.group .collapsed input:checkbox' ).click(unhideHidden);

				function unhideHidden(){
					if (jQuery(this).attr( 'checked'  ) ) {
						jQuery(this).parent().parent().parent().nextAll().removeClass( 'hidden' );
					}
					else {
						jQuery(this).parent().parent().parent().nextAll().each(
							function(){
								if (jQuery(this).filter( '.last' ).length) {
									jQuery(this).addClass( 'hidden' );
									return false;
								}
								jQuery(this).addClass( 'hidden' );
						});

					}
				}

				jQuery( '#nice-nav li:first' ).addClass( 'current' );
				jQuery( '#nice-nav li a' ).click(function(evt){

						jQuery( '#nice-nav li' ).removeClass( 'current' );
						jQuery(this).parent().addClass( 'current' );

						var clicked_group = jQuery(this).attr( 'href' );

						jQuery( '.group' ).hide();

						jQuery(clicked_group).fadeIn();
						evt.preventDefault();

					});

			// Update Message popup
			jQuery.fn.center = function () {
				this.animate({ 'top':( jQuery(window).height() - this.height() - 200 ) / 2 + jQuery(window).scrollTop() + 'px' }, 100 );
				this.css( 'left', 250 );
				return this;
			}


			jQuery( '#nice-popup-save' ).center();
			jQuery(window).scroll(function() {

				jQuery( '#nice-popup-save' ).center();

			});
		});
		//]]>
		</script>
<?php

}

/**
 * nice_admin()
 *
 */

function nice_admin(){
	// mmm donuts
}

/**
 * nicethemes()
 *
 * Create admin panel with options.
 *
 * @since 1.0.0
 *
 */
function nicethemes()
{

	$options = get_option( 'nice_template' );

	$interface = nice_formbuilder( $options );
	?>

	<div class="wrap" id="nice-container">

		<div id="nice-popup-save" class="nice-save-popup">
			<div class="nice-save-save"><?php _e( 'Changes saved successfully', 'nicethemes' ); ?></div>
		</div>

		<form action="" enctype="multipart/form-data" id="niceform">

		<?php
		// Add nonce for added security.
		if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'nice-options-update' ); }

		$nice_nonce = '';

		if ( function_exists( 'wp_create_nonce' ) ) { $nice_nonce = wp_create_nonce( 'nice-options-update' ); }

		if ( $nice_nonce != '' ) {

		?>
			<input type="hidden" name="_ajax_nonce" value="<?php echo esc_attr( $nice_nonce ); ?>" />
		<?php
		}
		?>

		<!-- BEGIN #header -->
		<div id="nice-header" class="clearfix">
		  <div class="logo">
		  </div>
		  <div class="icon-option">
		  	<input type="submit" value="<?php esc_attr_e( 'Save Changes', 'nicethemes' ); ?>" class="button button-highlighted" />
		  	<span class="nice-icon-loading"></span>
		  </div>
		  <div class="clear"></div>
		<!-- END #header -->
		</div>

		<!-- BEGIN #main -->
		<div id="main">

			<div id="nice-nav">
				<ul>
			  	<?php echo $interface->menu; ?>
				</ul>
		  	</div>

			<div id="nice-content">
				<?php echo $interface->content; ?>
			</div>

		<div style="clear:both;"></div>

		<!-- END #main -->
		</div>

		</form>

	</div>

	<div id="nice-modal-wrap" class="hidden">
	<div id="nice-modal-bg"></div>
	<div id="nice-modal">
	<div class="nice-modal-close" tabindex="0" title="Close"></div>
			<div id="nice-modal-content" class="thanks">

				<?php $my_theme = wp_get_theme(); ?>

				<h3 class="smile">Thank You</h3>
				<p>Thanks for choosing <strong><?php echo $my_theme->get( 'Name' ); ?></strong> by <a href="http://nicethemes.com" target="_blank">NiceThemes.com</a>. It really means the world to us.</p>
				<p>To get started, be sure to read your <a href="http://nicethemes.com/support/" target="_blank">theme documentation</a>. If you have any questions, please stop by our <a href="http://nicethemes.com/support/" target="_blank">support forums</a>.</p>

				<p>Sincerely, <a href="http://twitter.com/juanfraa" target="_blank">Juan</a></p>

				<br />
				<a href="https://twitter.com/nice_themes" class="twitter-follow-button" data-show-count="false" data-size="large">Follow @nice_themes</a>
<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
				<p class="follow">Follow us on Twitter to keep up to date with the latest releases and updates.</p>

			</div>
	</div>
	</div>
	<?php

}// end nicethemes()


add_action( 'wp_ajax_nice_post_action', 'nice_ajax_callback' );

/**
 * nice_ajax_save_options()
 *
 * Retrieve options and save them.
 *
 * @since 1.0.0
 *
 */
function nice_ajax_save_options()
{

	$nice_options = array();
	$data = array();
	$options = get_option( 'nice_template' );

	foreach( $options as $option ){

		if ( isset ( $option['id'] ) ) {
			$option_id = $option['id'];
			$option_type = $option['type'];

			if ( is_array( $option_type ) ) {

				foreach( $option_type as $inner_option ){

					$option_id = $inner_option['id'];
					if ( isset( $data[$option_id] ) )
						$data[$option_id] .= get_option( $option_id );
					else
						$data[$option_id] = get_option( $option_id );

				} // end foreach()

			} else {
				$data[$option_id] = get_option( $option_id );
			}
		}
	}

	$output = '<ul>';

	foreach ( $data as $name => $value ){

		if ( is_serialized( $value ) ) {

			$value = unserialize( $value );
			$nice_array_option = $value;
			$temp_options = '';
			foreach ( $value as $v ) {
				if ( isset ( $v ) ) $temp_options .= $v . ',';
			}
			$value = $temp_options;
			$nice_array[$name] = $nice_array_option;
		} else {
			$nice_array[$name] = $value;
		}
		$output .= '<li><strong>' . esc_html( $name ) . '</strong> - ' . esc_html( $value ) . '</li>';
	}
	$output .= '</ul>';
	$output = base64_encode( $output );

	update_option( 'nice_options', $nice_array );
}


/**
 * nice_ajax_callback()
 *
 * save data.
 *
 * @since 1.0.0
 *
 */

function nice_ajax_callback()
{
	global $wpdb; // this is how you get access to the database

	$save_type = $_POST['type'];

	//Uploads
	if( $save_type == 'upload' ){

		$clickedID = $_POST['data']; // Acts as the name
		$filename = $_FILES[ $clickedID ];
	   	$filename['name'] = preg_replace( '/[^a-zA-Z0-9._\-]/', '', $filename['name'] );

		$override['test_form'] = false;
		$override['action'] = 'wp_handle_upload';
		$uploaded_file = wp_handle_upload( $filename, $override );

				$upload_tracking[] = $clickedID;
				update_option( $clickedID , $uploaded_file['url'] );

		 if ( ! empty( $uploaded_file['error'] ) ) { echo 'Upload Error: ' . esc_html( $uploaded_file['error'] ); }
		 else { echo $uploaded_file['url']; } // Is the Response
	}
	elseif ( $save_type == 'image_reset' ){

			$id = esc_sql( $_POST['data'] ); // Acts as the name
			//echo $id;
			global $wpdb;
			$query = "DELETE FROM $wpdb->options WHERE option_name LIKE '$id'";
			$wpdb->query( $query );

	}
	elseif ( $save_type == 'options' OR $save_type == 'framework' ) {

		$data = $_POST['data'];

		parse_str( $data, $output );

		//Pull options
		$options = get_option( 'nice_template' );

		foreach( $options as $option_array ){

			if ( isset ( $option_array['id'] ) ){
				$id = $option_array['id'];
			} else { $id = NULL; }

			$old_value = get_option( $id );
			$new_value = '';

			if ( isset ( $output[$id] ) ){
				$new_value = $output[ $option_array['id'] ];
			}

			if ( isset ( $option_array['id'] ) ) { // Non - Headings...

					$type = $option_array['type'];


					if ( is_array( $type ) ){

						foreach( $type as $array ){

							if ( $array['type'] == 'text' ){
								$id = $array['id'];
								$std = $array['std'];
								$new_value = $output[$id];
								if ( $new_value == '' ){ $new_value = $std; }
								update_option( $id, stripslashes( $new_value ) );
								echo $new_value;
							}
						}

					} elseif ( $new_value == '' && $type == 'checkbox' ) { // Checkbox Save

						update_option( $id,'false' );

					} elseif ( $new_value == 'true' && $type == 'checkbox' ) { // Checkbox Save

						update_option( $id,'true' );

					} elseif ( $type == 'multicheck' ) { // Multi Check Save

						$option_options = $option_array['options'];

						foreach ( $option_options as $options_id => $options_value ) {

							$multicheck_id = $id . '_' . $options_id;

							if ( ! isset( $output[$multicheck_id] ) ){
								update_option( $multicheck_id, 'false' );
							} else {
								update_option( $multicheck_id, 'true' );
							}
						}

					} elseif ( $type == 'select_multiple' ) {

						update_option( $id, $new_value );

					} elseif ( $type == 'typography' ) {

						$typography_array = array();

						foreach ( array( 'size', 'family', 'style', 'color' ) as $v  ) {
							$value = '';
							$value = $output[ $option_array['id'] . '_' . $v ];
							if ( $v == 'family' ) {
								$typography_array[$v] = stripslashes( $value );
							} else {
								$typography_array[$v] = $value;
							}
						}

						update_option( $id, $typography_array );

					} elseif ( $type == 'slider' ) {

						update_option( $id, stripslashes( str_replace( $option_array['std']['unit'], '', $new_value ) ) );

					} elseif ( $type != 'upload_min' ) {

						update_option( $id, stripslashes( $new_value ) );

					}
				}
			}


		if( $save_type == 'options' OR $save_type == 'framework' ){
			/* Create, Encrypt and Update the Saved Settings */
			nice_ajax_save_options();

		}

  	die();

	}
}


/**
 * nicethemes_themes_page()
 *
 * The "More Themes" page handler.
 *
 * @since 1.0.2
 *
 * @print (html)
 */
function nicethemes_themes_page(){

	?>
	<div class="wrap">

		<div id="icon-themes" class="icon32"></div>

		<h2><?php _e( 'Themes by NiceThemes.com', 'nicethemes' ); ?></h2>

		<div id="nicethemes-themes">

			<ul>
				<?php
				if ( $rss_items = nicethemes_more_themes_rss() ) {

					foreach ( $rss_items as $item ){
						?>
						<li>
							<div class="theme">
								<p><?php echo html_entity_decode( $item->get_content() ); ?></p>
								<h3><a href="<?php echo nicethemes_theme_url( $item->get_title() ); ?>" target="_blank"><?php echo esc_html( $item->get_title() ); ?></a></h3>
								<p><a href="<?php echo nicethemes_theme_url( $item->get_title() ) ?>" class="button-primary" target="_blank"><?php _e( 'More Info', 'nicethemes' ); ?></a></p>
							</div>
						</li>
						<?php
					} // end foreach;

				} else {
					_e( '<p>Error: Error when fetching themes.</p>', 'nicethemes' );
				}
				?>
			</ul>

		</div>

	</div>
	<?php
}

/**
 * nicethemes_support_page()
 *
 * The "Support" page handler.
 *
 * @since 1.0.2
 *
 * @print (html)
 */
function nicethemes_support_page(){

	?><div class="nice-content">
		<div class="nice-frame">
			<div id="icon-tools" class="icon32"></div>
			<h2><?php _e( 'NiceThemes.com Support', 'nicethemes' ); ?></h2>
			<div id="nicethemes-support">
				<p>We have a variety of resources to help you get the most out of our themes.</p>
				<p><a href="http://nicethemes.com/support/" class="button-primary" target="_blank">Visit the Support Center &rarr;</a></p>

			</div>
		</div>
	</div>
	<?php
}

function nicethemes_support_page2(){

	?>
	<div class="wrap">
		<div id="icon-tools" class="icon32"></div>
		<h2><?php _e( 'NiceThemes.com Support', 'nicethemes' ); ?></h2>
		<div id="nicethemes-support">
			<p>We have a variety of resources to help you get the most out of our themes.</p>
			<p><a href="http://nicethemes.com/support/" class="button-primary" target="_blank">Visit the Support Center &rarr;</a></p>

		</div>

	</div>
	<?php
}

?>