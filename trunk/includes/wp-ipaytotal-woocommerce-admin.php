<?php
/**
 * The WooCommerce iPayTotal gateway class.
 *
 * @package wp-ipaytotal-woocommerce
 * @author  iPayTotal Ltd
 * @since   1.0.0
 */

/**
 * Class WOWP_IPTWPG_IPayTotal
 */
class WOWP_IPTWPG_IPayTotal extends WC_Payment_Gateway_CC {

	/**
	 * The API key as returned from WC_Settings database.
	 *
	 * @var string API key
	 */
	private $ipt_key_secret;

	/**
	 * wowp_iptwpg_ipaytotal constructor.
	 *
	 * Defines the gateway properties (title, description, capabilities...).
	 */
	public function __construct() {

		/**
		 * The payment gateway id, used throughout WooCommerce.
		 */
		$this->id = 'wowp_iptwpg_ipaytotal';

		/**
		 * The title for admin screens.
		 */
		$this->method_title = __( 'Credit/Debit Carda', 'wp-ipaytotal-woocommerce' );

		/**
		 * The description for admin screens.
		 *
		 * @see /wp-admin/admin.php?page=wc-settings&tab=checkout&section=wowp_iptwpg_ipaytotal
		 */
		$this->method_description = __( 'IPayTotal Payment Gateway Plug-in for WooCommerce', 'wp-ipaytotal-woocommerce' );

		/**
		 * The gateway's title on the frontend.
		 */
		$this->title = __( 'Credit/Debit Cardb', 'wp-ipaytotal-woocommerce' );

		/**
		 * The gateway's icon.
		 */
		$this->icon = null;

		/**
		 * Dictates if the gateway has fields on the checkout. e.g. cash on delivery might not.
		 */
		$this->has_fields = true;

		/**
		 * Supported features such as 'default_credit_card_form', 'refunds'.
		 */
		$this->supports = array( 'default_credit_card_form' );

		/**
		 * Defines the gateway's users customizable settings.
		 *
		 * @see https://docs.woocommerce.com/document/settings-api/
		 */
		$this->init_form_fields();

		/**
		 * Load the user settings from database.
		 *
		 * @see https://docs.woocommerce.com/document/settings-api/
		 */
		$this->init_settings();

		// Turn these settings into member variables we can use.
		foreach ( $this->settings as $setting_key => $value ) {
			$this->$setting_key = $value;
		}

		// Further check of SSL if you want.
		add_action( 'admin_notices', array( $this, 'do_ssl_check' ) );

		// Add the handler to save settings using the WC_Settings_API superclass process_admin_options method.
		if ( is_admin() ) {
			add_action( 'woocommerce_update_options_payment_gateways_' . $this->id, array( $this, 'process_admin_options' ) );
		}
	}


	/**
	 * Initializes $this->form_fields array to define gateway settings.
	 */
	public function init_form_fields() {

		$this->form_fields = array(
			'enabled'        => array(
				'title'   => __( 'Enable / Disable', 'wp-ipaytotal-woocommerce' ),
				'label'   => __( 'Enable this payment gateway', 'wp-ipaytotal-woocommerce' ),
				'type'    => 'checkbox',
				'default' => 'no',
			),
			'ipt_key_secret' => array(
				'title'    => __( 'API Secret Key', 'wp-ipaytotal-woocommerce' ),
				'type'     => 'text',
				'desc_tip' => __( 'This is the API Secret Key provided by iPayTotal when you signed up for an account.', 'wp-ipaytotal-woocommerce' ),
			),
		);

	}


	/**
	 * Determine the credit card type (Visa/Amex etc.) by running a regex on its number.
	 *
	 * Validates cards as:
	 * '1' - For Amex.
	 * '2' - For Visa.
	 * '3' - For Mastercard.
	 * '4' - For Discover & others.
	 *
	 * @param string $cc The credit card number.
	 * @param bool   $extra_check Flag to determine if validatecard function should be run.
	 *
	 * @return bool|string false if the card type cannot be determined, '1' for Amex, '2' for Visa, '3' for Mastercard. '4' for other.
	 */
	private function getCreditCardType( $cc, $extra_check = false ) {
		if ( empty( $cc ) ) {
			return false;
		}

		$cards   = array(
			'visa'       => '(4\d{12}(?:\d{3})?)',
			'amex'       => '(3[47]\d{13})',
			'jcb'        => '(35[2-8][89]\d\d\d{10})',
			'maestro'    => '((?:5020|5038|6304|6579|6761)\d{12}(?:\d\d)?)',
			'solo'       => '((?:6334|6767)\d{12}(?:\d\d)?\d?)',
			'mastercard' => '(5[1-5]\d{14})',
			'switch'     => '(?:(?:(?:4903|4905|4911|4936|6333|6759)\d{12})|(?:(?:564182|633110)\d{10})(\d\d)?\d?)',
		);
		$names   = array( 'Visa', 'American Express', 'JCB', 'Maestro', 'Solo', 'Mastercard', 'Switch' );
		$matches = array();
		$pattern = '#^(?:' . implode( '|', $cards ) . ')$#';
		$result  = preg_match( $pattern, str_replace( ' ', '', $cc ), $matches );
		if ( $extra_check && $result > 0 ) {
			$result = ( validatecard( $cc ) ) ? 1 : 0;
		}
		$card = ( $result > 0 ) ? $names[ count( $matches ) - 2 ] : false;

		switch ( $card ) {
			case 'Visa':
				return '2';

			case 'American Express':
				return '1';

			case 'Maestro':
			case 'Mastercard':
				return '3';

			default:
				return '4';
		}

	}


	/**
	 * Response handler for payment gateway.
	 *
	 * @param int $order_id The WooCommerce order id.
	 *
	 * @return array
	 * @throws Exception When a connection to the gateway cannot be created (presumably a local error).
	 */
	public function process_payment( $order_id ) {
		global $woocommerce;

		$customer_order = new WC_Order( $order_id );

		$products = $customer_order->get_items();

		$ipaytotal_card = new WOWP_IPTWPG_iPayTotal_API();

		if ( ! isset( $_POST['wowp_iptwpg_ipaytotal-card-number'] ) ) {
			throw new Exception( 'Card number not set' );
		}

		if ( ! isset( $_POST['wowp_iptwpg_ipaytotal-card-expiry'] ) ) {
			throw new Exception( 'Card expiry not set' );
		}

		$card_number = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-number'] ) );
		$card_type   = $this->getCreditCardType( $card_number );
		$card_no     = str_replace( array( ' ', '-' ), '', $card_number );

		$date_array = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-expiry'] ) );
		$date_array = explode( '/', str_replace( ' ', '', $date_array ) );
		$date_month = $date_array[0];
		$date_year  = $date_array[1];

		$cvv_number = ( isset( $_POST['wowp_iptwpg_ipaytotal-card-cvc'] ) ) ? sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-cvc'] ) ) : 'no';

		$data = array(
			'api_key'             => $this->ipt_key_secret,

			'first_name'          => $customer_order->get_billing_first_name(),
			'last_name'           => $customer_order->get_billing_last_name(),

			'address'             => $customer_order->get_billing_address_1(),
			'sulte_apt_no'        => wp_rand( 1, 99 ),
			'state'               => $customer_order->get_billing_state(),
			'city'                => $customer_order->get_billing_city(),
			'zip'                 => $customer_order->get_billing_postcode(),
			'country'             => $customer_order->get_billing_country(),

			'email'               => $customer_order->get_billing_email(),
			'phone_no'            => $customer_order->get_billing_phone(),
			'birth_date'          => wp_rand( 1, 12 ) . '/' . wp_rand( 1, 30 ) . '/' . wp_rand( 1985, 1991 ),

			'ip_address'          => $customer_order->get_customer_ip_address(),

			'amount'              => $customer_order->get_total(),
			'currency'            => $customer_order->get_currency(),

			'card_type'           => $card_type,
			'card_no'             => $card_no,
			'ccExpiryMonth'       => $date_month,
			'ccExpiryYear'        => $date_year,
			'cvvNumber'           => $cvv_number,

			'shipping_first_name' => $customer_order->get_shipping_first_name(),
			'shipping_last_name'  => $customer_order->get_shipping_last_name(),
			'shipping_address'    => $customer_order->get_shipping_address_1(),
			'shipping_country'    => $customer_order->get_shipping_address_2(),
			'shipping_state'      => $customer_order->get_shipping_country(),
			'shipping_city'       => $customer_order->get_shipping_state(),
			'shipping_zip'        => $customer_order->get_shipping_city(),
			'shipping_email'      => $customer_order->get_shipping_postcode(),
			'shipping_phone_no'   => $customer_order->get_billing_phone(),
		);

		// API endpoint URL.
		$environment_url = 'https://ipaytotal.solutions/api/transaction';

		$result = wp_remote_post(
			$environment_url,
			array(
				'method'    => 'POST',
				'body'      => wp_json_encode( $data ),
				'timeout'   => 90,
				'sslverify' => true,
				'headers'   => array( 'Content-Type' => 'application/json' ),
			)
		);

		if ( is_wp_error( $result ) ) {

			throw new Exception( __( 'There is issue for connectin payment gateway. Sorry for the inconvenience.', 'wp-ipaytotal-woocommerce' ) );

		} elseif ( empty( $result['body'] ) ) {

			throw new Exception( __( 'iPayTotal\'s Response was not get any data.', 'wp-ipaytotal-woocommerce' ) );
		}

		$response_body = $ipaytotal_card->get_response_body( $result );

		// 100 or 200 means the transaction was a success.
		if ( 'success' === $response_body['status'] ) {

			$response_message = $response_body['message'];

			// Add the gateway response message to the admin order notes.
			$customer_order->add_order_note( $response_message );

			// Mark the order as paid.
			$customer_order->payment_complete();

			// Empty the customer's cart.
			$woocommerce->cart->empty_cart();

			wc_add_notice( __( 'Payment successful. ' ) . $response_body['message'] . ' - ' . $response_body['descripcion'] . '.', 'error' );

			// Redirect the user to thank you page.
			$success = array(
				'result'   => 'success',
				'redirect' => $this->get_return_url( $customer_order ),
			);

			return $success;
		} else {
			wc_add_notice( __( 'Payment failed. ' ) . ' ' . $response_body['message'] . ' ' . $response_body['descripcion'] . '.', 'error' );
			$customer_order->update_status( 'failed' );
		}

	}


	/**
	 * Validate payment fields on the frontend.
	 *
	 * @see WC_Payment_Gateway
	 * @see https://rudrastyh.com/woocommerce/payment-gateway-plugin.html
	 *
	 * @return bool
	 */
	public function validate_fields() {
		return true;
	}

}
