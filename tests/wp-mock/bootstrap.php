<?php
/**
 * PHPUnit bootstrap file for WP_Mock.
 *
 * @package WP_IPayTotal_WooCommerce
 */

$project_root_dir = dirname( dirname( dirname( __FILE__ ) ) );
require_once $project_root_dir . '/vendor/autoload.php'; // Composer autoloader.

$plugin_root_dir = $project_root_dir . '/trunk';

WP_Mock::bootstrap();

