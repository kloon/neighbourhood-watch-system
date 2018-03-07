<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_Shortcodes' ) ) :
	class NWS_Shortcodes {

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			add_shortcode( 'nhw_member_application', array( $this, 'member_application_shortcode' ) );
		} // End __construct()

		/**
		 * Member application form shortcode
		 * @param  array $atts
		 * @return string
		 */
		public function member_application_shortcode( $atts ) {
			$this->maybe_register_member();
			ob_start();
			?>

			<form class="form-horizontal" method="post">
				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Address Details</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-2">
								<label for="strnr">Nr</label>
								<input type="text" class="form-control" id="strnr" name="strnr" required="true">
							</div>
							<div class="col-sm-5">
								<label for="complex">Complex</label>
								<select class="form-control" id="complex" name="complex">
									<option value="">Not applicable</option>
									<?php
									$complexes = Neighbourhood_Watch_System::get_instance()->residents->complexes;
									sort( $complexes );
									foreach ( $complexes as $complex ) {
										echo '<option value="' . esc_attr( $complex ) . '">' . esc_attr( $complex ) . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-sm-5">
								<label for="street">Street</label>
								<select class="form-control" id="street" name="street" required="true">
									<option value="">Please select...</option>
									<?php
									$streets = Neighbourhood_Watch_System::get_instance()->residents->streets;
									sort( $streets );
									foreach ( $streets as $street ) {
										echo '<option value="' . esc_attr( $street ) . '">' . esc_attr( $street ) . '</option>';
									}
									?>
								</select>
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Applicant Details</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-6">
								<label for="firstname">Full Names</label>
								<input type="text" class="form-control" id="firstname" name="firstname" required="true">
							</div>
							<div class="col-sm-6">
								<label for="lastname">Last Name</label>
								<input type="text" class="form-control" id="lastname" name="lastname" required="true">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<label for="homephone">Home Phone</label>
								<input type="text" class="form-control" id="homephone" name="homephone">
							</div>
							<div class="col-sm-6">
								<label for="cellphone">Cell Phone</label>
								<input type="text" class="form-control" id="cellphone" name="cellphone" required="true">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<label for="email">Email</label>
								<input type="email" class="form-control" id="email" name="email">
							</div>
							<div class="col-sm-6">
								<label for="idnr">ID Number</label>
								<input type="text" class="form-control" id="idnr" name="idnr" required="true">
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<label for="occupation">Occupation</label>
								<input type="text" class="form-control" id="occupation" name="occupation">
							</div>
						</div>
					</div>
				</div>

				<div class="panel panel-default">
					<div class="panel-heading">
						<h3 class="panel-title">Neighbourhood Watch</h3>
					</div>
					<div class="panel-body">
						<div class="form-group">
							<div class="col-sm-6">
								<label for="language">What is your preferred communication language?</label>
								<select class="form-control" id="language" name="language">
									<option value="Afrikaans">Afrikaans</option>
									<option value="English">English</option>
								</select>
							</div>
							<div class="col-sm-3">
								<label for="whatsapp">Would you like to be added to the Alert WhatsApp group?</label>
								<select class="form-control" id="whatsapp" name="whatsapp">
									<option value="yes">Yes</option>
									<option value="no">No</option>
								</select>
								<small>* By selecting Yes, you agree to the <a href="/whatsapp-terms-of-use/" target="_blank">WhatsApp Terms Of Use</a></small>
							</div>
							<div class="col-sm-3">
								<label for="whatsapp">Would you like to be added to the General WhatsApp group?</label>
								<select class="form-control" id="whatsapp-general" name="whatsapp-general">
									<option value="yes">Yes</option>
									<option value="no">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<label for="armed-response">Which Armed Response company do you use at home?</label>
								<select class="form-control" id="armed-response" name="armed-response">
									<option value="na" <?php selected( 'na', $security, true ); ?>>Not Applicable</option>
									<?php
									$security_companies = Neighbourhood_Watch_System::get_instance()->residents->security_companies;
									sort( $security_companies );
									foreach ( $security_companies as $name ) {
										echo '<option value="' . esc_attr( $name ) . '">' . esc_attr( $name ) . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="radio">Would you like to purchase a Neighbourhood Watch Radio?</label>
								<select class="form-control" id="radio" name="radio">
									<option value="yes">Yes</option>
									<option value="no" selected="selected">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-6">
								<label for="donate">Would you like to donate towards FernPark?</label>
								<select class="form-control" id="donate" name="donate">
									<option value="yes">Yes</option>
									<option value="no" selected="selected">No</option>
								</select>
							</div>
							<div class="col-sm-6">
								<label for="patrols">Would you like to participate in Patrols?</label>
								<select class="form-control" id="patrols" name="patrols">
									<option value="yes">Yes</option>
									<option value="no" selected="selected">No</option>
								</select>
							</div>
						</div>
						<div class="form-group">
							<div class="col-sm-12">
								<label for="other">Other questions / suggestions?</label>
								<textarea class="form-control" id="other" rows="4" name="other"></textarea>
							</div>
						</div>
					</div>
				</div>
				<div class="form-group">
					<div class="col-sm-12">
						<small>* By submitting this application form you agree that the information you provided are correct and that <a href="http://www.fernpark.co.za/fernpark-indemnity-form/" target="_blank">you indemnify FernPark against any actions committed or omitted by you</a> as a member.</small>
					</div>
					<input class="btn btn-primary" type="submit" value="Submit Application *">
				</div>
				<?php wp_nonce_field( 'register-nhw-membership', 'nhw-register-nonce' ); ?>
			</form>

			<?php
			return ob_get_clean();
		} // End member_application_shortcode()

		public function maybe_register_member() {
			if ( ! isset( $_POST['nhw-register-nonce'] ) || ! wp_verify_nonce( $_POST['nhw-register-nonce'], 'register-nhw-membership' ) ) {
				return;
			}

			$strnr 		= isset( $_POST['strnr'] ) ? sanitize_text_field( $_POST['strnr'] ) : '';
			$complex 	= isset( $_POST['complex'] ) ? sanitize_text_field( $_POST['complex'] ) : '';
			$street 	= isset( $_POST['street'] ) ? sanitize_text_field( $_POST['street'] ) : '';
			$firstname 	= isset( $_POST['firstname'] ) ? sanitize_text_field( $_POST['firstname'] ) : '';
			$lastname 	= isset( $_POST['lastname'] ) ? sanitize_text_field( $_POST['lastname'] ) : '';
			$homephone 	= isset( $_POST['homephone'] ) ? sanitize_text_field( $_POST['homephone'] ) : '';
			$cellphone 	= isset( $_POST['cellphone'] ) ? sanitize_text_field( $_POST['cellphone'] ) : '';
			$email 		= isset( $_POST['email'] ) ? sanitize_email( $_POST['email'] ) : '';
			$idnr 		= isset( $_POST['idnr'] ) ? sanitize_text_field( $_POST['idnr'] ) : '';
			$occupation = isset( $_POST['occupation'] ) ? sanitize_text_field( $_POST['occupation'] ) : '';

			$language 		= isset( $_POST['language'] ) ? sanitize_text_field( $_POST['language'] ) : '';
			$whatsapp 		= isset( $_POST['whatsapp'] ) ? sanitize_text_field( $_POST['whatsapp'] ) : '';
			$whatsappgen	= isset( $_POST['whatsapp-general'] ) ? sanitize_text_field( $_POST['whatsapp-general'] ) : '';
			$armedresponse 	= isset( $_POST['armed-response'] ) ? sanitize_text_field( $_POST['armed-response'] ) : '';
			$radio 			= isset( $_POST['radio'] ) ? sanitize_text_field( $_POST['radio'] ) : '';
			$donate			= isset( $_POST['donate'] ) ? sanitize_text_field( $_POST['donate'] ) : '';
			$patrols 		= isset( $_POST['patrols'] ) ? sanitize_text_field( $_POST['patrols'] ) : '';
			$other 			= isset( $_POST['other'] ) ? sanitize_text_field( $_POST['other'] ) : '';

			// Check if required fields are set
			if (
				empty( $strnr )
				|| empty( $street )
				|| empty( $firstname )
				|| empty( $lastname )
				|| empty( $cellphone )
				|| empty( $idnr )
			) {
				echo '<div class="alert alert-danger"><strong>Error!</strong> Please enter all the required fields.</div>';
				return;
			}

			// Update existing members details based on their ID number
			$args = array(
				'post_type' => 'resident',
				'meta_key' => '_id_number',
				'meta_value' => $idnr,
				'fields' => 'ids',
			);
			$existing_member = new WP_Query( $args );
			$existing = false;
			// If resident already in db, update their details, else insert a new record.
			if ( $existing_member->have_posts() ) {

				$member_id = $existing_member->posts[0];
				$existing_data = get_post_meta( $member_id );
				$member = get_post( $member_id );
				$existing = true;
				$post_args = array(
					'ID' 			=> $member_id,
					'post_title'   	=> $firstname . ' ' . $lastname,
					'post_name' 	=> sanitize_title( $firstname . ' ' . $lastname ),
					'post_type'		=> 'resident'
				);
				if ( ! empty( $other ) ) {
					$post_args['post_content'] 	= $member->post_content . PHP_EOL . 'Question/Suggestion: ' . $other;
				}

				$existing_nhw_member = get_post_meta( $member_id, '_nhw_member', true );
				if ( 'yes' !== $existing_nhw_member ) {
					$post_args['post_status'] = 'draft';
				}
				wp_update_post( $post_args );

				// Update user on MailChimp side if already a NHW member
				if ( 'yes' == $existing_nhw_member ) {
					$merge_vars = array(
						'email' => $email,
						'first_name' => $firstname,
						'last_name' => $lastname,
						'street' => $street,
						'nhw_member' => ucwords( 'yes' ),
						'nhw_patroller' => ucwords( $patrols ),
						'whatsapp' => ucwords( $whatsapp )
					);
					$subscribed = nhw_subscribe_mailchimp( $merge_vars );
				}
			} else {
				$post_args = array(
					'post_title'   	=> $firstname . ' ' . $lastname,
					'post_name' 	=> sanitize_title( $firstname . ' ' . $lastname ),
					'post_status' 	=> 'draft',
					'post_type'		=> 'resident'
				);

				if ( ! empty( $other ) ) {
					$post_args['post_content'] 	= 'Question/Suggestion: ' . $other;
				}

				$member_id = wp_insert_post( $post_args );
				if ( 0 == $member_id ) {
					echo '<div class="alert alert-danger"><strong>Error!</strong> There was an error registering you on the system, please contact us on hello@fernpark.co.za</div>';
					return;
				}
			}

			// Save address details
			update_post_meta( $member_id, '_street_nr', $strnr );
			update_post_meta( $member_id, '_complex', $complex );
			update_post_meta( $member_id, '_street', $street );

			// Save resident details
			update_post_meta( $member_id, '_first_name', $firstname );
			update_post_meta( $member_id, '_last_name', $lastname );
			update_post_meta( $member_id, '_home_phone', $homephone );
			update_post_meta( $member_id, '_cell_phone', $cellphone );
			update_post_meta( $member_id, '_email', $email );
			update_post_meta( $member_id, '_id_number', $idnr );
			update_post_meta( $member_id, '_occupation', $occupation );

			// Save other fields
			update_post_meta( $member_id, '_language', $language );
			update_post_meta( $member_id, '_nhw_whatsapp', $whatsapp );
			update_post_meta( $member_id, '_nhw_whatsapp_general', $whatsappgen );
			update_post_meta( $member_id, '_security', $armedresponse );
			update_post_meta( $member_id, '_interest_radio', $radio );
			update_post_meta( $member_id, '_interest_donate', $donate );
			update_post_meta( $member_id, '_nhw_patrol', $patrols );
			update_post_meta( $member_id, '_other', $other );
			update_post_meta( $member_id, '_nhw_member', 'yes' );

			// Just set these fields to no for backward compatibility
			update_post_meta( $member_id, '_nhw_zello', 'no' );
			update_post_meta( $member_id, '_nhw_facebook', 'no' );

			// Update idenity field to yes, just se we have record the user agreed when clicking the button.
			update_post_meta( $member_id, '_indemnity_agreed', 'yes' );
			update_post_meta( $member_id, '_indemnity_agreed_date', date( 'Y-m-d H:i:s' ) );

			// Send email and set message for new user
			if ( ! $existing ) {
				$mail_message = '
					<h1>New NHW Member</h1>
					<p>Howdy FernPark Committee,</p>
					<p>A new member has signed up to join the Neighbourhood Watch, please go through the details in the system and publish the user once you are satisfied.</p>
					First Name:' . $firstname . '<br/>
					Last Name:' . $lastname . '<br/>
					Address:' . $strnr . ' ' . $complex . ' ' . $street . '<br/>
					Home Phone:' . $homephone . '<br/>
					Cell Phone:' . $cellphone . '<br/>
					Email:' . $email . '<br/>
					ID Number:' . $idnr . '<br/>
					Occupation:' . $occupation . '<br/>

					Language:' . $language . '<br/>
					Wants to join Alert WhatsApp Group:' . $whatsapp . '<br/>
					Wants to join General WhatsApp Group:' . $whatsappgen . '<br/>
					Armed Response:' . $armedresponse . '<br/>
					Interested in Radio:' . $radio . '<br/>
					Interested to Donate:' . $donate . '<br/>
					Interested to Patrol:' . $patrols . '<br/>
					Comment/Suggestion:' . $other . '<br/>



				';
				// @todo: Load emails from a setting.
				wp_mail( array( 'no-reply@gmail.com' ), 'New NHW Member Signup', $mail_message, array( 'Content-Type: text/html; charset=UTF-8' ) );

				if ( ! empty( $email ) ) {
					// User Email
					$mail_message = '
						<h1>Welcome to FernPark NHW</h1>
						<p>Hi there,</p>
						<p>Thanks for signing up to the join the FernPark Neighbourhood Watch, one of our committee members will be going through your application and approving it, if they have any questions they will be in touch.</p>
						<p>If you have any questions in the mean time, feel free to just reply to this email.</p>
						<p>Here\'s to making FernPark as safer area!</p>
						<p>The FernPark Committee</p>
					';
					wp_mail( $email, 'Welcome to FernPark', $mail_message, array( 'From: FernPark <hello@fernpark.co.za>', 'Reply-To: FernPark <hello@fernpark.co.za>', 'Content-Type: text/html; charset=UTF-8' ) );
				}

				echo '<div class="alert alert-success"><strong>Thank You!</strong> Thank you for registering as a FernPark Neighbourhood Watch member, we will be in touch regarding your membership soon!</div>';
			} else {
				$mail_message = '
					<h1>NHW Member Information Updated</h1>
					<p>Howdy FernPark Committee,</p>
					<p>An existing member has updated their information, this email is reference and to see what has changed. New / Old values are both showed.</p>
					Field: New / Old<br/>
					Neighbourhood Watch Member: Yes / ' . $existing_data['_nhw_member'][0] . '<br/>
					First Name:' . $firstname . ' / ' . $existing_data['_first_name'][0] . '<br/>
					Last Name:' . $lastname . ' / ' . $existing_data['_last_name'][0] . '<br/>
					Address:' . $strnr . ' ' . $complex . ' ' . $street . ' / ' . $existing_data['_street_nr'][0] . ' ' . $existing_data['_complex'][0] . ' ' . $existing_data['_street'][0] . '<br/>
					Home Phone:' . $homephone . ' / ' . $existing_data['_home_phone'][0] . '<br/>
					Cell Phone:' . $cellphone . ' / ' . $existing_data['_cell_phone'][0] . '<br/>
					Email:' . $email . ' / ' . $existing_data['_email'][0] . '<br/>
					ID Number:' . $idnr . ' / ' . $existing_data['_id_number'][0] . '<br/>
					Occupation:' . $occupation . ' / ' . $existing_data['_occupation'][0] . '<br/>

					Language:' . $language . ' / ' . $existing_data['_language'][0] . '<br/>
					Wants to join Alert WhatsApp Group:' . $whatsapp . ' / ' . $existing_data['_nhw_whatsapp'][0] . '<br/>
					Wants to join General WhatsApp Group:' . $whatsappgen . ' / ' . $existing_data['_nhw_whatsapp_general'][0] . '<br/>
					Armed Response:' . $armedresponse . ' / ' . $existing_data['_security'][0] . '<br/>
					Interested in Radio:' . $radio . ' / ' . $existing_data['_interest_radio'][0] . '<br/>
					Interested to Donate:' . $donate . ' / ' . $existing_data['_interest_donate'][0] . '<br/>
					Interested to Patrol:' . $patrols . ' / ' . $existing_data['_nhw_patrol'][0] . '<br/>
					Comment/Suggestion:' . $other . '<br/>



				';
				// @todo: Load emails from a setting.
				wp_mail( array( 'no-reply@gmail.com' ), 'NHW Member Info Updated', $mail_message, array( 'Content-Type: text/html; charset=UTF-8' ) );

				if ( ! empty( $email ) ) {
					// User Email
					$mail_message = '
						<h1>FernPark Information Updated</h1>
						<p>Hi there,</p>
						<p>Thanks for taking the time to update your FernPark Neighbourhood Watch details, we appreciate it!
						<p>If you have any questions, feel free to just reply to this email.</p>
						<p>Here\'s to making FernPark as safer area!</p>
						<p>The FernPark Committee</p>
					';
					wp_mail( $email, 'Thanks for updating your information!', $mail_message, array( 'From: FernPark <hello@fernpark.co.za>', 'Reply-To: FernPark <hello@fernpark.co.za>', 'Content-Type: text/html; charset=UTF-8' ) );
				}

				echo '<div class="alert alert-success"><strong>Thank You!</strong> You were already registered in our system so we simply updated your information.</div>';
			}
		}
	}
endif;
