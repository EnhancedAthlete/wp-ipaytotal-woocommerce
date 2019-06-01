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
	public function test_add_action_plugins_loaded_wowp_iptwpg_load_plugin_textdomain() {

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
	public function test_add_action_plugins_loaded_wowp_iptwpg_ipaytotal_init() {

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
	public function test_add_filter_woocommerce_payment_gateways_wowp_iptwpg_add_ipaytotal_gateway() {

		$filter_name       = 'woocommerce_payment_gateways';
		$expected_priority = 10;
		$function          = 'wowp_iptwpg_add_ipaytotal_gateway';

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
	 * Ensure the `wowp_iptwpg_ipaytotal_action_links` function is added to the `plugin_action_links_*` fitler.
	 */
	public function test_add_filter_plugin_action_links_wowp_iptwpg_ipaytotal_action_links() {

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
	 * Verify the content of the action links.
	 *
	 * TODO: The Deactivate link isn't returned when the filter is run in the test, suggesting it's
	 * not being run on plugins.php page as it should.
	 */
	public function test_wowp_iptwpg_ipaytotal_action_links() {

		$expected_anchor    = get_site_url() . '/wp-admin/admin.php?page=wc-settings&tab=checkout';
		$expected_link_text = 'Settings';

		global $plugin_root_dir;

		$plugin_basename = $plugin_root_dir . '/wp-ipaytotal-woocommerce.php';

		$filter_name = 'plugin_action_links_' . ltrim( $plugin_basename, '/' );

		$this->go_to( '/wp-admin/plugins.php/ ' );

		$plugin_action_links = apply_filters( $filter_name, array() );

		$first_link = $plugin_action_links[0];

		$dom = new DOMDocument();

		@$dom->loadHtml( mb_convert_encoding( $first_link, 'HTML-ENTITIES', 'UTF-8' ) );

		$nodes = $dom->getElementsByTagName( 'a' );

		$this->assertEquals( 1, $nodes->length );

		/** @var DOMNode $node */
		$node = $nodes->item( 0 );

		$actual_anchor    = $node->getAttribute( 'href' );
		$actual_link_text = $node->nodeValue;

		$this->assertEquals( $expected_anchor, $actual_anchor );
		$this->assertEquals( $expected_link_text, $actual_link_text );
	}

	/**
	 * Ensure `wowp_iptwpg_ipaytotal_custom_credit_card_fields` has been added to `woocommerce_credit_card_form_fields` filter correctly.
	 */
	public function test_add_filter_woocommerce_credit_card_form_fields_wowp_iptwpg_ipaytotal_custom_credit_card_fields() {

		$filter_name       = 'woocommerce_credit_card_form_fields';
		$expected_priority = 10;
		$function          = 'wowp_iptwpg_ipaytotal_custom_credit_card_fields';

		$actual_filter_priority = has_filter( $filter_name, $function );

		$this->assertNotFalse( $actual_filter_priority );

		$this->assertEquals( $expected_priority, $actual_filter_priority );

	}

	/**
	 * Verify the content of the custom credit card fields.
	 *
	 * This method would be more appropriate in the WC_Payment_Gateway_CC subclass.
	 *
	 * @see WC_Payment_Gateway_CC
	 */
	public function test_wowp_iptwpg_ipaytotal_custom_credit_card_fields() {

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

			/** @var DOMNode $node */
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
