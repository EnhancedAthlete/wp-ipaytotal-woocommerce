<?php
/**
 * Tests for gateway helper methods defined in -api class.
 *
 * @package wp-ipaytotal-woocommerce
 */

/**
 * Class WOWP_IPTWPG_IPayTotal_API_Test
 */
class WOWP_IPTWPG_IPayTotal_API_Test extends \WP_Mock\Tools\TestCase {

	/**
	 * Ensure the constructor correctly parses the submitted credit card data.
	 */
	public function test_constructor() {

		$this->markTestSkipped( 'Data parsed here is not used by iPayTotal API.' );
	}

	/**
	 * Ensure the order's item's product details are parsed.
	 */
	public function test_get_detalle_data() {

		$this->markTestSkipped( 'Data parsed here is not used by iPayTotal API.' );
	}

	/**
	 * Ensure the parsed credit card data is returned.
	 */
	public function test_get_credit_card_data() {

		$this->markTestSkipped( 'This function is not used by iPayTotal API.' );

	}

	/**
	 * Ensure the response body is parsed correctly.
	 */
	public function test_get_response_body() {

		$this->markTestSkipped( 'Test data required.' );
	}

}
