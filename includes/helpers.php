<?php
/**
 * PaiementPro for Give | Helpers
 *
 * @since 1.0.0
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Get Merchant ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function paiementpro4give_get_merchant_id() {
	return give_get_option( 'paiementpro_live_merchant_id' );
}

/**
 * Get Credential ID.
 *
 * @since 1.0.0
 *
 * @return string
 */
function paiementpro4give_get_credential_id() {
	return give_get_option( 'paiementpro_live_credential_id' );
}

/**
 * Get API URL.
 *
 * @since 1.0.0
 *
 * @return string
 */
function paiementpro4give_get_api_url() {
	return give_get_option( 'paiementpro_live_api_url' );
}

/**
 * Get Supported Payment Methods list.
 *
 * @since 1.0.0
 *
 * @return array
 */
function paiementpro4give_get_supported_payment_methods() {
	return [
		'paiementpro4give_orange_money',
		'paiementpro4give_mtn_money',
		'paiementpro4give_moov_money',
		'paiementpro4give_card',
	];
}
