<?php
/**
 *
 * @package wp-ipaytotal-woocommerce
 */


class WOWP_IPTWPG_IPayTotal_Test extends \WP_Mock\Tools\TestCase {

	public function setUp(): void {
		\WP_Mock::setUp();

		global $plugin_root_dir;
		global $project_root_dir;

		require_once $project_root_dir . '/vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-settings-api.php';
		require_once $project_root_dir . '/vendor/woocommerce/woocommerce/includes/abstracts/abstract-wc-payment-gateway.php';
		require_once $project_root_dir . '/vendor/woocommerce/woocommerce/includes/gateways/class-wc-payment-gateway-cc.php';

		require_once $plugin_root_dir . '/includes/class-wowp-iptwpg-ipaytotal.php';

		// Handle WordPress methods called by constructor.
		\WP_Mock::userFunction(
			'get_option',
			array(
				'args' => array( 'woocommerce_wowp_iptwpg_ipaytotal_settings', null ),
			)
		);

		\WP_Mock::userFunction(
			'wp_list_pluck',
			array(
				'args'   => array( \WP_Mock\Functions::type( 'array' ), 'default' ),
				'return' => array(),
			)
		);

		\WP_Mock::userFunction(
			'is_admin',
			array(
				'return' => false,
			)
		);

	}

	/**
	 * Ensure credit card types are detected correctly and the result is correct for the API.
	 *
	 * Since iPayTotal only accepts MasterCard and Visa, no tests are defined for the other card types.
	 *
	 * @see https://www.paypalobjects.com/en_AU/vhelp/paypalmanager_help/credit_card_numbers.htm
	 */
	public function test_getCreditCardType() {

		$gateway = new WOWP_IPTWPG_IPayTotal();

		// Mastercard
		$expected = '3';

		$card_number = '5555 5555 5555 4444';
		$result      = $gateway->getCreditCardType( $card_number );
		$this->assertEquals( $expected, $result, "input: $card_number" );

		$card_number = '5105 1051 0510 5100';
		$result      = $gateway->getCreditCardType( $card_number );
		$this->assertEquals( $expected, $result, "input: $card_number" );

		// VISA
		$expected = '2';

		$card_number = '4111 1111 1111 1111';
		$result      = $gateway->getCreditCardType( $card_number );
		$this->assertEquals( $expected, $result, "input: $card_number" );

		$card_number = '4012 8888 888 81881';
		$result      = $gateway->getCreditCardType( $card_number );
		$this->assertEquals( $expected, $result, "input: $card_number" );

		$card_number = '4222 2222 2222 2';
		$result      = $gateway->getCreditCardType( $card_number );
		$this->assertEquals( $expected, $result, "input: $card_number" );

	}
}
