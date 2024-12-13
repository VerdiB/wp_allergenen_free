<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Attachment_Queries
{
	protected static ?self $_instance = null;
	protected const PATH = ALLERGENS_DIETARY_DIRNAME . '/assets/icons/custom/';
	protected string $_url;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function __construct()
	{
		$this->_url = get_home_url() . '/wp-content/plugins/allergens-dietary/assets/icons/custom/';
	}

	public static function attachment_insert(array $result)
	{
		global $wpdb;

		//get database table
		$table_icons = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';


		foreach ($result as $key => $value) {

			//insert allergies
			$wpdb->insert(
				$table_icons,
				array(
					'attachment_path' => $value['path'],
					'attachment_name' => $value['name'],
				)
			);
		}
	}
}
