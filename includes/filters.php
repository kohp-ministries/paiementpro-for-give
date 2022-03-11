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

	$gateways['paiementpro_mtn_money_ci'] = [
		'admin_label'    => esc_html__( 'PaiementPro - MTN Money Côte d\'ivoire', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'MTN Money Côte d\'ivoire', 'paiementpro-for-give' ),
	];
	
	$gateways['paiementpro_mtn_money_bj'] = [
		'admin_label'    => esc_html__( 'PaiementPro - MTN Money Bénin', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'MTN Money Bénin', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_moov_money_ci'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Moov Money Côte d\'ivoire', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Moov Money Côte d\'ivoire', 'paiementpro-for-give' ),
	];
	
	$gateways['paiementpro_moov_money_bj'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Moov Money Bénin', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Moov Money Bénin', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_orange_money_bf'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Orange Money Burkina faso', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Orange Money Burkina faso', 'paiementpro-for-give' ),
	];
	
	$gateways['paiementpro_orange_money_ci'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Orange Money Côte d\'ivoire', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Orange Money Côte d\'ivoire', 'paiementpro-for-give' ),
	];
	
	$gateways['paiementpro_orange_money_ml'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Orange Money Mali', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Orange Money Mali', 'paiementpro-for-give' ),
	];

	$gateways['paiementpro_card'] = [
		'admin_label'    => esc_html__( 'PaiementPro - Credit Card', 'paiementpro-for-give' ),
		'checkout_label' => esc_html__( 'Credit Card', 'paiementpro-for-give' ),
	];

	return $gateways;
}

add_filter( 'give_payment_gateways', 'paiementpro4give_register_gateway' );
