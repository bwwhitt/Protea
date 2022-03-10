<?php
/**
 * Plugin Name: Forminator Stripe Subscriptions Add-on
 * Version: 1.0.2
 * Plugin URI:  https://premium.wpmudev.org/project/forminator/
 * Description: Forminator Stripe Subscriptions Add-on
 * Author: WPMU DEV
 * Author URI: https://premium.wpmudev.org
 * Text Domain: forminator-stripe
 * Domain Path: /languages/
 * WDP ID: 3953609
 */

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

if ( ! defined( 'FORMINATOR_STRIPE_ADDON' ) ) {
	define( 'FORMINATOR_STRIPE_ADDON', '1.0.2' );
}

/**
 * Class Forminator_Stripe_Addon
 *
 * Main class. Initialize add-on
 *
 * @since 1.0
 */
if ( ! class_exists( 'Forminator_Stripe_Addon' ) ) {

    /**
	 * Stripe Add-on class
	 */
	class Forminator_Stripe_Addon {
		/**
		 * Plugin instance
		 *
		 * @since 1.0
		 * @var null
		 */
		private static $instance = null;

		/**
		 * Minimum version of Forminator, that the addon will work correctly
		 *
		 * @since 1.0
		 * @var string
		 */
		protected $_min_forminator_version = '1.15.0';

		/**
		 * Return the plugin instance
		 *
		 * @since 1.0
		 * @return Forminator_Stripe_Addon
		 */
		public static function get_instance() {
			if ( is_null( self::$instance ) ) {
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor.
		 *
		 * @since 1.0
		 */
		public function __construct() {
			if ( $this->is_supported_version() ) {
				add_action( 'forminator_loaded', array( $this, 'forminator_loaded' ), 10 );

				/**
				 * Triggered when plugin is loaded
				 */
				do_action( 'forminator_stripe_addon_loaded' );
			} else {
				// Initialize subscription after main plugin loaded only if Forminator version is supported.
				add_action( 'after_plugin_row_' . plugin_basename( __FILE__ ), array( $this, 'prefix_plugin_update_message' ), 10, 2 );
			}
		}

		/**
		 * Initialise Subscription class
		 *
		 * @since 1.0
		 */
		public function forminator_loaded() {
			// Load required files.
			require_once dirname( __FILE__ ) . '/library/class-forminator-stripe-api.php';
			require_once dirname( __FILE__ ) . '/library/class-forminator-stripe-subscription.php';

			// Initialise Subscriptions.
			$subscription = Forminator_Stripe_Subscription::get_instance();
		}

		/**
		 * Check if Forminator version is supported
		 *
		 * @since 1.0
		 *
		 * @return bool
		 */
		public function is_supported_version() {
			if ( defined( 'FORMINATOR_VERSION' ) ) {
				$is_forminator_version_supported = version_compare( FORMINATOR_VERSION, $this->_min_forminator_version, '>=' );

				if ( $is_forminator_version_supported >= 0 ) {
					return true;
				}
			}

			return false;
		}

		public function prefix_plugin_update_message( $data, $response ) { ?>
			<tr class="plugin-update-tr" id="forminator-update" data-slug="forminator-stripe" data-plugin="forminator-stripe/forminator-stripe.php">
				<td colspan="4" class="plugin-update colspanchange">
					<div class="notice inline notice-warning notice-alt">
						<p><?php esc_html_e( 'Forminator 1.15.0 is required! Activate it now or download it today!', 'forminator-stripe' ); ?></p>
					</div>
				</td>
			</tr>
		<?php }
	}
}

if ( ! function_exists( 'forminator_stripe_addon' ) ) {
	function forminator_stripe_addon() {
		return Forminator_Stripe_Addon::get_instance();
	}

	/**
	 * Init the plugin and load the plugin instance
	 *
	 * @since 1.0
	 */
	add_action( 'plugins_loaded', 'forminator_stripe_addon' );
}


