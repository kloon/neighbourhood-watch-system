<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Function for subsribing members to the mailing list
 * @param  array $data
 * @return bool
 */
function nhw_subscribe_mailchimp( $data ) {
	if ( ! class_exists( 'NWS_MailChimp' ) ) {
		require_once( plugin_dir_path( __FILE__ ) . 'class-nws-mailchimp.php' );
	}
	return NWS_MailChimp::subscribe_to_list( $data );
} // End nhw_subscribe_mailchimp()
