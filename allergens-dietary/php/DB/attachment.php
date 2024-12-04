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
		$this->_url = get_home_url() . '/wp-content/plugins/allergens-dietary-ictoria/assets/icons/custom/';
	}

	public function addAttachment(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';

		$wpdb->insert(
			$table_name,
			array(
				'attachment_name' => $data['name'],
				'attachment_path' => $this->_url . $data['name'],
			)
		);

		$this->placeAttachment($data);

		return (isset($wpdb->insert_id)) ? true : false;
	}

	public function deleteAttachment( string $attachment ) {
		global $wpdb;
		$table_attachment = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';

		$sqlConnection = $wpdb->prepare(
			"DELETE FROM $table_attachment
     		WHERE attachment_name = %s",
    		$attachment
		);

		$wpdb->query($sqlConnection);

		$this->removeAttachment($attachment);
	}

	public function checkAttachmentExists(string $attachmentName)
	{
		if (empty($attachmentName)) {
			return;
		}

		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';

		$sql = $wpdb->prepare(
			"SELECT attachment_name FROM $table_name WHERE attachment_name = %s",
			$attachmentName
		);

		$result = $wpdb->get_row($sql, ARRAY_A);

		return (!empty($result)) ? true : false;
	}


	public function updateAttachment(array $data, string $oldName)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_attachments';
		
		$wpdb->update(
			$table_name,
			array(
				'attachment_name' => $data['name'],
				'attachment_path' => $this->_url . $data['name'],
			),
			array(
				'attachment_name' => $oldName,
			)
		);

		$this->placeAttachment($data);
		$this->removeAttachment($oldName);
	}
	/**
	 * @brief This method places an attachment in the plugin directories
	 * under assets/icons/custom
	 * @param array $data
	 * @since 1.0.0
	 * @date 11-9-2024
	 * @author Ictoria
	 */
	private function placeAttachment(array $data)
	{

		if (false === file_exists(self::PATH)) {
			mkdir(self::PATH, 0777, true);
		}

		$full_path = self::PATH . $data['name'];
		if (false === file_exists($full_path)) {
			move_uploaded_file($data['tmp_name'], $full_path);
		}
	}


	/**
	 * @brief This method removes an attachment from the plugin directories
	 * under assets/icons/custom
	 * @param string $attachmentName
	 * @since 1.0.0
	 * @date 6-11-2024
	 * @author Ictoria
	 */
	private function removeAttachment(string $attachmentName)
	{
		if (empty($attachmentName)) {
			return;
		}

		if (true === file_exists(self::PATH . $attachmentName)) {
			unlink(self::PATH . $attachmentName);
		}
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
