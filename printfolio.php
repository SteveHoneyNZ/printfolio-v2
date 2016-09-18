<?php
/**
 * Plugin Name: Printfolio
 * Plugin URI: https://printfol.io
 * Description: A photographer's toolkit for selling online.
 * Version: 1.0
 * Author: Steve Honey
 * Author URI: me@stevenhoney.com
 * Text Domain: printfolio
 * Domain Path: /languages
 *
 * @package  WC_Printfolio
 * @category Core
 * @author   SteveHoney
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Required functions.
 */
if ( ! function_exists( 'woothemes_queue_update' ) ) {
	require_once( 'woo-includes/woo-functions.php' );
}

/**
 * Plugin updates.
 */
woothemes_queue_update( plugin_basename( __FILE__ ), 'ee76e8b9daf1d97ca4d3874cc9e35687', '583602' );

if ( ! class_exists( 'WC_Printfolio' ) ) :

/**
 * WooCommerce Photography main class.
 */
class WC_Printfolio {

	/**
	 * Plugin version.
	 *
	 * @var string
	 */
	const VERSION = '1.0';

	/**
	 * Instance of this class.
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin.
	 */
	private function __construct() {
		
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		if ( class_exists( 'WooCommerce' ) ) {
			$this->includes();

			if ( is_admin() ) {
				
				$this->admin_includes();

				add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
			
			}
		} else {
			
			add_action( 'admin_notices', array( $this, 'woocommerce_missing_notice' ) );
			
		}
	}

	/**
	 * Return an instance of this class.
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {
		
		if ( null == self::$instance ) {
			
			self::$instance = new self;
			
		}

		return self::$instance;
	}
	

	/**
	 * Get plugin file.
	 *
	 * @return string
	 */
	public static function get_plugin_file() {
		
		return __FILE__;
		
	}

	/**
	 * Get templates path.
	 *
	 * @return string
	 */
	public static function get_templates_path() {
		return plugin_dir_path( __FILE__ ) . 'templates/';
	}

	/**
	 * Get assets url.
	 *
	 * @return string
	 */
	public static function get_assets_url() {
		return plugins_url( 'assets/', __FILE__ );
	}

	/**
	 * Includes.
	 *
	 * @return void
	 */
	private function includes() {
		
		//include_once( 'includes/class-printfolio-product-photography.php' );
		include_once( 'includes/class-printfolio-frontend.php' );
		include_once( 'includes/class-printfolio-products.php' );
		include_once( 'includes/class-printfolio-ajax.php' );
		include_once( 'includes/class-printfolio-emails.php' );
		include_once( 'includes/class-printfolio-install.php' );
		include_once( 'includes/class-printfolio-taxonomies-attributes-termmeta.php' );

		// Integration with Products Add-ons.
		if ( class_exists( 'WC_Product_Addons' ) ) {
			
			include_once( 'includes/class-printfolio-products-addons.php' );
			
		}

		// Functions.
		include_once( 'includes/printfolio-template-functions.php' );
		include_once( 'includes/printfolio-helpers.php' );
	}

	/**
	 * Admin includes.
	 *
	 * @return void
	 */
	private function admin_includes() {
		
		include_once( 'includes/admin/class-printfolio-admin.php' );
		
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @return void
	 */
	public function load_plugin_textdomain() {
		
		$locale = apply_filters( 'plugin_locale', get_locale(), 'printfolio' );

		load_textdomain( 'printfolio', trailingslashit( WP_LANG_DIR ) . 'printfolio/printfolio-' . $locale . '.mo' );
		
		load_plugin_textdomain( 'printfolio', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
	}

	/**
	 * WooCommerce fallback notice.
	 *
	 * @return string
	 */
	public function woocommerce_missing_notice() {
		
		echo '<div class="error"><p>' . sprintf( __( 'Printfolio depends on the last version of %s to work!', 'printfolio' ), '<a href="http://www.woothemes.com/woocommerce/" target="_blank">' . __( 'WooCommerce', 'printfolio' ) . '</a>' ) . '</p></div>';
	
	}

	/**
	 * Add relevant links to plugins page.
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		
		$plugin_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=printfolio-settings' ) . '">' . __( 'Settings', 'printfolio' ) . '</a>',
			'support'  => '<a href="https://woothemes.com/my-account/create-a-ticket/">' . __( 'Support', 'printfolio' ) . '</a>',
			'docs'     => '<a href="http://docs.woothemes.com/documentation/woocommerce-extensions/photography/">' . __( 'Docs', 'printfolio' ) . '</a>',
		);

		return array_merge( $plugin_links, $links );
	}

	/**
	 * Install method.
	 */
	public static function install() {

		WC_Printfolio_Install::install();
		
	}
}

register_activation_hook( __FILE__, array( 'WC_Printfolio', 'install' ) );

add_action( 'plugins_loaded', array( 'WC_Printfolio', 'get_instance' ) );

endif;
