<?php
/**
 * iPayTotal - WooCommerce Payment Gateway.
 *
 * @since 1.0.0
 * @package wp-ipaytotal-woocommerce
 * @author iPayTotal Ltd
 * @author Brian Henry <BrianHenryIE@gmail.com>
 *
 * Plugin Name: iPayTotal - WooCommerce Payment Gateway
 * Plugin URI: https://ipaytotal.com/contact
 * Description: WooCommerce custom payment gateway integration with iPayTotal.
 * Version: 2.0.2
 * Author: iPayTotal, BrianHenryIE
 * Author URI: https://ipaytotal.com/ipaytotal-high-risk-merchant-account/
 * License: GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wp-ipaytotal-woocommerce
 * Domain Path: /languages/
 * WC requires at least: 3.0.0
 * WC tested up to: 4.9.8
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'WP_IPAYTOTAL_WOOCOMMERCE_VERSION', '3.0.0' );

require plugin_dir_path( __FILE__ ) . 'includes/class-wp-ipaytotal-woocommerce.php';

// phpcs:disable Squiz.PHP.DisallowMultipleAssignments.Found
$GLOBALS['wp_ipaytotal_woocommerce'] = $wp_ipaytotal_woocommerce = new WP_IPayTotal_WooCommerce();
$wp_ipaytotal_woocommerce->run();

