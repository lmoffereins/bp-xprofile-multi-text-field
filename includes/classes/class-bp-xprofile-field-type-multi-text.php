<?php

/**
 * The BP XProfile Field Type Multi Text Class
 * 
 * @package BP XProfile Multi Text Field
 * @subpackage Main
 */

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'BP_XProfile_Field_Type_Multi_Text' ) ) :
/**
 * Multi Text XProfile field type
 *
 * @since 1.0.0
 */
class BP_XProfile_Field_Type_Multi_Text extends BP_XProfile_Field_Type {

	/**
	 * Whether the fields should have suggested values
	 *
	 * @since 1.0.0
	 * @var bool
	 */
	public $suggest_values = false;

	/**
	 * Constructor for the multi textbox field type
	 *
	 * @since 1.0.0
 	 */
	public function __construct() {
		parent::__construct();

		$this->category = _x( 'Multi Fields', 'xprofile field type category', 'buddypress' );
		$this->name     = _x( 'Multi Text Box', 'xprofile field type', 'bp-xprofile-multi-text-field' );

		$this->do_settings_section = true;

		$this->set_format( '/^.*$/', 'replace' );

		do_action( 'bp_xprofile_field_type_multi_text', $this );
	}

	/**
	 * Output the edit field HTML for this field type
	 *
	 * Must be used inside the {@see bp_profile_fields()} template loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw_properties Optional key/value array of
	 *                              {@link http://dev.w3.org/html5/markup/input.checkbox.html permitted attributes}
	 *                              that you want to add.
	 */
	public function edit_field_html( array $raw_properties = array() ) {

		// User_id is a special optional parameter that we pass to
		// {@link BP_XProfile_ProfileData::get_value_byid()}.
		if ( isset( $raw_properties['user_id'] ) ) {
			$user_id = (int) $raw_properties['user_id'];
			unset( $raw_properties['user_id'] );
		} else {
			$user_id = bp_displayed_user_id();
		}

		$field_id = bp_get_the_profile_field_id();
		$original_option_values = maybe_unserialize( BP_XProfile_ProfileData::get_value_byid( $field_id, $user_id ) );

		if ( empty( $original_option_values ) && ! empty( $_POST['field_' . $field_id ] ) ) {
			$original_option_values = sanitize_text_field( $_POST['field_' . $field_id ] );
		}

		$option_values = ( $original_option_values ) ? (array) $original_option_values : array( '' );
		$option_values[] = ''; // Clone-able value

		?>

		<legend id="<?php bp_the_profile_field_input_name(); ?>-1">
			<?php bp_the_profile_field_name(); ?>
			<?php bp_the_profile_field_required_label(); ?>
		</legend>

		<?php

		/** This action is documented in bp-xprofile/bp-xprofile-classes */
		do_action( bp_get_the_profile_field_errors_action() ); ?>

		<div class="multi-text-wrapper" data-max-items-allowed="<?php echo bp_xprofile_get_meta( $field_id, 'field', 'max_items_allowed' ); ?>">

		<?php foreach ( $option_values as $k => $value ) :

			$style = ( $k + 1 === count( $option_values ) ) ? 'style="display: none;"' : ''; // Hide last item

			$r = bp_parse_args( $raw_properties, array(
				'type'  => 'text',
				'id'    => bp_get_the_profile_field_input_name() . '[]',
				'name'  => bp_get_the_profile_field_input_name() . '[]',
				'value' => $value,
			) ); ?>

			<div class="multi-text-input" <?php echo $style; ?>>
				<input <?php echo $this->get_edit_field_html_elements( $r ); ?> aria-labelledby="<?php bp_the_profile_field_input_name(); ?>-1" aria-describedby="<?php bp_the_profile_field_input_name(); ?>-3">

				<?php bp_xprofile_multi_text_field_the_buttons(); ?>
			</div>

		<?php endforeach; ?>

		</div>

		<?php if ( bp_get_the_profile_field_description() ) : ?>
			<p class="description" id="<?php bp_the_profile_field_input_name(); ?>-3"><?php bp_the_profile_field_description(); ?></p>
		<?php endif; ?>

		<?php
	}

	/**
	 * Output HTML for this field type on the wp-admin Profile Fields screen.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @since 1.0.0
	 *
	 * @param array $raw_properties Optional key/value array of permitted attributes that you want to add.
	 */
	public function admin_field_html( array $raw_properties = array() ) {

		$r = bp_parse_args( $raw_properties, array(
			'type' => 'text'
		) ); ?>

		<label for="<?php bp_the_profile_field_input_name(); ?>" class="screen-reader-text"><?php
			/* translators: accessibility text */
			esc_html_e( 'Multi Textbox', 'buddypress' );
		?></label>

		<div class="multi-text-input">
			<input <?php echo $this->get_edit_field_html_elements( $r ); ?>>

			<?php bp_xprofile_multi_text_field_the_buttons(); ?>
		</div>

		<?php
	}

	/**
	 * Output HTML for this field type's children options on the wp-admin Profile Fields "Add Field" and "Edit Field" screens.
	 *
	 * Must be used inside the {@link bp_profile_fields()} template loop.
	 *
	 * @see BP_XProfile_Field_Type::admin_new_field_html()
	 *
	 * @since 1.0.0
	 *
	 * @param BP_XProfile_Field $current_field The current profile field on the add/edit screen.
	 * @param string $control_type Optional. HTML input type used to render the current field's child options.
	 */
	public function admin_new_field_html( BP_XProfile_Field $current_field, $control_type = '' ) {
		$type = array_search( get_class( $this ), bp_xprofile_get_field_types() );
		if ( false === $type ) {
			return;
		}

		// Define field details
		$current_field = bp_xprofile_multi_text_field()->populate_field( $current_field );
		$class         = $current_field->type !== $type ? 'display: none;' : '';

		// Define field meta ids. Follow BP's pattern of `meta_name_{$type}`
		$esc_type          = esc_attr( $type );
		$max_items_allowed = "max_items_allowed_{$esc_type}";

		?>

		<div id="<?php echo $esc_type; ?>" class="postbox bp-options-box" style="<?php echo esc_attr( $class ); ?> margin-top: 15px;">
			<h3><?php esc_html_e( 'Please enter options for this Field:', 'buddypress' ); ?></h3>

			<div class="inside" aria-live="polite" aria-atomic="true" aria-relevant="all">
			<table class="form-table bp-multi-text-options">
				<tr>
					<th scope="row">
						<label for="<?php echo $max_items_allowed; ?>">
							<?php esc_html_e( 'Max Items Allowed', 'bp-xprofile-multi-text-field' ); ?>
						</label>
					</th>

					<td>
						<input type="number" name="<?php echo $max_items_allowed; ?>" id="<?php echo $max_items_allowed; ?>" value="<?php echo $current_field->max_items_allowed; ?>"/>
						<span class="description"><?php esc_html_e( 'Set to 0 to allow unlimited value items.', 'bp-xprofile-multi-text-field' ); ?></span>
					</td>
				</tr>
			</table>
			</div>

		</div>

		<?php
	}
}

endif;
