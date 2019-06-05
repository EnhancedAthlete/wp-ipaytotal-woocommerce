<?php
/**
 *
 * @package wp-ipaytotal-woocommerce
 */


class WOWP_IPTWPG_IPayTotal_Test extends WP_UnitTestCase {


	public function setUp() {
		parent::setUp();

	}

	/**
	 * Ensure the class is configured correctly.
	 */
	public function test_constructor() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		// The gateway id should be:
		$this->assertEquals( 'wowp_iptwpg_ipaytotal', $gateway->id );

		// The admin area title should be:
		$this->assertEquals( 'Credit/Debit Card', $gateway->method_title );

		// Ensure method_description is:
		$this->assertEquals( 'IPayTotal Payment Gateway Plug-in for WooCommerce', $gateway->method_description );

		// The title shown on to users should be:
		$this->assertEquals( 'Credit/Debit Card', $gateway->title );

		$this->assertNull( $gateway->icon );
		$this->assertTrue( $gateway->has_fields );

		$this->assertEqualSets( [ 'default_credit_card_form' ], $gateway->supports );

		// The gateway default enabled setting is 'no'
		$this->assertEquals( 'no', $gateway->enabled );

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

		$gateway = new WOWP_IPTWPG_IPayTotal();

		$validate_fields = $gateway->validate_fields();

		$this->assertTrue( $validate_fields );
	}

}
