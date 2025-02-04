<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_License_Tabs
 * @brief Class that creates the tabs after you filled in the licence key
 * the user can click on the tabs to edit their allergens
 * @author ictoriabv
 * @date 12-9-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Tabs
{
    private static ?self $_instance = null;
    
    public function __construct()
	{
        //load js
		wp_enqueue_script('Allergens_Dietary_Show_Allergens', plugin_dir_url(__FILE__) . 'assets/js/script.js', array('jquery'));

        //load css
        wp_enqueue_style('allergens-dietary-css', plugins_url('assets/css/allergens-dietary.css', __FILE__));
	}

    public function showtabs()
    {
        //flexbox voor tabs
        ?>
        <div id="tabs_flexbox" class="nav-tab-wrapper">
            <a class="nav-tab" href="<?php echo esc_attr(get_admin_url(null, 'admin.php?page=allergens-dietary-show-allergens'))?>"><?php echo esc_html_e("See allergens", "allergens-dietary") ?></a>
            <a class="nav-tab" href="<?php echo esc_attr(get_admin_url(null, 'admin.php?page=allergens-dietary-Info'))?>"><?php echo esc_html_e("Info", "allergens-dietary")?></a>
        </div>
        <section id="added"></section> <br> <br>
        <?php
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
        wp_enqueue_style('allergens-dietary-css', plugins_url('assets/css/allergens-dietary.css', __FILE__));
        wp_enqueue_style('allergens-dietary-admin-css', plugins_url('assets/css/allergens-dietary.css', __FILE__));
    }
}

/*$myInstance = new Allergens_Dietary_Tabs;
$myInstance->js_add_help_tab();

add_action('added', 'js_add_help_tab', 50);*/