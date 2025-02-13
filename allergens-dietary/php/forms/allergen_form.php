<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!interface_exists('Allergens_Dietary_Form_I')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/Iallergen_form.php';
}

if (!class_exists('Allergens_Dietary_License_Form')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_form_license.php';
}

/*if (!class_exists('Allergens_Dietary_Allergen_Form')) {
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_add_allergen.php';
}

if (!class_exists('Allergens_Dietary_Update_Allergen_Form')) {
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_update_allergen.php';
}*/

enum FormType
{
	case ALLERGENS;
	case LICENSE;
	case UPDATE;

	public function match(FormType $formType): bool
	{
		return $this === $formType;
	}
}

/**
 * @class Allergens_Dietary_Form
 * @brief This class is a singleton strategy
 * that creates a form for the allergens and dietary restrictions plugin.
 * @author ictoriabv
 * @date 2-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Form
{
	private static ?self $_instance = null;
	private static FormType $_formType;
	private static Allergens_Dietary_Form_I $_formObject;

	private function __construct(bool $isTable = false)
	{
		if (FormType::LICENSE === self::$_formType) {
			try{
				self::$_formObject = new Allergens_Dietary_License_Form();
			} catch(Exception $error){
				wp_die(esc_html(__('Something went wrong!', 'allergens-dietary')));
			}
		}
		if (!isset(self::$_formType) || false === self::$_formType->match(self::$_formType)) {
			throw new Exception('FormType not yet supported/implemented');
		}
	}

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	public static function setFormType(FormType $formType)
	{
		self::$_formType = $formType;
	}

	public static function getFormType()
	{
		return self::$_formType;
	}

	/**
	 * @author ictoriabv
	 * @brief Handles the html of the selected form in $_formObject
	 * The sanitizing and processing of the information given by the form is handled elsewhere
	 * In the correct class of the selected form type
	 * @param string $allergenName
	 * @return void
	 * @since 0.3.0.0
	 * @version 0.18.6.0
	 */
	public function showForm(string $allergenName = null)
	{
		if(isset($_POST['allergens-forms-nonce']) && !empty($_POST['allergens-forms-nonce'])){
			$nonce = sanitize_text_field(wp_unslash($_POST['allergens-forms-nonce']));
			if (!wp_verify_nonce($nonce,'allergen-forms-action')){
				wp_die(esc_html(__('Something went wrong!','allergens-dietary')));
			}
			
			$_data = [];
			if ( isset( $_POST['license_key'] ) ) {
				$_data['license_key'] = sanitize_text_field( wp_unslash( $_POST['license_key'] ) );
			}
			
	
			if (!empty($_POST['submit'])) {
				self::$_formObject->submit($_data);
			}
		}

		
		?>
		<div class="allergens_form health-check-body">
			<form action="" method="post" enctype="multipart/form-data" class="add_allergens_form">
			<?php 
			wp_nonce_field('allergen-forms-action', 'allergens-forms-nonce');
			self::$_formObject->showForm($allergenName);
			?>
			</form>
		</div>
		<?php
	
	
	}
}
