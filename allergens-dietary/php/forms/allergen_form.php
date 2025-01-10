<?php

if (!defined('ABSPATH')) {
	exit;
}

if (!interface_exists('I_Allergens_Dietary_Form')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/Iallergen_form.php';
}

if (!class_exists('Allergens_Dietary_License_Form')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_form_license.php';
}

if (!class_exists('Allergens_Dietary_License_Form')) {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/lists/form_type.php';
}

/*if (!class_exists('Allergens_Dietary_Allergen_Form')) {
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_add_allergen.php';
}

if (!class_exists('Allergens_Dietary_Update_Allergen_Form')) {
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/forms/allergen_update_allergen.php';
}*/

// enum FormType
// {
// 	case ALLERGENS;
// 	case LICENSE;
// 	case UPDATE;
// 	public function match(FormType $formType): bool
// 	{
// 		return $this === $formType;
// 	}
// }

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
	private static $instances = [];
	protected static FormType $_formType;
	protected static I_Allergens_Dietary_Form $_formObject;

	public function __construct(bool $isTable = false)
	{
		if (FormType::LICENSE === static::$_formType) {
			static::$_formObject = new Allergens_Dietary_License_Form();
		}
		if (!isset(self::$_formType) || false === self::$_formType->match(self::$_formType)) {
			throw new Exception('FormType not yet supported/implemented');
		}
	}

	public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

	public static function setFormType(FormType $formType)
	{
		static::$_formType = $formType;
	}

	public static function getFormType()
	{
		return static::$_formType;
	}

	public function submitUpdate()
	{

		if (!empty($_POST)) {
			$_data = $_POST;
		}

		if (!empty($_FILES)) {
			$_data = array_merge($_data, $_FILES);
		}

		if (!empty($_POST['submit'])) {
			static::$_formObject->submit($_data);
		}
	}

	public function showForm(string $allergenName = null)
	{

		$showOnPage = ["allergens-dietary-show-allergens"];
		$showOnPageSecondOption = ["allergens-dietary-add-allergen"];

		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

		if (!empty($_POST)) {
			$_data = $_POST;
		}

		if (!empty($_FILES)) {
			$_data = array_merge($_data, $_FILES);
		}

		if (!in_array($page, $showOnPage, true)) {
			if (!empty($_POST['submit'])) {
				static::$_formObject->submit($_data);
			}
		}

		if (in_array($page, $showOnPage, true)) {
			echo '<div class="allergens_table_form" style="display: none;" id="' . $allergenName . '_form">';
			static::$_formObject->showForm($allergenName);
			echo '</div>';
		} else {
			if (in_array($page, $showOnPageSecondOption, true)) {
				echo '<div class="allergens_form health-check-body"><form action="" method="post" style="max-width: 350px;" enctype="multipart/form-data" class="add_allergens_form">';
				static::$_formObject->showForm($allergenName);
				echo '</form></div>';
			}else{
				echo '<div class="allergens_form health-check-body"><form action="" method="post" enctype="multipart/form-data" class="add_allergens_form">';
				static::$_formObject->showForm($allergenName);
				echo '</form></div>';
			}
		}
	}
}
