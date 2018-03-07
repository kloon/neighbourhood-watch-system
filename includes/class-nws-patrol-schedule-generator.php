<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_Patrol_Schedule_Generator' ) ) :
	class NWS_Patrol_Schedule_Generator {

		/**
		 * Constructor
		 */
		public function __construct() {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			add_action( 'admin_menu', array( $this, 'admin_menu' ) );
			add_action( 'admin_init', array( $this, 'handle_generator' ) );
		} // End __construct()

		public function enqueue_scripts( $hook ) {
			if ( 'fernpark_page_nhw-patrol-schedule-generator' == $hook ) {
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.11.4/themes/smoothness/jquery-ui.css' );
			}
		}

		public function admin_menu() {
			add_submenu_page( 'nhw-system', __( 'Patrol Schedule Generator' ), __( 'Patrol Schedule Generator' ), 'edit_posts', 'nhw-patrol-schedule-generator', array( $this, 'patrol_schedule_generator_page' ) );
		}

		public function patrol_schedule_generator_page() {
			?>
			<div class="wrap">
				<h1><?php _e( 'Generate Patrol Schedule' ); ?></h1>
				<form method="post">
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="start-date">Start Date</label>
							</th>
							<td>
								 <input id="start-date" name="start-date" class="date" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="end-date">End Date</label>
							</th>
							<td>
								<input name="end-date" id="end-date" class="date" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="patrol-slots">Patrol Slots Per Day</label>
							</th>
							<td>
								<input name="patrol-slots" type="number" id="patrol-slots" />
							</td>
						</tr>
						<tr>
							<th scope="row">
								<label for="patrollers-per-slots">Patroller Per Slot</label>
							</th>
							<td>
								<input name="patrolers-per-slots" type="number" id="patrolers-per-slots" />
							</td>
						</tr>
					</table>
					<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="Generate &amp; Export CSV Schedule"></p>
					<?php wp_nonce_field( 'generating-nhw-patrol-sechule', 'nhw-patrol-schedule-nonce' ); ?>
				</form>
			</div>
			<script>
				jQuery(document).ready(function() {
					jQuery( '.date' ).datepicker({
						dateFormat : 'yy-mm-dd'
					});
				});
			</script>
			<?php
		}

		public function handle_generator() {
			global $pagenow;

			// Do nothing if not on the right page
			if ( 'admin.php' !== $pagenow && ( ! isset( $_GET['page'] ) || 'nhw-patrol-schedule-generator' !== $_GET['page'] ) ) {
				return;
			}

			// Verify we have a valid nonce
			if ( ! isset( $_POST['nhw-patrol-schedule-nonce'] ) || ! wp_verify_nonce( $_POST['nhw-patrol-schedule-nonce'], 'generating-nhw-patrol-sechule' ) ) {
				return;
			}

			if ( ! isset( $_POST[''] ) || ! isset( $_POST[''] ) || ! isset( $_POST[''] ) || ! isset( $_POST[''] ) ) {

			}

			$from_date = strtotime( $_POST['start-date'] );
			$to_date = strtotime( $_POST['end-date'] );
			$slot_counter = intval( $_POST['patrol-slots'] );
			$per_slot = intval( $_POST['patrolers-per-slots'] );
			$csv = '';

			//print $_POST['start-date'] . ' = ' . $from_date . '<br/>';
			//print $_POST['end-date'] . ' = ' . $to_date . '<br/>';
			//echo date( 'Y-m-d', $from_date );
			//die();

			$args = array(
				'post_type' => 'resident',
				'meta_key' => '_nhw_patrol',
				'meta_value' => 'yes',
				'fields' => 'ids',
				'posts_per_page' => -1,
			);
			$patrollers = new WP_Query( $args );
			$patroller_list_original = $patrollers->posts;
			shuffle( $patroller_list_original );
			$patroller_list = $patroller_list_original;
			shuffle( $patroller_list );

			ob_start();

			$date = $from_date;
			/*
			$slots = array();
			$patrollers_dates = array();
			for ( $slot = 0; $slot < $slot_counter; $slot++ ) {
				while ( $date <= $to_date ) {
					for ( $i = 0; $i < $per_slot; $i++ ) {
						if ( empty( $patroller_list ) ) {
							$patroller_list = $patroller_list_original;
							shuffle( $patroller_list );
						}
						$patroller = array_pop( $patroller_list );
						shuffle( $patroller_list );
						$patrollers_dates[ date( 'Y-m-d', $rest_date ) ][] = $patroller;
						$first_name = get_post_meta( $patroller, '_first_name', true );
						$last_name = get_post_meta( $patroller, '_last_name', true );
						if ( ( $i + 1 ) == $per_slot ) {
							$slots[ date( 'Y-m-d', $date ) ][ $slot ] .= $first_name . ' ' . $last_name;
						} else {
							$slots[ date( 'Y-m-d', $date ) ][ $slot ] .= $first_name . ' ' . $last_name . ' / ';
						}
					}
					$date = strtotime( '+1 day', $date );
				}
				$date = $from_date;
			}

			while ( $date <= $to_date ) {
				echo date( 'Y-m-d', $date ) . ';';
				foreach ( $slots[ date( 'Y-m-d', $date ) ] as $slot ) {
					echo $slot . ';';
				}
				$date = strtotime( '+1 day', $date );
				echo "\n";
			}
			*/

			$slots_counter=0;
			while ( $date <= $to_date ) {
				echo date( 'Y-m-d', $date ) . ';';
				while ( $slots_counter < $slot_counter ) {
					$slots_counter++;
					for ( $i = 0; $i < $per_slot; $i++ ) {
						if ( empty( $patroller_list ) ) {
							$patroller_list = $patroller_list_original;
						}
						$patroller = array_pop( $patroller_list );
						echo get_post_meta( $patroller, '_first_name', true );
						echo ' ';
						echo get_post_meta( $patroller, '_last_name', true );
						if ( ( $i + 1 ) == $per_slot ) {
							echo ';';
						} else {
							echo ' / ';
						}
					}

				}
				$date = strtotime( '+1 day', $date );
				$slots_counter = 0;
				echo "\n";
 			}

			header( "Content-type: text/csv" );
			header( "Content-Disposition: attachment; filename=fernpark-patrol-schedule-" .  date( 'Y-m-d', $from_date ) . '-' . date( 'Y-m-d', $to_date ) . ".csv" );
			header( "Pragma: no-cache" );
			header( "Expires: 0" );

			echo ob_get_clean();
			die();
		}
	}
endif;