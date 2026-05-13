<?php

if (!defined('ABSPATH')) {
    exit;
}

/*
Plugin Name: Allergens and Dietary
Text Domain: allergens-dietary
Domain Path: /languages/
*/
class Allergens_Dietary_load_language
{
	public function __construct()
	{
		add_action('init', array($this, 'translation_init'));
		add_action('plugins_loaded', array($this, 'translation_init'));

	}


	public function translation_init()
	{
		load_plugin_textdomain('allergens-dietary', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

}


?>