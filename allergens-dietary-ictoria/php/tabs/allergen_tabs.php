<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_Ictoria_License_Tabs
 * @brief Class that creates the tabs after you filled in the licence key
 * the user can click on the tabs to edit their allergens
 * @author T.K.
 * @date 12-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Ictoria_Tabs
{
    private static ?self $_instance = null;
    
    public function __construct()
	{
        //load js
		wp_register_script('Allergens_Dietary_Ictoria_Show_Allergens', plugins_url(ALLERGENS_DIETARY_ICTORIA_NAME.'/assets/js/script.js'), array('jquery'));
        wp_enqueue_script( 'Allergens_Dietary_Ictoria_Show_Allergens');

        //load css
        wp_register_style('allergens-dietary-ictoria-css', plugins_url(ALLERGENS_DIETARY_ICTORIA_NAME.'/assets/css/allergens-dietary-ictoria.css'));
	    wp_enqueue_style('allergens-dietary-ictoria-css');
	}

    public function showtabs()
    {
        //flexbox voor tabs
        $html = '<div id="tabs_flexbox" class="nav-tab-wrapper">';
        $html .= '<a class="nav-tab" href="'.get_admin_url(null, 'admin.php?page=allergens-dietary-show-allergens').'">' . __("See allergens", "allergens-dietary-ictoria") . '</a>';
        // $html .= '<a class="nav-tab" href="'.get_admin_url(null, 'admin.php?page=allergens-dietary-add-allergen').'">' . __("Create allergens", "allergens-dietary-ictoria") . '</a>';
        // $html .= '<a class="nav-tab" href="'.get_admin_url(null, 'admin.php?page=allergens-dietary-update-allergen').'">' . __("Change allergens", "allergens-dietary-ictoria") . '</a>';
        $html .= '<a class="nav-tab" href="'.get_admin_url(null, 'admin.php?page=allergens-dietary-Info').'">' . __("Info", "allergens-dietary-ictoria") . '</a>';
        $html .= '</div>';
        $html .= '<section id="added"></section> <br> <br>';
        echo $html;
        //moet nog aangepast worden in css
    }
    public function showpages()
    {
        //hier moet bijvoorbeeld een functie komen die de inhoud van de pagina verandert.
    }

    /*public function js_add_help_tab() {
        $screen = get_current_screen();
        print_r("hello");
    
        $screen->add_help_tab( array(
            'id'       => 'hello-world',
            'title'    => __( 'Hello World' ),
            'content'  => '<p>Lorem ipsum</p>',
            'priority' => 10,
        ) );
    }*/

    public static function getInstance()
    {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    public static function getStyles()
    {
        wp_register_style('allergens-dietary-ictoria-css', plugins_url(ALLERGENS_DIETARY_ICTORIA_NAME . '/assets/css/allergens-dietary-ictoria.css'));
        wp_enqueue_style('allergens-dietary-ictoria-admin-css', plugins_url('assets/css/allergens-dietary-ictoria.css', ALLERGENS_DIETARY_ICTORIA_FILE));
    }
}

/*$myInstance = new Allergens_Dietary_Ictoria_Tabs;
$myInstance->js_add_help_tab();

add_action('added', 'js_add_help_tab', 50);*/