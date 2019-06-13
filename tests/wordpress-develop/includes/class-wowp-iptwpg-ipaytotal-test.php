<?php
/**
 * Tests for WOWP_IPTWPG_IPayTotal. Tests constructor values, user input field setup, payment submission.
 *
 * phpcs:disable Generic.Classes.DuplicateClassName.Found
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Class WOWP_IPTWPG_IPayTotal_Test
 */
class WOWP_IPTWPG_IPayTotal_Test extends WP_UnitTestCase {

	/**
	 * Sets up the environment. Imports WP_Error.
	 */
	public function setUp() {
		parent::setUp();

		global $project_root_dir;
		require_once $project_root_dir . '/vendor/cyruscollier/wordpress-develop/src/wp-includes/class-wp-error.php';

	}

	/**
	 * Ensure the WOWP_IPTWPG_IPayTotal is configured correctly.
	 */
	public function test_constructor() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		// The gateway id should be set.
		$this->assertEquals( 'wowp_iptwpg_ipaytotal', $gateway->id );

		// The admin area title should be set.
		$this->assertEquals( 'Credit/Debit Card', $gateway->method_title );

		// Ensure method_description is set.
		$this->assertEquals( 'IPayTotal Payment Gateway Plug-in for WooCommerce', $gateway->method_description );

		// The title shown on to users should be set.
		$this->assertEquals( 'Credit/Debit Card', $gateway->title );

		$this->assertNull( $gateway->icon );
		$this->assertTrue( $gateway->has_fields );

		$this->assertEqualSets( [ 'default_credit_card_form' ], $gateway->supports );

		// The gateway default enabled setting is 'no'.
		$this->assertEquals( 'no', $gateway->enabled );

	}

	/**
	 * init_form_fields is called by the constructor.
	 */
	public function test_init_form_fields() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		// Verify the two form fields are added.
		$this->assertEqualSets( [ 'enabled', 'ipt_key_secret' ], array_keys( $gateway->form_fields ) );

	}

	/**
	 * Tests the URL is correct and that the required fields are submitted to the API, without extraneous fields.
	 *
	 * @see https://wordpress.stackexchange.com/questions/133414/how-do-i-mock-http-requests-for-phpunit
	 */
	public function test_process_payment() {

		// Prepare gateway options:
		// Could normally populate directly at $gateway->form_fields but the previous developer opted to create instance variables for each option.
		update_option(
			'woocommerce_wowp_iptwpg_ipaytotal_settings',
			array(
				'enabled'        => 'yes',
				'ipt_key_secret' => 'YjpuyoOFLmEaTGubLI09klkYV5czKscDpGqb85l7RD05vWhD5agW1aZ1gYloeeYWYqyDTv', // Not a real API key.
			)
		);

		$gateway = new WOWP_IPTWPG_IPayTotal();

		/**
		 * Add a filter on WordPress's HTTP request to cancel the request and to
		 * verify the data is correct.
		 *
		 * @see WP_Http l.258
		 *
		 * @param false|array|WP_Error $should_stop_execution   Whether to preempt an HTTP request's return value. Default false.
		 * @param array                $request                 HTTP request arguments.
		 * @param string               $url                     The request URL.
		 */
		add_filter(
			'pre_http_request',
			function( $should_stop_execution, $request, $url ) {

				$this->assertEquals( 'https://ipaytotal.solutions/api/transaction', $url );

				$body   = json_decode( $request['body'], true );
				$fields = array_keys( $body );

				$required_fields = array(
					'api_key',
					'first_name',
					'last_name',
					'address',
					'country',
					'state',
					'city',
					'zip',
					'email',
					'phone_no',
					'card_type',
					'amount',
					'currency',
					'card_no',
					'ccExpiryMonth',
					'ccExpiryYear',
					'cvvNumber',
				);

				$optional_fields = array(
					'sulte_apt_no',
					'birth_date',
					'shipping_first_name',
					'shipping_last_name',
					'shipping_address',
					'shipping_country',
					'shipping_state',
					'shipping_city',
					'shipping_zip',
					'shipping_email',
					'shipping_phone_no',
					'is_recurring',
				);

				// Ensure all required fields are present.
				foreach ( $required_fields as $required_field ) {
					$this->assertTrue( in_array( $required_field, $fields, true ), 'Missing API field: ' . $required_field . 'asd' );
				}

				// Ensure all fields being submitted to the API are valid.
				$this->assertEquals( 0, count( array_diff( $required_fields + $optional_fields, $fields ) ) );

				return new WP_Error( '0', __FUNCTION__ );

			},
			10,
			3
		);

		// Prepare dummy order.
		$order = WC_Helper_Order::create_order();

		// Set $_POST variables.
		$_POST['wowp_iptwpg_ipaytotal-card-number'] = '4111 1111 1111 1111';
		$_POST['wowp_iptwpg_ipaytotal-card-expiry'] = '10/2020';
		$_POST['wowp_iptwpg_ipaytotal-card-cvc']    = '123';

		try {
			$gateway->process_payment( $order->get_id() );

		} catch ( Exception $e ) {

			$this->assertEquals( 'There is issue connecting to the payment gateway. Sorry for the inconvenience.', $e->getMessage() );
		}

	}

	/**
	 * Validation is currently unimplemented and always passes.
	 */
	public function test_validate_fields() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		$validate_fields = $gateway->validate_fields();

		$this->assertTrue( $validate_fields );
	}

	/**
	 * Ensure `custom_credit_card_fields` has been added to `woocommerce_credit_card_form_fields` filter correctly.
	 */
	public function test_add_filter_woocommerce_credit_card_form_fields_wowp_iptwpg_ipaytotal_custom_credit_card_fields() {

		$wowp_iptwpg_ipaytotal = new WOWP_IPTWPG_IPayTotal();

		$filter_name       = 'woocommerce_credit_card_form_fields';
		$expected_priority = 10;
		$function          = array( $wowp_iptwpg_ipaytotal, 'custom_credit_card_fields' );

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );

	}

	/**
	 * Verify the content of the custom credit card fields.
	 *
	 * @see WC_Payment_Gateway_CC
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
	 */
	public function test_custom_credit_card_fields() {

		// TODO: Navigate the tests to the checkout page rather than instantiating the class directly.
		$wowp_iptwpg_ipaytotal = new WOWP_IPTWPG_IPayTotal();

		$payment_gateway_id = 'wowp_iptwpg_ipaytotal';

		$cc_fields = apply_filters( 'woocommerce_credit_card_form_fields', array(), $payment_gateway_id );

		$expected_fields = array( 'card-name-field', 'card-expiry-field', 'card-number-field', 'card-cvc-field' );

		// Check that all and only the expected fields are present.
		$this->assertEqualSets( $expected_fields, array_keys( $cc_fields ) );

		foreach ( $cc_fields as $field ) {

			$dom = new DOMDocument();

			@$dom->loadHtml( mb_convert_encoding( $field, 'HTML-ENTITIES', 'UTF-8' ) );

			$paragraphs = $dom->getElementsByTagName( 'p' );

			// Each field's html is a paragraph.
			$this->assertCount( 1, $paragraphs );

			$paragraph = $paragraphs->item( 0 );

			// Each paragraph has two children.
			$this->assertEquals( 2, $paragraph->childNodes->length );

			// A label.
			$this->assertEquals( 'label', $paragraph->childNodes->item( 0 )->nodeName );

			// And an input.
			$this->assertEquals( 'input', $paragraph->childNodes->item( 1 )->nodeName );

		}

		// Tests should fail when anything is changed!

		$card_name_field = '<p class="form-row form-row-wide"><label for="wowp_iptwpg_ipaytotal-card-name">Cardholder Name<span class="required" title="required">*</span></label><input id="wowp_iptwpg_ipaytotal-card-name" class="input-text wc-credit-card-form-card-name" type="text" maxlength="30" autocomplete="off" placeholder="CARDHOLDER NAME" name="wowp_iptwpg_ipaytotal-card-name" /></p>';
		$this->assertEquals( $card_name_field, $cc_fields['card-name-field'] );

		$card_number_field = '<p class="form-row form-row-wide"><label for="wowp_iptwpg_ipaytotal-card-number">Card Number<span class="required">*</span></label><input id="wowp_iptwpg_ipaytotal-card-number" class="input-text wc-credit-card-form-card-number" inputmode="numeric" autocomplete="cc-number" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="•••• •••• •••• ••••" name="wowp_iptwpg_ipaytotal-card-number" /></p>';
		$this->assertEquals( $card_number_field, $cc_fields['card-number-field'] );

		$card_expiry_field = '<p class="form-row form-row-first"><label for="wowp_iptwpg_ipaytotal-card-expiry">Expiry (MM/YYYY)<span class="required">*</span></label><input id="wowp_iptwpg_ipaytotal-card-expiry" class="input-text wc-credit-card-form-card-expiry" inputmode="numeric" autocomplete="cc-exp" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" placeholder="MM / YYYY" name="wowp_iptwpg_ipaytotal-card-expiry" /></p>';
		$this->assertEquals( $card_expiry_field, $cc_fields['card-expiry-field'] );

		$card_cvc_field = '<p class="form-row form-row-last"><label for="wowp_iptwpg_ipaytotal-card-cvc">Card Code<span class="required">*</span></label><input id="wowp_iptwpg_ipaytotal-card-cvc" class="input-text wc-credit-card-form-card-cvc"inputmode="numeric" autocomplete="off" autocorrect="no" autocapitalize="no" spellcheck="no" type="tel" maxlength="4" placeholder="CVV" style="width:100px" name="wowp_iptwpg_ipaytotal-card-cvc" /></p>';
		$this->assertEquals( $card_cvc_field, $cc_fields['card-cvc-field'] );

	}


}
