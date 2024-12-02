<?php

if (! defined('ABSPATH')) {
    exit;
}

/**
 * @class Allergens_Dietary_Ictoria_Allergy_Product_Queries
 * @brief This class is a singleton that handles all the queries for the allergens and dietary restrictions DB table.
 * @author V.B.
 * @date 4-10-2024
 * @since 1.0.0
 */

class Allergens_Dietary_Ictoria_Allergy_Product_Queries
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

        $wpdb->insert(
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
        $sql = '';
        if (is_null($allergen)) {
            $sql = $wpdb->prepare(
                "SELECT ap.allergy_name
                FROM %i as ap
                JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy as a on ap.allergy_name = a.allergy_name
                WHERE ap.product_id = %d and a.is_active = 1",
                array($table_name, $product_id)
            );
        } else {
            $sql = $wpdb->prepare(
                "SELECT ap.allergy_name
                FROM %i as ap
                JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy as a on ap.allergy_name = a.allergy_name
                WHERE ap.product_id = %d and a.is_active = 1 and ap.allergy_name = %s",
                array($table_name, $product_id, $allergen)
            );
        }


        return $wpdb->get_results($sql, ARRAY_A);
    }

    public function deleteAllergyProduct(int $product_id, string $allergen)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';
        $sql = $wpdb->prepare(
            "DELETE FROM %i
            WHERE product_id = %d AND allergy_name = %s",
            array($table_name, $product_id, $allergen)
        );

        return $wpdb->query($sql);
    }
    // public function getFilteredProducts( ?array $allergens, ?array $dietary ) {
    //     $allergens = (is_null($allergens) || empty($allergens)) ? "" : $allergens;
    //     $dietary = (is_null($dietary) || empty($dietary)) ? "" : $dietary;
    //     global $wpdb;

    //     $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';
    //     $sql = $wpdb->prepare(
    //         "SELECT DISTINCT ap.product_id
    //         FROM %i AS ap
    //         JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy AS a ON ap.allergy_name = a.allergy_name
    //         WHERE ap.product_id NOT IN (
    //             SELECT ap_sub.product_id
    //             FROM {$wpdb->prefix}allergens_dietary_ictoria_allergy_product AS ap_sub
    //             JOIN {$wpdb->prefix}allergens_dietary_ictoria_allergy AS a_sub ON ap_sub.allergy_name = a_sub.allergy_name
    //             WHERE a_sub.is_allergy = 1
    //             AND a_sub.allergy_name IN ('". (is_array($allergens)?implode("','",$allergens): $allergens) ."')
    //         )".(!empty($dietary) ? "
    //         AND a.is_allergy = 0
    //         AND a.allergy_name IN ('" .(is_array($dietary)?implode("','",$dietary): $dietary)."')": "") ,
    //         $table_name
    //     );
    //     error_log($sql);
    //     return $wpdb->get_results( $sql, ARRAY_A);
    // }

    /**
     * @param array $allergens
     * @param array $dietary
     * @return array $results
     * @brief Searches and selects product ids with the selected allergens and or dietary restrictions
     * where if a product has an allergy that is being searched for is being excluded.
     * Whereas a dietary restriction works different where products who do not have a dietary restriction will be excluded
     * @author @MaikHendriks
     * @since 0.16.5.1
     * @date 18-11-2024
     */
    public function getFilteredProducts(?array $allergens, ?array $dietary)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'allergens_dietary_ictoria_allergy_product';
        $allergens_table = $wpdb->prefix . 'allergens_dietary_ictoria_allergy';

        // Initialize base query
        $query_parts[] = "SELECT DISTINCT ap.product_id 
                          FROM {$table_name} ap
                          JOIN {$allergens_table} a ON ap.allergy_name = a.allergy_name";

        $where_conditions = [];

        // Handle allergens filtering
        if (!empty($allergens)) {
            $allergens_list = "'" . implode("','", array_map('esc_sql', $allergens)) . "'";
            $query_parts[] = "LEFT JOIN (
                SELECT DISTINCT product_id 
                FROM {$table_name} ap_exclude
                JOIN {$allergens_table} a_exclude ON ap_exclude.allergy_name = a_exclude.allergy_name
                WHERE a_exclude.is_allergy = 1 
                AND a_exclude.allergy_name IN ({$allergens_list})
            ) as excluded_products ON ap.product_id = excluded_products.product_id";

            $where_conditions[] = "excluded_products.product_id IS NULL";
        }

        // Handle dietary requirements filtering
        if (!empty($dietary)) {
            // For each dietary requirement, ensure the product has it
            foreach ($dietary as $index => $diet) {
                $diet = esc_sql($diet);
                $alias = "diet_check_{$index}";

                $query_parts[] = "JOIN (
                    SELECT DISTINCT product_id 
                    FROM {$table_name} ap_diet
                    JOIN {$allergens_table} a_diet ON ap_diet.allergy_name = a_diet.allergy_name
                    WHERE a_diet.is_allergy = 0 
                    AND a_diet.allergy_name = '{$diet}'
                ) as {$alias} ON ap.product_id = {$alias}.product_id";
            }
        }

        // Combine all parts of the query
        $sql = implode(" ", $query_parts);
        if (!empty($where_conditions)) {
            $sql .= " WHERE " . implode(" AND ", $where_conditions);
        }

        return $wpdb->get_results($sql, ARRAY_A);
    }
}
