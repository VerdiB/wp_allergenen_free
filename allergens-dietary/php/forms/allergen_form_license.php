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

if ( ! class_exists( 'Allergens_Dietary_License_RUD' ) ) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/license_rud.php';
}

if ( ! enum_exists('FormType')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/lists/form_type.php';
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

	public function __construct() 
	{

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
				name="licenseForm[license_key]"
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


	// Deze functie zorgt voor het tonen van de licentiesleutel op de pagina van de gebruiker
	// MITS die aan de voorwaardes voldoet
	public function licenseActivator(array $data)
	{
		$userInput = $data["license_key"];
		$licenseRud = new Allergens_Dietary_Pro_License_RUD();
		$license = $licenseRud->getLicenseKey();

		$notices = new Allergens_Dietary_Notices;

		if($userInput === $license)
		{
			$availability = $licenseRud->getLicenseAvailability();
			if ($availability === "Ongebruikt")
			{
				var_dump($license);
				return $license;
				$licenseRud->updateLicense();
			}
			else 
			{
				$notice = $notices->error_notice('notice-error', "This license key is not valid. Please try again.");
			}
		}
		else
		{
			$notice = $notices->error_notice('notice-error', "This license key is already taken by another user. Please try again.");
		}
	}
	
	public function submit( array $data ) {

		// echo '<pre>';
		// print_r($data);
		// echo '</pre>';
		// return;

		if ( ! empty( $data ) ) {
			$post_data = $this->sanitize( $data );
			$this->licenseActivator($post_data);

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
