<?php
// exit if user can access this file directly
if (!defined('ABSPATH')) {
	exit;
}

'
/*
Plugin Name: Allergens and Dietary
Plugin URI:
Version:     0.18.5.1
Description: Adds Allergens and Dietary options that can be used with WooCommerce products.
Requires at least: 6.3.1
Requires PHP: 7.4
Author:      Ictoria.nl
Author URI:  http://ictoria.nl
License:     GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.html
Text Domain: allergens-dietary
Domain Path: /languages/
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

// Set constant values that are used to retain file location references
define('ALLERGENS_DIETARY_NAME', 'allergens-dietary');
define('ALLERGENS_DIETARY_FILE', __FILE__); // contains the full path to the plugin file
define('ALLERGENS_DIETARY_DIRNAME', __DIR__);
define('ALLERGENS_DIETARY_BASE', plugin_basename(__FILE__)); // contains the path: plugin_directory/plugin_file
// Check if WooCommerce is active and store the result in a constant value
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
	define('ALLERGENS_DIETARY_WC_ACTIVE', true);
	define('ALLERGENS_DIETARY_WC_DIRNAME', dirname(__FILE__, 2) . '/woocommerce');
	// define('ALLERGENS_DIETARY_WC_DIRNAME', dirname(__FILE__, 2) );
} else {
	define('ALLERGENS_DIETARY_WC_ACTIVE', false);
}

// load file with generic static methods
require_once ALLERGENS_DIETARY_DIRNAME . '/php/activator.php';
add_action('plugins_loaded', array('Allergens_Dietary_Activator', 'load_textdomain'));

/*
Plugin Name: Allergens and Dietary
Text Domain: allergens-dietary
Domain Path: /languages/
*/
class load_language
{
	public function __construct()
	{
		add_action('plugins_loaded', array($this, 'translation_init'));
	}

	function translation_init()
	{
		load_plugin_textdomain('allergens-dietary', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}
}

$nl_NL = new load_language();
$en_US = new load_language();

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
			// save chosen settings in the allergens_dietary_ictoria_settings(WP options table)
			// add the initial_setup_done option to allergens_dietary_ictoria_settings (value: true) to prevent this popup from showing on every activation after the first
		}
		$folderName = '/var/www/html/wp-content/plugins/allergens-dietary/cache'; // Geef het juiste pad naar de map op

		if (!file_exists($folderName)) {

			mkdir("/var/www/html/wp-content/plugins/allergens-dietary/cache");

		}

		$map = '/var/www/html/wp-content/plugins/allergens-dietary/cache'; // Geef het juiste pad naar de map op
		$file = '/cache.php';

		$completepath = $map . $file;

		if (!file_exists($completepath)) {
			Allergens_Dietary_Activator::activate();
		}

		$inhoud = "<?php\n";
		$inhoud .= "// this is an automaticly generated PHP-file\n";
	}

	// function that runs when the deactivation hook is called
	public static function on_deactivation()
	{
		// cookies might be needed if the filter needs to store previous search settings, and will have to be removed if this function is called

		// temporary delete_option for testing without having to uninstall/reinstall. This code is also found in the uninstall.php file of this plugin
		// delete_option('allergens_dietary_ictoria_settings');
		// delete_option('allergens_dietary_ictoria_options');
	}
}
register_activation_hook(ALLERGENS_DIETARY_BASE, array('Allergens_Dietary_Startup', 'on_activation'));
register_deactivation_hook(ALLERGENS_DIETARY_BASE, array('Allergens_Dietary_Startup', 'on_deactivation'));

if (ALLERGENS_DIETARY_WC_ACTIVE) {
	if (is_admin()) {
		// class is used to add the plugin to the list of integrated plugins that WooCommerce uses
		class Allergens_Dietary_Wc_Integration_Startup
		{
			public function __construct()
			{
				add_action('plugins_loaded', array($this, 'init_integration'));
			}

			public function init_integration()
			{
				// Check if the WC_Integration class exists

				if (! class_exists('WC_Integration')) {
					// include_once ALLERGENS_DIETARY_DIRNAME . '/php/wc_integration.php';
					// add_filter('woocommerce_integrations', array($this, 'add_integration'));
					require_once ALLERGENS_DIETARY_DIRNAME . '/php/notice/notice.php';
					$level = Notice_Types::ERROR;
					$message = __('%1$sThe WooCommerce Integration class was not found. Please make sure WooCommerce is installed correctly%2$s', 'allergens-dietary');
					Allergens_Dietary_Notices::getInstance()->display_admin_notice($level, $message);
				}
			}
		}
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
	Allergens_Dietary_Plugin_Menu::instance();
} else {
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/errors/error_notice.php';
	// WooCommerce is not installed or inactive, show error message
	require_once ALLERGENS_DIETARY_DIRNAME . '/php/errors/error_notice.php';

	$level = Notice_Types::ERROR;
	$message = __('%1$sWooCommerce is inactive or not installed. Please install & activate WooCommerce%2$s', 'allergens-dietary');
	Allergens_Dietary_Notices::getInstance()->display_admin_notice($level, $message);
}
// Add a filter to modify the HTML for the auto-update setting link
add_filter('plugin_auto_update_setting_html', 'my_plugin_auto_update_link_html', 10, 3);

// get auto-update links
function my_plugin_auto_update_link_html($html, $plugin_file, $plugin_data)
{
	if ($plugin_file === 'allergens-dietary/allergens-dietary.php') {
		$auto_updates_enabled = get_site_option('auto_update_plugins', array());

		// Check if the current plugin is in the list of auto-updated plugins
		if (in_array($plugin_file, $auto_updates_enabled)) {
			$html = '<a href="#" class="my-plugin-toggle-auto-update" data-plugin="' . esc_attr($plugin_file) . '" data-action="disable">Auto-updates uitschakelen</a>';
		} else {
			$html = '<a href="#" class="my-plugin-toggle-auto-update" data-plugin="' . esc_attr($plugin_file) . '" data-action="enable">Auto-updates inschakelen</a>';
		}
	}
	return $html;
}

// Enqueue the JavaScript file for handling auto-update toggles
add_action('admin_enqueue_scripts', 'my_plugin_enqueue_admin_script');
function my_plugin_enqueue_admin_script()
{
	wp_enqueue_script('my-plugin-admin-js', plugins_url('admin.js', __FILE__), array('jquery'), null, true);
	wp_localize_script('my-plugin-admin-js', 'myPluginAjax', array(
		'ajax_url' => admin_url('admin-ajax.php'),
		'nonce' => wp_create_nonce('my_plugin_auto_update_nonce')
	));
}

// Handle the Ajax request to toggle auto-updates
add_action('wp_ajax_my_plugin_toggle_auto_update', 'my_plugin_toggle_auto_update');
function my_plugin_toggle_auto_update()
{
	check_ajax_referer('my_plugin_auto_update_nonce', 'security');

	// Check if the plugin and action parameters are set
	if (isset($_POST['plugin']) && isset($_POST['toggle_action'])) {
		$plugin = sanitize_text_field($_POST['plugin']);
		$action = sanitize_text_field($_POST['toggle_action']);

		$auto_updates = get_site_option('auto_update_plugins', array());

		// Enable auto-updates if requested
		if ($action === 'enable') {
			if (!in_array($plugin, $auto_updates)) {
				$auto_updates[] = $plugin;
				update_site_option('auto_update_plugins', $auto_updates);
			}
			// Disable auto-updates if requested
		} elseif ($action === 'disable') {
			if (in_array($plugin, $auto_updates)) {
				$auto_updates = array_diff($auto_updates, array($plugin));
				update_site_option('auto_update_plugins', $auto_updates);
			}
		}

		wp_send_json_success();
	} else {
		wp_send_json_error();
	}
}

// Filter to control auto-update settings for the plugin
add_filter('auto_update_plugin', 'my_plugin_auto_update_control', 10, 2);
function my_plugin_auto_update_control($update, $item)
{
	if ($item->plugin === 'allergens-dietary/allergens-dietary.php') {
		return get_site_option('auto_update_plugins', array()) ? true : false;
	}

	return $update;
}

// Enqueue Thickbox scripts and styles
add_action('admin_enqueue_scripts', 'load_thickbox');
function load_thickbox()
{
	wp_enqueue_script('thickbox');
	wp_enqueue_style('thickbox');
}

// Add a "View Details" link for the changelog
add_filter('plugin_row_meta', 'add_changelog_view_link', 10, 2);
function add_changelog_view_link($plugin_meta, $plugin_file)
{
	if ($plugin_file == 'allergens-dietary/allergens-dietary.php') {
		$plugin_meta[] = '<a href="' . esc_url(admin_url('admin-ajax.php?action=view_changelog&TB_iframe=true&width=600&height=550')) . '" class="thickbox">Details bekijken</a>';
	}
	return $plugin_meta;
}

// Ajax handler for displaying changelog in Thickbox
add_action('wp_ajax_view_changelog', 'display_changelog_in_thickbox');
function display_changelog_in_thickbox()
{
	echo '<div class="wrap">';
	echo '<h1>Changelog</h1>';
	echo '<div>';
	echo wpautop(get_plugin_changelog());
	echo '</div>';
	echo '</div>';
	exit;
}

// // Changelog voor admin menu
// add_action('admin_menu', 'allergens_dietary_changelog_menu');
// function allergens_dietary_changelog_menu() {
//     add_menu_page('Changelog', 'Changelog', 'manage_options', 'allergens-dietary-changelog', 'allergens_dietary_changelog_pagina');
// }

function get_plugin_changelog()
{
	$readme_file = plugin_dir_path(__FILE__) . 'readme.txt';

	if (file_exists($readme_file)) {
		$content = file_get_contents($readme_file);
		$changelog = '';

		$changelog_start = strpos($content, '== Changelog ==');
		if ($changelog_start !== false) {
			$changelog_start += strlen('== Changelog ==');
			$changelog_end = strpos($content, '==', $changelog_start);
			$changelog = substr($content, $changelog_start, $changelog_end - $changelog_start);
		}

		return trim($changelog);
	}

	return 'Changelog not found.';
}

// // Changelog voor admin menu
// function allergens_dietary_changelog_pagina() {
//     echo '<div class="wrap">';
//     echo '<h1>Changelog</h1>';
//     echo '<div>';
//     echo wpautop(get_plugin_changelog()); 
//     echo '</div>';
//     echo '</div>';
// }