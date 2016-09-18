<?php
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wrap">

	<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>

	<?php settings_errors(); ?>
	<form method="post" action="options.php">

		<?php
			settings_fields( 'printfolio_options' );
			do_settings_sections( 'printfolio_options' );

			submit_button();
		?>

	</form>

</div>
