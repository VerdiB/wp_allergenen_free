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

	private function __construct() {}

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

		$result = $wpdb->get_results($wpdb->prepare(
			"SELECT allergy_name FROM %i WHERE allergy_name = %s",
			array($table_name, $allergenName)
		));


		return (count($result) > 0) ? true : false;
	}

	public function getAllAllergens()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$result = $wpdb->get_results(
			$wpdb->prepare("SELECT allergy_name, is_allergy 
			FROM %i
			WHERE is_active = 1
			ORDER BY  is_allergy DESC, allergy_name ASC", $table_name),
			ARRAY_A
		);

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

	/**
	 * @author ictoriabv
	 * @param string $allergenName
	 * @return object
	 */
	public function getAllergen(string $allergenName)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$result = $wpdb->get_results($wpdb->prepare(
			"SELECT * FROM %i WHERE allergy_name = %s",
			array($table_name, $allergenName)
		));

		return $result;
	}

	/**
	 * @author ictoriabv
	 * @brief method that activates once during the first instal
	 * making sure all standard allergens are inserted in the db
	 * And calls related methods for inserting the images and paths
	 * @return void
	 * @since 0.1.0.0
	 */
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



		$exists = $wpdb->get_var($wpdb->prepare(
			"SELECT * FROM %i WHERE allergy_name = 'alcohol'",
			$table_allergens
		));

		//checks if database record of the standard allergies already exists
		if ($exists == 0) {

			foreach ($allergens_result as $key => $value) {

				$exists = $wpdb->get_var($wpdb->prepare(
					"SELECT allergy_name
					FROM %i
					WHERE allergy_name = %s",
					array($table_allergens, $value['title'])
				));

				if ($exists == 0) {
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
				 FROM %i
				 WHERE allergy_name = %s",
				array($table_allergy, $allergy_name)
			));
		} catch (Exception $e) {
			echo esc_html('Error: ' . $e->getMessage());
		}

		return $is_default == 1 ? true : false;
	}

	public static function getItems()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$data = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT allergy_name, allergy_description, is_allergy, is_active
				 FROM %i",
				$table_name
			),
			ARRAY_A
		);

		return $data;
	}

	public function search_allergen(string $search_word)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		
		$results = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT allergy_name, allergy_description, is_allergy, is_active, is_default_option
				FROM %i
				WHERE allergy_name LIKE %s",
			array($table_name, '%'.$search_word.'%')),
			ARRAY_A
		);

		return $results;
	}

	public static function getColumns()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
		$columns = $wpdb->get_results($wpdb->prepare("SHOW COLUMNS FROM %i", $table_name), ARRAY_A);

		return $columns;
	}

	public function change_status(array $allergen){
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$wpdb->update(
			$table_name,
			array(
				'is_active' => $allergen['is_active']
			),
			array(
				'allergy_name' => $allergen['allergy_name']
			)
		);

	}

	/**
	 * @author ictoriabv
	 * @important This method has GET and SERVER globals
	 * These globals need to be checked, sanitized and moved
	 * These globals need to move to where the method is being used 
	 * @param array $data
	 * @param string $message
	 * @return void
	 */
	public function activationUpdate(array $data)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$updatenumber = 0;

		foreach ($data['item'] as $key => $value) {

			$result = $wpdb->get_row($wpdb->prepare(
				"SELECT * FROM %i WHERE allergy_name = %s",
				array($table_name, $value)
			));

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

		}
	}

	
}
