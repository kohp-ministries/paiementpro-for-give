<?php
/**
 * PaiementPro for Give | Orange Money
 *
 * @since 1.0.0
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class PaiementPro4Give_Orange_Money {
	public function __construct() {
		add_action( 'give_gateway_paiementpro_orange_money', [ $this, 'process_donation' ] );
		add_action( 'give_paiementpro_orange_money_cc_form', '__return_false' );
		add_action( 'init', [ $this, 'verify_payment' ] );
	}

	public function process_donation( $data ) {
		// Check for any stored errors.
		$errors = give_get_errors();

		if ( ! $errors ) {

			$form_id         = ! empty( $data['post_data']['give-form-id'] ) ? intval( $data['post_data']['give-form-id'] ) : false;
			$first_name      = ! empty( $data['post_data']['give_first'] ) ? $data['post_data']['give_first'] : '';
			$last_name       = ! empty( $data['post_data']['give_last'] ) ? $data['post_data']['give_last'] : '';
			$email           = ! empty( $data['post_data']['give_email'] ) ? $data['post_data']['give_email'] : '';
			$donation_key    = ! empty( $data['purchase_key'] ) ? $data['purchase_key'] : '';
			$currency        = give_get_currency( $form_id );

			// Setup the donation details.
			$data_to_send = array(
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
			);

			// Record the pending payment.
			$donation_id = give_insert_payment( $data_to_send );

			// Verify donation payment.
			if ( ! $donation_id ) {

				// Record the error.
				give_record_gateway_error(
					__( 'Payment Error', 'paiementpro-for-give' ),
					sprintf(
					/* translators: %s: payment data */
						__( 'Payment creation failed before processing payment via PaiementPro. Payment data: %s', 'paiementpro-for-give' ),
						wp_json_encode( $data )
					),
					$donation_id
				);

				// Problems? Send back.
				give_send_back_to_checkout( '?payment-mode=' . $data['post_data']['payment-mode'] );
			}

			// Auto set payment to abandoned in one hour if donor is not able to donate in that time.
			wp_schedule_single_event( current_time( 'timestamp', 1 ) + HOUR_IN_SECONDS, 'paiementpro4give_set_donation_abandoned', [ $donation_id ] );


			$url         = paiementpro4give_get_api_url();
			$merchant_id = paiementpro4give_get_merchant_id();
			$args        = [
				'headers' => [
					'Content-Type' => 'application/x-www-form-urlencoded'
				],
				'body'    => [
					'merchantId'      => $merchant_id,
					'currency'        => 952,
					'amount'          => $data['price'],
					'channel'         => 'OM',
					'customer_id'     => '',
					'description'     => 'Hello',
					'email'           => $email,
					'firstname'       => $first_name,
					'lastname'        => $last_name,
					'phone_mobile'    => '9876543210',
					'referenceNumber' => $donation_key,
					'notificationURL' => give_get_success_page_uri(),
					'returnContext'   => wp_json_encode( [
						'id_order' => $donation_id,
					] ),
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
						'id' => $donation_id,
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

	/**
	 * This function will validate payment on donation receipt page.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function verify_payment() {

		$getData       = give_clean( $_GET );
		$donationId    = ! empty( $getData['order_id'] ) ? $getData['order_id'] : false;
		$transactionId = ! empty( $getData['pp_status'] ) ? $getData['pp_status'] : '';

		// Bailout, if donation id not received from paiementpro gateway.
		if ( ! $donationId ) {
			return;
		}

		$donation_gateway = give_get_payment_gateway( $donationId );

		// Bailout, if not `orange_money` payment gateway.
		if ( 'paiementpro_orange_money' !== $donation_gateway ) {
			return;
		}

		$credentialId = paiementpro4give_get_credential_id();
		$merchantId   = paiementpro4give_get_merchant_id();
		$errorStatus  = sha1("{$donationId}error{$merchantId}{$credentialId}" );
		$successStatus = sha1("{$donationId}success{$merchantId}{$credentialId}" );

		// Success, if transaction id exists, else failed.
		if ( ! empty( $transactionId ) && $transactionId === $successStatus ) {
			give_update_payment_status( $donationId, 'publish' );
		} elseif ( ! empty( $transactionId ) && $transactionId === $errorStatus ) {
			give_update_payment_status( $donationId, 'failed' );
		}
	}

}

new PaiementPro4Give_Orange_Money();