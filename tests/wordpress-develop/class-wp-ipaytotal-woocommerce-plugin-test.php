<?php
/**
 * Class WP_IPayTotal_WooCommerce_Plugin_Test. Tests the root plugin file.
 *
 * @package wp-ipaytotal-woocommerce
 * @author Brian Henry <BrianHenryIE@gmail.com>
 */

/**
 * Verifies hooks and filters are correctly added during initialization
 */
class WP_IPayTotal_WooCommerce_Plugin_Test extends WP_UnitTestCase {


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
	 *
	 * phpcs:disable WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
	 * phpcs:disable WordPress.PHP.NoSilencedErrors.Discouraged
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
