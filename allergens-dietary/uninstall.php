<?php
// exit if user can access this file directly
if (! defined('ABSPATH')) {
	exit;
}
// exit if uninstall.php is not called by WordPress
if (! defined('WP_UNINSTALL_PLUGIN')) {
	exit;
}


// get db object
global $wpdb;

/**
 * Array of tables to be deleted during the uninstallation process.
 *
 * This array contains the names of database tables that are associated with the allergens and dietary plugin.
 * These tables will be deleted when the plugin is uninstalled.
 *
 * @var array
 */
$tables = array(
	'allergens_dietary_ictoria_allergy_attachment',
	'allergens_dietary_ictoria_allergy_product',
	'allergens_dietary_ictoria_attachments',
	'allergens_dietary_ictoria_allergy',
);

/**
 * Array of foreign keys to be deleted during the uninstallation process.
 *
 * This array contains the SQL statements that are used to delete foreign keys from the database tables.
 */
$fk_del = array(
	"ALTER TABLE {$wpdb->prefix}allergens_dietary_ictoria_allergy_product
	DROP FOREIGN KEY FK_AllergyProduct_WCproduct,
	DROP FOREIGN KEY FK_AllergyProduct_Allergy",
	"ALTER TABLE {$wpdb->prefix}allergens_dietary_ictoria_allergy_attachment
	DROP FOREIGN KEY FK_AllergyAttach_Allergy,
	DROP FOREIGN KEY FK_AllergyAttach_Attach",
);

function allergens_dietary_delete_translations(){

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

/**
 *
 */
function allergens_dietary_delete_fk($fk)
{
	global $wpdb;
	$wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		$wpdb->prepare($fk));// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	if ($wpdb->last_error) {
		$wpdb->flush();
		return;
	}

	return allergens_dietary_delete_fk($fk);
}

function allergens_dietary_delete_tables($table)
{
	global $wpdb;
	$wpdb->get_results(// phpcs:ignore WordPress.DB.DirectDatabaseQuery
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.SchemaChange
		$wpdb->prepare("DROP TABLE %i", $table));

	if ($wpdb->last_error) {
		$wpdb->flush();
		return;
	}

	return allergens_dietary_delete_tables($table);
}

$wpdb->hide_errors(); 
foreach ($fk_del as $fk) {
	allergens_dietary_delete_fk($fk);
}

foreach ($tables as $table) {
	$table_name = $wpdb->prefix . $table;
	allergens_dietary_delete_tables($table_name);
}

allergens_dietary_delete_translations();