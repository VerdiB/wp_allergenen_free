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



	/**
	 * @brief This method returns the instance of the class.
	 * @return Allergens_Dietary_Allergen_Queries
	 * @author ictoriabv
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	private static $instances = [];

	public static function getInstance()
    {
        $subclass = static::class;
        if (!isset(self::$instances[$subclass])) {
            self::$instances[$subclass] = new static();
        }
        return self::$instances[$subclass];
    }

	protected function __construct()
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

	
	/**
	 * @author ictoriabv
	 * @important This method has GET and SERVER globals
	 * These globals need to be checked, sanitized and moved
	 * These globals need to move to where the method is being used 
	 * the same goes for the redirection
	 * @param int $return_page
	 * @param string $message
	 * @return void
	 */
	public function singleActivationUpdate()
	{
		global $wpdb;
		
		$table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

		$updatenumber = 0;


		$result = $wpdb->get_row($wpdb->prepare(
			"SELECT allergy_name, is_active FROM %i WHERE allergy_name = %s",
			array($table_name, $_GET['item'])
		));

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
	}
}
