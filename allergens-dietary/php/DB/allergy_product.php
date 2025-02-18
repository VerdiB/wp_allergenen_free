<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_Allergy_Product_Queries
 * @brief This class is a singleton that handles all the queries for the allergens and dietary restrictions DB table.
 * @author ictoriabv
 * @date 4-10-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Allergy_Product_Queries
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

    public function addAllergyProduct(int $product_id, string $allergen)
    {

        global $wpdb;

        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';

        $wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $table_name,
            array(
                'product_id'    => $product_id,
                'allergy_name' => $allergen,
            )
        );

        return (isset($wpdb->insert_id)) ? true : false;
    }

    public function getAllergyProduct(int $product_id, string $allergen  = null)
    {
        global $wpdb;

        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';
        $allergy = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';
        $sql = '';
        if (is_null($allergen)) {
            $sql = $wpdb->get_results(
                $wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                    "SELECT ap.allergy_name
                FROM %i as ap
                JOIN %i as a on ap.allergy_name = a.allergy_name
                WHERE ap.product_id = %d and a.is_active = 1",
                    array($table_name, $allergy, $product_id)
                ),
                ARRAY_A
            );
        } else {
            $sql = $wpdb->get_results(
                $wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
                    "SELECT ap.allergy_name
                FROM %i as ap
                JOIN %i as a on ap.allergy_name = a.allergy_name
                WHERE ap.product_id = %d and a.is_active = 1 and ap.allergy_name = %s",
                    array($table_name, $allergy, $product_id, $allergen)
                ),
                ARRAY_A
            );
        }


        return $sql;
    }

    public function deleteAllergyProduct(int $product_id, string $allergen)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';

        return $wpdb->query($wpdb->prepare( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
            "DELETE FROM %i
            WHERE product_id = %d AND allergy_name = %s",
            array($table_name, $product_id, $allergen)
        ));
    }

    /**
     * @param array $allergens
     * @param array $dietary
     * @return array $results
     * @brief Searches and selects product ids with the selected allergens and or dietary restrictions
     * where if a product has an allergy that is being searched for is being excluded.
     * Whereas a dietary restriction works different where products who do not have a dietary restriction will be excluded
     * @author ictoriabv
     * @since 0.16.5.1
     * @date 18-11-2024
     */
    public function getFilteredProducts(?array $allergens, ?array $dietary)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';
        $allergens_table = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

        // Initialize base query
        $query_part_1[] = "SELECT DISTINCT ap.product_id 
                          FROM %i ap
                          JOIN {$allergens_table} a ON ap.allergy_name = a.allergy_name";

        // Handle allergens filtering
        if (!empty($allergens)) {

            $allergens_list = "'" . implode("','", array_map('esc_sql', $allergens)) . "'";
            $query_part_2[] = "LEFT JOIN (
                SELECT DISTINCT product_id 
                FROM {$table_name} ap_exclude
                JOIN {$allergens_table} a_exclude ON ap_exclude.allergy_name = a_exclude.allergy_name
                WHERE a_exclude.is_allergy = 1 
                AND a_exclude.allergy_name IN ({$allergens_list})
            ) as excluded_products ON ap.product_id = excluded_products.product_id";
        }

        // Handle dietary requirements filtering
        if (!empty($dietary)) {
            // For each dietary requirement, ensure the product has it
            foreach ($dietary as $index => $diet) {
                $diet = esc_sql($diet);
                $alias = "diet_check_{$index}";

                $query_part_3[] = "JOIN (
                    SELECT DISTINCT product_id 
                    FROM {$table_name} ap_diet
                    JOIN {$allergens_table} a_diet ON ap_diet.allergy_name = a_diet.allergy_name
                    WHERE a_diet.is_allergy = 0 
                    AND a_diet.allergy_name = '{$diet}'
                ) as {$alias} ON ap.product_id = {$alias}.product_id";
            }
        }

        $sql = array($query_part_1, $query_part_2, $query_part_3);
        $newQuery = "";

        foreach ($sql as $query) {
            $this_part = $query + " ";
            echo($this_part . " ");
            $newQuery .= $this_part;
        }

        $newQuery .= "AND WHERE excluded_products.product_id IS NULL";
        
        return $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $sql,
                $table_name
            ),
            ARRAY_A
        );
    }
}
