<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class Allergens_Dietary_Allergen_Queries
 * @brief This class is a singleton that handles all the queries for the allergens and dietary restrictions DB table.
 * @author ictoriabv
 * @date 11-9-2024
 * @since 1.0.0
 */
class Allergens_Dietary_Allergen_Queries
{
	private static ?self $_instance = null;

	/**
	 * @brief This method returns the instance of the class.
	 * @return Allergens_Dietary_Allergen_Queries
	 * @author ictoriabv
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	public static function getInstance()
	{
		if (self::$_instance === null) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	private function __construct()
	{
	}

	/**
	 * @brief This method adds an allergen to the DB.
	 * @param array $data
	 * @return bool
	 * @since 1.0.0
	 * @date 11-9-2024
	 * @author ictoriabv
	 */
	public function addAllergens(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$wpdb->insert(
			$table_name,
			array(
				'allergy_name' => $data['allergen_name'],
				'allergy_description' => $data['allergen_description'],
				'is_allergy' => $data['type'],
			),
			array(
				'%s',
				'%s',
				'%d',
			)
		);

		return (isset($wpdb->insert_id)) ? true : false;
	}

	public function checkAllergenExists(string $allergenName)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$sql = $wpdb->prepare(
			"SELECT allergy_name FROM $table_name WHERE allergy_name = %s",
			$allergenName
		);

		$result = $wpdb->get_results($sql);

		return (count($result) > 0) ? true : false;
	}

	public function getAllAllergens()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$sql = "SELECT allergy_name, is_allergy 
		FROM $table_name
		WHERE is_active = 1
		ORDER BY  is_allergy DESC, allergy_name ASC";

		$result = $wpdb->get_results($sql, ARRAY_A);

		return $result;
	}

	public function updateAllergens(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$wpdb->update(
			$table_name,
			array(
				'allergy_name' => $data['allergen_name'],
				'allergy_description' => $data['allergen_description'],
				'is_allergy' => $data['type'],
			),
			array(
				'allergy_name' => $data['allergen_name_hidden'],
			)
		);
	}

	public function getAllergen(string $allergenName)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$sql = $wpdb->prepare(
			"SELECT * FROM $table_name WHERE allergy_name = %s",
			$allergenName
		);

		$result = $wpdb->get_results($sql);

		return $result;
	}

	public static function includeItems()
	{
		if (!class_exists('Allergens_Dietary_Allergy_Attachment_Queries')) {
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/allergy_attachment.php';
		}
		if (!class_exists('Allergens_Dietary_Attachment_Queries')) {
			require_once ALLERGENS_DIETARY_DIRNAME . '/php/DB/attachment.php';
		}

		//get arrays
		Allergens_Dietary_Activator::initialize();

		$allergens_result = Allergens_Dietary_Activator::allergens_options();
		$icon_allergy_result = Allergens_Dietary_Activator::allergy_icon_options();
		$icon_result = Allergens_Dietary_Activator::icon_options();

		global $wpdb;

		//get database table
		$table_allergens = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';


		$sql = $wpdb->prepare(
			"SELECT * FROM $table_allergens WHERE allergy_name = 'alcohol'"
		);

		$exists = $wpdb->get_var($sql);

		//checks if database record of the standard allergies already exists
		if ($exists == 0) {

	foreach($allergens_result as $key => $value){
		$sql = $wpdb->prepare(
			"SELECT allergy_name FROM $table_allergens WHERE allergy_name = %s"
			,$value['title']);
			
			$exists = $wpdb->get_var( $sql );
		if ($exists == 0){
			$isallergy = 0;
			$isdefault = 0;
					if ($value['default']) {
						$isdefault = 1;
					}

					if ($value['category'] == "allergen") {
						$isallergy = 1;
					} else {
						$isallergy = 0;
					}
					//insert allergies
					$wpdb->insert(
						$table_allergens,
						array(
							'allergy_name' => $value['title'],
							'allergy_description' => $value['description'],
							'is_allergy' => $isallergy,
							'is_default_option' => $isdefault,
						)
					);
				}
			}
		}

		//activate other inserters
		Allergens_Dietary_Attachment_Queries::attachment_insert($icon_allergy_result);
		Allergens_Dietary_Allergy_Attachment_Queries::allergy_connection($icon_result);
	}

	public function is_default_allergen(string $allergy_name): bool
	{
		global $wpdb;

		$table_allergy = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		try {
			if (empty($allergy_name)) {
				throw new Exception('No correct allergy given.');
			}

			$is_default = $wpdb->get_var($wpdb->prepare(
				"SELECT is_default_option 
				 FROM $table_allergy 
				 WHERE allergy_name = %s",
				$allergy_name
			));
		} catch (Exception $e) {
			echo 'Error: ' . $e->getMessage();
		}

		return $is_default == 1 ? true : false;
	}

	public static function getItems(){
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$data = $wpdb->get_results("SELECT allergy_name, allergy_description, is_allergy, is_active FROM $table_name", ARRAY_A);
	
		return $data;
	}

	public static function getColumns(){
		global $wpdb;
        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$columns = $wpdb->get_results("SHOW COLUMNS FROM $table_name", ARRAY_A);
	
		return $columns;
	}

	public function activationUpdate(array $data, string $message)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$updatenumber = 0;

		foreach ($data['item'] as $key => $value) {

			$sql = $wpdb->prepare(
				"SELECT * FROM $table_name WHERE allergy_name = '%s'",
				$value
			);

			$result = $wpdb->get_row($sql);

			if (!empty($result)) {

				if ($result->is_active == 0) {
					$updatenumber = 1;
				} else {
					$updatenumber = 0;
				}
			}

			$data = array(
				'is_active' => $updatenumber,
			);

			$where = array(
				'allergy_name' => $value
			);

			$format = array('%s', '%s');

			$wpdb->update(
				$table_name,
				$data,
				$where,
				$format
			);

			if (!empty($_GET)) {
				$url = strtok($_SERVER["REQUEST_URI"], '?');
				$separator = strpos($url, '?') === false ? '?' : '&';
				header("Location: $url" . $separator . "page=allergens-dietary-show-allergens" . (isset($return_page) ? '&paged=' . $return_page : '') . "&messaged=" . urlencode($message));
			}
		}
	}

	public function singleActivationUpdate(int $return_page, string $message)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$updatenumber = 0;

		if (isset($_GET['item'])) {
			$sql = $wpdb->prepare(
				"SELECT allergy_name, is_active FROM $table_name WHERE allergy_name = '%s'",
				$_GET['item']
			);

			$result = $wpdb->get_row($sql);

			if ($result->is_active == 0) {
				$updatenumber = 1;
			} else {
				$updatenumber = 0;
			}

			$data = array(
				'is_active' => $updatenumber,
			);

			$where = array(
				'allergy_name' => $_GET['item']
			);

			$format = array('%s', '%s');

			$wpdb->update(
				$table_name,
				$data,
				$where,
				$format
			);

			if (!empty($_GET)) {
				$url = strtok($_SERVER["REQUEST_URI"], '?');
				$separator = strpos($url, '?') === false ? '?' : '&';
				header("Location: $url" . $separator . "page=allergens-dietary-show-allergens" . (isset($return_page) ? '&paged=' . $return_page : '') . "&messaged=" . urlencode($message));
			}
		}
	}
}