<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<table class="form-table">
	<tr class="form-field collection-form-field">
		<th scope="row">
			<label for="collections"><?php _e( 'Collections', 'printfolio' ); ?></label>
		</th>
		<td>
			<input type="hidden" id="collections" class="printfolio-collections-select" name="collections" style="width: 25em;" value="<?php echo implode( ',', array_keys( $collections ) ); ?>" data-selected='[<?php
				$total   = count( $collections );
				$current = 0;
				foreach ( $collections as $collection_id => $collection_name ) {
					$current++;

					echo '{"id": "'. $collection_id . '", "text": "' . esc_attr( $collection_name ) . '"}';
					echo ( $total !== $current ) ? ',' : '';
				}
			?>]' />
			<div class="photography-add-collection">
				<a href="#"><?php _e( '+ Add Collection', 'printfolio' ); ?></a>
				<div class="fields">
					<input type="text" class="input-text regular-input new-collection" />
					<button type="submit" class="button"><?php _e( 'Add New Collection', 'printfolio' ); ?></button>
				</div>
			</div>
		</td>
	</tr>
</table>
