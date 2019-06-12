<?php
/**
 * Tests for WP_IPayTotal_WooCommerce. Tests the actions are correctly added.
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Class WP_IPayTotal_WooCommerce_Test
 */
class WP_IPayTotal_WooCommerce_Test extends WP_UnitTestCase {

	/**
	 * Ensure the `wowp_iptwpg_ipaytotal_init` method is added to the `plugins_loaded` action.
	 */
	public function test_add_action_plugins_loaded_wowp_iptwpg_ipaytotal_init() {

		$action_name       = 'plugins_loaded';
		$expected_priority = 0;

		$wp_ipaytotal_woocommerce = $GLOBALS['wp_ipaytotal_woocommerce'];
		$function = array( $wp_ipaytotal_woocommerce, 'woocommerce_init' );

		$actual_action_priority = has_action( $action_name, $function );

		$this->assertNotFalse( $actual_action_priority );

		$this->assertEquals( $expected_priority, $actual_action_priority );
	}



	/**
	 * Ensure the `WOWP_IPTWPG_IPayTotal` class is added to the `woocommerce_payment_gateways` filter.
	 */
	public function test_add_filter_woocommerce_payment_gateways_wowp_iptwpg_add_ipaytotal_gateway() {

		$filter_name       = 'woocommerce_payment_gateways';
		$expected_priority = 10;

		$wp_ipaytotal_woocommerce = $GLOBALS['wp_ipaytotal_woocommerce'];
		$function          = array( $wp_ipaytotal_woocommerce, 'add_gateway_to_woocommerce' );

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );

	}

	/**
	 * Verify the filter function correctly adds the payment gateway to WooCommerce.
	 */
	public function test_woocommerce_payment_gateways_populated() {

		$woocommerce_payment_gateways = apply_filters( 'woocommerce_payment_gateways', array() );

		$this->assertContains( 'WOWP_IPTWPG_IPayTotal', $woocommerce_payment_gateways );

	}

	/**
	 * Ensure the function which calls `load_plugin_textdomain` is enqueued at the `plugins_loaded` action.
	 */
	public function test_set_locale() {

		$action_name       = 'plugins_loaded';
		$expected_priority = 10;

		$wp_ipaytotal_woocommerce = $GLOBALS['wp_ipaytotal_woocommerce'];

		$function = array( $wp_ipaytotal_woocommerce->i18n, 'load_plugin_textdomain' );

		$actual_action_priority = has_action( $action_name, $function );

		$this->assertNotFalse( $actual_action_priority );

		$this->assertEquals( $expected_priority, $actual_action_priority );
	}

	/**
	 * Ensure the `plugin_action_links` function is correctly added to the `plugin_action_links_*` fitler.
	 */
	public function test_add_filter_plugin_action_links() {

		global $plugin_root_dir;

		$plugin_basename = $plugin_root_dir . '/wp-ipaytotal-woocommerce.php';

		$filter_name       = 'plugin_action_links_' . ltrim( $plugin_basename, '/' );
		$expected_priority = 10;

		$wp_ipaytotal_woocommerce = $GLOBALS['wp_ipaytotal_woocommerce'];

		$function = array( $wp_ipaytotal_woocommerce->plugins_page, 'plugin_action_links' );

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );
	}


}
