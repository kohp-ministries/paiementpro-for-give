<?php
/**
 * PaiementPro for Give | Settings
 *
 * @since 1.0.0
 */

// Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Admin Section.
 *
 * @param array $sections List of sections.
 *
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function paiementpro4give_register_section( $sections ) {
	$sections['paiementpro'] = __( 'PaiementPro', 'paiementpro4give-for-give' );

	return $sections;
}

add_filter( 'give_get_sections_gateways', 'paiementpro4give_register_section' );

/**
 * Register Admin Settings.
 *
 * @param array $settings List of settings.
 *
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function paiementpro4give_register_settings( $settings ) {

	$current_section = give_get_current_setting_section();

	switch ( $current_section ) {
		case 'paiementpro':
			$settings = array(
				array(
					'type' => 'title',
					'id'   => 'give_title_gateway_settings_paiementpro',
					'desc' => esc_html__( 'This plugin does not support test mode. So, please process the donations with LIVE mode.', 'paiementpro-for-give' ),
				),
				array(
					'name' => esc_html__( 'Merchant ID', 'paiementpro-for-give' ),
					'desc' => esc_html__( 'Please enter the Merchant ID from your PaiementPro account.', 'paiementpro-for-give' ),
					'id'   => 'paiementpro_live_merchant_id',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'Credential ID', 'paiementpro-for-give' ),
					'desc' => esc_html__( 'Please enter the Credential ID from your PaiementPro account.', 'paiementpro-for-give' ),
					'id'   => 'paiementpro_live_credential_id',
					'type' => 'text',
				),
				array(
					'name' => esc_html__( 'API URL', 'paiementpro-for-give' ),
					'desc' => esc_html__( 'Please enter the API URL from your PaiementPro account.', 'paiementpro-for-give' ),
					'id'   => 'paiementpro_live_api_url',
					'type' => 'text',
				),
				array(
					'type' => 'sectionend',
					'id'   => 'give_title_gateway_settings_paiementpro',
				)
			);
			break;
	}
	return $settings;
}

add_filter( 'give_get_settings_gateways', 'paiementpro4give_register_settings' );

/**
 * This function is used to add settings page link on plugins page.
 *
 * @param array $links List of links on plugin page.
 *
 * @since  1.0.0
 * @access public
 *
 * @return array
 */
function paiementpro4give_add_plugin_links( $links ) {

	$links['settings'] = sprintf(
		'<a href="%1$s">%2$s</a>',
		esc_url_raw( admin_url( 'edit.php?post_type=give_forms&page=give-settings&tab=gateways&section=paiementpro' ) ),
		__( 'Settings', 'paiementpro-for-give' )
	);

	asort( $links );

	return $links;
}

add_filter( 'plugin_action_links_' . MGMFG_PLUGIN_BASENAME, 'paiementpro4give_add_plugin_links' );
