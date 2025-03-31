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

	public function addAllergyAttachment(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';

		$wpdb->insert(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
			$sql = $wpdb->get_row($wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
			$sql = $wpdb->get_row($wpdb->prepare(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
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

	public function getAllAllergyAttachmments()
	{
		global $wpdb;
		$allergy_attachment = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_attachment';
		$allergy = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$attachment = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';


		return $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT al.allergy_name, al.allergy_description, al.is_allergy,
				al.is_default_option, att.attachment_name, att.attachment_path
				FROM %i as aa
				JOIN %i as al
				ON aa.allergy_name = al.allergy_name
				JOIN %i as att
				ON aa.attachment_name = att.attachment_name
				WHERE al.is_active = 1
				ORDER BY al.is_allergy DESC, al.allergy_name ASC" 
			,array($allergy_attachment, $allergy, $attachment)),
			ARRAY_A);
	}

}
