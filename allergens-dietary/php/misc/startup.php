<?php

if (!defined('ABSPATH')) {
    exit;
}


// class that contains the functions that are used by the activation/deactivation/uninstall hooks

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


?>