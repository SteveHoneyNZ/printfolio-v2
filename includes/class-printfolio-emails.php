<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * WooCommerce Photography Emails.
 *
 * @package  WC_Printfolio/Emails
 * @category Class
 * @author   SteveHoney
 */
class WC_Printfolio_Emails {

	/**
	 * Initialize emails actions.
	 */
	public function __construct() {
		add_filter( 'woocommerce_email_classes', array( $this, 'emails' ) );
	}

	/**
	 * Include email templates.
	 *
	 * @param  array $emails
	 *
	 * @return array
	 */
	public function emails( $emails ) {
		if ( ! isset( $emails['WC_Email_Photography_New_Collection'] ) ) {
			$emails['WC_Email_Photography_New_Collection'] = include( 'emails/class-wc-mail-photography-new-collection.php' );
		}

		return $emails;
	}
}

new WC_Printfolio_Emails();
