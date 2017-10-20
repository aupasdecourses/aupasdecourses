<?php
/**
 *	Table of Contents (backup.php)
 *
 *	- nice_options_backup_menu()
 *	- nice_options_backup_notices()
 *	- nice_options_backup_menu()
 *	- nice_options_backup_menu()
 *	- nice_options_backup_menu()
 *	- nice_options_backup_menu()
 *	- nice_options_backup_menu()
 *
 */

// If it's admin, load the menu + logic and notices.
if ( is_admin() ){
	add_action( 'admin_menu', 'nice_options_backup_menu', 12 );
	add_action( 'admin_notices', 'nice_options_backup_notices', 10 );
}

/**
 * nice_options_backup_menu()
 *
 * Add the submenu, load the page logic for export/import actions.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_menu(){

	$page = add_submenu_page( 'nicethemes', __( 'Import/Export', 'nicethemes'), __( 'Import/Export', 'nicethemes' ), 'manage_options', 'nice-options-backup', 'nice_options_backup_panel');

	add_action( 'load-' . $page, 'nice_options_backup_logic' );

}

/**
 * nice_options_backup_notices()
 *
 * Display the different notices according to results.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_notices(){

	if ( ! isset( $_GET['page'] ) || ( $_GET['page'] != 'nice-options-backup' ) ) { return; }

	echo '<div id="import-notice" class="updated"><p>' . sprintf( __( 'Please note that this backup manager backs up only your options and not your content. To backup your content, please use the %sWordPress Export Tool%s.', 'nicethemes' ), '<a href="' . admin_url( 'export.php' ) . '">', '</a>' ) . '</p></div><!--/#import-notice .message-->' . "\n";

	if ( isset( $_GET['error'] ) && $_GET['error'] == 'true' ) {
		echo '<div id="message" class="error"><p>' . __( 'There was a problem importing your options. Please Try again.', 'nicethemes' ) . '</p></div>';
	} else if ( isset( $_GET['error-export'] ) && $_GET['error-export'] == 'true' ) {
		echo '<div id="message" class="error"><p>' . __( 'There was a problem exporting your options. Please Try again.', 'nicethemes' ) . '</p></div>';
	} else if ( isset( $_GET['invalid'] ) && $_GET['invalid'] == 'true' ) {
		echo '<div id="message" class="error"><p>' . __( 'The import file you\'ve provided is invalid. Please try again.', 'nicethemes' ) . '</p></div>';
	} else if ( isset( $_GET['imported'] ) && $_GET['imported'] == 'true' ) {
		echo '<div id="message" class="updated"><p>' . sprintf( __( 'Options successfully imported. | Return to %sTheme Options%s', 'nicethemes' ), '<a href="' . admin_url( 'admin.php?page=nicethemes' ) . '">', '</a>' ) . '</p></div>';
	}

}

/**
 * nice_options_backup_screen_help()
 *
 * Add the Help Toggle (contextual help) for the Backup section.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_screen_help ( $contextual_help, $screen_id, $screen ) {

	if ( isset( $_GET['page'] ) || ( $_GET['page'] == 'nice-options-backup' ) ){

	$contextual_help =
		'<h3>' . __( 'Welcome to the NiceThemes Backup Manager.', 'nicethemes' ) . '</h3>' .
		'<p>' . __( 'Here are a few notes on using this screen.', 'nicethemes' ) . '</p>' .
		'<p>' . __( 'The backup manager allows you to backup or restore your "Theme Options" to or from a text file.', 'nicethemes' ) . '</p>' .
		'<p>' . __( 'To create a backup, simply hit the "Download Export File" button.', 'nicethemes' ) . '</p>' .
		'<p>' . __( 'To restore your options from a backup, browse your computer for the file (under the "Import Options" heading) and hit the "Upload File and Import" button. This will restore only the settings that have changed since the backup.', 'nicethemes' ) . '</p>' .

		'<p><strong>' . __( 'Please note that only valid backup files generated through the NiceThemes Backup Manager should be imported.', 'nicethemes' ) . '</strong></p>' .

		'<p><strong>' . __( 'Looking for assistance?', 'nicethemes' ) . '</strong></p>' .
		'<p>' . sprintf( __( 'Please post your query on the %sNiceThemes Support Forums%s where we will do our best to help you.', 'nicethemes' ), '<a href="http://nicethemes.com/support" target="_blank">', '</a>' ) . '</p>';

	} // End IF Statement

	return $contextual_help;

}


/**
 * nice_options_backup_logic()
 *
 * Given the params, call the import or export actions.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_logic(){

	if ( ! isset( $_POST['nice-options-import'] ) && isset( $_POST['nice-options-export'] ) && ( $_POST['nice-options-export'] == true ) ) {
		nice_options_backup_export();
	}

	if ( ! isset( $_POST['nice-options-export'] ) && isset( $_POST['nice-options-import'] ) && ( $_POST['nice-options-import'] == true ) ) {
		nice_options_backup_import();
	}

	add_action( 'contextual_help', 'nice_options_backup_screen_help', 10, 3 );

}


/**
 * nice_options_backup_panel()
 *
 * The panel :)
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_panel(){

	if ( isset( $_POST['export-type'] ) ) {
		$export_type = esc_attr( $_POST['export-type'] );
	}
?>
	<div class="wrap">
		<?php screen_icon( 'tools' ); ?>
		<h2><?php _e( 'Backup Options', 'nicethemes' ); ?></h2>


		<h3><?php _e( 'Import Options', 'nicethemes' ); ?></h3>

		<p><?php _e( 'If you have options in a backup file on your computer, this form can import those into this site. To get started, upload your backup file to import from below.', 'nicethemes' ); ?></p>

		<div class="form-wrap">
			<form enctype="multipart/form-data" method="post" action="<?php echo admin_url( 'admin.php?page=nice-options-backup' ); ?>">
				<?php wp_nonce_field( 'nice-options-import' ); ?>
				<label for="nice-options-import-file"><?php printf( __( 'Upload File: (Maximum Size: %s)', 'nicethemes' ), ini_get( 'post_max_size' ) ); ?></label>
				<input type="file" id="nice-options-import-file" name="nice-options-import-file" size="25" />
				<input type="hidden" name="nice-options-import" value="1" />
				<input type="submit" class="button" value="<?php _e( 'Upload File and Import', 'nicethemes' ); ?>" />
			</form>
		</div><!--/.form-wrap-->

		<h3><?php _e( 'Export Options', 'nicethemes' ); ?></h3>

		<p><?php _e( 'When you click the button below, a text file (.json) will be created for you to save to your computer.', 'nicethemes' ); ?></p>
		<p><?php echo sprintf( __( 'This text file can be used to restore your options here on "%s", or to easily setup another website with the same options".', 'nicethemes' ), get_bloginfo( 'name' ) ); ?></p>

		<form method="post" action="<?php echo admin_url( 'admin.php?page=nice-options-backup' ); ?>">
			<?php wp_nonce_field( 'nice-options-export' ); ?>
			<input type="hidden" name="nice-options-export" value="1" />
			<input type="submit" class="button" value="<?php _e( 'Download Export File', 'nicethemes' ); ?>" />
		</form>

	</div><!--/.wrap-->
<?php

}

/**
 * nice_options_backup_export()
 *
 * Get the current options and put them into a json file.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_export(){

	check_admin_referer( 'nice-options-export' ); // Security check.

	$options = get_option( 'nice_options' );

	if ( ! $options ) { return; }

	// Add a custom marker.
	$options['nice-options-backup-validator'] = date( 'Y-m-d h:i:s' );

	// Generate the export file.
	$output = json_encode( (array)$options );

	header( 'Content-Description: File Transfer' );
	header( 'Cache-Control: public, must-revalidate' );
	header( 'Pragma: hack' );
	header( 'Content-Type: text/plain' );
	header( 'Content-Disposition: attachment; filename="nicethemes-backup-' . date( 'Y-m-d-His' ) . '.json"' );
	header( 'Content-Length: ' . strlen( $output ) );
	echo $output;
	exit;

}

/**
 * nice_options_backup_import()
 *
 * Get the options from a well formatted .json
 * Put them in the DB.
 *
 * @since 1.1.5
 * @return void
 */

function nice_options_backup_import(){

	// Check the nonce
	check_admin_referer( 'nice-options-import' );

	if ( ! isset( $_FILES['nice-options-import-file'] ) || $_FILES['nice-options-import-file']['name'] == '' ) { return; } // We can't import the settings without a settings file.

	// Extract file contents
	$upload = file_get_contents( $_FILES['nice-options-import-file']['tmp_name'] );

	// Decode the JSON from the uploaded file
	$options = json_decode( $upload, true );

	// Check for errors
	if ( ! $options || $_FILES['nice-options-import-file']['error'] ) {
		wp_redirect( admin_url( 'admin.php?page=nice-options-backup&error=true' ) );
		exit;
	}

	// Make sure this is a valid backup file.
	if ( ! isset( $options['nice-options-backup-validator'] ) ) {
		wp_redirect( admin_url( 'admin.php?page=nice-options-backup&invalid=true' ) );
		exit;
	} else {
		unset( $options['nice-options-backup-validator'] ); // Now that we've checked it, we don't need the field anymore.
	}

	// Make sure the options are saved to the global options collection as well.
	$nice_options = get_option( 'nice_options' );

	$has_updated = false; // If this is set to true at any stage, we update the main options collection.

	// Cycle through data, import settings
	foreach ( (array)$options as $key => $settings ) {

		$settings = maybe_unserialize( $settings ); // Unserialize serialized data before inserting it back into the database.

		// We can run checks using get_option(), as the options are all cached. See wp-includes/functions.php for more information.
		if ( get_option( $key ) != $settings ) {
			update_option( $key, $settings );
		}

		if ( is_array( $nice_options ) ) {
			if ( isset( $nice_options[$key] ) && $nice_options[$key] != $settings ) {
				$nice_options[$key] = $settings;
				$has_updated = true;
			}
		}
	}

	if ( $has_updated == true ) {
		update_option( 'nice_options', $nice_options );
	}

	// Redirect, add success flag to the URI
	wp_redirect( admin_url( 'admin.php?page=nice-options-backup&imported=true' ) );
	exit;

}
