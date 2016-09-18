<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Printfolio Taxonomies, Product Attributes and Termmeta
 *
 * @package  WC_Printfolio/Taxonomies
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Taxonomies_Attributes_Termmeta {

	/**
	 * Initialize the taxonomies.
	 */
	public function __construct() {
		
		add_action( 'init', array( $this, 'register_printfolio_collection_post_type' ), 6 );
		
		add_action( 'init', array( __CLASS__, 'register_printfolio_product_attributes' ), 6 );
		
		add_action( 'init', array( __CLASS__, 'register_printfolio_termmeta' ), 6 );
		
	}
	
	
public function register_printfolio_collection_post_type() {

	$labels = array(
		'name'                => _x( 'Printfolio Collections', 'Post Type General Name', 'printfolio' ),
		'singular_name'       => _x( 'Printfolio  Collection', 'Post Type Singular Name', 'printfolio' ),
		'menu_name'           => __( 'Printfolio Collections', 'printfolio' ),
		'parent_item_colon'   => __( 'Printfolio  Collection', 'printfolio' ),
		'all_items'           => __( 'All Printfolio Collections', 'printfolio' ),
		'view_item'           => __( 'View Collection', 'printfolio' ),
		'add_new_item'        => __( 'Add New Collection', 'printfolio' ),
		'add_new'             => __( 'Add New', 'printfolio' ),
		'edit_item'           => __( 'Edit Collection', 'printfolio' ),
		'update_item'         => __( 'Update Collection', 'printfolio' ),
		'search_items'        => __( 'Search Collections', 'printfolio' ),
		'not_found'           => __( 'Not found', 'printfolio' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'printfolio' ),
	);
	
	$args = array(
		'label'                 => __( 'Printfolio Collection', 'printfolio' ),
		'description'           => __( 'Printfolio Collection', 'printfolio' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'editor', 'thumbnail', 'page-attributes', ),
		'taxonomies'            => array( 'product_cat', 'product_tag' ),
		'hierarchical'          => true,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => false,
		'menu_position'         => 1,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => 'collections',
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	
	$register_post_type = register_post_type( 'pf_collection', $args );
	
	error_log(print_r($register_post_type,true));
	
}

	/**
	 * Register printfolio product variation attributes.
	 *
	 * @return void
	 */
	public static function register_printfolio_product_attributes() {
		
	global $wpdb;
		
	$objects = apply_filters( 'printfolio_variation_objects', array( 'product' ) );
		
	$printfolio_variation_attributes = apply_filters( 'printfolio_variation_attributes', array(
		array(
			'attribute_name' => 'pwinty_product_variations',
			'attribute_label' => __( 'Pwinty Variations', 'printfolio' )
		), array(
			'attribute_name' => 'self_print_product_variations',
			'attribute_label' => __( 'Self Print Variations', 'printfolio' )
		), array(
			'attribute_name' => 'digital_product_variations',
			'attribute_label' => __( 'Digital Variations', 'printfolio' )
		)
	) );
	
	foreach( $printfolio_variation_attributes as $attribute ){
		
		$exists = check_attribute_exists( $attribute["attribute_name"] );
		
		if( !$exists ){
			
			$attribute = array (
			'attribute_name' => $attribute["attribute_name"],
			'attribute_label' => $attribute["attribute_label"],
			'attribute_type' => 'select', 
			'attribute_orderby' => 'menu_order'
			);
			
			$wpdb->insert( $wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute );
			
			do_action( 'woocommerce_attribute_added', $wpdb->insert_id, $attribute );

			$register_attribute = register_taxonomy( 
				'pa_' . $attribute["attribute_name"],
				$objects,
				array(
					'label' => $attribute["attribute_label"],
					'hierarchical' => false
				)
			);
			error_log(print_r($register_attribute,true));
			
		}
		
	}
						
	flush_rewrite_rules();

	delete_transient( 'wc_attribute_taxonomies' );
	
	$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "woocommerce_attribute_taxonomies" );
	
	set_transient( 'wc_attribute_taxonomies', $attribute_taxonomies );

}

	/**
	 * Register printfolio termmeta.
	 *
	 * @return void
	 */
	public static function register_printfolio_termmeta() {
			
		$printfolio_termmeta_keys = apply_filters( 'printfolio_termmeta_keys', array( 
			'printfolio_variation_price', 
			'printfolio_variation_horizontal_resolution',
			'printfolio_variation_vertical_resolution',
			'printfolio_variation_horizontal_size',
			'printfolio_variation_vertical_size',
			'printfolio_variation_has_',
			'printfolio_pwinty_variation_cost_price_gbp',
			'printfolio_pwinty_variation_cost_price_usd'
			
			
		) );
		
		foreach( $printfolio_termmeta_keys as $meta_key ){
			
			register_meta( 'term', $meta_key, $meta_key . '_sanitize' );
		
		}	
	
	}

}
	
	
/**
 * Check if an attribute already exists.
 *
 * @param string $attribute_name
 * @return string|null
 */
function check_attribute_exists( $attribute_name ) {
	
	global $wpdb;
	
	$query = $wpdb->get_var( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name='%s' LIMIT 1", $attribute_name ) );
	
	return $query;
	
}
	
new WC_Printfolio_Taxonomies_Attributes_Termmeta();
