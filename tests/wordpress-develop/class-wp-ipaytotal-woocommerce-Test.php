<?php
/**
 * Class wp-ipaytotal-woocommerce-test
 *
 * @package
 */

/**
 * Verifies hooks and filters are correctly added during initialization
 */
class WP_IPayTotal_WooCommerce_Test extends WP_UnitTestCase {

	public function setUp() {
		parent::setUp();

	}

	/**
	 *
	 */
	function test_add_action_plugins_loaded_wowp_iptwpg_load_plugin_textdomain() {

		$action_name       = 'plugins_loaded';
		$expected_priority = 10;
		$function          = 'wowp_iptwpg_load_plugin_textdomain';

		$actual_action_priority = has_action( $action_name, $function );

		$this->assertNotFalse( $actual_action_priority );

		$this->assertEquals( $expected_priority, $actual_action_priority );
	}

	/**
	 * Ensure the `wowp_iptwpg_ipaytotal_init` method is added to the `plugins_loaded` action.
	 */
	function test_add_action_plugins_loaded_wowp_iptwpg_ipaytotal_init() {

		$action_name       = 'plugins_loaded';
		$expected_priority = 0;
		$function          = 'wowp_iptwpg_ipaytotal_init';

		$actual_action_priority = has_action( $action_name, $function );

		$this->assertNotFalse( $actual_action_priority );

		$this->assertEquals( $expected_priority, $actual_action_priority );
	}

	/**
	 * Ensure the `WOWP_IPTWPG_IPayTotal` class is added to the `woocommerce_payment_gateways` filter.
	 */
	function test_add_filter_woocommerce_payment_gateways_wowp_iptwpg_add_ipaytotal_gateway() {

		$filter_name = 'woocommerce_payment_gateways';
		$expected_priority    = 10;
		$function    = 'wowp_iptwpg_add_ipaytotal_gateway';

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );

	}

	/**
	 * Ensure the `wowp_iptwpg_ipaytotal_action_links` function is added to the `plugin_action_links_*` fitler.
	 */
	function test_add_filter_plugin_action_links_wowp_iptwpg_ipaytotal_action_links() {

		global $plugin_root_dir;

		$plugin_basename = $plugin_root_dir . '/wp-ipaytotal-woocommerce.php';

		$filter_name       = 'plugin_action_links_' . ltrim( $plugin_basename, '/' );
		$expected_priority = 10;
		$function          = 'wowp_iptwpg_ipaytotal_action_links';

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );
	}

	/**
	 * Ensure `wowp_iptwpg_ipaytotal_custom_credit_card_fields` has been added to `woocommerce_credit_card_form_fields` filter correctly.
	 */
	function test_add_filter_woocommerce_credit_card_form_fields_wowp_iptwpg_ipaytotal_custom_credit_card_fields() {

		$filter_name       = 'woocommerce_credit_card_form_fields';
		$expected_priority = 10;
		$function          = 'wowp_iptwpg_ipaytotal_custom_credit_card_fields';

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );

	}

}
