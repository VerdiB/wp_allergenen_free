<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_License_Info
 * @brief Class that creates the info
 * the user can see the info
 * @author ictoriabv
 * @date 12-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Info
{
    protected static ?self $_instance = null;



    public function showInfo()
    {

        Allergens_Dietary_Activator::load_style();
        Allergens_Dietary_Activator::enqueue_styles();
        
        //flexbox voor tabs
        $html = '<div id="info_grid" class="nav-tab-wrapper">';
        $html .= '<div><h1 class="premium">' . __("PREMIUM  [Requires licence]", "allergens-dietary") . '</h1>';
        $html .= '<ol>';
        $html .= '<li class="contains">' . __('Updating allergies', 'allergens-dietary') . '</li>';
        $html .= '<li class="contains">' . __("Changing allergy themes", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Deleting allergies", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Adding allergies", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Custom look on product", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Custom look in store", "allergens-dietary") . '</li>';
        $html .= ' </ol>';
        $html .= ' </div>';
        $html .= '<div><h1 class="free">' . __("FREE VERSION  [Standard]", "allergens-dietary") . '</h1>';
        $html .= ' <ol>';
        $html .= '<li class="contains">' . __("Connecting allergies to products", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Wordpress theme fiendly styles", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("An allergen overview", "allergens-dietary") . '</li>';
        $html .= '<li class="contains">' . __("Turning the use of allergies on/off", "allergens-dietary") . '</li>';
        $html .= '      </ol>';
        $html .= '  </div>';
        $html .= '  </div>';
        $html .= ' <br> <br>';
        echo $html;
        //moet nog aangepast worden in css
    }

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getStyles()
    {
        wp_register_style('allergens-dietary-css', plugins_url(ALLERGENS_DIETARY_NAME . '/assets/css/allergens-dietary.css'));
        wp_enqueue_style('allergens-dietary-admin-css', plugins_url('assets/css/allergens-dietary.css', ALLERGENS_DIETARY_FILE));
    }
}