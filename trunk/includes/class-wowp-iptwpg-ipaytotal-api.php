<?php
/**
 * Data manipulation for API.
 *
 * Parses newlines from API response.
 *
 * @package wp-ipaytotal-woocommerce
 * @author  iPayTotal Ltd
 * @since   1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WOWP_IPTWPG_IPayTotal_API
 */
class WOWP_IPTWPG_IPayTotal_API {

	/**
	 * The result of a version check on WooCommerce >= 3.0.
	 *
	 * @var bool True if WooCommerce version is below 3.0.
	 */
	public $wc_pre_30;


	/**
	 * WOWP_IPTWPG_iPayTotal_API constructor.
	 *
	 * Parses credit card details from POST.
	 */
	public function __construct() {

		$this->wc_pre_30 = version_compare( WC_VERSION, '3.0.0', '<' );

		$name_on_card     = '';
		$account_number   = '';
		$expiration_month = '';
		$expiration_year  = '';
		$card_ccv         = 'no';

		if ( isset( $_POST['wowp_iptwpg_ipaytotal-card-name'] ) ) {
			$name_on_card = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-name'] ) );
			$name_on_card = mb_convert_encoding( $name_on_card, 'HTML-ENTITIES' );
		}

		if ( isset( $_POST['wowp_iptwpg_ipaytotal-card-number'] ) ) {
			$account_number = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-number'] ) );
			$account_number = str_replace( array( ' ', '-' ), '', $account_number );
		}

		if ( isset( $_POST['wowp_iptwpg_ipaytotal-card-expiry'] ) ) {
			$date_input       = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-expiry'] ) );
			$date_array       = explode( '/', str_replace( ' ', '', $date_input ) );
			$expiration_month = $date_array[0];
			$expiration_year  = $date_array[1];
		}

		if ( isset( $_POST['wowp_iptwpg_ipaytotal-card-cvc'] ) ) {
			$card_ccv = sanitize_text_field( wp_unslash( $_POST['wowp_iptwpg_ipaytotal-card-cvc'] ) );
		}

		$this->credit_card_data = array(
			'nameCard'        => $name_on_card,
			'accountNumber'   => $account_number,
			'expirationMonth' => $expiration_month,
			'expirationYear'  => $expiration_year,
			'CVVCard'         => $card_ccv,
		);
	}


	/**
	 * Parses WooCommerce products to JSON for API.
	 *
	 * @param WC_Product[] $products The order's products to include in the API call.
	 *
	 * @return string
	 */
	public function get_detalle_data( $products ) {

		$detalle = array();

		foreach ( $products as $product ) {
			$detalle[] = array(
				'id_producto' => $product->get_product_id(),
				'cantidad'    => $product->get_quantity(),
				'tipo'        => $product->get_type(),
				'nombre'      => $product->get_name(),
				'precio'      => get_post_meta( $product->get_product_id(), '_regular_price', true ),
				'Subtotal'    => $product->get_total(),
			);
		}

		$detalle_data = wp_json_encode( $detalle );

		return $detalle_data;
	}


	/**
	 * Getter for card data parsed from POST in constructor.
	 *
	 * @return string
	 */
	public function get_credit_card_data() {

		$credit_card_data = wp_json_encode( $this->credit_card_data );

		return $credit_card_data;
	}


	/**
	 * Parses API response body.
	 *
	 * Removes newlines and returns JSON object before "|" character.
	 *
	 * @param array|WP_Error $response The response to the API POST request.
	 *
	 * @return array|mixed|object
	 */
	public function get_response_body( $response ) {

		/**
		 * Get body response while get not error.
		 *
		 * @var string $response_body
		 */
		$response_body = wp_remote_retrieve_body( $response );

		foreach ( preg_split( "/\r?\n/", $response_body ) as $line ) {
			$resp = explode( '|', $line );
		}

		// values get.
		$r = json_decode( $resp[0], true );

		return $r;
	}

}

