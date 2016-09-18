<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Admin.
 *
 * @package  WC_Printfolio/Admin
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Admin {

	/**
	 * Initialize the admin customers actions.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 10 );
		add_action( 'admin_menu', array( $this, 'remove_top_level_menu_item' ), 100 );
		add_action( 'parent_file', array( $this, 'fix_collections_menu' ) );
		add_filter( 'woocommerce_screen_ids', array( $this, 'screen_ids' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'add_attachment', array( $this, 'attachment_custom_field' ) );
		add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'bulk_edit_save_meta' ), 10 );
	}

	/**
	 * Include any classes we need within admin.
	 *
	 * @return void
	 */
	public function includes() {
		include_once( 'class-printfolio-admin-customers.php' );
		include_once( 'class-printfolio-admin-collections.php' );
		include_once( 'class-printfolio-admin-settings.php' );
	}

	/**
	 * Register menus.
	 *
	 * @return void
	 */
	public function admin_menu() {
		add_menu_page(
			'Printfolio',
			'Printfolio',
			'manage_printfolio',
			'printfolio',
			'__return_false',
			'dashicons-camera',
			'55.9'
		);

		add_submenu_page(
			'printfolio',
			__( 'All Collections', 'printfolio' ),
			__( 'All Collections', 'printfolio' ),
			'manage_printfolio',
			'edit.php?post_type=pf_collection'
		);

		add_submenu_page(
			'printfolio',
			__( 'New Collection', 'printfolio' ),
			__( 'New Collection', 'printfolio' ),
			'manage_printfolio',
			'post-new.php?post_type=pf_collection'
			//array( $this, 'page_batch_upload' )
		);

		add_submenu_page(
			'printfolio',
			__( 'Printfolio Settings', 'printfolio' ),
			__( 'Settings', 'printfolio' ),
			'manage_woocommerce',
			'printfolio-options',
			array( $this, 'page_settings' )
		);
	}

	/**
	 * Remove the "Photography" menu item.
	 *
	 * @return void
	 */
	public function remove_top_level_menu_item() {
		global $submenu;

		if ( isset( $submenu['printfolio'] ) ) {
			foreach ( $submenu['printfolio'] as $key => $value ) {
				if ( 'printfolio' == $value[2] ) {
					unset( $submenu['printfolio'][ $key ] );
					return;
				}
			}
		}
	}

	/**
	 * Fix collections menu.
	 *
	 * @param  string $parent_file
	 *
	 * @return string
	 */
	public function fix_collections_menu( $parent_file ) {
		global $submenu_file;
		$screen = get_current_screen();

		if ( 'images_collections' == $screen->taxonomy && $parent_file == 'edit.php?post_type=product' ) {
			$parent_file = 'printfolio';
			$submenu_file = 'edit-tags.php?taxonomy=images_collections&post_type=product';
		}

		return $parent_file;
	}

	/**
	 * Batch Upload page.
	 *
	 * @return string
	 */
	public function page_batch_upload() {
		$max_upload_size = wp_max_upload_size();
		if ( ! $max_upload_size ) {
			$max_upload_size = 0;
		}

		include_once( 'views/html-batch-upload.php' );
	}

	/**
	 * Settings page.
	 *
	 * @return string
	 */
	public function page_settings() {
		
		// admin_page_display();
		
	}

	/**
	 * Add screen ID.
	 *
	 * @param  array $ids
	 *
	 * @return array
	 */
	public function screen_ids( $screen_ids ) {
		$prefix       = sanitize_title( __( 'Photography', 'printfolio' ) );
		$screen_ids[] = $prefix . '_page_printfolio-batch-upload';
		$screen_ids[] = $prefix . '_page_printfolio-settings';

		return $screen_ids;
	}

	/**
	 * Get plupload args.
	 *
	 * @return array
	 */
	public function get_plupload_args() {
		$args = array(
			'runtimes'            => 'html5,silverlight,flash,html4',
			'browse_button'       => 'printfolio-uploader-browse-button',
			'container'           => 'printfolio-uploader-upload-ui',
			'drop_element'        => 'printfolio-drag-drop-area',
			'file_data_name'      => 'async-upload',
			'multiple_queues'     => true,
			'max_file_size'       => wp_max_upload_size() . 'b',
			'url'                 => admin_url( 'async-upload.php' ),
			'flash_swf_url'       => includes_url( 'js/plupload/plupload.flash.swf' ),
			'silverlight_xap_url' => includes_url( 'js/plupload/plupload.silverlight.xap' ),
			'filters'             => array(
				array(
					'title'      => __( 'Allowed Files', 'printfolio' ),
					'extensions' => 'jpg,jpeg,gif,png'
				),
			),
			'multipart'           => true,
			'urlstream_upload'    => true,
			'multipart_params'    => array(
				'post_id'  => 0,
				'_wpnonce' => wp_create_nonce( 'media-form' ),
				'type'     => '',
				'tab'      => '',
				'short'    => 3
			),
			'resize'              => false
		);

		if ( wp_is_mobile() ) {
			$args['multi_selection'] = false;
		}

		return apply_filters( 'plupload_init', $args );
	}

	/**
	 * Enqueue Select2.
	 *
	 * @return void
	 */
	private function enqueue_select2() {
		if ( version_compare( WOOCOMMERCE_VERSION, '2.3.0', '<' ) ) {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
			wp_enqueue_script( 'select2', WC_Printfolio::get_assets_url() . 'js/select2/select2' . $suffix . '.js', array( 'jquery' ), '3.5.1', true );
			wp_enqueue_style( 'select2-styles', WC_Printfolio::get_assets_url() . 'css/select2.css', array(), WC_Printfolio::VERSION );
		}

		wp_enqueue_style( 'printfolio-collections-field-styles', WC_Printfolio::get_assets_url() . 'css/collections-field.css', array(), WC_Printfolio::VERSION );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
		$screen        = get_current_screen();
		$screen_prefix = sanitize_title( __( 'Photography', 'printfolio' ) );
		$suffix        = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Products list screen
		if ( 'edit-product' == $screen->id ) {
			wp_enqueue_style( 'printfolio-admin', WC_Printfolio::get_assets_url() . 'css/admin.css', array(), WC_Printfolio::VERSION, 'all' );
		}

		// Uploader screen
		if ( $screen->id === $screen_prefix . '_page_printfolio-batch-upload' ) {
			// Media libs.
			wp_enqueue_media();

			// Accounting.
			wp_enqueue_script( 'accounting' );

			// Batch upload.
			$this->enqueue_select2();
			wp_enqueue_script( 'printfolio-batch-upload', WC_Printfolio::get_assets_url() . 'js/admin/batch-upload' . $suffix . '.js', array( 'jquery', 'plupload-handlers', 'jquery-ui-sortable', 'accounting', 'underscore', 'select2' ), WC_Printfolio::VERSION, true );
			wp_enqueue_style( 'printfolio-batch-upload', WC_Printfolio::get_assets_url() . 'css/batch-upload.css', array(), WC_Printfolio::VERSION, 'all' );

			if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3.0', '<' ) ) {
				$ajax_loading_image = WC()->plugin_url() . '/assets/images/ajax-loader.gif';
			} else {
				$ajax_loading_image = '';
			}

			wp_localize_script(
				'printfolio-batch-upload',
				'WCPhotographyBatchUploadParams',
				array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'plupload'                 => $this->get_plupload_args(),
					'ajax_loading_image'       => $ajax_loading_image,
					'batch_upload_nonce'       => wp_create_nonce( 'WC_Printfolio_batch_upload_nonce' ),
					'search_collections_nonce' => wp_create_nonce( 'WC_Printfolio_search_collections_nonce' ),
					'add_collection_nonce'     => wp_create_nonce( 'WC_Printfolio_add_collection_nonce' ),
					'delete_image_nonce'       => wp_create_nonce( 'WC_Printfolio_delete_image_nonce' ),
					'save_images_nonce'        => wp_create_nonce( 'WC_Printfolio_save_images_nonce' ),
					'search_placeholder'       => __( 'Search for a collection&hellip;', 'printfolio' ),
					'loading'                  => __( 'Loading&hellip;', 'printfolio' ),
					'collection_error'         => __( 'An error occurred while creating the collection! Please try again.', 'printfolio' ),
					'edit_success_message'     => __( 'Photographs edited successfully!', 'printfolio' )
				)
			);
		}

		// User screen
		if ( 'user' == $screen->id && 'add' == $screen->action || 'profile' == $screen->id || 'user-edit' == $screen->id ) {
			$this->enqueue_select2();
			wp_enqueue_script( 'printfolio-customers', WC_Printfolio::get_assets_url() . 'js/admin/customers' . $suffix . '.js', array( 'jquery', 'select2' ), WC_Printfolio::VERSION, true );
			wp_enqueue_style( 'woocommerce-admin-styles', WC()->plugin_url() . '/assets/css/admin.css' );

			wp_localize_script(
				'printfolio-customers',
				'WCPhotographyCustomerParams',
				array(
					'ajax_url'                 => admin_url( 'admin-ajax.php' ),
					'search_collections_nonce' => wp_create_nonce( 'WC_Printfolio_search_collections_nonce' ),
					'add_collection_nonce'     => wp_create_nonce( 'WC_Printfolio_add_collection_nonce' ),
					'search_placeholder'       => __( 'Search for a collection&hellip;', 'printfolio' ),
					'loading'                  => __( 'Loading&hellip;', 'printfolio' ),
					'collection_error'         => __( 'An error occurred while creating the collection! Please try again.', 'printfolio' )
				)
			);
		}

		// Product screen
		if ( 'product' == $screen->id ) {
			wp_enqueue_script( 'printfolio-admin-products', WC_Printfolio::get_assets_url() . 'js/admin/product' . $suffix . '.js', array( 'jquery' ), WC_Printfolio::VERSION, true );
		}

		if ( 'images_collections' == $screen->taxonomy ) {
			wp_enqueue_media();
			wp_enqueue_style( 'printfolio-admin-collections', WC_Printfolio::get_assets_url() . 'css/collections.css', array(), WC_Printfolio::VERSION, 'all' );
			wp_enqueue_script( 'printfolio-admin-collections', WC_Printfolio::get_assets_url() . 'js/admin/collections' . $suffix . '.js', array( 'jquery' ), WC_Printfolio::VERSION, true );
			wp_localize_script(
				'printfolio-admin-collections',
				'WCPhotographyAdminCollectionsParams',
				array(
					'upload_title' => __( 'Choose an image', 'printfolio' ),
					'upload_use'   => __( 'Use image', 'printfolio' ),
					'placeholder'  => wc_placeholder_img_src()
				)
			);
		}
	}

	/**
	 * Attachment custom field.
	 *
	 * @param  string $attachment_id
	 *
	 * @return void
	 */
	public function attachment_custom_field( $attachment_id ) {
		$attachment = get_post( $attachment_id );
		$post       = get_post( $attachment->post_parent );

		// Chec if has a parent.
		if ( is_wp_error( $post ) || ! $post ) {
			return;
		}

		$product_type   = get_the_terms( $post->ID, 'product_type' );
		$is_photography = false;

		// Check if is a photography.
		if ( is_wp_error( $product_type ) || ! $product_type ) {
			return;
		}

		foreach ( $product_type as $key => $value ) {
			if ( 'photography' == $value->slug ) {
				$is_photography = true;
				break;
			}
		}

		if ( ! $is_photography ) {
			return;
		}

		update_post_meta( $attachment->ID, '_is_photography_attachment', true );
	}


	/**
	 * Calculate and set a photo item's prices when edited via the bulk edit
	 *
	 * @param object $product An instance of a WC_Product_* object.
	 */
	public function bulk_edit_save_meta( $product ) {
		if ( ! $product->is_type( 'photography' ) ) {
			return;
		}

		$price_changed = false;

		$old_regular_price = $product->regular_price;
		$old_sale_price    = $product->sale_price;

		// copy from subs & wc-admin-post-types
		// see https://github.com/woothemes/woocommerce/pull/9684
		if ( ! empty( $_REQUEST['change_regular_price'] ) && isset( $_REQUEST['_regular_price'] ) ) {

			$change_regular_price = absint( $_REQUEST['change_regular_price'] );
			$regular_price = esc_attr( stripslashes( $_REQUEST['_regular_price'] ) );

			switch ( $change_regular_price ) {
				case 1 :
					$new_price = $regular_price;
				break;
				case 2 :
					if ( strstr( $regular_price, '%' ) ) {
						$percent = str_replace( '%', '', $regular_price ) / 100;
						$new_price = $old_regular_price + ( $old_regular_price * $percent );
					} else {
						$new_price = $old_regular_price + $regular_price;
					}
				break;
				case 3 :
					if ( strstr( $regular_price, '%' ) ) {
						$percent = str_replace( '%', '', $regular_price ) / 100;
						$new_price = $old_regular_price - ( $old_regular_price * $percent );
					} else {
						$new_price = $old_regular_price - $regular_price;
					}
				break;
			}

			if ( isset( $new_price ) && $new_price != $old_regular_price ) {
				$price_changed = true;
				update_post_meta( $product->id, '_regular_price', $new_price );
				update_post_meta( $product->id, '_subscription_price', $new_price );
				$product->regular_price = $new_price;
			}
		}

		if ( ! empty( $_REQUEST['change_sale_price'] ) && isset( $_REQUEST['_sale_price'] ) ) {

			$change_sale_price = absint( $_REQUEST['change_sale_price'] );
			$sale_price = esc_attr( stripslashes( $_REQUEST['_sale_price'] ) );

			switch ( $change_sale_price ) {
				case 1 :
					$new_price = $sale_price;
				break;
				case 2 :
					if ( strstr( $sale_price, '%' ) ) {
						$percent = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $old_sale_price + ( $old_sale_price * $percent );
					} else {
						$new_price = $old_sale_price + $sale_price;
					}
				break;
				case 3 :
					if ( strstr( $sale_price, '%' ) ) {
						$percent = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $old_sale_price - ( $old_sale_price * $percent );
					} else {
						$new_price = $old_sale_price - $sale_price;
					}
				break;
				case 4 :
					if ( strstr( $sale_price, '%' ) ) {
						$percent = str_replace( '%', '', $sale_price ) / 100;
						$new_price = $product->regular_price - ( $product->regular_price * $percent );
					} else {
						$new_price = $product->regular_price - $sale_price;
					}
				break;
			}

			if ( isset( $new_price ) && $new_price != $old_sale_price ) {
				$price_changed = true;
				update_post_meta( $product->id, '_sale_price', $new_price );
				$product->sale_price = $new_price;
			}
		}

		if ( $price_changed ) {
			update_post_meta( $product->id, '_sale_price_dates_from', '' );
			update_post_meta( $product->id, '_sale_price_dates_to', '' );

			if ( $product->regular_price < $product->sale_price ) {
				$product->sale_price = '';
				update_post_meta( $product->id, '_sale_price', '' );
			}

			if ( $product->sale_price ) {
				update_post_meta( $product->id, '_price', $product->sale_price );
			} else {
				update_post_meta( $product->id, '_price', $product->regular_price );
			}
		}

	}

}

new WC_Printfolio_Admin();
