<?php

if (!defined('ABSPATH')) {
	exit;
}

class Allergens_Dietary_Allergy_Attachment_Queries
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

	protected function __construct() {}

	public function getallergyAttachment(string $allergy_name, bool $isForm = true)
	{
		global $wpdb;

		$sql = "";
		$allergy_attachment = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';
		$allergy = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$attachments = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';
		if ($isForm) {
			$sql = $wpdb->get_row($wpdb->prepare(
				"SELECT a.allergy_name, a.allergy_description, a.is_allergy, aa.attachment_name, att.attachment_path
				FROM %i as aa
				JOIN %i as a
				ON aa.allergy_name = a.allergy_name
				JOIN %i as att
				ON aa.attachment_name = att.attachment_name
				WHERE aa.allergy_name = %s",
				array($allergy_attachment, $allergy, $attachments, $allergy_name)
			), ARRAY_A);
		} else {
			$sql = $wpdb->get_row($wpdb->prepare(
				"SELECT a.allergy_name, a.allergy_description, att.attachment_path
			FROM  %i as aa
			JOIN %i as a
			ON aa.allergy_name = a.allergy_name
			JOIN %i as att
			ON aa.attachment_name = att.attachment_name
			WHERE aa.allergy_name = %s",
				array($allergy_attachment, $allergy, $attachments, $allergy_name)
			),ARRAY_A);
		}

		return $sql;
	}

	public function getAllAllergyAttachmments($skip_default = false)
	{
		global $wpdb;
		$allergy_attachment = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';
		$allergy = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$attachment = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';
		$sql = "SELECT al.allergy_name, al.allergy_description, al.is_allergy,
			al.is_default_option, att.attachment_name, att.attachment_path
            FROM %i as aa
            JOIN %i as al
            ON aa.allergy_name = al.allergy_name
			JOIN %i as att
			ON aa.attachment_name = att.attachment_name
			WHERE al.is_active = 1
			";
		if ($skip_default) {
			$sql .= " AND al.is_default_option = 0";
		}
		$sql .= " ORDER BY al.is_allergy DESC, al.allergy_name ASC";

		return $wpdb->get_results(
			$wpdb->prepare($sql // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			,array($allergy_attachment, $allergy, $attachment)),
			ARRAY_A);
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
