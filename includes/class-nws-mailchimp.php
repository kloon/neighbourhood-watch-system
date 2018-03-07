<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_MailChimp' ) ) :
	class NWS_MailChimp {

		/**
		 * API Base URL
		 * @var string
		 */
		private static $api_url = "https://us12.api.mailchimp.com/2.0";

		/**
		 * Our MailChimp API key
		 * @var string
		 */
		private static $api_key = '';

		/**
		 * Our mailing list ID
		 * @var string
		 */
		private static $list_id = '';

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {

		} // End __construct()

		/**
		 * Make a call to the API
		 * @param  string $endpoint
		 * @param  array  $body
		 * @param  string $method
		 * @return Object
		 */
		private static function perform_request( $endpoint, $body = array(), $method = 'POST' ) {
			// Set API key if not set
			if ( ! isset( $body['apikey'] ) ) {
				$body['apikey'] = self::$api_key;
			}
			$args = array(
				'method' 	  => $method,
				'timeout'     => 45,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'accept'       	=> 'application/json',
					'content-type' 	=> 'application/json',
				),
				'body'        => json_encode( $body ),
				'cookies'     => array(),
			);
			$response = wp_remote_request( self::$api_url . $endpoint, $args );

			if ( is_wp_error( $response ) ) {
				//throw new Exception( 'Error permorming remote MailChimp request.' );
			}
			return $response;
		} // End perform_request()

		/**
		 * Subscribe member to list
		 * @param  array $member_data
		 * @return bool
		 */
		public static function subscribe_to_list( $member_data ) {
			$data = array(
				'id' => self::$list_id,
				'email' => array( 'email' => $member_data['email'] ),
				'merge_vars' => array(
					'FNAME' => $member_data['first_name'],
					'LNAME' => $member_data['last_name'],
					'STREET' => $member_data['street'],
					'NHWMEMBER' => $member_data['nhw_member'],
					'NHWPATROLL' => $member_data['nhw_patroller'],
					'WHATSAPP' => $member_data['whatsapp'],
					'ZELLO' => $member_data['zello'],
					'FACEBOOK' => $member_data['facebook'],
					'BUSINESS' => $member_data['business'],
				),
				'double_optin' => false,
				'update_existing' => true,
			);
			$response = self::perform_request( '/lists/subscribe.json', $data );
			$response = wp_remote_retrieve_body( $response );
			if ( is_wp_error( $response ) ) {
				return false;
			}
			$response = json_decode( $response, true );
			if ( isset( $response['status'] ) && 'error' ==  $response['status'] ) {
				return false;
			}
			return true;
		} // End subscribe_to_list()

	}

endif;