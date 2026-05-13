<?php

if (!defined('ABSPATH')) {
    exit;
}

class Allergens_Dietary_Plugin_Remover{


    /**
     * Array of tables to be deleted during the uninstallation process.
     *
     * This array contains the names of database tables that are associated with the allergens and dietary plugin.
     * These tables will be deleted when the plugin is uninstalled.
     *
     * @var array
     */
    protected array $ad_tables = array(
	'allergens_dietary_allergy_attachment',
	'allergens_dietary_allergy_product',
	'allergens_dietary_attachments',
	'allergens_dietary_allergy',
);


    /**
     * Array of foreign keys to be deleted during the uninstallation process.
     *
     * This array contains the SQL statements that are used to delete foreign keys from the database tables.
     */
    protected array $ad_fk_del;

    public function __construct() {

        global $wpdb;

        $this->ad_fk_del = array(
            "ALTER TABLE {$wpdb->prefix}allergens_dietary_allergy_product
            DROP FOREIGN KEY FK_AllergyProduct_WCproduct,
            DROP FOREIGN KEY FK_AllergyProduct_Allergy",
            "ALTER TABLE {$wpdb->prefix}allergens_dietary_allergy_attachment
            DROP FOREIGN KEY FK_AllergyAttach_Allergy,
            DROP FOREIGN KEY FK_AllergyAttach_Attach",
        );
    }

    private function delete_translation(){

        if (!function_exists('WP_Filesystem')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        } 
        
        WP_Filesystem();
        global $wp_filesystem;
        
        $language_path_dest = ABSPATH . '/wp-content/languages';
        $language_file_basename = 'allergens-dietary';
        

        $files = glob( $language_path_dest . '/' . $language_file_basename . '-*' );
        
        if ( $files ) {
            foreach ( $files as $file ) {
                $filename = basename( $file );
                if ( preg_match( '/^allergens-dietary-[a-z]{2}_[A-Z]{2,3}\.(po|mo)$/', $filename ) ) {
                    if ( $wp_filesystem->exists( $file ) ) {
                        $wp_filesystem->delete( $file );
                    }
                }
            }
        }
    }

    private function delete_fk(string $fk){
        global $wpdb;
        $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            $wpdb->prepare($fk));// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

        if ($wpdb->last_error) {
            $wpdb->flush();
            return;
        }

        return $this->delete_fk($fk);
    }

    private function delete_tables(string $table) {
        global $wpdb;
        $wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
            // phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
            $wpdb->prepare("DROP TABLE %i", $table));

        if ($wpdb->last_error) {
            $wpdb->flush();
            return;
        }

        return $this->delete_tables($table);
    }

    public function handle_delete(){

        global $wpdb;

        $wpdb->hide_errors(); 
        foreach ($this->ad_fk_del as $fk) {
            $this->delete_fk($fk);
        }

        foreach ($this->ad_tables as $table) {
            $table_name = $wpdb->prefix . $table;
            $this->delete_tables($table_name);
        }

        $this->delete_translation();

    }

}

?>