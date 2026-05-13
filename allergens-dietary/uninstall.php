<?php
// exit if user can access this file directly
if (! defined('ABSPATH') ) {
	exit;
}
// exit if uninstall.php is not called by WordPress
if (! defined('WP_UNINSTALL_PLUGIN') ) {
	exit;
}

if (! class_exists('Allergens_Dietary_Plugin_Remover') ) {
	require_once ALLERGENS_DIETARY_DIRNAME . 'php/misc/plugin_remover.php' ;
}


$allergens_dietary_remover = new Allergens_Dietary_Plugin_Remover();

$allergens_dietary_remover->handle_delete();