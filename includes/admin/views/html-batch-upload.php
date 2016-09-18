<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="wrap">
	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
	<p><?php _e( 'Use this screen to upload your photographs. Before uploading, set any of the following parameters to apply them to each uploaded photograph.', 'printfolio' ); ?></p>

	<div id="printfolio-uploader">
		<div id="printfolio-uploader-error"></div>
		<div id="printfolio-uploader-upload-ui" class="hide-if-no-js">

			<table class="form-table">
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="printfolio-batch-sku"><?php _e( 'SKU Pattern', 'printfolio' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'SKU Pattern', 'printfolio' ); ?></span></legend>
							<input class="input-text regular-input" type="text" name="sku" id="printfolio-batch-sku">
							<span class="description"><?php echo sprintf( __( 'Specify a pattern to ensure your photos have a unique SKU. E.g. %swc-%s', 'printfolio' ), '<code>', '</code>' ); ?></span>
						</fieldset>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row" class="titledesc">
						<label for="printfolio-batch-price"><?php _e( 'Price', 'printfolio' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Price', 'printfolio' ); ?></span></legend>
							<input class="wc_input_price input-text regular-input" type="text" name="price" id="printfolio-batch-price" placeholder="0">
							<span class="description"><?php echo _e( 'Set a global price that will be set for each photo uploaded.', 'printfolio' ); ?></span>
						</fieldset>
					</td>
				</tr>
				<tr class="collection-form-field" valign="top">
					<th scope="row" class="titledesc">
						<label for="printfolio-batch-collection"><?php _e( 'Collections', 'printfolio' ); ?></label>
					</th>
					<td class="forminp">
						<fieldset>
							<legend class="screen-reader-text"><span><?php _e( 'Collections', 'printfolio' ); ?></span></legend>
							<input type="hidden" id="printfolio-batch-collection" class="printfolio-collections-select" name="collections" style="width: 300px;" />
							<span class="description"><?php echo _e( 'Specify which collection(s) these photos belong to.', 'printfolio' ); ?></span>
							<div class="photography-add-collection">
								<a href="#"><?php _e( '+ Add Collection', 'printfolio' ); ?></a>
								<div class="fields">
									<input type="text" class="input-text regular-input new-collection" />
									<button type="submit" class="button"><?php _e( 'Add New Collection', 'printfolio' ); ?></button>
								</div>
							</div>
						</fieldset>
					</td>
				</tr>
			</table>

			<?php do_action( 'WC_Printfolio_batch_upload_fields' ); ?>

			<div id="printfolio-drag-drop-area">
				<div class="drag-drop-inside">
					<p class="drag-drop-info"><?php _e( 'Drop images here', 'printfolio' ); ?></p>
					<p><?php _ex( 'or', 'Uploader: Drop images here - or - Select Images', 'printfolio' ); ?></p>
					<p class="drag-drop-buttons">
						<input id="printfolio-uploader-browse-button" type="button" value="<?php esc_attr_e( 'Select Images', 'printfolio' ); ?>" class="button" />
					</p>
				</div>
			</div>

			<?php do_action( 'WC_Printfolio_batch_upload_fields_after' ); ?>

			<p class="max-upload-size"><?php printf( __( 'Maximum upload file size: %s.', 'printfolio' ), esc_html( size_format( $max_upload_size ) ) ); ?></p>
		</div>

		<div id="printfolio-html-upload-ui" class="hide-if-js">
			<p><?php _e( 'You can\'t send images because your browser is too old or do not have JavaScript enabled!', 'printfolio' ); ?></p>
		</div>
	</div>

	<div id="printfolio-image-edit" class="meta-box-sortables" style="display: none;">

		<p class="submit"><button type="button" class="button button-primary"><?php _e( 'Save Changes', 'printfolio' ); ?></button></p>

		<div class="postbox">
			<div class="wc-metaboxes-wrapper">
				<p class="toolbar">
					<a href="#" class="close_all"><?php _e( 'Close all', 'printfolio' ); ?></a><a href="#" class="expand_all"><?php _e( 'Expand all', 'printfolio' ); ?></a>
					<strong><?php _e( 'Photographs', 'printfolio' ); ?></strong>
				</p>

				<div class="wc-metaboxes">
				</div>
			</div>
		</div>

		<p class="submit"><button type="button" class="button button-primary"><?php _e( 'Save Changes', 'printfolio' ); ?></button></p>
	</div>

	<script type="text/template" id="printfolio-image-template">
		<div id="photography-<%- id %>" class="wc-metabox closed" data-index="<%- index %>" data-id="<%- id %>">
			<h3>
				<img src="<%- thumbnail %>" alt="" class="thumbnail" />
				<button type="button" class="remove button"><?php _e( 'Remove', 'printfolio' ); ?></button>
				<div class="handlediv" title="<?php _e( 'Click to toggle', 'printfolio' ); ?>"></div>
				<strong class="image-name"><%- index %>. <%- sku %></strong>
			</h3>
			<div class="wc-metabox-content">
				<div class="fields">
					<?php do_action( 'WC_Printfolio_batch_upload_edit_fields' ); ?>
					<p class="form-field first">
						<label for="photography-<%- id %>-sku"><?php _e( 'SKU', 'printfolio' ); ?></label>
						<input type="text" id="photography-<%- id %>-sku" class="short sku-field" name="photography[<%- id %>][sku]" value="<%- sku %>" />
					</p>
					<p class="form-field last">
						<label for="photography-<%- id %>-price"><?php _e( 'Price', 'printfolio' ); ?></label>
						<input type="text" id="photography-<%- id %>-price" class="wc_input_price price-field input-text regular-input" name="photography[<%- id %>][price]" value="<%- price %>" />
					</p>
					<div class="collection-form-field">
						<p class="form-field full">
							<label for="photography-<%- id %>-collections"><?php _e( 'Collections', 'printfolio' ); ?></label>
							<input type="hidden" id="photography-<%- id %>-collections" class="printfolio-collections-select" name="photography[<%- id %>][collections]" style="width: 300px;" value="<%- collections_ids %>" data-selected='[<%
							var collectionsSize = _.size( collections ),
								current = 0;
							_.each( collections, function( collection_name, collection_id ) {
								current++;
								%>{"id": "<%- collection_id %>", "text": "<%- collection_name %>"}<% if ( current !== collectionsSize ){ %>,<% }
							}); %>]' />
						</p>
						<p class="form-field full photography-add-collection">
							<a href="#"><?php _e( '+ Add Collection', 'printfolio' ); ?></a>
							<span class="fields">
								<input type="text" class="input-text regular-input new-collection" />
								<button type="submit" class="button"><?php _e( 'Add New Collection', 'printfolio' ); ?></button>
							</span>
						</p>
					</div>
					<p class="form-field full">
						<label for="photography-<%- id %>-caption"><?php _e( 'Caption', 'printfolio' ); ?></label>
						<textarea id="photography-<%- id %>-caption" rows="4" cols="50" class="caption-field" name="photography[<%- id %>][caption]"></textarea>
					</p>
					<?php do_action( 'WC_Printfolio_batch_upload_after_edit_fields' ); ?>
				</div>
			</div>
		</div>
	</script>
</div>
