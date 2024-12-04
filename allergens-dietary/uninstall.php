<?php
// exit if user can access this file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
// exit if uninstall.php is not called by WordPress
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'allergens_dietary_ictoria_settings' );
// if enabled, export all product data added by this plugin before removing them from WP and offer a dowloadable .csv file
if ( $settings['auto-export'] == true ) {
	// call export function here
}

// run code that removes all plugin data from products
// retrieve a list of post_id's
$ids = get_posts(
	array(
		'posts_per_page' => -1,
		'post_type'      => array( 'product', 'product_variation' ),
		'fields'         => 'ids',
	)
);
// loop through all products and delete the plugin data
for ( $i = 0; $i < count( $ids ); $i++ ) {
	delete_post_meta( $ids[ $i ], 'allergens_dietary_ictoria' );
}

// delete all options added by this plugin. Files are automatically removed by WP
delete_option( 'allergens_dietary_ictoria_options' );
delete_option( 'allergens_dietary_ictoria_settings' );

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
function delete_fk( $fk ) {
	global $wpdb;
	$wpdb->get_results( $fk );

	if ( $wpdb->last_error ) {
		$wpdb->flush();
		return;
	}

	return delete_fk( $fk );
}

function delete_tables( $table ) {
	global $wpdb;
	$query = "DROP TABLE {$table}";
	$wpdb->get_results( $query );

	if ( $wpdb->last_error ) {
		$wpdb->flush();
		return;
	}

	return delete_tables( $table );
}

$wpdb->hide_errors();
foreach ( $fk_del as $fk ) {
	delete_fk( $fk );
}

foreach ( $tables as $table ) {
	$table_name = $wpdb->prefix . $table;
	delete_tables( $table_name );
}
