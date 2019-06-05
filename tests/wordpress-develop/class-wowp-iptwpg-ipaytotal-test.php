<?php
/**
 *
 * @package wp-ipaytotal-woocommerce
 */


class WOWP_IPTWPG_IPayTotal_Test extends WP_UnitTestCase {

	/** @var WOWP_IPTWPG_IPayTotal */
	protected $wowp_iptwpg_ipaytotal;

	public function setUp() {
		parent::setUp();

		$this->wowp_iptwpg_ipaytotal = new WOWP_IPTWPG_IPayTotal();

	}

	/**
	 * Ensure the class is configured correctly.
	 */
	public function test_constructor() {

		$gateway = $this->wowp_iptwpg_ipaytotal;

		// The gateway id should be:
		$this->assertEquals( 'wowp_iptwpg_ipaytotal', $gateway->id );

		// The admin area title should be:
		$this->assertEquals( 'Credit/Debit Card', $gateway->method_title );

	}

	}

}
