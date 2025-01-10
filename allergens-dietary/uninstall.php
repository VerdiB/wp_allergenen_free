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

/**
 *
 */
function delete_fk($fk)
{
	global $wpdb;
	$wpdb->get_results($wpdb->prepare($fk));// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared

	if ($wpdb->last_error) {
		$wpdb->flush();
		return;
	}

	return delete_fk($fk);
}

function delete_tables($table)
{
	global $wpdb;
	$wpdb->get_results($wpdb->prepare("DROP TABLE %i", $table));

	if ($wpdb->last_error) {
		$wpdb->flush();
		return;
	}

	return delete_tables($table);
}

$wpdb->hide_errors(); 
foreach ($fk_del as $fk) {
	delete_fk($fk);
}

foreach ($tables as $table) {
	$table_name = $wpdb->prefix . $table;
	delete_tables($table_name);
}
