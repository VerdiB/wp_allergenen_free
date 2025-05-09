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
            $sql = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
            $sql = $wpdb->get_results( // phpcs:ignore WordPress.DB.DirectDatabaseQuery
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
        $query_part[] = "SELECT DISTINCT ap.product_id 
                          FROM %i ap
                          JOIN {$allergens_table} a ON ap.allergy_name = a.allergy_name";
        $clause = [];

        // Handle allergens filtering

        if (!empty($allergens)) {
            $i = 0;
            foreach ($allergens as $allergen) {
                $allergen = esc_sql($allergen);
                $alias = "excluded_products_$i";
                $query_part[] = "LEFT JOIN (
                    SELECT DISTINCT ap_exclude.product_id 
                    FROM {$table_name} ap_exclude
                    JOIN {$allergens_table} a_exclude ON ap_exclude.allergy_name = a_exclude.allergy_name
                    WHERE ap_exclude.product_id NOT IN (
                        SELECT product_id
                        FROM {$wpdb->prefix}allergens_dietary_ictoria_allergy_product
                        WHERE allergy_name = '{$allergen}'
                    )
                ) as {$alias} ON ap.product_id = {$alias}.product_id";
        
                $clauses[] = "{$alias}.product_id IS NOT NULL";
                $i++;
            }
        
            $query_part[] = "WHERE " . implode(" AND ", $clauses);
        }

        // Handle dietary requirements filtering
        if (!empty($dietary)) {
            // For each dietary requirement, ensure the product has it
            foreach ($dietary as $index => $diet) {
                $diet = esc_sql($diet);
                $alias = "diet_check_{$index}";
                error_log(print_r($alias, true));

                $query_part[] = "JOIN (
                    SELECT DISTINCT product_id 
                    FROM {$table_name} ap_diet
                    JOIN {$allergens_table} a_diet ON ap_diet.allergy_name = a_diet.allergy_name
                    WHERE a_diet.is_allergy = 0 
                    AND a_diet.allergy_name = '{$diet}'
                ) as {$alias} ON ap.product_id = {$alias}.product_id";
            }
        }

        $sql = array_merge_recursive($query_part, $clause);
        $newQuery = "";

        foreach ($sql as $query) {
            $newQuery .= $query . " ";
        }

        error_log(print_r($newQuery, true));
        error_log(print_r($sql, true));

        return $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare(
                // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
                $newQuery,
                $table_name
            ),
            ARRAY_A
        );
    }
}
