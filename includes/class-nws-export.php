<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_Export' ) ) :
	class NWS_Export {

		private $custom_fields = array(
			'_first_name' => 'First Name',
			'_last_name' => 'Last Name',
			'_id_number' => 'ID Number',
			'_street_nr' => 'Street/Complex Nr',
			'_complex' => 'Complex',
			'_street' => 'Street',
			'_email' => 'Email',
			'_email2' => 'Additional Email',
			'_home_phone' => 'Home Phone',
			'_cell_phone' => 'Cellphone',
			'_cell_phone2' => 'Additional Phone',
			'_business' => 'Business',
			'_security' => 'Security Company',
			'_nhw_member' => 'NHW Member',
			'_nhw_patrol' => 'Patroller',
			'_nhw_zello' => 'Zello Access',
			'_nhw_whatsapp' => 'WhatsApp Access',
			'_nhw_facebook' => 'Facebook Access',
			'_occupation'	=> 'Occupation',
			'_interest_radio' => 'Interested in Radio',
			'_interest_donate' => 'Interested to Donate',
			'_other' => 'Comments/Suggestions By Member',
			'_indemnity_agreed' => 'Online Indemnity Agreed',
			'_indemnity_agreed_date' => 'Online Indemnity Agreed Date',
		);

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'handle_export' ) );
		} // End __construct()

		public function admin_menu() {
			add_submenu_page( 'nhw-system', __( 'Export Neighbourhood Watch Data' ), __( 'Export' ), 'edit_posts', 'nhw-export', array( $this, 'export_page' ) );
		}

		public function export_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'Export Neighbourhood Watch Data' ); ?></h1>
				<form method="post">
					<table class="form-table">
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
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Export to CSV"></p>
					<?php wp_nonce_field( 'exporting-nhw-data', 'nhw-export-nonce' ); ?>
				</form>
			</div>
			<?php
		}

		public function handle_export() {
			global $pagenow;

			// Do nothing if not on the right page
			if ( 'admin.php' !== $pagenow && ( ! isset( $_GET['page'] ) || 'nhw-export' !== $_GET['page'] ) ) {
				return;
			}

			// Verify we have a valid nonce
			if ( ! isset( $_POST['nhw-export-nonce'] ) || ! wp_verify_nonce( $_POST['nhw-export-nonce'], 'exporting-nhw-data' ) ) {
				return;
			}

			$args = array(
				'post_type' => 'resident',
				'posts_per_page' => -1,
				'order_by' => 'meta_value',
				'order' => 'ASC',
				'meta_key' => '_street',
			);

			$has_meta_filter = false;
			$meta_filter_count = 0;
			if ( isset( $_POST['street'] ) && ! empty( $_POST['street'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_street',
					'value' => sanitize_text_field( $_POST['street'] ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( isset( $_POST['members-only'] ) || ! empty( $_POST['members-only'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_nhw_member',
					'value' => sanitize_text_field( strtolower( $_POST['members-only'] ) ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( isset( $_POST['patrollers-only'] ) || ! empty( $_POST['patrollers-only'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_nhw_patrol',
					'value' => sanitize_text_field( strtolower( $_POST['patrollers-only'] ) ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( isset( $_POST['whatsapp-only'] ) || ! empty( $_POST['whatsapp-only'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_nhw_whatsapp',
					'value' => sanitize_text_field( strtolower( $_POST['whatsapp-only'] ) ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( isset( $_POST['zello-only'] ) || ! empty( $_POST['zello-only'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_nhw_zello',
					'value' => sanitize_text_field( strtolower( $_POST['zello-only'] ) ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( isset( $_POST['facebook-only'] ) || ! empty( $_POST['facebook-only'] ) ) {
				$args['meta_query'][] = array(
					'key'   => '_nhw_facebook',
					'value' => sanitize_text_field( strtolower( $_POST['facebook-only'] ) ),
					'compare' => '='
				);
				$meta_filter_count++;
			}
			if ( $meta_filter_count > 1 ) {
				$args['meta_query']['relation'] = 'AND';
			}

			$csv = "";
			$query = new WP_Query( $args );
			if ( $query->have_posts() ) :
				foreach ( $this->custom_fields as $key => $value ) {
					$csv .= "\"$value\";";
				}
				$csv .= "\n";
				while ( $query->have_posts() ) :
					$query->the_post();
					$post_id = get_the_id();
					$custom_fields = get_post_custom( $post_id );
					foreach ( $this->custom_fields as $key => $value ) {
						if ( isset( $custom_fields[ $key ] ) ) {
							if ( in_array( $key, array( '_home_phone','_cell_phone','_cell_phone2' ) ) && ! empty( $custom_fields[ $key ][0] ) ) {
								$number = (string)$custom_fields[ $key ][0];
								$number = str_replace( '-', '', $number );
								$number = str_replace( ' ', '', $number );
								$number = '(' . substr( $number, 0, 3 ) . ') ' . substr( $number, 3, 3 ) . '-' . substr( $number, 6 );
								$csv .= "\"" . trim( $number ) ."\";";
							} else {
								$csv .= "\"" . trim( $custom_fields[ $key ][0] ) ."\";";
							}
						} else {
							$csv .= ";";
						}
					}
					$csv .= "\n";
				endwhile;
				wp_reset_postdata();
			endif;

			header( "Content-type: text/csv" );
			header( "Content-Disposition: attachment; filename=fernpark-members-" . date('Y-m-d') . ".csv" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );

			echo $csv;
			die();
		}
	}
endif;