<?php
/**
 * The template for displaying photography content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-photography.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product, $photography_loop;

// Store loop count we're currently on
if ( empty( $photography_loop['loop'] ) ) {
	$photography_loop['loop'] = 0;
}

// Store column count for displaying the grid
if ( empty( $photography_loop['columns'] ) ) {
	$photography_loop['columns'] = apply_filters( 'loop_shop_columns', 2 );
}

// Ensure visibility.
if ( ! $product || ! $product->is_visible() ) {
	return;
}

// Increase loop count.
$photography_loop['loop']++;

// Extra post classes.
$classes = array();
if ( 0 == ( $photography_loop['loop'] - 1 ) % $photography_loop['columns'] || 1 == $photography_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $photography_loop['loop'] % $photography_loop['columns'] )
	$classes[] = 'last';

?>

<li <?php post_class( $classes ); ?>>

	<?php
		/**
		 * WC_Printfolio_before_shop_loop_item hook.
		 *
		 * @hooked WC_Printfolio_template_image - 10
		 */
		do_action( 'WC_Printfolio_before_shop_loop_item' );
	?>

	<div class="photography-content">
		<?php
			/**
			 * WC_Printfolio_shop_loop_item hook.
			 *
			 * @hooked WC_Printfolio_template_sku - 10
			 * @hooked WC_Printfolio_template_price - 20
			 * @hooked WC_Printfolio_template_addons - 30
			 * @hooked WC_Printfolio_template_add_to_cart - 50
			 * @hooked WC_Printfolio_template_short_description - 70
			 */
			do_action( 'WC_Printfolio_shop_loop_item' );
		?>
	</div>

	<?php do_action( 'WC_Printfolio_after_shop_loop_item' ); ?>

</li>
