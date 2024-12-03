<?php

// exit if user can access this file directly
if (!defined('ABSPATH')) {
	exit;
}

if (!interface_exists('I_Allergens_Dietary_Ictoria_Form')) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/forms/Iallergen_form.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Allergy_Attachment_Queries')) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/allergy_attachment.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Attachment_Queries')) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/attachment.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Allergen_Queries')) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/allergen.php';
}
/**
 * @brief This shows the tabs on add/update allergens .
 * @author T.K.
 * @since 1.0.0
 * @date 18-9-2024
 */
/**
 * @brief This shows the tabs on add/update allergens .
 * @author T.K.
 * @since 1.0.0
 * @date 18-9-2024
 */

if (!class_exists('Allergens_Dietary_Ictoria_Tabs')) {
	include_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/tabs/allergen_tabs.php';
}
/*
if (!enum_exists('Mime_Types')) {
	include_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/lists/mime_types.php';
}*/


if (!class_exists('Allergens_Dietary_Ictoria_Notices')) {
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/notice/notice.php';
}

if (! enum_exists( 'Notice_Types' ) ){
	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/notice/notice_Types.php';
}
/********************************************************************/
/********************************************************************/

class Allergens_Dietary_Ictoria_Allergen_Form implements I_Allergens_Dietary_Ictoria_Form
{


	private ?array $_allergen = null;
	private array $MIME_TYPES;
	private array $MIME_NAMES;
	private static string $message = '';
	private static Notice_Types $_type; 

	public function __construct()
	{
		$this->MIME_TYPES = (enum_exists('Mime_Types'))? Mime_Types::get_mime_types(): array() ;
		$this->MIME_NAMES = (enum_exists('Mime_Types'))? array_map(fn($case) => $case->name, Mime_Types::cases()): array();
	}

	/**
	 * @param string|null $allergenName
	 * @brief This method shows the form to add/update allergens .
	 * @return void
	 * @author V.B.
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	public function showForm(?string $allergenName = null)
	{
		if (!is_null($allergenName)) {
			// TODO: Implement showForm() method. when the allergen name is not null
			$this->_allergen = Allergens_Dietary_Ictoria_Allergy_Attachment_Queries::getInstance()->getAllergyAttachment($allergenName);
		}

		$showOnPage = ["allergens-dietary-show-allergens"];

		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

		if (in_array($page, $showOnPage, true)) {
			
			//Allergen name and style elements 
			$html = '<fieldset class="update_form inline-edit-product.quick-edit-row">';
			$html .= '<div class="inline-edit-wrapper" aria-labelledby="quick-edit-legend">';
			$html .= '<fieldset class="inline-edit-col-left"><div>';
			$html .= '<legend class="inline-edit-legend">' . __("Quick Edit", "allergens-dietary-ictoria") . '</legend>';
			$html .= '<input disabled type="hidden" id="the_hidden_allergy_name" class="update_" name="allergen_name_hidden" value="' . ((!empty($this->_allergen)) ? $this->_allergen['allergy_name'] : '') . '"/>';
			$html .= '<label for="allergen_name">' . __('Allergen name', 'allergens-dietary-ictoria') . '</label>';
			$html .= '<input type="text" class="update_" name="allergen_name" id="allergen_name" value="' . ((!empty($this->_allergen)) ? $this->_allergen['allergy_name'] : '') . '" maxlength="50" disabled required/>';
			
			//Type dropdown.
			$html .= '<div class="dropdown-row">';
			$html .= '<label for="type">' . __('Type', 'allergens-dietary-ictoria') . '</label>';
			$html .= '<select name="type" id="type" class="type">';
			$html .= self::do_dropdown();
			$html .= '</select>';
			$html .= '</div>';

			//description input
			$html .= '<label for="allergen_description">' . __('Allergen description', 'allergens-dietary-ictoria') . '</label>';
			$html .= '<textarea class="update_" name="allergen_description" id="allergen_description" style="width: 300px; min-height: 100px; resize: none;" maxlength="255" disabled>'. ((!empty($this->_allergen)) ? $this->_allergen['allergy_description'] : '') . '</textarea><br><br>';
			
			//submit
			$html .= '</div><input disabled type="submit" class="update_ button button-primary save" name="submit" class="button button-primary" value="' . __('Update', 'allergens-dietary-ictoria') . '"/><br><br></fieldset>';
			
			//Image figure below. Max image size is 40x40.
			$html .= '<fieldset class="inline-edit-col-right drag-drop-buttons"><div class="item">';
			$html .= '<figure style="text-align: center;"><img class="update_ allergen_icon_img" style="max-height: 40px; max-width: 40px;" disabled id="allergen_icon_img" src="' . ((!empty($this->_allergen)) ? $this->_allergen['attachment_path'] : "") . '" alt="' . ((!empty($this->_allergen)) ? $this->_allergen['attachment_name'] : "") . '">';
			$html .= '<figcaption style="font-size: 10px; font-weight: bold; color: gray;">' . __('Max size of an icon is 40x40 pixels.', 'allergens-dietary-ictoria') . '</figcaption>';
			$html .= '<br><label class="label-quick-edit wp-core-ui button">';
			$html .= '<input type="file" class="update_ allergen_icon_file_input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" name="allergen_icon" id="allergen_icon_file_input" disabled>';
			$html .= '<input type="hidden" class="update_" name="allergen_icon_hidden" value="' . ((!empty($this->_allergen)) ? $this->_allergen['attachment_name'] : "") . '" disabled>';
			$html .= '<span>'.__('Set image', 'allergens-dietary-ictoria') .'</span>';
			$html .= '</label>';
			$html .= '</figure>';

			//close fieldsets
			$html .= '</div><br></fieldset>';
			$html .= '</div></fieldset">';
		} else {

			//Allergen name and style elements 
			$html = '<table style="width: 100%"><fieldset id="the-list" class="inline-edit-product.quick-edit-row">';
			$html .= '<fieldset class="inline-edit-col-left"><div class="inline-edit-row">';
			$html .= '<legend style="font-weight: bold;" class="inline-edit-legend">' . __("Add allergen", "allergens-dietary-ictoria") . '</legend><br>';
			$html .= '<div class="inline-edit-wrapper" aria-labelledby="quick-edit-legend">';
			$html .= '<tr><th class="align-header" scope="row"><label for="allergen_name">' . __('Allergen name', 'allergens-dietary-ictoria') . '</label></th>';
			$html .= '<td><input type="text" name="allergen_name" id="allergen_name" style="width: 100%;" value="' . ((!empty($this->_allergen)) ? $this->_allergen['allergy_name'] : '') . '" required/></td></tr>';
			
			//Type dropdown.
			$html .= '<tr><th class="align-header" scope="row"><label for="type">' . __('Type', 'allergens-dietary-ictoria') . '</label></th>';
			$html .= '<td><select name="type" id="type" style="width: 100%" required>';
			$html .= self::do_dropdown();
			$html .= '</select></td></tr>';

			//description input
			$html .= '</fieldset></table><br>';
			$html .= '<label class="bold" for="allergen_description"><span style="display: block; margin-bottom: 10px;">' . __('Allergen description', 'allergens-dietary-ictoria') . '</span></label>';
			$html .= '<textarea class="update_" name="allergen_description" id="allergen_description" style="width: 100%; max-width: 400px; min-height: 100px; resize: none;" maxlength="255">'. ((!empty($this->_allergen)) ? $this->_allergen['allergy_description'] : '') . '</textarea>';
			
			//Image figure below. Max image size is 40x40.
			$html .= '<table><div class="item"><fieldset class="inline-edit-col-right drag-drop-buttons"><div class="item">';
			$html .= '<tr class="item-row">';
			$html .= '<td><label class="label-quick-edit wp-core-ui button">';
			$html .= '<input type="file" class="update_ allergen_icon_file_input" accept="image/png, image/jpeg, image/jpg, image/webp, image/svg+xml" name="allergen_icon" id="allergen_icon_file_input">';
			$html .= '<input type="hidden" class="update_" name="allergen_icon_hidden" value="' . ((!empty($this->_allergen)) ? $this->_allergen['attachment_name'] : "") . '">';
			$html .= '<span>'.__('Set image', 'allergens-dietary-ictoria') .'</span>';
			$html .= '</label></td>';
			$html .= '<td><figure style="text-align: center;"><th class="item-header"><br><img class="update_ add_allergen_icon_img" id="allergen_icon_img"  style="max-height: 40px; max-width: 40px;" src="' . get_home_url() . '/wp-content/plugins/allergens-dietary-ictoria/assets/icons/no_icon_selected.png" alt="no_icon_selected.png">';
			$html .= '<figcaption style="font-size: 10px; max-width: 200px; font-weight: bold; color: gray;">' . __('Max size of an icon is 40x40 pixels.', 'allergens-dietary-ictoria') . '</figcaption></figure></td></tr></div>';
			$html .= '</fieldset></table>';
			
			//submit
			$html .= '<td><br><br><input type="submit" name="submit" class="button button-primary" value="' . __('Add allergen', 'allergens-dietary-ictoria') . '"/></td>';
			
			//close fieldset and table
			$html .= '</div>';
		}

		echo $html;
	}


	/**
	 * @param array $data
	 * @brief This method submits the form data to the DB.
	 * @throws Exception if the file is not a valid image
	 * @return void
	 * @author V.B.
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	public function submit(array $data)
	{
		$data = $this->sanitize($data);

		$file_type_input = wp_check_filetype($data['allergen_icon']['name']);
		$valid_icon = in_array($file_type_input['type'], $this->MIME_TYPES) ? true : false;
		$showOnPage = "allergens-dietary-add-allergen";
		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
		$empty_file_input = empty($data['allergen_icon']['name']) ? true : false;

		// DB Query's
		$all_query = Allergens_Dietary_Ictoria_Allergen_Queries::getInstance();
		$att_query = Allergens_Dietary_Ictoria_Attachment_Queries::getInstance();
		$all_att_query = Allergens_Dietary_Ictoria_Allergy_Attachment_Queries::getInstance();

		$attachment_exists = $att_query->checkAttachmentExists($data['allergen_icon']['name']);

		// This is temporary, should be WP conform errors!'
		if (empty($data) || !isset($data)) {
			self::$_type = Notice_Types::ERROR;
			self::$message = __('Form has not been set!', 'allergens-dietary-ictoria');
			$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
			$notice->display_admin_notice(self::$_type,self::$message);
			return;
		} elseif (empty($data['allergen_name'])) {
			self::$_type = Notice_Types::ERROR;
			self::$message = __('Allergen Name Can\'t be empty or blank!','allergens-dietary-ictoria');
			$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
			return;
		}

		// ADD ALLERGEN PAGE!
		if (!isset($data['allergen_name_hidden']) && $page === $showOnPage) {

			$no_icon_selected = false;

			if ($all_query->checkAllergenExists($data['allergen_name'])) {
				self::$_type = Notice_Types::ERROR;
				self::$message = __('Allergen name already exists','allergens-dietary-ictoria');
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
				return;
			}

			// Default image
			if ($empty_file_input) {
				$imagePath = get_home_url() . '/wp-content/plugins/allergens-dietary-ictoria/assets/icons/no_icon_selected.png';
				$imageName = sanitize_file_name(basename($imagePath));
				$data['allergen_icon'] = [
					'name' => $imageName,
					'tmp_name' => $imagePath,
					'type' => 'image/png',
				];
				$no_icon_selected = true;
			}

			// This is temporary, should be WP conform errors!
			if (!$valid_icon && !$no_icon_selected) {
				self::$_type = Notice_Types::ERROR;
				self::$message = __('The file is not a valid image. Supported image types are: ', 'allergens-dietary-ictoria') . implode(', ', $this->MIME_NAMES);
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
				return;
			}

			$all_query->addAllergens($data);
			if (!$no_icon_selected && !$attachment_exists) { // Don't add attachment to the table, image already exists there.
				$att_query->addAttachment($data['allergen_icon']);
			}
			$all_att_query->addAllergyAttachment($data);

			// Temporary feedback, should be of WP conform.
			self::$_type= Notice_Types::SUCCESS;
			self::$message = __('Succesfully added new allergen: ' . $data['allergen_name'] . '.', 'allergens-dietary-ictoria');
			$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
			$notice->display_admin_notice(self::$_type,self::$message);
			// return;
		} else { // QUICK EDIT PAGE!
			// This is temporary, should be WP conform errors!
			if ($all_query->checkAllergenExists($data['allergen_name']) && $data['allergen_name_hidden'] !== $data['allergen_name']) {
				self::$_type = Notice_Types::ERROR;
				self::$message = __('Allergen name already exists', 'allergens-dietary-ictoria');
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
				return;
			}
			if (!$valid_icon && !$empty_file_input) {
				self::$_type = Notice_Types::ERROR;
				self::$message = __('The file is not a valid image. Supported image types are: ' . implode(', ', $this->MIME_NAMES), 'allergens-dietary-ictoria') . '.';
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
				return;
			}

			$all_query->updateAllergens($data);
			if ($empty_file_input) {
				self::$_type = Notice_Types::SUCCESS;
				self::$message = __('Allergen successfully updated.', 'allergens-dietary-ictoria');
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
				return;
			} // update only the new allergen data when not uploading a new image. name, description etc.
			

			if ($attachment_exists) { // if the attachment exists, set to existing img and only remove attachment when not used.
				$all_att_query->updateAllergyAttachment($data['allergen_name'], $data['allergen_icon']['name']);
				if ($data['allergen_icon_hidden'] !== 'no_icon_selected.png' && !$all_att_query->attachmentIsUsed($data['allergen_icon_hidden'])) {
					$att_query->deleteAttachment($data['allergen_icon_hidden']);
				}
				self::$_type = Notice_Types::SUCCESS;
				self::$message = __('Allergen successfully updated.', 'allergens-dietary-ictoria');
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
			} else {
				// Prevent losing no_icon_selected.png as image in DB, and if there are multiple of the old img don't change all of them.
				if ($data['allergen_icon_hidden'] === 'no_icon_selected.png' || $all_att_query->checkMultipleAttachmentsExists($data['allergen_icon_hidden'])) {
					$att_query->addAttachment($data['allergen_icon']);
					$all_att_query->updateAllergyAttachment($data['allergen_name'], $data['allergen_icon']['name']);
				} else {
					$att_query->updateAttachment($data['allergen_icon'], $data['allergen_icon_hidden']);
				}
				self::$_type = Notice_Types::SUCCESS;
				self::$message = __('Allergen successfully updated.', 'allergens-dietary-ictoria');
				$notice = Allergens_Dietary_Ictoria_Notices::getInstance();
				$notice->display_admin_notice(self::$_type,self::$message);
			}
		}
	}

	/**
	 * @param array $data
	 * @brief This method sanitizes the form data for the DB.
	 * @return array $data
	 * @author V.B.
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	public function sanitize(array $data)
	{
		$data['allergen_name'] = sanitize_text_field(wp_unslash($data['allergen_name']));
		$data['allergen_description'] = sanitize_text_field(wp_unslash($data['allergen_description']));
		$data['type'] = absint(sanitize_text_field(wp_unslash($data['type'])));
		$data['allergen_icon']['name'] = sanitize_file_name($data['allergen_icon']['name']);

		return $data;
	}
	private function do_dropdown()
	{
		$html = '';

		$ShowOnPage = ["allergens-dietary-add-allergen"];
		$page = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';

		if (in_array($page, $ShowOnPage, true)) {
			if (empty($this->_allergen['is_allergy'])) {
				$html = '<option value="1" selected>' . __('Allergy', 'allergens-dietary-ictoria') . '</option>';
				$html .= '<option value="0">' . __('Dietary restriction', 'allergens-dietary-ictoria') . '</option>';
				return $html;
			}
		}

		if (!empty($this->_allergen)) {
			if ($this->_allergen['is_allergy'] == 1) {
				$html = '<option value="1" class="option" selected disabled>' . __('Allergy', 'allergens-dietary-ictoria') . '</option>';
				$html .= '<option value="0" class="option" disabled>' . __('Dietary restriction', 'allergens-dietary-ictoria') . '</option>';
			} else {
				$html = '<option value="1" class="option" disabled>' . __('Allergy', 'allergens-dietary-ictoria') . '</option>';
				$html .= '<option value="0" class="option" selected disabled>' . __('Dietary restriction', 'allergens-dietary-ictoria') . '</option>';
			}
		}
		return $html;
	}
}