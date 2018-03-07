<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_SMS' ) ) :
	class NWS_SMS {

		/**
		 * API Base URL
		 * @var string
		 */
		private static $api_url = 'https://api.clickatell.com/rest/';

		/**
		 * Our Clickatell API key
		 * @var string
		 */
		private static $api_token = 'Gu5Cjp.R9KgvY.LXuZmSO3xWdN7vSvm56NbqrqfOpPnIlHHyUQNbsXXX9AVA1PU1YP1h1zgF1n55mj';


		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'handle_sms_send' ) );
		} // End __construct()

		public function admin_menu() {
			add_submenu_page( 'nhw-system', __( 'SMS Neighbourhood Watch Members' ), __( 'SMS' ), 'edit_posts', 'nhw-sms', array( $this, 'sms_page' ) );
		}

		public function sms_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'SMS Neighbourhood Watch Members' ); ?></h1>
				<p>Please note this functionality is still under construction and not operational yet!</p>
				<form method="post">
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="message">SMS Message</label>
							</th>
							<td>
								<textarea id="message" name="message" rows="5" cols="50"></textarea>
								<p>SMS Credits Left: <strong><?php echo round( self::retrieve_balance() ); ?></strong></p>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="street">Street</label>
							</th>
							<td>
								<select name="street">
									<option value=""><?php _e( 'All' ); ?></option>
									<?php
										foreach ( Neighbourhood_Watch_System::get_instance()->residents->streets as $street ) {
											echo '<option value="' . $street . '">' . $street . '</option>';
										}
									?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="members-only">NHW Members Only?</label>
							</th>
							<td>
								<input name="members-only" type="checkbox" id="members-only" value="yes"/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="patrollers-only">NHW Patrollers Only?</label>
							</th>
							<td>
								<input name="patrollers-only" type="checkbox" id="patrollers-only" value="yes"/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="whatsapp-only">WhatsApp Group Members Only?</label>
							</th>
							<td>
								<input name="whatsapp-only" type="checkbox" id="whatsapp-only" value="yes"/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="zello-only">Zello Members Only?</label>
							</th>
							<td>
								<input name="zello-only" type="checkbox" id="zello-only" value="yes"/>
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="facebook-only">Facebook Group Members Only?</label>
							</th>
							<td>
								<input name="facebook-only" type="checkbox" id="facebook-only" value="yes"/>
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Send SMS"></p>
					<?php wp_nonce_field( 'sms-nhw-members', 'nhw-sms-nonce' ); ?>
				</form>
			</div>
			<?php
		}

		public function handle_sms_send() {

		}

		/**
		 * Make a call to the API
		 * @param  string $endpoint
		 * @param  array  $body
		 * @param  string $method
		 * @return Object
		 */
		private static function perform_request( $endpoint, $body = array(), $method = 'POST' ) {
			$args = array(
				'method' 	  => $method,
				'timeout'     => 45,
				'httpversion' => '1.0',
				'blocking'    => true,
				'headers'     => array(
					'X-Version'	  => 1,
					'Authorization'	=> 'Bearer ' . self::$api_token,
					'accept'       	=> 'application/json',
					'content-type' 	=> 'application/json',
				),
				'body'        => json_encode( $body ),
				'cookies'     => array(),
			);
			$response = wp_remote_request( self::$api_url . $endpoint, $args );
			return $response;
		} // End perform_request()

		/**
		 * Send SMS message
		 * @param  array  $recipients
		 * @param  string $message
		 * @return bool
		 */
		public static function send_message( $recipients = array(), $message ) {
			$data = array(
				'text' => $message,
				'to' => $recipients,
			);
			$response = self::perform_request( 'message', $data );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			return json_decode( wp_remote_retrieve_body( $response ) );
		} // End send_message()

		/**
		 * Retrieve the credit balance from your clickatell account
		 * @return bool|string
		 */
		public static function retrieve_balance() {
			$response = self::perform_request( 'account/balance', array(), 'GET' );
			if ( is_wp_error( $response ) ) {
				return false;
			}

			$data = json_decode( wp_remote_retrieve_body( $response ) );
			if ( isset( $data->data->balance ) ) {
				return $data->data->balance;
			}

			return false;
		} // End retrieve_balance()
	}
endif;