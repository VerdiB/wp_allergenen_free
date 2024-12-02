<?php
// tests/wp-tests-config.php

// Path to the WordPress codebase you'd like to test.
define( 'ABSPATH', dirname( __FILE__ ) . '/../../' );

// Test with WordPress debug mode on (default).
define( 'WP_DEBUG', true );

// ** MySQL settings ** //
define( 'DB_NAME', getenv('WORDPRESS_DB_NAME') ?: 'wordpress_test' );
define( 'DB_USER', getenv('WORDPRESS_DB_USER') ?: 'root' );
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') ?: '' );
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') ?: 'mysql' );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix  = 'wptests_';   // Only numbers, letters, and underscores please!
// $table_prefix = 'wp_'; //use current wordpress table prefix

define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Blog' );

// Path to the WordPress codebase you'd like to test.
define( 'ABSPATH', dirname( __FILE__ ) . '/../../' );

// Path to the WordPress tests checkout.
define( 'WP_TESTS_DIR', ABSPATH . 'wordpress-tests' );

// Allow WordPress to be updated from the source.
define( 'DISALLOW_FILE_MODS', false );

// Bootstrap WordPress
require_once WP_TESTS_DIR . '/includes/bootstrap.php';
