<?php
/**
 * PHPUnit bootstrap file
 */

$tests_dir = rtrim(getenv('WP_TESTS_DIR'), '/');
if (!is_dir($tests_dir)) {
    throw new RuntimeException('WP_TESTS_DIR environment variable must be set to run tests');
}

// Give access to tests_add_filter() function.
require_once $tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {
    $plugin_dir = getenv('WP_PLUGIN_DIR') ?? __DIR__ . '/..';
    if (!is_dir($plugin_dir)) {
        throw new RuntimeException('WP_PLUGIN_DIR environment variable must be set to run tests');
    }
    require rtrim($plugin_dir, '/') . '/yoti.php';
}
tests_add_filter('muplugins_loaded', '_manually_load_plugin');

// Start up the WP testing environment.
require $tests_dir . '/includes/bootstrap.php';
