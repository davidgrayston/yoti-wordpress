<?php

// Path to WP installation.
define( 'ABSPATH', '/var/www/html/' );

// Path to the theme to test with.
define( 'WP_DEFAULT_THEME', 'default' );

// Test with WordPress debug mode (default).
define( 'WP_DEBUG', true );

// DB Settings.
define( 'DB_NAME', 'wordpress' );
define( 'DB_USER', 'wordpress' );
define( 'DB_PASSWORD', getenv('WORDPRESS_DB_PASSWORD') );
define( 'DB_HOST', getenv('WORDPRESS_DB_HOST') );
define( 'DB_CHARSET', 'utf8' );
define( 'DB_COLLATE', '' );

$table_prefix = 'wptests_';

// Test site settings.
define( 'WP_TESTS_DOMAIN', 'example.org' );
define( 'WP_TESTS_EMAIL', 'admin@example.org' );
define( 'WP_TESTS_TITLE', 'Test Yoti' );
define( 'WPLANG', '' );

// PHP binary.
define( 'WP_PHP_BINARY', 'php' );
