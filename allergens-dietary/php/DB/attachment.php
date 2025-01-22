<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Attachment_Queries
{
	private static array $instances;

	public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

	protected function __construct(){}

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
