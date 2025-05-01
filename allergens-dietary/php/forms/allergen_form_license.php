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

if ( ! class_exists( 'Allergens_Dietary_Pro_License_RUD' ) ) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/license_rud.php';
}

if ( ! enum_exists('FormType')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/lists/form_type.php';
}

if ( ! enum_exists('Allergens_Dietary_Notices')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
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
	private Allergens_Dietary_Pro_License_RUD $_license_Rud;

	public function __construct() 
	{
		$this->_license_Rud = Allergens_Dietary_Pro_License_RUD::getInstance();
		$license = $this->_license_Rud->getLicenseKey();
		$endDate = $this->_license_Rud->getLicenseEndDate();
		$currentDate = date('d-m-Y');

		if ($currentDate === $endDate || $currentDate > $endDate)
		{
			Allergens_Dietary_Notices::getInstance()->display_admin_notice(Notice_Types::WARNING, "License key '{$license}' has surpassed its expiration date. Consider updating the Allergens & Dietary plugin.");
		}

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
		$license = $this->_license_Rud->getLicenseKey();


		if ($userInput === $license)
		{
			$availability = $this->_license_Rud->getLicenseAvailability();
			if ($availability === "Ongebruikt")
			{
				$this->_license_Rud->updateLicense();
			}
			else 
			{
				Allergens_Dietary_Notices::getInstance()->display_admin_notice(Notice_Types::ERROR, "This license key is already taken by another user. Please try again.");
			}
		}
		else
		{
			Allergens_Dietary_Notices::getInstance()->display_admin_notice(Notice_Types::ERROR, "This license key is not valid. Please try again.");
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
