<?php
/**
 * PaiementPro for Give | Actions
 *
 * @since 1.0.0
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * This function will validate payment on donation receipt page.
 *
 * @since 1.0.0
 *
 * @return void
 */
function paiementpro4give_verify_payment() {

	$getData       = give_clean( $_GET );
	$donationId    = ! empty( $getData['order_id'] ) ? $getData['order_id'] : false;
	$transactionId = ! empty( $getData['pp_status'] ) ? $getData['pp_status'] : '';

	// Bailout, if donation id not received from paiementpro gateway.
	if ( ! $donationId ) {
		return;
	}

	$donationGateway   = give_get_payment_gateway( $donationId );
	$supportedGateways = paiementpro4give_get_supported_payment_methods();

	// Bailout, if not `$donationGateway` payment gateway.
	if ( ! in_array( $donationGateway, $supportedGateways, true ) ) {
		return;
	}

	$credentialId  = paiementpro4give_get_credential_id();
	$merchantId    = paiementpro4give_get_merchant_id();
	$successStatus = sha1("{$donationId}success{$merchantId}{$credentialId}" );

	// Success, if transaction id exists, else failed.
	if ( ! empty( $transactionId ) && $transactionId === $successStatus ) {
		give_update_payment_status( $donationId, 'publish' );
	} else {
		give_update_payment_status( $donationId, 'failed' );
	}
}

add_action( 'init', 'paiementpro4give_verify_payment' );