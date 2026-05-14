<?php
// exit if user can access this file directly
if (!defined('ABSPATH')) {
	exit;
}

'
/*
Plugin Name: Allergens and Dietary
Requires plugins: woocommerce
Plugin URI:
Version:     0.19.1.3
Description: Adds Allergens and Dietary options that can be used with WooCommerce products.
Author:      Verdi-B
License:     GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: allergens-dietary
Domain Path: /languages/
Requires at least: 6.3.1
Requires PHP: 7.4
WC Tested Up To: 9.3.3

*/
';

__('Adds Allergens and Dietary options that can be used with WooCommerce products.', 'allergens-dietary');

// "Allergens and Dietary" is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// any later version.
//
// "Allergens and Dietary" is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with "Allergens and Dietary". If not, see https://www.gnu.org/licenses/gpl-3.0.html


/**
 * @brief This function handles dependencies in the old way if the user has an old version of WordPress
 * @author Verdi-B
 * @date 11-12-2024
 * @since 0.18.5.1
 */
 define('ALLERGENS_DIETARY_DIRNAME', plugin_dir_path(__FILE__));
 define('ALLERGENS_DIETARY_FILE', __FILE__); // contains the full path to the plugin file
 define('ALLERGENS_DIETARY_VERSION', '0.19.1.3'); //version constant of the plugin

// Set constant values that are used to retain file location references
define('ALLERGENS_DIETARY_NAME', 'allergens-dietary');
define('ALLERGENS_DIETARY_BASE', plugin_basename(__FILE__)); // contains the path: plugin_directory/plugin_file
// Check if WooCommerce is active and store the result in a constant value
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	define('ALLERGENS_DIETARY_WC_ACTIVE', true);
	define('ALLERGENS_DIETARY_WC_DIRNAME', plugin_basename(ALLERGENS_DIETARY_NAME.'../woocommerce'));
} else {
	define('ALLERGENS_DIETARY_WC_ACTIVE', false);
}

// load file with generic static methods
require_once ALLERGENS_DIETARY_DIRNAME . '/php/activator.php';
// add_action('plugins_loaded', array('Allergens_Dietary_Activator', 'load_textdomain'));


if (!function_exists('is_plugin_active')) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php'; 	
}

if (! class_exists('Allergens_Dietary_Wc_Integration_Startup')) {
    require_once ALLERGENS_DIETARY_DIRNAME . '/php/woocommerce/woo_integration.php';
}

// if (! class_exists('Allergens_Dietary_Startup')) {
//     require_	once ALLERGENS_DIETARY_DIRNAME . '/php/misc/startup.php';
// }


class Allergens_Dietary_Startup
{
	// function that runs when the activation hook is called
	public static function on_activation()
	{
		// show popup asking for certain setting options if this is the first activation after installing the plugin.
		if (!isset($settings['initial_setup_done'])) {
			// show popup asking wether or not the user wants to automatically export all relevant product data on uninstall
			// tell user (within popup) that above setting can be set at all times from the plugin settings menu
			// save chosen settings in the allergens_dietary_settings(WP options table)
			// add the initial_setup_done option to allergens_dietary_settings (value: true) to prevent this popup from showing on every activation after the first
		}
		$folderName = ALLERGENS_DIETARY_DIRNAME . '/logs'; // Geef het juiste pad naar de map op

		if (!file_exists($folderName)) {
			$activator = new Allergens_Dietary_Activator();
			$activator::activate();	
			// check voor een variant die in de wp dirs een map aanmaakt
			// Dit om te zorgen zodat wanneer een update gereleased word er geen errors komen door onze plugin

			//Todo: Als de plugin geüpdätet wordt, wordt wordt deze functie alsnog uitgevoerd, dit is niet de bedoeling. Het doel van de kaart, is deze wp_mkdir_p() functie veranderen met iets dat wel werkt.
			wp_mkdir_p($folderName);

		}

	}

	// function that runs when the deactivation hook is called
	public static function on_deactivation()
	{
		// cookies might be needed if the filter needs to store previous search settings, and will have to be removed if this function is called

		// temporary delete_option for testing without having to uninstall/reinstall. This code is also found in the uninstall.php file of this plugin
		// delete_option('allergens_dietary_settings');
		// delete_option('allergens_dietary_options');
	}


    public function prevent_Wrong_Activation(){
	if (!is_plugin_active('woocommerce/woocommerce.php')) {
		require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
		// WooCommerce is not installed or inactive, show error message
	
		$level = 'notice-error';
		$message = __('WooCommerce is inactive or not installed. Please install & activate WooCommerce', 'allergens-dietary');
		Allergens_Dietary_Notices::getInstance()->error_notice($level, $message);
		deactivate_plugins('allergens-dietary/allergens-dietary.php');
		$return_url = admin_url('plugins.php?plugin_status=all&paged=1&s');
		// $message = 'WooCommerce is inactive or not installed. Please install & activate WooCommerce <br><br> <a href="' . esc_url($return_url) . '">Go back</a>';
		wp_die(esc_html__('WooCommerce is inactive or not installed. Please install & activate WooCommerce ', 'allergens-dietary'));
		exit;
	}
	}

}

// $nl_NL = new Allergens_Dietary_load_language();
// $en_US = new Allergens_Dietary_load_language();

register_activation_hook(ALLERGENS_DIETARY_BASE , array('Allergens_Dietary_Startup', 'on_activation'));
register_deactivation_hook(ALLERGENS_DIETARY_BASE, array('Allergens_Dietary_Startup', 'on_deactivation'));

if (ALLERGENS_DIETARY_WC_ACTIVE) {
	if (is_admin()) {
		// class is used to add the plugin to the list of integrated plugins that WooCommerce uses
		
		$Allergens_Dietary_Wc_Integration_Startup = new Allergens_Dietary_Wc_Integration_Startup(__FILE__);
		// load and run the plugin admin files
		include_once ALLERGENS_DIETARY_DIRNAME . '/php/woocommerce/product_settings.php';
		Allergens_Dietary_Product_Settings::instance();
		// echo 'looking in the main file';

		include_once ALLERGENS_DIETARY_DIRNAME . '/php/activator.php';
	}
	// load generic files used by the plugin when active
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/woocommerce/products.php';
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/filter.php';

	Allergens_Dietary_Products::instance();
	Allergens_Dietary_Filter::instance();
	Allergens_Dietary_Activator::load_style();
	include_once ALLERGENS_DIETARY_DIRNAME . '/php/Allergens_Dietary_Plugin_Menu.php';

	if (false === file_exists(dirname(__FILE__, 2) . '/allergens-dietary-pro')){
		Allergens_Dietary_Plugin_Menu::instance();
	}
	elseif (false === is_plugin_active('allergens-dietary-pro/allergens-dietary-pro.php') && true === file_exists(dirname(__FILE__, 2) . '/allergens-dietary-pro')){
		Allergens_Dietary_Plugin_Menu::instance();	
	}
	
	
} else {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
	// WooCommerce is not installed or inactive, show error message

	if (!function_exists('is_plugin_active')) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
		// $allergens_dietary_startup = new Allergens_Dietary_Startup();
		// $allergens_dietary_startup->prevent_Wrong_Activation();
		// $allergens_dietary_startup = null;
}

