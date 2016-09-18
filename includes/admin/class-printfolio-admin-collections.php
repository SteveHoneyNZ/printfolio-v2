<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * WooCommerce Photography Admin Collections.
 *
 * @package  WC_Printfolio/Admin/Collections
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Admin_Collections {

	/**
	 * Initialize the admin collections actions.
	 */
	public function __construct() {
		add_action( 'delete_term', array( $this, 'delete_term' ), 5 );
		add_action( 'images_collections_add_form_fields', array( $this, 'collections_add_fields' ) );
		add_action( 'images_collections_edit_form_fields', array( $this, 'collections_edit_fields' ), 10, 2 );
		add_action( 'created_term', array( $this, 'save_collections_fields' ), 10, 3 );
		add_action( 'edit_term', array( $this, 'save_collections_fields' ), 10, 3 );
		add_filter( 'manage_edit-images_collections_columns', array( $this, 'collections_columns' ) );
		add_filter( 'manage_images_collections_custom_column', array( $this, 'collections_column' ), 10, 3 );
		add_filter( 'images_collections_row_actions', array( $this, 'collections_actions' ), 10, 2 );
	}

	/**
	 * When a term is deleted, delete its meta.
	 *
	 * @param int $term_id
	 *
	 * @return void
	 */
	public function delete_term( $term_id ) {
		$term_id = intval( $term_id );

		if ( ! $term_id ) {
			return;
		}

		global $wpdb;

		$wpdb->query( "DELETE FROM {$wpdb->woocommerce_termmeta} WHERE `woocommerce_term_id` = " . $term_id );
	}

	/**
	 * Collection thumbnail fields.
	 *
	 * @return string
	 */
	public function collections_add_fields() {
		
		include_once 'views/html-add-new-collection.php';
		
	}

	/**
	 * Edit collections thumbnail field.
	 *
	 * @param mixed $term Term (collections) being edited
	 * @param mixed $taxonomy Taxonomy of the term being edited
	 *
	 * @return string
	 */
	public function collections_edit_fields( $term, $taxonomy ) {
		$visibility	    = get_woocommerce_term_meta( $term->term_id, 'visibility', true );
		$image 			= '';
		$thumbnail_id 	= absint( get_woocommerce_term_meta( $term->term_id, 'thumbnail_id', true ) );
		if ( $thumbnail_id ) {
			$image = wp_get_attachment_thumb_url( $thumbnail_id );
		} else {
			$image = wc_placeholder_img_src();
		}

		include_once 'views/html-edit-collection.php';
	}

	/**
	 * Save collections fields.
	 *
	 * @param mixed $term_id Term ID being saved
	 * @param mixed $tt_id
	 * @param mixed $taxonomy Taxonomy of the term being saved
	 *
	 * @return void
	 */
	public function save_collections_fields( $term_id, $tt_id, $taxonomy ) {
		if ( isset( $_POST['collection_visibility'] ) ) {
			update_woocommerce_term_meta( $term_id, 'visibility', wc_clean( $_POST['collection_visibility'] ) );
		}

		if ( isset( $_POST['collection_thumbnail_id'] ) ) {
			update_woocommerce_term_meta( $term_id, 'thumbnail_id', absint( $_POST['collection_thumbnail_id'] ) );
		}
	}

	/**
	 * Thumbnail column added to category admin.
	 *
	 * @param array $columns
	 *
	 * @return array
	 */
	public function collections_columns( $columns ) {
		$new_columns               = array();
		$new_columns['cb']         = $columns['cb'];
		$new_columns['thumb']      = __( 'Image', 'printfolio' );
		$new_columns['name']       = $columns['name'];
		$new_columns['visibility'] = __( 'Visibility', 'printfolio' );
		$new_columns['actions']    = '';

		return $new_columns;
	}

	/**
	 * Thumbnail column value added to category admin.
	 *
	 * @param array $columns
	 * @param mixed $column
	 * @param int $id
	 *
	 * @return array
	 */
	public function collections_column( $columns, $column, $id ) {
		if ( 'thumb' == $column ) {

			$image        = '';
			$thumbnail_id = get_woocommerce_term_meta( $id, 'thumbnail_id', true );

			if ( $thumbnail_id ) {
				$image = wp_get_attachment_thumb_url( $thumbnail_id );
			} else {
				$image = wc_placeholder_img_src();
			}

			// Prevent esc_url from breaking spaces in urls for image embeds
			// Ref: http://core.trac.wordpress.org/ticket/23605
			$image = str_replace( ' ', '%20', $image );

			$columns .= '<img src="' . esc_url( $image ) . '" alt="' . __( 'Thumbnail', 'printfolio' ) . '" class="wp-post-image" height="48" width="48" />';
		}

		if ( 'visibility' == $column ) {
			$columns .= WC_Printfolio_i18n_collection_visibility( $id );
		}

		if ( 'actions' == $column ) {
			$collection = get_term( $id, 'images_collections' );

			$columns .= '<a class="button-secondary" href="edit.php?images_collections=' . esc_attr( $collection->slug ) . '&post_type=product" title="' . __( 'View products on this collection.', 'printfolio' ) . '">' . sprintf( __( 'View Products (%d)', 'printfolio' ), $collection->count ) . '</a>';
			$columns .= ' <a class="button-secondary" href="' . get_term_link( $collection ) . '" title="' . __( 'View collection page.', 'printfolio' ) . '">' . __( 'View Collection', 'printfolio' ) . '</a>';
		}

		return $columns;
	}

	/**
	 * Custom collections row actions.
	 *
	 * @param  array $actions
	 * @param  string $collection
	 *
	 * @return array
	 */
	public function collections_actions( $actions, $collection ) {
		$new_actions           = array();
		$new_actions['edit']   = $actions['edit'];
		$new_actions['delete'] = $actions['delete'];

		return $new_actions;
	}
}

new WC_Printfolio_Admin_Collections();
