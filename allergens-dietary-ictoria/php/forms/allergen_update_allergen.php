<?php

// exit if user can access this file directly
if (!defined('ABSPATH')) {
    exit;
}

if (!interface_exists('I_Allergens_Dietary_Ictoria_Form')) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/forms/Iallergen_form.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Allergen_Queries')) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/allergen.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Attachment_Queries')) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/attachment.php';
}

if (!class_exists('Allergens_Dietary_Ictoria_Allergy_Attachment_Queries')) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/DB/allergy_attachment.php';
}

if (!enum_exists('Mime_Types')) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/lists/mime_types.php';
}

if ( ! class_exists( 'Allergens_Dietary_Ictoria_Notices' ) ) {
    require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/notice/notice.php';
}

/**
 * @brief This shows the tabs on add/update allergens .
 * @author T.K.
 * @since 1.0.0
 * @date 18-9-2024
 */

// if ( ! class_exists( 'Allergens_Dietary_Ictoria_Tabs' ) ) {
// 	require_once ALLERGENS_DIETARY_ICTORIA_DIRNAME . '/php/tabs/allergen_tabs.php';
// }

/********************************************************************/

class Allergens_Dietary_Ictoria_Update_Allergen_Form implements I_Allergens_Dietary_Ictoria_Form
{
    private array $MIME_TYPES;
    private array $MIME_NAMES;
    private array $_allergens;
    private static string $_message = '';

    public function __construct()
    {
        $this->MIME_TYPES = Mime_Types::get_mime_types();
        $this->MIME_NAMES = array_map(fn($case) => $case->name, Mime_Types::cases());
        $this->_allergens = Allergens_Dietary_Ictoria_Allergy_Attachment_Queries::getInstance()->getAllAllergyAttachmments(true);
    
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
        $html = '';

        $html .= '<h1>' . __('Update custom allergen icons', 'allergens-dietary-ictoria') . '</h1>';
        $html .= '<table>';
        $html .= '<thead>';
        $html .= '<tr>';
        $html .= '<th>' . __('Allergen', 'allergens-dietary-ictoria') . '</th>';
        $html .= '<th>' . __('Description', 'allergens-dietary-ictoria') . '</th>';
        $html .= '<th>' . __('Current Icon', 'allergens-dietary-ictoria') . '</th>';
        $html .= '<th>' . __('Upload New Icon', 'allergens-dietary-ictoria') . '</th>';
        $html .= '</tr>';
        $html .= '</thead>';
        $html .= '<tbody>';

        foreach ($this->_allergens as $allergen) {
            $html .= '<tr>';
            $html .= '<td>' . esc_html($allergen['allergy_name']) . '</td>';
            $html .= '<td>' . esc_html($allergen['allergy_description']) . '</td>';
            $html .= '<td>' . '<img style="max-height: 40px; max-width: 40px;" src="' . esc_html($allergen['attachment_path']) . '" alt="' . esc_html($allergen['attachment_name']) . '" </td>';
            $html .= '<td>';
            $html .= '<input type="hidden" name="allergen_icon_hidden[' . esc_attr($allergen['allergy_name']) . ']" value="' . ($allergen['attachment_name'] ? esc_attr($allergen['attachment_name']) : "") . '" >';
            $html .= '<input type="file" name="' . esc_attr($allergen['allergy_name']) . '" id="allergen_icon_' . esc_attr($allergen['allergy_name']) . '" >';
            $html .= '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody>';
        $html .= '</table>';
        $html .= '<input type="submit" name="submit" value="' . __('Update Icons', 'allergens-dietary-ictoria') . '" >';

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


        foreach ($data as $icon) {
            // check if file is an image and if it is not, skip it
            if (false === in_array($icon['type'], $this->MIME_TYPES)) {
                self::$_message = __('The new file: ' . $icon['name'] . ' is not a valid image.  Supported image types are: ' . implode(', ', $this->MIME_NAMES), 'allergens-dietary-ictoria') . '.';
                $notice = Allergens_Dietary_Ictoria_Notices::getInstance();
                $notice->display_admin_notice(Notice_Types::ERROR, __(self::$_message, 'allergens-dietary-ictoria'));		
    
                continue;
            }
            // check if file is in database already and if it is, skip it
            if (true === Allergens_Dietary_Ictoria_Attachment_Queries::getInstance()->checkAttachmentExists($icon['name'])) {
                self::$_message =__('The new file: ' . $icon['name'] . ' already exists.', 'allergens-dietary-ictoria');
                $notice = Allergens_Dietary_Ictoria_Notices::getInstance();
                $notice->display_admin_notice(Notice_Types::ERROR, __(self::$_message, 'allergens-dietary-ictoria'));		
    
                continue;
            }

            Allergens_Dietary_Ictoria_Attachment_Queries::getInstance()->updateAttachment($icon, $icon['oldName']);
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

        foreach ($data['allergen_icon_hidden'] as $key => $value) {
            $data['allergen_icon_hidden'][$key] = sanitize_file_name($value);
        }

        $tmpArr = array();

        foreach ($this->_allergens as $allergen) {
            //check if file is uploaded
            if (isset($data[$this->replace_spaces($allergen['allergy_name'])])) {
                //check and sanitize file name
                if (
                    is_array($data[$this->replace_spaces($allergen['allergy_name'])]) &&
                    !empty($data[$this->replace_spaces($allergen['allergy_name'])]['name'])
                ) {
                    $tmpArr[$allergen['allergy_name']]['name'] = sanitize_file_name($data[$this->replace_spaces($allergen['allergy_name'])]['name']);
                    $tmpArr[$allergen['allergy_name']]['tmp_name'] = $data[$allergen['allergy_name']]['tmp_name'];
                    $tmpArr[$allergen['allergy_name']]['oldName'] = $data['allergen_icon_hidden'][$allergen['allergy_name']];
                    $tmpArr[$allergen['allergy_name']]['type'] = $data[$allergen['allergy_name']]['type'];
                }
            }
        }

        $data = $tmpArr;
        unset($tmpArr);

        return $data;
    }

    private function replace_spaces(string $allergens): string
    {
        return (preg_match('/\s/', $allergens)) ? str_replace(' ', '_', $allergens) : $allergens;
        ;
    }
}
