<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Printfolio Admin Settings.
 *
 * @package  WC_Printfolio/Admin/Settings
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Admin_Settings {
	
	/**
 	 * Option key, and option page slug
 	 * @var string
 	 */
	public $settings_id = 'printfolio-options';

	/**
 	 * Options page metabox id
 	 * @var string
 	 */
	private $metabox_id = 'printfolio-options-metabox';

	/**
	 * Options Page title
	 * @var string
	 */
	protected $title = '';

	/**
	 * Options Page hook
	 * @var string
	 */
	protected $options_page = '';

	/**
	 * Holds an instance of the object
	 *
	 * @var Printfolio_Options
	 **/
	private static $instance = null;

	/**
	 * Constructor
	 * @since 0.1.0
	 */
	public function __construct() {
		
		$this->includes();

		$this->title = __( 'Printfolio Options', 'printfolio' );
		
		add_action( 'admin_init', array( $this, 'init' ) );
		
		add_action( 'cmb2_admin_init', array( $this, 'add_options_page_metabox' ) );
		
	}	

	/**
	 * Register our setting to WP
	 * @since  0.1.0
	 */
	public function init() {
		
		register_setting( $this->settings_id, $this->settings_id );
		
	}
	
	/**
	 * Includes.
	 *
	 * @return void
	 */
	private function includes() {
		
		if ( file_exists( dirname( __FILE__ ) . '/vendor/cmb2/init.php' ) ) {
			
			require_once dirname( __FILE__ ) . '/vendor/cmb2/init.php';
			
		}
		
	}

	/**
	 * Admin page markup. Mostly handled by CMB2
	 * @since  0.1.0
	 */
	public function admin_page_display() {
		?>
		<div class="wrap cmb2-options-page <?php echo $this->settings_id; ?>">
			<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
			<?php cmb2_metabox_form( $this->metabox_id, $this->settings_id ); ?>
		</div>
		<?php
	}

	/**
	 * Add the options metabox to the array of metaboxes
	 * @since  0.1.0
	 */
	function add_options_page_metabox() {

		// hook in our save notices
		add_action( "cmb2_save_options-page_fields_{$this->metabox_id}", array( $this, 'settings_notices' ), 10, 2 );

		$prefix = 'printfolio_';
	
		$printfolio_cmb = new_cmb2_box( array(
			'id'         => $this->metabox_id,
			'hookup'     => false,
			'cmb_styles' => false,
			'show_on'    => array(
				// These are important, don't remove
				'key'   => 'options-page',
				'value' => array( $this->settings_id, )
			),
		) );

		$printfolio_cmb->add_field( array(
			'name'         => __( 'Collection Images', 'printfolio' ),
			'desc'         => __( 'Upload/Manage images', 'printfolio' ),
			'id'           => $prefix . 'file_list',
			'type'         => 'file_list',
			'preview_size' => array( 100, 100 ),
		) );
		
		$printfolio_cmb->add_field( array(
			'name'    => 'Product Description',
			'desc'    => 'The product description used for all images.',
			'id'      => 'product_desc',
			'type'    => 'wysiwyg',
			'options' => array(
				'media_buttons' => false,
				'editor_css' => '<style>.mce-panel{    
				max-height: 300px;
				overflow: hidden;
				}</style>',
				),
		) );
		
		$printfolio_cmb->add_field( array(
			'name'     => 'Print Variations',
			'desc'     => '',
			'id'       => 'print_variations_taxonomy',
			'taxonomy' => 'pa_print_variations',
			'type'     => 'taxonomy_multicheck',
		) );	
		
		$printfolio_cmb->add_field( array(
			'name'     => 'Print Variations',
			'desc'     => '',
			'id'       => 'print_variations_taxonomy',
			'taxonomy' => 'pa_print_variations',
			'type'     => 'taxonomy_multicheck',
		) );
		
		$printfolio_cmb->add_field( array(
			'name' => __( 'Add Exif Data as Custom Fields', 'printfolio' ),
			'id'   => $prefix . 'checkbox',
			'type' => 'checkbox',
		) );
	
	}

	/**
	 * Register settings notices for display
	 *
	 * @since  0.1.0
	 * @param  int   $object_id Option key
	 * @param  array $updated   Array of updated fields
	 * @return void
	 */
	public function settings_notices( $object_id, $updated ) {
		if ( $object_id !== $this->settings_id || empty( $updated ) ) {
			return;
		}

		add_settings_error( $this->settings_id . '-notices', '', __( 'Settings updated.', 'myprefix' ), 'updated' );
		settings_errors( $this->settings_id . '-notices' );
	}

	/**
	 * Public getter method for retrieving protected/private variables
	 * @since  0.1.0
	 * @param  string  $field Field to retrieve
	 * @return mixed          Field value or exception is thrown
	 */
	public function __get( $field ) {
		// Allowed fields to retrieve
		if ( in_array( $field, array( 'key', 'metabox_id', 'title', 'options_page' ), true ) ) {
			return $this->{$field};
		}

		throw new Exception( 'Invalid property: ' . $field );
	}

}

/**
 * Helper function to get/return the Printfolio_Options object
 * @since  0.1.0
 * @return Printfolio_Options object
 */
function printfolio_admin() {
	return WC_Printfolio_Options::get_instance();
}

/**
 * Wrapper function around cmb2_get_option
 * @since  0.1.0
 * @param  string  $settings_id Options array key
 * @return mixed        Option value
 */
function printfolio_get_option( $settings_id = '' ) {
	return cmb2_get_option( printfolio_admin()->settings_id, $settings_id );
}

new WC_Printfolio_Admin_Settings();
