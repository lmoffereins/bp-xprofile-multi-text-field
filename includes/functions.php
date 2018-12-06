<?php

/**
 * BP XProfile Multi Text Field Functions
 *
 * @package BP XProfile Multi Text Field
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Return the plugin's field type key
 *
 * @since 1.0.0
 *
 * @return string Field type key
 */
function bp_xprofile_multi_text_field_type() {
	return bp_xprofile_multi_text_field()->type;
}

/**
 * Return whether the field is a Multi Text field
 *
 * @since 1.0.0
 *
 * @param BP_XProfile_Field|int $field_id Optional. Profile object or ID. Defaults to the current field.
 * @return bool Is the field of type Multi Text?
 */
function bp_xprofile_is_multi_text_field( $field_id = 0 ) {

	// Define local variable
	$type = false;

	// Default to the current field
	if ( empty( $field_id ) ) {
		$field_id = bp_get_the_profile_field_id();
	}

	// Get type from field ID
	if ( is_numeric( $field_id ) ) {
		$type = BP_XProfile_Field::get_type( $field_id );

	// Get type from field object
	} elseif ( is_a( $field_id, 'BP_XProfile_Field' ) ) {
		$type = $field_id->type;
	}

	return bp_xprofile_multi_text_field_type() === $type;
}

/** Template ******************************************************************/

/**
 * Output the markup for a Multi Text field's buttons
 *
 * @since 1.0.0
 *
 * @param BP_XProfile_Field|int $field Field object or ID.
 */
function bp_xprofile_multi_text_field_the_buttons( $field = 0 ) {
	echo bp_xprofile_multi_text_field_get_buttons( $field );
}

/**
 * Return the markup for a Multi Text field's buttons
 *
 * @since 1.0.0
 *
 * @uses apply_filters() Calls 'bp_xprofile_multi_text_field_get_buttons'
 *
 * @param BP_XProfile_Field|int $field Field object or ID.
 * @return string Buttons markup
 */
function bp_xprofile_multi_text_field_get_buttons( $field = 0 ) {

	// Start output buffer
	ob_start(); ?>

	<button type="button" class="remove-field">
		<span class="screen-reader-text"><?php esc_html_e( 'Remove field', 'bp-xprofile-multi-text-field' ); ?></span>
	</button>
	<button type="button" class="add-field">
		<span class="screen-reader-text"><?php esc_html_e( 'Add field', 'bp-xprofile-multi-text-field' ); ?></span>
	</button>

	<?php

	$buttons = ob_get_clean();

	return apply_filters( 'bp_xprofile_multi_text_field_get_buttons', $buttons, $field );
}
