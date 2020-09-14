<?php
/**
 * PaiementPro for Give | Filters
 *
 * @since 1.0.0
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function paiementpro4give_register_gateway( $gateways ) {

	$gateways['paiementpro_mtn_money'] = [
		'admin_label'    => esc_html__( 'PaiementPro - MTN Money', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'MTN Money', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_moov_money'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Moov Money', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Moov Money', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_orange_money'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Orange Money', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Orange Money', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_card'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Credit Card', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Credit Card', 'paiementpro-for-give' ),
	];

	return $gateways;
}

add_filter( 'give_payment_gateways', 'paiementpro4give_register_gateway' );
