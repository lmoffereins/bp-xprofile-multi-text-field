<?php

/**
 * BP XProfile Multi Text Field Sub-Actions
 *
 * @package BP XProfile Multi Text Field
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

/**
 * Run dedicated activation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'bp_xprofile_multi_text_field_activation'
 */
function bp_xprofile_multi_text_field_activation() {
	do_action( 'bp_xprofile_multi_text_field_activation' );
}

/**
 * Run dedicated deactivation hook for this plugin
 *
 * @since 1.0.0
 *
 * @uses do_action() Calls 'bp_xprofile_multi_text_field_deactivation'
 */
function bp_xprofile_multi_text_field_deactivation() {
	do_action( 'bp_xprofile_multi_text_field_deactivation' );
}
