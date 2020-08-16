<?php
/**
 * Plugin Name:       PaiementPro for Give
 * Version:           1.0.0
 * Author:            PaiementPro
 * Author URI:        https://paiementpro.net
 * Plugin URI:        https://wordpress.org/plugins/paiementpro-for-give/
 * Description:       Accept donations for GiveWP using PaiementPro payment gateway.
 * Requires at least: 4.8
 * Requires PHP:      5.6
 * Text Domain:       paiementpro-for-give
 * License:           GPL v2 or later
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

 // Bailout, if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'PaiementPro4Give' ) ) {

	final class PaiementPro4Give {

		/**
		 * Instance.
		 *
		 * @since  1.0.0
		 * @access static
		 * @var
		 */
		static private $instance;

		/**
		 * Notices (array)
		 *
		 * @since 1.0.3
		 *
		 * @var array
		 */
		public $notices = [];

		/**
		 * Singleton pattern.
		 *
		 * @since  1.0.0
		 * @access private
		 */
		private function __construct() {
		}

		/**
		 * Get instance.
		 *
		 * @return static
		 * @since  1.0.0
		 * @access static
		 *
		 */
		static function get_instance() {
			if ( null === static::$instance ) {
				self::$instance = new self();
				self::$instance->setup();
			}

			return self::$instance;
		}

		/**
		 * Setup PaiementPro for Give.
		 *
		 * @return void
		 * @since  1.0.0
		 * @access private
		 *
		 */
		private function setup() {
			// Setup constants.
			$this->setup_constants();

			// Give init hook.
			add_action( 'plugins_loaded', [ $this, 'init' ], 101 );
			add_action( 'admin_notices', [ $this, 'admin_notices' ], 15 );
		}

		/**
		 * Setup constants.
		 *
		 * @return void
		 * @since  1.0
		 * @access public
		 *
		 */
		public function setup_constants() {
			if ( ! defined( 'PAIEMENTPRO4GIVE_VERSION' ) ) {
				define( 'PAIEMENTPRO4GIVE_VERSION', '1.0.0' );
			}

			if ( ! defined( 'PAIEMENTPRO4GIVE_MIN_GIVE_VER' ) ) {
				define( 'PAIEMENTPRO4GIVE_MIN_GIVE_VER', '2.5.0' );
			}

			if ( ! defined( 'PAIEMENTPRO4GIVE_PLUGIN_FILE' ) ) {
				define( 'PAIEMENTPRO4GIVE_PLUGIN_FILE', __FILE__ );
			}

			if ( ! defined( 'PAIEMENTPRO4GIVE_PLUGIN_BASENAME' ) ) {
				define( 'PAIEMENTPRO4GIVE_PLUGIN_BASENAME', plugin_basename( PAIEMENTPRO4GIVE_PLUGIN_FILE ) );
			}

			if ( ! defined( 'PAIEMENTPRO4GIVE_PLUGIN_DIR' ) ) {
				define( 'PAIEMENTPRO4GIVE_PLUGIN_DIR', plugin_dir_path( PAIEMENTPRO4GIVE_PLUGIN_FILE ) );
			}

			if ( ! defined( 'PAIEMENTPRO4GIVE_PLUGIN_URL' ) ) {
				define( 'PAIEMENTPRO4GIVE_PLUGIN_URL', plugin_dir_url( PAIEMENTPRO4GIVE_PLUGIN_FILE ) );
			}
		}

		/**
		 * Load the text domain.
		 *
		 * @access private
		 * @return void
		 * @since  1.0.0
		 *
		 */
		public function load_textdomain() {

			// Set filter for plugin's languages directory.
			$lang_dir = dirname( PAIEMENTPRO4GIVE_PLUGIN_BASENAME ) . '/languages/';
			$lang_dir = apply_filters( 'paiementpro4give_languages_directory', $lang_dir );

			// Traditional WordPress plugin locale filter.
			$locale  = apply_filters( 'plugin_locale', get_locale(), 'paiementpro-for-give' );
			$mo_file = sprintf( '%1$s-%2$s.mo', 'paiementpro-for-give', $locale );

			// Setup paths to current locale file.
			$local_mo_file  = $lang_dir . $mo_file;
			$global_mo_file = WP_LANG_DIR . '/paiementpro-for-give/' . $mo_file;

			if ( file_exists( $global_mo_file ) ) {
				load_textdomain( 'paiementpro-for-give', $global_mo_file );
			} elseif ( file_exists( $local_mo_file ) ) {
				load_textdomain( 'paiementpro-for-give', $local_mo_file );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'paiementpro-for-give', false, $lang_dir );
			}
		}

		/**
		 * Set hooks
		 *
		 * @return void
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function init() {
			// Bailout, if environment is not suitable for loading plugin.
			if ( ! $this->check_environment() ) {
				return;
			}

			$this->load_textdomain();

			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/helpers.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/admin/settings.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/payment-methods/class-paiementpro4give-mtn-money.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/payment-methods/class-paiementpro4give-moov-money.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/payment-methods/class-paiementpro4give-orange-money.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/filters.php';
			require_once PAIEMENTPRO4GIVE_PLUGIN_DIR . 'includes/actions.php';

			// Display admin notice when admin credentials to process payments are not set.
			if (
				! paiementpro4give_get_merchant_id() ||
				! paiementpro4give_get_credential_id() ||
				! paiementpro4give_get_api_url()
			) {
				PaiementPro4Give()->add_admin_notice(
					'empty-credentials',
					'error',
					__( 'Please save the <strong>Merchant ID</strong> and <strong>Credential ID</strong> to accept donations via PaiementPro.', 'give' )
				);
			}
		}

		/**
		 * Check plugin environment.
		 *
		 * @return bool
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function check_environment() {
			// Flag to check whether plugin file is loaded or not.
			$is_working = true;

			// Load plugin helper functions.
			if ( ! function_exists( 'is_plugin_active' ) ) {
				require_once ABSPATH . '/wp-admin/includes/plugin.php';
			}

			/* Check to see if Give is activated, if it isn't deactivate and show a banner. */
			// Check for if give plugin activate or not.
			$is_give_active = defined( 'GIVE_PLUGIN_BASENAME' ) ? is_plugin_active( GIVE_PLUGIN_BASENAME ) : false;

			if ( empty( $is_give_active ) ) {
				// Show admin notice.
				$this->add_admin_notice( 'prompt_give_activate', 'error', sprintf( __( '<strong>Activation Error:</strong> You must have the <a href="%s" target="_blank">Give</a> plugin installed and activated for <strong>PaiementPro for Give</strong> to activate.', 'give' ), 'https://givewp.com' ) );
				$is_working = false;
			}

			return $is_working;
		}

		/**
		 * Check plugin for Give environment.
		 *
		 * @return bool
		 * @since  1.0.0
		 * @access public
		 *
		 */
		public function get_environment_warning() {
			// Flag to check whether plugin file is loaded or not.
			$is_working = true;

			// Verify dependency cases.
			if (
				defined( 'GIVE_VERSION' )
				&& version_compare( GIVE_VERSION, PAIEMENTPRO4GIVE_MIN_GIVE_VER, '<' )
			) {

				/* Min. Give. plugin version. */
				// Show admin notice.
				$this->add_admin_notice( 'prompt_give_incompatible', 'error', sprintf( __( '<strong>Activation Error:</strong> You must have the <a href="%1$s" target="_blank">Give</a> core version %2$s for the "iPay88 for Give" add-on to activate.', 'give' ), 'https://givewp.com', PAIEMENTPRO4GIVE_MIN_GIVE_VER ) );

				$is_working = false;
			}

			return $is_working;
		}

		/**
		 * Allow this class and other classes to add notices.
		 *
		 * @param $slug
		 * @param $class
		 * @param $message
		 *
		 * @since 1.0.0
		 *
		 */
		public function add_admin_notice( $slug, $class, $message ) {
			$this->notices[ $slug ] = [
				'class'   => $class,
				'message' => $message,
			];
		}

		/**
		 * Display admin notices.
		 *
		 * @since 1.0.0
		 */
		public function admin_notices() {

			$allowed_tags = [
				'a'      => [
					'href'  => [],
					'title' => [],
					'class' => [],
					'id'    => [],
				],
				'br'     => [],
				'em'     => [],
				'span'   => [
					'class' => [],
				],
				'strong' => [],
			];

			foreach ( (array) $this->notices as $notice_key => $notice ) {
				echo "<div class='" . esc_attr( $notice['class'] ) . "'><p>";
				echo wp_kses( $notice['message'], $allowed_tags );
				echo '</p></div>';
			}

		}
	}

	/**
	 * Returns class object instance.
	 *
	 * @return PaiementPro4Give bool|object
	 * @since 1.0.0
	 *
	 */
	function PaiementPro4Give() {
		return PaiementPro4Give::get_instance();
	}

	PaiementPro4Give();
}
