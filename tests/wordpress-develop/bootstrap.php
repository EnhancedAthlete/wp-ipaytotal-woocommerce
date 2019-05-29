<?php
/**
 * PHPUnit bootstrap file for wordpress-develop.
 *
 * @package WP_IPayTotal_WooCommerce
 */

$plugin_root_dir = dirname( dirname( dirname( __FILE__ ) ) );

$_tests_dir = $plugin_root_dir . '/vendor/cyruscollier/wordpress-develop/tests/phpunit';

$_wp_tests_config = $plugin_root_dir . '/tests/wordpress-develop/wp-tests-config.php';

if ( ! file_exists( $_wp_tests_config ) ) {
	echo 'wp-tests-config.php not found.';
	exit( 1 );
}

define( 'WP_CONFIG_FILE_PATH', $_wp_tests_config );

$_woocommerce_bootstrap = $plugin_root_dir . '/vendor/woocommerce/woocommerce/tests/bootstrap.php';

// Later picked up by WooCommerce tests.
// @codingStandardsIgnoreLine
putenv( "WP_TESTS_DIR=$_tests_dir" );

// Verify that Composer dependencies have been installed.
if ( ! file_exists( $_tests_dir . '/includes/functions.php' ) || ! file_exists( $_woocommerce_bootstrap ) ) {
	echo 'Unable to find the WordPress and WooCommerce. Run `composer install`.';
	exit( 1 );
}

// Give access to tests_add_filter() function.
require_once $_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function _manually_load_plugin() {

	$plugin_root_dir = dirname( dirname( dirname( __FILE__ ) ) );

	require_once $plugin_root_dir . '/vendor/woocommerce/woocommerce/woocommerce.php';
	require_once $plugin_root_dir . '/trunk/wp-ipaytotal-woocommerce.php';

}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require_once $_woocommerce_bootstrap;
