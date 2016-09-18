<?php
/**
 * Photography loop product quantity.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

if ( defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '2.3', '>=' ) ) {
	$class = '';
} else {
	$class = ' legacy-quantity';
}

?>

<div class="photography-quantity<?php echo $class; ?>">

<?php
	woocommerce_quantity_input( array(
		'input_name'  => 'quantity[' . $product->id . ']',
		'input_value' => apply_filters( 'WC_Printfolio_quantity_input_value', 0, $product ),
		'min_value'   => apply_filters( 'WC_Printfolio_quantity_input_min', 0, $product ),
		'max_value'   => apply_filters( 'WC_Printfolio_quantity_input_max', $product->backorders_allowed() ? '' : $product->get_stock_quantity(), $product )
	) );
?>

</div>
