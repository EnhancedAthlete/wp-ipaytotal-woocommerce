<?php
/**
 * Class WP_IPayTotal_WooCommerce_Plugin_Test. Tests the root plugin file.
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Verifies the plugin has been instantiated and added to the $GLOBALS variable.
 */
class WP_IPayTotal_WooCommerce_Plugin_Test extends WP_UnitTestCase {

	public function test_plugin_instantiated() {

		$this->assertArrayHasKey( 'wp_ipaytotal_woocommerce', $GLOBALS );

		$this->assertInstanceOf( 'WP_IPayTotal_WooCommerce', $GLOBALS['wp_ipaytotal_woocommerce'] );
	}

}
