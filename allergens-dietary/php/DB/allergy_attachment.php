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

	public function addAllergyAttachment(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		$wpdb->insert(
			$table_name,
			array(
				'allergy_name' => $data['allergen_name'],
				'attachment_name' => $data['allergen_icon']['name'],
			)
		);

		return (isset($wpdb->insert_id)) ? true : false;
	}

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

	public function updateAllergyAttachment(string $old_allergy_name, string $attachment)
	{

		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		return $wpdb->update(
			$table_name,
			array(
				'attachment_name' => $attachment,
			),
			array(
				'allergy_name' => $old_allergy_name,
			)
		);
	}

	public function attachmentIsUsed(string $attachment)
	{
		global $wpdb;

		$table = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';
		
		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(attachment_name) FROM %i
			WHERE attachment_name = %s",
			array($table,$attachment)
		));

		return $count > 0;
	}

	public function checkAllergyAttachmentExists(string $allergy_name)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		$result = $wpdb->get_results( $wpdb->prepare(
			"SELECT attachment_name FROM %i WHERE allergy_name = %s",
			array($table_name, $allergy_name),
		));

		return (!empty($result)) ? true : false;
	}

	public function deleteAllergyAttachment(string $allergy)
	{
		global $wpdb;

		$table_aa = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';
		$table_am = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';
		$table_a = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$wpdb->query($wpdb->prepare(
			"DELETE aa, a, am 
			FROM %i AS aa
			JOIN %i AS a 
			ON a.allergy_name = aa.allergy_name
			JOIN %i as am
			ON am.attachment_name = aa.attachment_name
			WHERE aa.allergy_name = %s  
			AND a.is_default_option != 1",
			array($table_aa, $table_a, $table_am, $allergy)
		));
	}

	public function checkMultipleAttachmentsExists(string $attachment): bool
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		$count = $wpdb->get_var($wpdb->prepare(
			"SELECT COUNT(attachment_name) FROM %i
			WHERE attachment_name = %s",
			array($table_name, $attachment)
		));

		return $count > 1;
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
