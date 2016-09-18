<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Frontend.
 *
 * @package  WC_Printfolio/Frontend
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Frontend {

	/**
	 * Initialize the frontend actions.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Frontend Scripts.
	 *
	 * @return void
	 */
	public function frontend_scripts() {
		$suffix                  = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$photography_assets_path = str_replace( array( 'http:', 'https:' ), '', WC_Printfolio::get_assets_url() );
		$woocommerce_assets_path = str_replace( array( 'http:', 'https:' ), '', WC()->plugin_url() ) . '/assets/';

		wp_enqueue_style( 'printfolio-frontend', $photography_assets_path . 'css/frontend.css', array(), WC_Printfolio::VERSION, 'all' );

		// Collections scripts.
		if ( is_tax( 'images_collections' ) ) {

			if ( 'yes' === get_option( 'woocommerce_enable_lightbox' ) ) {
				wp_enqueue_script( 'prettyPhoto', $woocommerce_assets_path . 'js/prettyPhoto/jquery.prettyPhoto' . $suffix . '.js', array( 'jquery' ), '3.1.5', true );
				wp_enqueue_script( 'prettyPhoto-init', $woocommerce_assets_path . 'js/prettyPhoto/jquery.prettyPhoto.init' . $suffix . '.js', array( 'jquery','prettyPhoto' ) );
				wp_enqueue_style( 'woocommerce_prettyPhoto_css', $woocommerce_assets_path . 'css/prettyPhoto.css' );
			}

			wp_enqueue_script( 'printfolio-collections', $photography_assets_path . 'js/frontend/collections' . $suffix . '.js', array( 'jquery' ), WC_Printfolio::VERSION, true );
		}

		if ( is_account_page() ) {
			$current_user = wp_get_current_user();

			wp_enqueue_script( 'printfolio-my-collections', $photography_assets_path . 'js/frontend/my-collections' . $suffix . '.js', array( 'jquery' ), WC_Printfolio::VERSION, true );
			wp_localize_script(
				'printfolio-my-collections',
				'WCPhotographyMyCollectionsParams',
				array(
					'ajax_url' => admin_url( 'admin-ajax.php' ),
					'security' => wp_create_nonce( 'WC_Printfolio_my_account_edit_visibility_nonce' ),
					'customer' => $current_user->user_login
				)
			);
		}
	}
}

new WC_Printfolio_Frontend();
