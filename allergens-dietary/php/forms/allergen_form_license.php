<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'Allergens_Dietary_Form_I' ) ) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/Iallergen_form.php';
}

if ( ! class_exists( 'Allergens_Dietary_Allergen_Queries' ) ) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergen.php';
}


/**
 * @class Allergens_Dietary_License_Form
 * @brief Class that creates the form for the license key where
 * the user can enter the license key for the plugin to get premium functions unlocked
 * @author ictoriabv
 * @date 2-9-2024
 * @implements Allergens_Dietary_Form_I
 * @see Allergens_Dietary_Form_I
 * @since 1.0.0
 */
class Allergens_Dietary_License_Form implements Allergens_Dietary_Form_I {
	/**
	 * @brief Constructor for the Allergens_Dietary_License_Form class
	 * for now it is empty and does nothing but it's common courtesy to have it
	 * @return void
	 */
	public function __construct() {
	}

	public function showForm( string $allergenName = null ) {
		if ( ! is_null( $allergenName ) ) {
			return;
		}

		// TODO: Getting license key that is in use by site if it exists
		?>
		<fieldset>
			<label for="license_key"><?php echo esc_html__( 'License key', 'allergens-dietary' ); ?></label><br>
			<input 
				type="text" 
				name="license_key" 
				id="license_key" 
				value=""
			><br><br>
			<input 
				type="submit" 
				class="button button-primary" 
				id="submitButton" 
				name="submit" 
				value="<?php echo esc_attr__( 'Verify license key', 'allergens-dietary' ); ?>"
			>
		</fieldset>
	<?php
	}

	public function submit( array $data ) {
		if ( ! empty( $data ) ) {
			$post_data = $this->sanitize( $data );
			// TODO: save the license key in the external database
		} else {
			return;
		}
	}

	public function sanitize( array $data ) {
		$data['license_key'] = sanitize_text_field( wp_unslash( $data['license_key'] ) );
		return $data;
	}
}
