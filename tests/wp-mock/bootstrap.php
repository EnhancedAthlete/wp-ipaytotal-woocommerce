<?php
/**
 * PHPUnit bootstrap file for WP_Mock.
 *
 * @package WP_IPayTotal_WooCommerce
 */

$root_dir = dirname( dirname( dirname( __FILE__ ) ) );
require_once $root_dir . '/vendor/autoload.php'; // Composer autoloader.

WP_Mock::bootstrap();

