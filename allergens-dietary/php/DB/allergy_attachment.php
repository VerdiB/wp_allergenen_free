<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Allergy_Attachment_Queries
{
	private static ?self $_instance = null;

	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new static();
		}
		return self::$_instance;
	}

	private function __construct() {}

	public function getallergyAttachment(string $allergy_name, bool $isForm = true)
	{
		global $wpdb;

		$sql = "";

		if ($isForm) {
			$sql = $wpdb->prepare(
				"SELECT a.allergy_name, a.allergy_description, a.is_allergy, aa.attachment_name, att.attachment_path
				FROM  {$wpdb->prefix}allergens_dietary_ictoria_allergy_attachment as aa
				JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy as a
				ON aa.allergy_name = a.allergy_name
				JOIN {$wpdb->prefix}allergens_dietary_ictoria_attachments as att
				ON aa.attachment_name = att.attachment_name
				WHERE aa.allergy_name = %s",
				$allergy_name
			);
		} else {
			$sql = $wpdb->prepare(
				"SELECT a.allergy_name, a.allergy_description, att.attachment_path
			FROM  {$wpdb->prefix}allergens_dietary_ictoria_allergy_attachment as aa
			JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy as a
			ON aa.allergy_name = a.allergy_name
			JOIN {$wpdb->prefix}allergens_dietary_ictoria_attachments as att
			ON aa.attachment_name = att.attachment_name
			WHERE aa.allergy_name = %s",
				$allergy_name
			);
		}

		return $wpdb->get_row($sql, ARRAY_A);
	}

	public function getAllAllergyAttachmments($skip_default = false)
	{
		global $wpdb;
		$table = "{$wpdb->prefix}allergens_dietary_ictoria_allergy_attachment";

		$sql = "SELECT al.allergy_name, al.allergy_description, al.is_allergy,
			al.is_default_option, att.attachment_name, att.attachment_path
            FROM %i as aa
            JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy as al
            ON aa.allergy_name = al.allergy_name
			JOIN {$wpdb->prefix}allergens_dietary_ictoria_attachments as att
			ON aa.attachment_name = att.attachment_name
			WHERE al.is_active = 1
			";
		if ($skip_default) {
			$sql .= " AND al.is_default_option = 0";
		}
		$sql .= " ORDER BY al.is_allergy DESC, al.allergy_name ASC";

		$prepared_sql = $wpdb->prepare($sql, $table);


		return $wpdb->get_results($prepared_sql, ARRAY_A);
	}

	public static function allergy_connection(array $result)
	{

		global $wpdb;

		//get database table
		$table_allergens_icons = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		//insert allergies
		foreach ($result as $value) {
			$wpdb->insert(
				$table_allergens_icons,
				array(
					'attachment_name' => $value['name'],
					'allergy_name' => $value['title'],
				)
			);
		}
	}
}
