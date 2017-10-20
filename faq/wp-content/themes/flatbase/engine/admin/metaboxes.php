<?php
/**
 * Table of Contents (metaboxes.php)
 *
 *	- nice_custom_get_info()
 *	- nice_custom_get_text()
 *	- nice_custom_get_textarea()
 *	- nice_custom_get_select()
 *	- nice_custom_get_file()
 *	- nice_custom_get_checkbox()
 *	- nice_custom_get_radio()
 *	- nice_custom_get_id()
 *	- nice_custom_get_value()
 *	- nice_metabox_add()
 *	- nice_metaboxes_init()
 *	- nice_metabox_save_data()
 *	- nice_metabox_header()
 *	- nice_metaboxes()
 *	- nice_metaboxes_scripts()
 *
 */

/**
 * nice_custom_get_info()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_custom_get_info' ) ) :

	function nice_custom_get_info( $field ){

		$id = nice_custom_get_id( $field );

		$html  = "\t" . '<tr id="' . $id . '" class="nice-custom-info" >';
		$html .= "\t\t" . '<td colspan="2">' . $field['desc']  . '</td>' . "\n";
		$html .= "\t" . '</tr>' . "\n";

		return $html;
	}

endif;


/**
 * nice_custom_get_text()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the text input.
 */

if ( ! function_exists( 'nice_custom_get_text' ) ) :

	function nice_custom_get_text( $field ){

		$id = nice_custom_get_id( $field );

		$html  = "\t" . '<tr id="' . $id . '" >';
		$html .= "\t\t" . '<th><label for="' . esc_attr( $id ) . '">' . $field['label'] . '</label></th>' . "\n";
		$html .= "\t\t" . '<td><input class="nice-input-text " type="' . $field['type'] . '" value="' . esc_attr( nice_custom_get_value( $field ) ) . '" name="' . $field['name'] . '" id="' . $id  . '"/>';
		$html .= '<span class="description">' . $field['desc'] . '</span></td>' . "\n";
		$html .= "\t" . '</tr>' . "\n";

		return $html;
	}

endif;

/**
 * nice_custom_get_textarea()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the textarea input.
 */

if ( ! function_exists( 'nice_custom_get_textarea' ) ) :

	function nice_custom_get_textarea( $field ){

		$id = nice_custom_get_id( $field );

		$html  = "\t" . '<tr id="' . $id . '" >';
		$html .= "\t\t" . '<th><label for="' . esc_attr( $id ) . '">' . $field['label'] . '</label></th>' . "\n";
		$html .= "\t\t" . '<td><textarea class="nice_textarea " name="' . $field['name'] . '" id="' . $id . '">' . esc_textarea( stripslashes( nice_custom_get_value( $field ) ) ) . '</textarea>';
		$html .= '<span class="description">' . $field['desc'] . '</span></td>' . "\n";
		$html .= "\t" . '</tr>' . "\n";

		return $html;
	}

endif;


/**
 * nice_custom_get_select()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the select input.
 */

if ( ! function_exists( 'nice_custom_get_select' ) ) :

	function nice_custom_get_select( $field ){

		$field_id = nice_custom_get_id( $field );

		$html  = "\t" . '<tr id="' . $field_id . '" >';
		$html .= "\t\t" . '<th><label for="' . esc_attr( $field_id ) . '">' . $field['label'] . '</label></th>' . "\n";
		$html .= "\t\t" . '<td><select class="nice-select" id="' . esc_attr( $field_id ) . '" name="' . esc_attr( $field['name'] ) . '">';

		$options = $field['options'];

		if ( $options ) {

			$selected_value = nice_custom_get_value( $field );

			foreach ( $options as $id => $option ) {

				$selected = '';

				if ( $selected_value != '' ) {

					$selected = selected( $selected_value, $id, false );

				} elseif ( isset( $field['default'] ) ) {

						$selected = selected( $field['default'], $id, false );

				}

				$html .= '<option value="' . esc_attr( $id ) . '" ' . $selected . '>' . $option . '</option>';

			}

		}

		$html .= '</select><span class="description">' . esc_html( $field['desc'] ) . '</span></td>' . "\n";
		$html .= "\t" . '</tr>' . "\n";

		return $html;
	}

endif;


/**
 * nice_custom_get_file()
 *
 * @since 1.0.0
 */

if ( ! function_exists( 'nice_custom_get_file' ) ) :

	function nice_custom_get_file( $field ){
		// nothing here yet.
	}

endif;

/**
 * nice_custom_get_checkbox()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the checkbox input.
 */

if ( ! function_exists( 'nice_custom_get_checkbox' ) ) :

	function nice_custom_get_checkbox( $field ){

		$value = nice_custom_get_value( $field );
		$id = nice_custom_get_id( $field );

		$checked = checked( $value, 'true', false );

		$html  = "\t" . '<tr id="' . $id . '" >';
		$html .= "\t\t" . '<th><label for="' . esc_attr( $id ) . '">' . $field['label'] . '</label></th>' . "\n";
		$html .= "\t\t" . '<td><input type="checkbox" ' . $checked . ' class="nice-input-checkbox" value="true"  id="' . $id . '" name="' . $field['name'] . '" />';
		$html .= '<span class="description" style="display:inline">' . $field['desc'] . '</span></td>' . "\n";
		$html .= "\t" . '</tr>' . "\n";

		return $html;
	}

endif;


/**
 * nice_custom_get_radio()
 *
 * Retrieve option info in order to return the field in html code.
 *
 * @since 1.0.0
 *
 * @param array item $field. Option info in order return the html code.
 * @return string with the radio input.
 */

if ( ! function_exists( 'nice_custom_get_radio' ) ) :

	function nice_custom_get_radio( $field ){

		$field_id = nice_custom_get_id( $field );

		$options = $field['options'];

		if ( $options ) {

			$html  = "\t" . '<tr id="' . $id . '" >';
			$html .= "\t\t" . '<th><label for="' . esc_attr( $field_id ) . '">' . esc_html( $field['label'] ) . '</label></th>' . "\n";
			$html .= "\t\t" . '<td>';

			$selected_value = nice_custom_get_value( $field );

			foreach ( $options as $id => $option ) {

				$checked = checked( $selected_value, $id, false );

				$html .= '<input type="radio" ' . $checked . ' value="' . esc_attr( $id ) . '" class="nice-input-radio"  name="' . $field['name'] . '" />';
				$html .= '<span class="description" style="display:inline">' .  $option  . '</span><div class="nice_spacer"></div>';

			}
			$html .= "\t" . '</tr>' . "\n";
		}

		return $html;
	}

endif;


/**
 * nice_custom_get_id()
 *
 * Retrieve custom field ID for html purposes.
 *
 * @since 1.0.0
 *
 * @param array element $field
 * @return string ID
 */

if ( ! function_exists( 'nice_custom_get_id' ) ) :

	function nice_custom_get_id( $field ){

		return 'nicethemes_' . $field['name'];
	}

endif;

/**
 * nice_custom_get_value()
 *
 * Retrieve custom field value. If there's no value in db
 * it sets the standard value
 *
 * @since 1.0.0
 *
 * @return string value
 */

if ( ! function_exists( 'nice_custom_get_value' ) ) :

	function nice_custom_get_value( $field ){

		global $post;

		$db_value = get_post_meta( $post->ID, $field['name'], true );

		if ( $db_value == '' && isset( $field['std'] ) ) return $field['std'];
		else return $db_value;

	}

endif;


/**
 * nice_metabox_add()
 *
 * Retrieve custom fields html. Print the result.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_metabox_add' ) ) :

function nice_metabox_add( $post, $callback ){

		global $post;

		$nice_fields = get_option( 'nice_custom_fields' );

		$html = '<table id="nice-metabox" class="form-table">';

		foreach ( $nice_fields as $field ):

			$nice_id = 'nicethemes_' . $field["name"];
			$nice_name = $field["name"];

			switch( $field['type'] ):

				case 'info':
					$html .= nice_custom_get_info( $field );
				break;

				case 'text':
					$html .= nice_custom_get_text( $field );
				break;

				case 'select':
					$html .= nice_custom_get_select( $field );
				break;

				case 'textarea':
					$html .= nice_custom_get_textarea( $field );
				break;

				case 'upload':
					$html .= nice_custom_get_file( $field );
				break;

				case 'checkbox':
					$html .= nice_custom_get_checkbox( $field );
				break;

				case 'radio':
					$html .= nice_custom_get_radio( $field );
				break;

			endswitch;

		endforeach;

		$html .= '</table>';

		echo $html;

	}

endif;

/**
 * nice_metaboxes_init()
 *
 * Add meta boxes for each post type.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_metaboxes_init' ) ) :

	function nice_metaboxes_init(){

		$post_types = get_post_types();

		$nice_fields = get_option( 'nice_custom_fields' );

		$nice_theme = wp_get_theme();

		foreach ( $post_types as $type ) :

			$settings = array(
								'id' 			=> 'nicethemes-settings',
								'title' 		=> sprintf( __( '%s Settings', 'nicethemes' ), $nice_theme->get( 'Name' ) ),
								'callback' 		=> 'nice_metabox_add',
								'page' 			=> $type,
								'priority' 		=> 'normal',
								'callback_args' => ''
							);

			if ( ! empty ( $nice_fields ) ) add_meta_box( $settings['id'], $settings['title'], $settings['callback'], $settings['page'], $settings['priority'], $settings['callback_args'] );

		endforeach;

	}

endif;


/**
 * nice_metabox_save_data()
 *
 * Saves data for custom fields
 *
 * @since 1.0.0
 *
 * @return int ID
 */

if ( ! function_exists( 'nice_metabox_save_data' ) ) :

	function nice_metabox_save_data(){

		global $globals, $post;

		$ID = '';

		if ( isset( $_POST['post_ID'] ) ) {

			$ID = intval( $_POST['post_ID'] );

		} // End IF Statement

		// Don't continue if we don't have a valid post ID.

		if ( $ID == 0 ) {

			return;

		} // End IF Statement

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return $ID;
		}

		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $ID ) ) {
				return $ID;
			}
		} elseif ( ! current_user_can( 'edit_post', $ID ) ) {
			return $ID;
		}

		if ( isset( $_POST['action'] ) && ( $_POST['action'] == 'editpost' ) ) :

			$nice_fields = get_option( 'nice_custom_fields' );

			foreach ( $nice_fields as $field ) :

				if ( $field['type'] != 'info' ) :

					$old_value = '';
					$old_value = get_post_meta( $ID, $field['name'], true );

					if ( isset ( $_POST[$field['name']] ) ) {

						$new_value = '';
						$new_value = $_POST[ $field['name'] ];

						if ( $new_value && $new_value != $old_value ) {
							update_post_meta( $ID, $field['name'], $new_value );
						} elseif ( '' == $new_value && $old_value ) {
							delete_post_meta( $ID, $field['name'], $old_value );
						} elseif ( $old_value == '' ){
							add_post_meta( $ID, $field['name'], $new_value, true );
						}

					} elseif ( $field['type'] == 'checkbox' && ! isset( $_POST[$field['name']] ) ) {

						delete_post_meta( $ID, $field['name'], $old_value );

					} else {

						if ( isset( $_POST[ $field['name'] ] ) ){
							$new_value = $_POST[ $field['name'] ];
							update_post_meta( $ID, $field['name'], $new_value );
						}

					}


				endif;

			endforeach;

		endif;

	}

endif;

function nice_metabox_header(){
?>

<style type="text/css">
/* Some Nice CSS for MetaBoxes*/
#nice-metabox input[type=text] { margin: 0 0 10px 0; width:80%; padding: 5px; color: #222; }
#nice-metabox td .nice-select { margin: 0 0 10px 0; color:#222; width: 60%; padding: 5px; }
#nice-metabox th label { color:#555; font-weight:bold; }
#nice-metabox span.description { display:block; cursor:help; color:#999;-webkit-transition: color 0.2s ease-in; /*safari and chrome */
 -o-transition: color 0.2s ease-in; /* opera */ -moz-transition: color 0.2s ease-in;  /* FF4+ */ -ms-transition: color 0.2s ease-in;  /* IE10? */ transition: color 0.2s ease-in;  }
#nice-metabox span.description:hover { color: #555; }
#nice-metabox th,
#nice-metabox td,
#nice-metabox .nice-row{ border-bottom:1px solid #e3e3e3; padding:15px 5px;text-align: left; vertical-align:top; }
#nice-metabox tr:last-child td,
#nice-metabox tr:last-child th{ border-bottom:none; }
#nice-metabox td .nice_textarea { width:80%; height:100px;margin:0 0 10px 0; color:#222;padding: 5px; }
#nice-metabox td .nice-input-checkbox{ display: inline-block; float: left; margin-right: 8px; }
#nice-metabox tr.nice-custom-info{ font-size: 11px; line-height: 1.5em; }
.branch-3-7, .branch-3-6 #nice-metabox tr.nice-custom-info{ background: #f8f8f8; }
#nice-metabox .border-bottom-none { border-bottom: none; }

</style>

<?php

}

/**
 * nice_metaboxes()
 *
 * init metaboxes action.
 *
 * @since 1.0.0
 *
 */

if ( ! function_exists( 'nice_metaboxes' ) ) :

	function nice_metaboxes(){

		nice_metaboxes_init();
	}

endif;


if ( ! function_exists( 'nice_metaboxes_scripts' ) ) :

	function nice_metaboxes_scripts( $hook ) {

	  	if ( $hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page-new.php' OR $hook == 'page.php' ) {
			add_action( 'admin_head', 'nice_metabox_header' );
			wp_enqueue_script( 'jquery-ui-core' );
			//wp_register_script( 'jquery-ui-datepicker', get_template_directory_uri() . '', array( 'jquery-ui-core' ));
			wp_enqueue_script( 'jquery-ui-datepicker' );
			//wp_register_script( 'jquery-input-mask', get_template_directory_uri() . '', array( 'jquery' ));
			wp_enqueue_script( 'jquery-input-mask' );

			wp_register_script( 'nicethemes-metaboxes', get_template_directory_uri() . '/engine/admin/js/metaboxes.js', array( 'jquery' ) );
			wp_enqueue_script( 'nicethemes-metaboxes' );
	  	}

	}

endif;

add_action( 'admin_enqueue_scripts', 'nice_metaboxes_scripts', 10, 1 );
add_action( 'admin_menu', 'nice_metaboxes' );
add_action( 'edit_post' , 'nice_metabox_save_data' );

?>