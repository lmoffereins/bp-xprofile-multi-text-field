<?php

/**
 * The BuddyPress XProfile Multi Text Field Plugin
 *
 * @package BP XProfile Multi Text Field
 * @subpackage Main
 */

/**
 * Plugin Name:       BP XProfile Multi Text Field
 * Description:       BuddyPress profile field type that can have multiple text inputs
 * Plugin URI:        https://github.com/lmoffereins/bp-xprofile-multi-text-field/
 * Version:           1.0.0
 * Author:            Laurens Offereins
 * Author URI:        https://github.com/lmoffereins/
 * Text Domain:       bp-xprofile-multi-text-field
 * Domain Path:       /languages/
 * GitHub Plugin URI: lmoffereins/bp-xprofile-multi-text-field
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BP_XProfile_Multi_Text_Field' ) ) :
/**
 * The main plugin class
 *
 * @since 1.0.0
 */
final class BP_XProfile_Multi_Text_Field {

	/**
	 * The profile field type
	 *
	 * @since 1.0.0
	 * @var string
	 */
	public $type = 'multi_text';

	/**
	 * Setup and return the singleton pattern
	 *
	 * @since 1.0.0
	 *
	 * @return The single BP_XProfile_Multi_Text_Field
	 */
	public static function instance() {

		// Store instance locally
		static $instance = null;

		if ( null === $instance ) {
			$instance = new BP_XProfile_Multi_Text_Field;
			$instance->setup_globals();
			$instance->includes();
			$instance->setup_actions();
		}

		return $instance;
	}

	/**
	 * Prevent the plugin class from being loaded more than once
	 */
	private function __construct() { /* Nothing to do */ }

	/** Private methods *************************************************/

	/**
	 * Setup default class globals
	 *
	 * @since 1.0.0
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version      = '1.0.0';

		/** Paths *************************************************************/

		// Setup some base path and URL information
		$this->file         = __FILE__;
		$this->basename     = plugin_basename( $this->file );
		$this->plugin_dir   = plugin_dir_path( $this->file );
		$this->plugin_url   = plugin_dir_url ( $this->file );

		// Includes
		$this->includes_dir = trailingslashit( $this->plugin_dir . 'includes' );
		$this->includes_url = trailingslashit( $this->plugin_url . 'includes' );

		// Assets
		$this->assets_dir   = trailingslashit( $this->plugin_dir . 'assets' );
		$this->assets_url   = trailingslashit( $this->plugin_url . 'assets' );

		// Languages
		$this->lang_dir     = trailingslashit( $this->plugin_dir . 'languages' );

		/** Misc **************************************************************/

		$this->extend       = new stdClass();
		$this->domain       = 'bp-xprofile-multi-text-field';
	}

	/**
	 * Include the required files
	 *
	 * @since 1.0.0
	 */
	private function includes() {
		require( $this->includes_dir . 'functions.php'   );
		require( $this->includes_dir . 'sub-actions.php' );
	}

	/**
	 * Setup default actions and filters
	 *
	 * @since 1.0.0
	 */
	private function setup_actions() {

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'bp_xprofile_multi_text_field_activation'   );
		add_action( 'deactivate_' . $this->basename, 'bp_xprofile_multi_text_field_deactivation' );

		// Load textdomain
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ), 20 );

		// Main
		add_filter( 'bp_xprofile_get_field_types',     array( $this, 'register_field_type' )        );
		add_filter( 'xprofile_data_value_before_save', array( $this, 'save_profile_data'   ), 10, 4 );

		// Admin
		add_action( 'xprofile_field_after_save', array( $this, 'save_field'      ) );
		add_action( 'wp_enqueue_scripts',        array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_enqueue_scripts',     array( $this, 'enqueue_scripts' ) );
	}

	/** Plugin **********************************************************/

	/**
	 * Load the translation file for current language. Checks the languages
	 * folder inside the plugin first, and then the default WordPress
	 * languages folder.
	 *
	 * Note that custom translation files inside the plugin folder will be
	 * removed on plugin updates. If you're creating custom translation
	 * files, please use the global language folder.
	 *
	 * @since 1.0.0
	 *
	 * @uses apply_filters() Calls 'plugin_locale' with {@link get_locale()} value
	 * @uses load_textdomain() To load the textdomain
	 * @uses load_plugin_textdomain() To load the textdomain
	 */
	public function load_textdomain() {

		// Traditional WordPress plugin locale filter
		$locale        = apply_filters( 'plugin_locale', get_locale(), $this->domain );
		$mofile        = sprintf( '%1$s-%2$s.mo', $this->domain, $locale );

		// Setup paths to current locale file
		$mofile_local  = $this->lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/bp-xprofile-multi-text-field/' . $mofile;

		// Look in global /wp-content/languages/bp-xprofile-multi-text-field folder
		load_textdomain( $this->domain, $mofile_global );

		// Look in local /wp-content/plugins/bp-xprofile-multi-text-field/languages/ folder
		load_textdomain( $this->domain, $mofile_local );

		// Look in global /wp-content/languages/plugins/
		load_plugin_textdomain( $this->domain );
	}

	/** Public methods **************************************************/

	/**
	 * Method description
	 *
	 * @since 1.0.0
	 *
	 * @param array $types Registered field types
	 * @return array Registered field types
	 */
	public function register_field_type( $types ) {

		// Require field class
		require_once( $this->includes_dir . 'classes/class-bp-xprofile-field-type-multi-text.php' );

		// Register field type
		if ( class_exists( 'BP_XProfile_Field_Type_Multi_Text' ) ) {
			$types[ bp_xprofile_multi_text_field_type() ] = 'BP_XProfile_Field_Type_Multi_Text';
		}

		return $types;
	}

	/** BP_XProfile_Field *****************************************************/

	/**
	 * Return the field type meta keys
	 *
	 * @since 1.0.0
	 *
	 * @uses apply_filters() Calls 'bp_xprofile_multi_text_field_meta_keys'
	 * @return array Meta keys
	 */
	public function get_meta_keys() {

		/**
		 * Filter the available field meta keys for the multi text field type
		 *
		 * @since 1.0.0
		 *
		 * @param array $meta_keys The multi text field meta keys
		 */
		return (array) apply_filters( 'bp_xprofile_multi_text_field_meta_keys', array(
			'max_items_allowed',
		) );
	}

	/**
	 * Setup field meta on field object population
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field Field object
	 * @return BP_XProfile_Field Field object
	 */
	public function populate_field( $field ) {

		// Populate field meta
		foreach ( $this->get_meta_keys() as $meta ) {
			$field->$meta = bp_xprofile_get_meta( $field->id, 'field', $meta );
		}

		return $field;
	}

	/**
	 * Save field object
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $field Field object
	 */
	public function save_field( $field ) {

		// Type is posted escaped
		$type = esc_attr( bp_xprofile_multi_text_field_type() );

		// Save field meta
		foreach ( $this->get_meta_keys() as $meta ) {

			// Delete when the option was not posted
			if ( ! isset( $_POST["{$meta}_{$type}"] ) ) {
				bp_xprofile_delete_meta( $field->id, 'field', $meta );

			// Update metadata
			} else {
				bp_xprofile_update_meta( $field->id, 'field', $meta, $_POST["{$meta}_{$type}"] );
			}
		}
	}

	/**
	 * Enqueue scripts and styles
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		// Register assets
		wp_register_script( 'bp-xprofile-multi-text-field', $this->assets_url . 'js/bp-xprofile-multi-text-field.js', array( 'jquery' ), $this->version, true );
		wp_register_style( 'bp-xprofile-multi-text-field', $this->assets_url . 'css/bp-xprofile-multi-text-field.css', array(), $this->version );

		// When on profile fields or edit page
		if ( is_admin() && ! empty( $_GET['page'] ) && in_array( $_GET['page'], array( 'bp-profile-setup', 'bp-profile-edit' ) ) ) {
			wp_enqueue_style( 'bp-xprofile-multi-text-field' );

			if ( 'bp-profile-edit' === $_GET['page'] ) {
				wp_enqueue_script( 'bp-xprofile-multi-text-field' );
			}
		}

		// When on a BP page
		if ( ! is_admin() && is_buddypress() ) {
			wp_enqueue_script( 'bp-xprofile-multi-text-field' );
			wp_enqueue_style( 'bp-xprofile-multi-text-field' );
		}
	}

	/** BP_XProfile_ProfileData ***********************************************/

	/**
	 * Modify the profile data value that is saved
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $value Profile data value
	 * @param int $data_id Profile data ID
	 * @param bool $reserialize Whether to serialize the output
	 * @param BP_XProfile_ProfileData $profile_data Profile data object
	 * @return mixed Profile data value
	 */
	public function save_profile_data( $value, $data_id, $reserialize, $profile_data ) {

		// This is a Multi Text field
		if ( bp_xprofile_is_multi_text_field( $profile_data->field_id ) ) {

			// Is serialized?
			$serialized = is_serialized( $value );

			// Remove empty values
			$value = array_values( array_filter( maybe_unserialize( $value ) ) );

			// Reserialize
			if ( $serialized ) {
				$value = serialize( $value );
			}
		}

		return $value;
	}
}

/**
 * Return single instance of this main plugin class
 *
 * @since 1.0.0
 * 
 * @return BP_XProfile_Multi_Text_Field
 */
function bp_xprofile_multi_text_field() {
	return BP_XProfile_Multi_Text_Field::instance();
}

// Initiate plugin on load
bp_xprofile_multi_text_field();

endif; // class_exists
