 <?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Printfolio Install.
 *
 * @package  WC_Printfolio/Install
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Install {

	/**
	 * Initialize the install actions.
	 */
	public function __construct() {

		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		
	}

	/**
	 * Check version.
	 *
	 * @return void
	 */
	public static function check_version() {
		
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'WC_Printfolio_version' ) != WC_Printfolio::VERSION ) ) {
			
			self::install();
			
			do_action( 'WC_Printfolio_updated' );
			
		}
	}

	/**
	 * Install/update Printfolio.
	 */
	public static function install() {
		
		include_once( 'class-printfolio-options.php' );

		// Update version.
		update_option( 'WC_Printfolio_version', WC_Printfolio::VERSION );

		$settings = get_option( 'printfolio_options' );
		
		if ( ! $settings ) {
			
			new WC_Printfolio_Options();

			update_option( 'printfolio_options', $settings );
			
		}

		self::setup_user_caps();

		// Flush rules after install.
		flush_rewrite_rules();
	}

	/**
	 * Initial settings.
	 *
	 * @return array
	 */
	public static function get_initial_settings() {
		
		new WC_Printfolio_Options();
		
	}

	/**
	 * Setup user caps.
	 */
	public static function setup_user_caps() {
		
		$admin = get_role( 'administrator' );
		
		$shop_manager = get_role( 'shop_manager' );

		if ( ! empty ( $admin ) && ! empty( $shop_manager ) ) {
			
			$admin->add_cap( 'manage_printfolio' );
			
			$shop_manager->add_cap( 'manage_printfolio' );
		}
	}
}

new WC_Printfolio_Install();
