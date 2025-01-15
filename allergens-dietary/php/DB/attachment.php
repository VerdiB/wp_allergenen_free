<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Attachment_Queries
{
	private static ?self $_instance = null;
	private const PATH = ALLERGENS_DIETARY_DIRNAME . '/assets/icons/custom/';
	private string $_url;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct()
	{
		$this->_url = get_home_url() . '/wp-content/plugins/allergens-dietary/assets/icons/custom/';
	}

}
