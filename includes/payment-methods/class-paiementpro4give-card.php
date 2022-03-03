<?php
/**
 * PaiementPro for Give | Credit Card
 *
 * @since 1.0.2
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaiementPro4Give_Card {

	/**
	 * Constructor
	 *
	 * @since  1.0.2
	 * @access public
	 *
	 * @return void
	 */
	public function __construct() {
		add_action( 'give_gateway_paiementpro_card', [ $this, 'process_donation' ] );
		add_action( 'give_paiementpro_card_cc_form', '__return_false' );
	}

	/**
	 * Process Donation
	 *
	 * @param array $data List of posted data.
	 *
	 * @since  1.0.2
	 * @access public
	 *
	 * @return void
	 */
	public function process_donation( $data ) {
		// Check for any stored errors.
		$errors = give_get_errors();

		if ( ! $errors ) {

			$form_id      = ! empty( $data['post_data']['give-form-id'] ) ? intval( $data['post_data']['give-form-id'] ) : false;
			$first_name   = ! empty( $data['post_data']['give_first'] ) ? $data['post_data']['give_first'] : '';
			$last_name    = ! empty( $data['post_data']['give_last'] ) ? $data['post_data']['give_last'] : '';
			$email        = ! empty( $data['post_data']['give_email'] ) ? $data['post_data']['give_email'] : '';
			$donation_key = ! empty( $data['purchase_key'] ) ? $data['purchase_key'] : '';
			$currency     = give_get_currency( $form_id );

			// Setup the donation details.
			$data_to_send = [
				'price'           => $data['price'],
				'give_form_title' => $data['post_data']['give-form-title'],
				'give_form_id'    => $form_id,
				'give_price_id'   => isset( $data['post_data']['give-price-id'] ) ? $data['post_data']['give-price-id'] : '',
				'date'            => $data['date'],
				'user_email'      => $email,
				'purchase_key'    => $data['purchase_key'],
				'currency'        => $currency,
				'user_info'       => $data['user_info'],
				'status'          => 'pending',
				'gateway'         => $data['gateway'],
			];

			// Record the pending payment.
			$donation_id = give_insert_payment( $data_to_send );

			// Verify donation payment.
			if ( ! $donation_id ) {

				// Record the error.
				give_record_gateway_error(
					__( 'Payment Error', 'give' ),
					sprintf(
					/* translators: %s: payment data */
						__( 'Payment creation failed before processing payment via PaiementPro. Payment data: %s', 'give' ),
						wp_json_encode( $data )
					),
					$donation_id
				);

				// Problems? Send back.
				give_send_back_to_checkout( '?payment-mode=' . $data['post_data']['payment-mode'] );
			}

			// Auto set payment to abandoned in one hour if donor is not able to donate in that time.
			wp_schedule_single_event( time() + HOUR_IN_SECONDS, 'paiementpro4give_set_donation_abandoned', [ $donation_id ] );

			$url          = paiementpro4give_get_api_url();
			$merchant_id  = paiementpro4give_get_merchant_id();
			$args         = [
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded',
				],
				'body'    => [
					'merchantId'      => $merchant_id,
					'currency'        => 952, // CHF.
					'amount'          => $data['price'],
					'channel'         => 'CARD',
					'customer_id'     => '',
					'description'     => give_payment_gateway_donation_summary( $data ),
					'email'           => $email,
					'firstname'       => $first_name,
					'lastname'        => $last_name,
					// 'phone_mobile'    => '',
					'phone_mobile'    => '9876543210',
					'referenceNumber' => $donation_key,
					'notificationURL' => give_get_success_page_uri(),
					'returnContext'   => wp_json_encode(
						[
							'id_order' => $donation_id,
						]
					),
				],
			];
			$response     = wp_remote_post( "{$url}init2.php", $args );
			$responseBody = wp_remote_retrieve_body( $response );
			$responseCode = wp_remote_retrieve_response_code( $response );

			if ( 200 === $responseCode ) {

				$responseBodyParts = explode( '|', $responseBody );
				$sessionId         = $responseBodyParts[1];

				$redirect_to_url = add_query_arg(
					[
						'sessionid' => $sessionId,
						'id'        => $donation_id,
					],
					"{$url}processing.php"
				);

			} else {
				// Send user to failed page and change donation status to failed as well.
				give_update_payment_status( $donation_id, 'failed' );
				$redirect_to_url = give_get_failed_transaction_uri();
			}

			wp_redirect( $redirect_to_url );
			give_die();
		}
	}
}

new PaiementPro4Give_Card();
