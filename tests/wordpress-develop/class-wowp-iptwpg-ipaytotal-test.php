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

	/**
	 * init_form_fields is called by the constructor.
	 */
	public function test_init_form_fields() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		// Verify the two form fields are added:
		$this->assertEqualSets( [ 'enabled', 'ipt_key_secret' ], array_keys( $gateway->form_fields ) );
		
	}


	/**
	 * Validation is currently unimplemented and always passes.
	 */
	public function test_validate_fields() {

		$validate_fields = $this->wowp_iptwpg_ipaytotal->validate_fields();

		$this->assertTrue( $validate_fields );
	}

}
