<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! interface_exists( 'I_Allergens_Dietary_Ictoria_Form' ) ) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/forms/Iallergen_form.php';
}

if ( ! class_exists( 'Allergens_Dietary_Ictoria_Allergen_Queries' ) ) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/allergen.php';
}


/**
 * @class Allergens_Dietary_Ictoria_License_Form
 * @brief Class that creates the form for the license key where
 * the user can enter the license key for the plugin to get premium functions unlocked
 * @author V.B.
 * @date 2-9-2024
 * @implements I_Allergens_Dietary_Ictoria_Form
 * @see I_Allergens_Dietary_Ictoria_Form
 * @since 1.0.0
 */
class Allergens_Dietary_Ictoria_License_Form implements I_Allergens_Dietary_Ictoria_Form {
	/**
	 * @brief Constructor for the Allergens_Dietary_Ictoria_License_Form class
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
		$html  = '<fieldset>
		<label for="license_key">' . __( 'License key', 'allergens-dietary-ictoria' ) . '</label><br>
		<input type="text" name="license_key" id="license_key" value=""><br><br>
		<input type="submit" class="button button-primary" id="submitButton" name="submit" value="' . __( 'Verify license key', 'allergens-dietary-ictoria' ) . '">';
		$html .= '</fieldset>';

		echo $html;
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
