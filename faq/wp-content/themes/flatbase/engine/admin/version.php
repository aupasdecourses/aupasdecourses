<?php
/**
 * Table of Contents (version.php)
 *
 *	- nice_framework_version_init()
 *	- nice_framework_get_latest_version()
 *	- niceupdates()
 *	- nice_framework_silent_update()
 *	- nice_version_check()
 *  - nice_version_notice()
 *  - nice_schedule_version_check()
 *
 */

/**
 * nice_framework_version_init()
 *
 * Init version. If the framework has been updated,
 * update nice_framework_option to current version
 * stored in $version.
 *
 * @since 1.0.0
 *
 */
function nice_framework_version_init(){

	$version = NICE_FRAMEWORK_VERSION;
	if ( get_option( 'nice_framework_version' ) != $version )	update_option( 'nice_framework_version', $version );
}

add_action( 'init', 'nice_framework_version_init', 10 );

/**
 * nice_framework_get_latest_version()
 *
 * Get remote framework version.
 *
 * @since 1.0.0
 *
 */
function nice_framework_get_latest_version(){

	require_once( ABSPATH . 'wp-admin' . '/includes/image.php' );
	require_once( ABSPATH . 'wp-admin' . '/includes/file.php' );
	require_once( ABSPATH . 'wp-admin' . '/includes/media.php' );

	if ( is_admin() ) {

		if ( isset ( $_REQUEST['page'] ) && ( strip_tags( trim( $_REQUEST['page'] ) ) == 'niceupdates' ) ){
			
			//NICE_UPDATES_URL
			$url = NICE_UPDATES_URL . '/framework/changelog.txt';

			$temp_file_addr = download_url( $url );

			if ( ! is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
				foreach ( $file_contents as $line_num => $line ) {
					$current_line =  $line;

					if ( $line_num > 1 ) {

						if ( preg_match( '/^[=]/', $line ) ) {

								$current_line = substr( $current_line , 0, strpos( $current_line, '(' ) ); // compatible with php4
								$current_line = preg_replace( '~[^0-9,.]~', '', $current_line );
								$output['version'] = $current_line;
								break;
						}
					}
				}
				unlink( $temp_file_addr );
				update_option( 'nice_framework_remote_version', $output['version'] );

			} else {
				$output['version'] = get_option( 'nice_framework_version' );
			}

			return $output;
		}
	}
}

/**
 * niceupdates()
 *
 * Updates panel. Check if there's an available update.
 * Show the form so the user can update his framework version.
 *
 * @since 1.0.0
 *
 */
function niceupdates()
{
	echo '<div id="nice-container">';

	echo '<h1>' . __( 'Nice Updates', 'nicethemes' ) . '</h1><br />';

	$current_version = get_option( 'nice_framework_version' );
	$latest_version = nice_framework_get_latest_version();
	$latest_version = $latest_version['version'];

	$method = get_filesystem_method();

	$to = ABSPATH . 'wp-content/themes/' . get_option( 'template' ) . '/engine/';

	if ( isset( $_POST['password'] ) ) {

			$cred = $_POST;
			$filesystem = WP_Filesystem( $cred );

	} elseif ( isset ( $_POST['nice_ftp_credentials'] ) ){

			$cred = unserialize( base64_decode( $_POST['nice_ftp_credentials'] ) );
			$filesystem = WP_Filesystem( $cred );

	} else {

			$filesystem = WP_Filesystem();

	}

	$url = admin_url( 'admin.php?page=niceupdates' );
	
	echo '<h2>' . __( 'Framework Updates', 'nicetehemes' ) .'</h2>';

	if ( $filesystem == false ) {

			request_filesystem_credentials( $url );

	}  else {

			// update needed
			if ( version_compare( $latest_version, $current_version ) ){

				?>
				<form id="niceupdates" method="post">
				<input type="hidden" name="nice_ftp_credentials" value="<?php echo esc_attr( base64_encode( serialize( $_POST ) ) ); ?>" />
				<input type="hidden" name="nice_action" value="update" />

				<div id="message" class="updated nice-update-notice" style="display:block !important;">
					<p><?php printf( __( '<strong>There\'s a new Framework version(%s) available</strong>, please update.', 'nicethemes' ), $latest_version ); ?></p>
				</div>
				
				<p><?php printf( __( 'You\'re currently running the NiceFramework version %s' , 'nicethemes' ), $current_version ); ?></p>

				<p><?php _e( 'By clicking the &quot;Update&quot; button, our latest NiceFramework version will be downloaded and extracted to your current theme\'s <code>/engine/</code> folder.', 'nicethemes' );?><br />
				
				<?php _e( 'Please make a backup copy of your theme files and update WordPress to its latest version before updating the Framework.', 'nicethemes' ); ?></p>
				<p><?php _e( 'Update your version now to ensure continued improvements and bug fixing.', 'nicethemes' ); ?></p>

				<input type="submit" value="<?php esc_attr_e( 'Update', 'nicethemes'); ?>  &rarr;" class="button button-highlighted"  />



				</form>
				<br />
				<em><?php _e( 'NOTE: Please make sure your theme folder is writable', 'nicethemes' ); ?></em>
				<?php

			} else { ?>
						<h3><?php _e( 'Yey, Nice! Your NiceThemes framework is up to date.', 'nicethemes' ); ?></h3>
						<p><strong><?php _e( 'Your Framework version for this site:', 'nicethemes' ); ?></strong> <?php echo $current_version; ?></p>
			<?php }

	}
	
	echo '<br /><br /><hr class="nice-sep" />';

	echo '<h2>' . __( 'Theme Updates', 'nicetehemes' ) .'</h2>';
	
	$my_theme = wp_get_theme();
	echo __( 'You are running the theme:', 'nicethemes' ) . ' ' . $my_theme->get( 'Name' ) . "<br />";
	echo __( 'Version:', 'nicethemes' ) . ' ' . $my_theme->get( 'Version' );
	
	$current_theme_version = $my_theme->get( 'Version' );
	$latest_theme_version = nice_theme_get_latest_version( array( 'theme_slug' => strtolower( $my_theme->get( 'Name' ) ) ) );
	
	if ( version_compare( $latest_theme_version, $current_theme_version ) ) {
		?>

		<div id="message" class="updated nice-update-notice" style="display:block !important;">
			<p><?php printf( __( '<strong>There\'s a new Theme version(%s) available</strong>, please update.', 'nicethemes' ), $latest_theme_version ); ?></p>
		</div>

		<img class="nice-theme-image alignleft" src="<?php echo get_template_directory_uri() . '/screenshot.png'; ?>" />
		 		<h3><?php _e( 'Update Download and Instructions', 'nicethemes' ); ?></h3>
				<p><strong>Important:</strong> make a backup of the <?php echo $theme_name; ?> theme inside your WordPress installation folder <code><?php echo str_replace( site_url(), '', get_template_directory_uri() ); ?></code> before attempting to update.</p>


				<p><strong>Instructions:</strong> To update the <?php echo $my_theme->get( 'Name' ); ?> theme, login to your <a href="http://nicethemes.com" target="_blank">NiceThemes.com</a> account, head over to your dashboard and re-download the theme as you did when you purchased it.</p>
				
				<p>Extract the ZIP's contents, find the extracted theme folder, and upload the new files using FTP to the <code><?php echo str_replace( site_url(), '', get_template_directory_uri() ); ?></code> folder. This will overwrite the old files and is why it's important to backup any changes you've made to the theme files beforehand.</p>
								
				<p><?php _e( 'If you didn\'t make any changes to the theme files, you are free to overwrite them with the new files without risk of losing theme settings, pages, posts, etc, and backwards compatibility is guaranteed.', 'nicethemes' ); ?></p>
				
				<p><?php _e( 'If you have made changes to the theme files, you will need to compare your changed files to the new files listed in the changelog below and merge them together.', 'nicethemes' ); ?></p>

				<p><strong>NOTE:</strong> If you've purchased the theme through ThemeForest, please send an email to support@nicethemes.com with your username and purchase ID.</p>

<?php
	} else {
		?>
		
			<h3><?php _e( 'Your theme is up to date.', 'nicethemes' ); ?></h3>
		
		<?php
	}

	echo '</div>';
}

/**
 * nice_framework_silent_update()
 *
 * grab the last framework update and unzip it.
 *
 * @since 1.0.0
 *
 */
function nice_framework_silent_update(){

	if ( isset( $_REQUEST['page'] ) ){

	if ( strtolower( strip_tags( trim( $_REQUEST['page'] ) ) ) == 'niceupdates' ){

		//Setup Filesystem
		$method = get_filesystem_method();

		if ( isset( $_POST['nice_ftp_credentials'] ) ){

			$cred = unserialize( base64_decode( $_POST['nice_ftp_credentials'] ) );
			$wpfs = WP_Filesystem( $cred );

		} else {

			$wpfs = WP_Filesystem();

		};

		if ( $wpfs == false){

				function nice_framework_update_filesystem_warning() {
					$method = get_filesystem_method();
					echo '<div id="filesystem-warning" class="updated fade"><p>Failed: Filesystem preventing downloads. ( ' . $method . ')</p></div>';
				}
				add_action( 'admin_notices', 'nice_framework_update_filesystem_warning' );
				return;
		}
		
		if ( isset ( $_REQUEST['nice_action'] ) ) {

		if ( strtolower( trim( strip_tags( $_REQUEST['nice_action'] ) ) ) == 'update' ){
		//NICE_UPDATES_URL
		$temp_file_addr = download_url( 'http://updates.nicethemes.com/framework/framework.zip' );

		if ( is_wp_error( $temp_file_addr) ) {

			$error = $temp_file_addr->get_error_code();

			if ( $error == 'http_no_url' ) {
			//The source file was not found or is invalid
				function nice_framework_update_missing_source_warning() {
					echo "<div id='source-warning' class='updated fade'><p>" . __( 'Failed: Invalid URL Provided', 'nicethemes' ) . "</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_missing_source_warning' );
			} else {
				function nice_framework_update_other_upload_warning() {
					echo "<div id='source-warning' class='updated fade'><p>Failed: Upload - $error</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_other_upload_warning' );

			}

			return;

		  }
		//Unzip it
		global $wp_filesystem;
		$to = $wp_filesystem->wp_content_dir() . '/themes/' . get_option( 'template' ) . '/engine/';

		$dounzip = unzip_file( $temp_file_addr, $to );

		unlink( $temp_file_addr ); // Delete Temp File

		if ( is_wp_error( $dounzip ) ) {

			//DEBUG
			$error = $dounzip->get_error_code();
			$data = $dounzip->get_error_data( $error);

			if ( $error == 'incompatible_archive' ) {
				//The source file was not found or is invalid
				function nice_framework_update_no_archive_warning() {
					echo "<div id='nice-no-archive-warning' class='updated fade'><p>Failed: Incompatible archive</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_no_archive_warning' );
			}
			if ( $error == 'empty_archive' ) {
				function nice_framework_update_empty_archive_warning() {
					echo "<div id='nice-empty-archive-warning' class='updated fade'><p>Failed: Empty Archive</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_empty_archive_warning' );
			}
			if ( $error == 'mkdir_failed' ) {
				function nice_framework_update_mkdir_warning() {
					echo "<div id='nice-mkdir-warning' class='updated fade'><p>Failed: mkdir Failure</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_mkdir_warning' );
			}
			if ( $error == 'copy_failed' ) {
				function nice_framework_update_copy_fail_warning() {
					echo "<div id='nice-copy-fail-warning' class='updated fade'><p>Failed: Copy Failed</p></div>";
				}
				add_action( 'admin_notices', 'nice_framework_update_copy_fail_warning' );
			}

			return;

		}

		function nice_framework_updated_success() {
			echo "<div id='framework-upgraded' class='updated fade'><p>New framework successfully downloaded, extracted and updated.</p></div>";
		}

		add_action( 'admin_notices', 'nice_framework_updated_success' );

		}
		}
	}
	}

}

add_action( 'admin_head','nice_framework_silent_update' );

/**
 * nice_version_check()
 *
 * Check the remote framework changelog
 * for updates.
 *
 * @since 1.0.0
 *
 * @return (str) remote_version
 *
 */
function nice_version_check( $args ){
	$current_version = get_option( 'nice_framework_version' );
	
	$url = NICE_UPDATES_URL . '/framework/changelog.txt';

	$temp_file_addr = download_url( $url );
	if ( ! is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
		foreach ( $file_contents as $line_num => $line ) {
			$current_line =  $line;

			if ( $line_num > 1 ) {	// Not the first or second... dodgy :P

				if ( preg_match( '/^[=]/', $line ) ) {

						$current_line = substr( $current_line , 0, strpos( $current_line, '(' ) ); // compatible with php4
						$current_line = preg_replace( '~[^0-9,.]~','', $current_line );
						$output['version'] = $current_line;
						break;
				}
			}
		}
		unlink( $temp_file_addr );
	}else{
		$output['version'] = get_option( 'nice_framework_version' );
	}

	$msg = 'New Framework version <strong>( ' . $output['version'] . ' )</strong> ready to be installed. <a href="' . admin_url( 'admin.php?page=niceupdates' ). '">Click here</a>';
	update_option( 'nice_framework_updates', $msg );
	update_option( 'nice_framework_remote_version', $output['version'] );
}

add_action( 'nice_cron_version_check', 'nice_version_check' );

/**
 * nice_version_notice()
 *
 * display a notice if the framework needs to be updated
 *
 * @since 1.0.0
 *
 */
function nice_version_notice(){
	// display a notice if the framework need an update

}
//add_action( 'admin_notices', 'nice_version_notice', 5 );

/**
 * nice_schedule_version_check()
 *
 * Schedule a framework version check. (weekly)
 *
 * @since 1.0.0
 *
 */
function nice_schedule_version_check() {

	if ( ! wp_next_scheduled( 'nice_cron_version_check' ) ) {

		$latest_version = nice_framework_get_latest_version();
		$latest_version = $latest_version['version'];

		//wp_schedule_event( time(), 'weekly', 'nice_cron_version_check', array( 'current'=> get_option( 'nice_framework_version' ), 'latest' => $latest_version ) );
	}

}

// uncomment for this check to start working
//add_action( 'init', 'nice_schedule_version_check' );

function nice_theme_get_latest_version( $args ) {

	$defaults = array( 'theme_slug' => '' );

	$args = wp_parse_args( $args, $defaults );

	extract( $args );

	if ( ! empty( $theme_slug ) ) {
	
		$url = NICE_UPDATES_URL . '/themes/' . $theme_slug . '/changelog.txt';
	
		$temp_file_addr = download_url( $url );
		
		if ( ! is_wp_error( $temp_file_addr ) && $file_contents = file( $temp_file_addr ) ) {
			
			foreach ( $file_contents as $line_num => $line ) {
				$current_line =  $line;
	
				if ( $line_num > 1 ) {	// Not the first or second... dodgy :P
	
					if ( preg_match( '/^[=]/', $line ) ) {
						
							//echo $line;
	
							// only with php > 5 //stristr( $current_line, '( ', true );
							$current_line = substr( $current_line , 0, strpos( $current_line, '(' ) ); // compatible with php4
							$current_line = preg_replace( '~[^0-9,.]~','', $current_line );
							$output['version'] = $current_line;
							break;
					}
				}
			}
			
			unlink( $temp_file_addr );
		
		} else {
			return false;
		}
	
	}

	return $output['version'];

}

?>