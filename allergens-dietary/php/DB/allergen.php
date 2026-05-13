<?php

if (!defined('ABSPATH')) {
	exit;
}

/**
 * @class Allergens_Dietary_Allergen_Queries
 * @brief This class is a singleton that handles all the queries for the allergens and dietary restrictions DB table.
 * @author Verdi-B
 * @date 11-9-2024
 * @since 1.0.0
 */
class Allergens_Dietary_Allergen_Queries
{



	/**
	 * @brief This method returns the instance of the class.
	 * @return Allergens_Dietary_Allergen_Queries
	 * @author Verdi-B
	 * @since 1.0.0
	 * @date 11-9-2024
	 */
	protected static array $instances;

	public static function getInstance()
	{
		$subclass = static::class;
		if (!isset(self::$instances[$subclass])) {
			self::$instances[$subclass] = new static();
		}
		return self::$instances[$subclass];
	}

	protected function __construct() {}


	public function getAllAllergens()
	{
		global $wpdb;

		$table_name = $wpdb->prefix . 'allergens_dietary_allergy';

		$result = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
				"	SELECT allergy_name, is_allergy 
					FROM %i
					WHERE is_active = 1
					ORDER BY  is_allergy DESC, allergy_name ASC",
				$table_name
			),
			ARRAY_A
		);

		return $result;
	}


	public function getItems()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_allergy';
		$data = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT allergy_name, allergy_description, is_allergy, is_active, is_default_option
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
		$table_name = $wpdb->prefix . 'allergens_dietary_allergy';

		$results = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$wpdb->prepare(
				"SELECT allergy_name, allergy_description, is_allergy, is_active, is_default_option
				FROM %i
				WHERE allergy_name LIKE %s",
				array($table_name, '%' . $search_word . '%')
			),
			ARRAY_A
		);

		return $results;
	}


	public function change_status(array $allergen)
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'allergens_dietary_allergy';

		$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
			$table_name,
			array(
				'is_active' => $allergen['is_active']
			),
			array(
				'allergy_name' => $allergen['allergy_name']
			)
		);
	}
}
