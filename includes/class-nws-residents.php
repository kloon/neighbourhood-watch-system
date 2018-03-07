<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_Residents' ) ) :

	class NWS_Residents {

		protected static $_instance = null;

		public $streets = array(
			'Janie Str',
			'Ferndale Str',
			'Kinkel Rd',
			'Sandler Str',
			'Loubser Str',
			'Kort Str',
			'Endive Str',
			'Fennel Cl',
			'Olympus Rd',
			'Athena Str',
			'Demeter Str',
			'De Ville Cl',
			'Eldorado Cir',
			'HO De Villiers Str',
			'John Gainsford Str',
			'Albie De Waal Str',
			'Eben Olivier Str',
			'Jannie Engelbrecht Str',
            'Wit Kareeboom Str',
            'Rooi Kareeboom Str',
			'Roslyn Str',
			'Nina Str',
			'Dana Str',
			'Jeanette Str',
			'Lee Str',
			'Tiny Naude Str',
			'Tiny Neetling Str',
			'Gert Kotze Str',
		);

		public $complexes = array(
			'Kleingeluk 1',
			'Kleingeluk 2',
			'Kleingeluk 3',
			'Olympus Complex',
			'Athena Complex',
			'Apollo Complex',
		);

		public $security_companies = array(
			'ADT',
			'Bassett',
			'SJC',
			'Titanium Securitas',
			'Bolt',
			'Roman',
			'Chub',
			'Capital',
		);

		/**
		 * Constructor
		 * @return void
		 */
		public function __construct() {
			add_action( 'init', array( $this, 'register_resident_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'do_meta_boxes', array( $this, 'change_image_box' ) );
			add_action( 'save_post', array( $this, 'save' ) );
			add_filter( 'manage_resident_posts_columns', array( $this, 'set_admin_columns' ) );
			add_action( 'manage_resident_posts_custom_column' , array( $this, 'admin_columns' ), 10, 2 );
			add_action( 'wp_loaded', array( $this, 'check_handle_imports' ) );
			add_action( 'restrict_manage_posts', array( $this, 'restrict_manage_posts' ) );
			add_filter( 'parse_query', array( $this, 'filters_query' ) );
		} // End __construct()

		/**
		* Registers a new post type
		* @uses $wp_post_types Inserts new post type object into the list
		*
		* @param string  Post type key, must not exceed 20 characters
		* @param array|string  See optional args description above.
		* @return object|WP_Error the registered post type object, or an error object
		*/
		public function register_resident_post_type() {

			$labels = array(
				'name'                => __( 'Residents', 'nws' ),
				'singular_name'       => __( 'Resident', 'nws' ),
				'add_new'             => _x( 'Add New Resident', 'nws', 'nws' ),
				'add_new_item'        => __( 'Add New Resident', 'nws' ),
				'edit_item'           => __( 'Edit Resident', 'nws' ),
				'new_item'            => __( 'New Resident', 'nws' ),
				'view_item'           => __( 'View Resident', 'nws' ),
				'search_items'        => __( 'Search Residents', 'nws' ),
				'not_found'           => __( 'No Residents found', 'nws' ),
				'not_found_in_trash'  => __( 'No Residents found in Trash', 'nws' ),
				'parent_item_colon'   => __( 'Parent Resident Name:', 'nws' ),
				'menu_name'           => __( 'Residents', 'nws' ),
			);

			$args = array(
				'labels'               => $labels,
				'hierarchical'        => false,
				'description'         => 'Residents in the Neighbourhood Watch Area',
				'taxonomies'          => array(),
				'public'              => false,
				'show_ui'             => true,
				'show_in_menu'        => 'nhw-system',
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'menu_icon'           => 'dashicons-groups',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'             => true,
				'capability_type'     => 'post',
				'supports'            => array(
					'thumbnail',
					'editor'
				)
			);

			register_post_type( 'resident', $args );
		} // End register_member_post_type()

		/**
		 * Add the meta box for the resident information
		 * @return void
		 */
		public function add_meta_box() {
			add_meta_box(
				'nws-resident-info',
				__( 'Resident Information', 'nws' ),
				array( $this, 'render_info_meta_box_content' ),
				'resident',
				'normal',
				'high'
			);

			add_meta_box(
				'nws-resident-nhw-info',
				__( 'NHW Involvement', 'nws' ),
				array( $this, 'render_nhw_meta_box_content' ),
				'resident',
				'side',
				'low'
			);
		} // End add_meta_box()

		/**
		 * Render the Resident info meta box fields and content
		 * @return void
		 */
		public function render_info_meta_box_content() {
			global $post;
			$post_id = $post->ID;

			$first_name 	= get_post_meta( $post_id, '_first_name', true );
			$last_name 		= get_post_meta( $post_id, '_last_name', true );
			$id_number		= get_post_meta( $post_id, '_id_number', true );
			$street_nr 		= get_post_meta( $post_id, '_street_nr', true );
			$complex_name	= get_post_meta( $post_id, '_complex', true );
			$street_name	= get_post_meta( $post_id, '_street', true );
			$email 			= get_post_meta( $post_id, '_email', true );
			$email2 		= get_post_meta( $post_id, '_email2', true );
			$home_phone 	= get_post_meta( $post_id, '_home_phone', true );
			$cell_phone 	= get_post_meta( $post_id, '_cell_phone', true );
			$cell_phone2 	= get_post_meta( $post_id, '_cell_phone2', true );
			$business		= get_post_meta( $post_id, '_business', true );
			$resident_type	= get_post_meta( $post_id, '_resident_type', true );
			$security		= get_post_meta( $post_id, '_security', true );

			ob_start();
			?>

			<table id="nws-resident-info" class="form-table">
				<tr valign="top">
					<th scope="row"><label for="nws-resident-first-name">First Name</label></th>
					<td>
						<input name="nws-resident-first-name" type="text" id="nws-resident-first-name" class="regular-text" value="<?php echo esc_attr( $first_name ); ?>">
						<p class="description">Enter the resident's First Name.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-last-name">Last Name</label></th>
					<td>
						<input name="nws-resident-last-name" type="text" id="nws-resident-last-name" class="regular-text" value="<?php echo esc_attr( $last_name ); ?>">
						<p class="description">Enter the resident's Last Name.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-id-number">ID Number</label></th>
					<td>
						<input name="nws-resident-id-number" type="number" id="nws-resident-id-number" class="regular-text" value="<?php echo esc_attr( $id_number ); ?>">
						<p class="description">Enter the resident's ID Number.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-str-nr">Street/Complex Nr</label></th>
					<td>
						<input name="nws-resident-str-nr" type="number" id="nws-resident-str-nr" class="small-text" value="<?php echo esc_attr( $street_nr ); ?>">
						<p class="description">Enter the resident's street or complex number.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-complex">Complex</label></th>
					<td>
						<select name="nws-resident-complex">
							<?php
								echo '<option value="" '. selected( '', $complex_name, false ) .'>Not Applicable</option>';
								sort( $this->complexes );
								foreach ( $this->complexes as $complex ) {
									echo '<option value="' . esc_attr( $complex ) . '" ' . selected( $complex, $complex_name, false ) . '>' . sanitize_text_field( $complex ) . '</option>';
								}
							?>
						</select>
						<p class="description">Select the resident's complex name if they live in a complex.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-street">Street</label></th>
					<td>
						<select name="nws-resident-street">
							<option value="0">Please select</option>
							<?php
								sort( $this->streets );
								foreach ( $this->streets as $street ) {
									echo '<option value="' . esc_attr( $street ) . '" ' . selected( $street, $street_name, false ) . '>' . sanitize_text_field( $street ) . '</option>';
								}
							?>
						</select>
						<p class="description">Select the resident's street or complex.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-email">Email Address</label></th>
					<td>
						<input name="nws-resident-email" type="email" id="nws-resident-email" class="regular-text" value="<?php echo esc_attr( $email ); ?>">
						<p class="description">Enter the resident's email address.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-email2">Additional Email Address</label></th>
					<td>
						<input name="nws-resident-email2" type="email" id="nws-resident-email2" class="regular-text" value="<?php echo esc_attr( $email2 ); ?>">
						<p class="description">Enter the resident's additional email address.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-home-phone">Home Phone</label></th>
					<td>
						<input name="nws-resident-home-phone" type="tel" id="nws-resident-home-phone" class="regular-text" value="<?php echo esc_attr( $home_phone ); ?>">
						<p class="description">Enter the resident's home telephone number.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-cell">Cellphone</label></th>
					<td>
						<input name="nws-resident-cell" type="tel" id="nws-resident-cell" class="regular-text" value="<?php echo esc_attr( $cell_phone ); ?>">
						<p class="description">Enter the resident's cellphone number.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-cell2">Additional Cellphone</label></th>
					<td>
						<input name="nws-resident-cell2" type="tel" id="nws-resident-cell2" class="regular-text" value="<?php echo esc_attr( $cell_phone2 ); ?>">
						<p class="description">Enter the resident's additional cellphone number.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-business">Business?</label></th>
					<td>
						<select name="nws-resident-business">
							<option value="no" <?php selected( 'no', $business, true ); ?>>No</option>
							<option value="yes" <?php selected( 'yes', $business, true ); ?>>Yes</option>
							<option value="both" <?php selected( 'both', $business, true ); ?>>Both</option>
						</select>
						<p class="description">Is this a business?.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-type">Resident Type?</label></th>
					<td>
						<select name="nws-resident-type">
							<option value="owner" <?php selected( 'owner', $resident_type, true ); ?>>Owner</option>
							<option value="tenant" <?php selected( 'tenant', $resident_type, true ); ?>>Tenant</option>
						</select>
						<p class="description">Is this the owner of the property or a tenant?.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-security">Alarm/Security Company?</label></th>
					<td>
						<select name="nws-resident-security">
							<option value="">Please select</option>
							<option value="na" <?php selected( 'na', $security, true ); ?>>Not Applicable</option>
							<?php
								sort( $this->security_companies );
								foreach ( $this->security_companies as $name ) {
									echo '<option value="' . esc_attr( $name ) . '" ' . selected( $name, $security, false ) . '>' . esc_attr( $name ) . '</option>';
								}
							?>
							<option value="other" <?php selected( 'other', $security, true ); ?>>Other</option>
						</select>
						<p class="description">Security / Armed Response Company the resident make use of?.</p>
					</td>
				</tr>
			</table>

			<?php
			wp_nonce_field( 'nws-member-info', 'nws-member-info-nonce' );
			echo ob_get_clean();
		} // End render_info_meta_box_content

		/**
		 * Render sidebar metabox with details about nhw membership
		 * @return void
		 */
		public function render_nhw_meta_box_content() {
			global $post;
			$post_id = $post->ID;

			$is_nhw_member 	= get_post_meta( $post_id, '_nhw_member', true );
			$is_patroller 	= get_post_meta( $post_id, '_nhw_patrol', true );
			$is_zello		= get_post_meta( $post_id, '_nhw_zello', true );
			$is_whatsapp 	= get_post_meta( $post_id, '_nhw_whatsapp', true );
			$is_facebook 	= get_post_meta( $post_id, '_nhw_facebook', true );

			ob_start();
			?>

			<table id="nws-resident-nhw-info" class="form-table">
				<tr valign="top">
					<th scope="row"><label for="nws-resident-nhw-member">NHW Member?</label></th>
					<td>
						<input name="nws-resident-nhw-member" type="checkbox" id="nws-resident-nhw-member" value="yes" <?php checked( 'yes', $is_nhw_member, true ); ?>>
						<p class="description">Is this resident a Neighbourhood Watch member?</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-nhw-patrol">Patroller?</label></th>
					<td>
						<input name="nws-resident-nhw-patrol" type="checkbox" id="nws-resident-nhw-patrol" value="yes" <?php checked( 'yes', $is_patroller, true ); ?>>
						<p class="description">Do this resident do Neighbourhood Watch patrols?</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-nhw-zello">Zello?</label></th>
					<td>
						<input name="nws-resident-nhw-zello" type="checkbox" id="nws-resident-nhw-zello" value="yes" <?php checked( 'yes', $is_zello, true ); ?>>
						<p class="description">Is this resident part of the Zello channel?</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-nhw-whatsapp">WhatsApp?</label></th>
					<td>
						<input name="nws-resident-nhw-whatsapp" type="checkbox" id="nws-resident-nhw-whatsapp" value="yes" <?php checked( 'yes', $is_whatsapp, true ); ?>>
						<p class="description">Is this resident part of the WhatsApp group?</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-resident-nhw-facebook">Facebook Group?</label></th>
					<td>
						<input name="nws-resident-nhw-facebook" type="checkbox" id="nws-resident-nhw-facebook" value="yes" <?php checked( 'yes', $is_facebook, true ); ?>>
						<p class="description">Is this resident part of the Facebook group?</p>
					</td>
				</tr>
			</table>

			<?php
			echo ob_get_clean();
		} // End render_nhw_meta_box_content()

		/**
		 * Remove features image meta box and replace it with another under a new name
		 * @return void
		 */
		public function change_image_box() {
    		remove_meta_box( 'postimagediv', 'resident', 'side' );
    		add_meta_box( 'postimagediv', __( 'Resident Headshot' ), 'post_thumbnail_meta_box', 'resident', 'side' );
		} // End change_image_box()

		/**
		 * Save the meta data
		 * @param  int $post_id
		 * @return void
		 */
		public function save( $post_id ) {
			// Check if our nonce is set.
			if ( ! isset( $_POST['nws-member-info-nonce'] ) ) {
				return $post_id;
			}

			$nonce = $_POST['nws-member-info-nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'nws-member-info' ) ) {
				return $post_id;
			}

			// If this is an autosave, our form has not been submitted,
			// so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Check the user's permissions.
			if ( 'resident' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

			/* OK, its safe for us to save the data now. */
			$_first_name 	= ( $_POST['nws-resident-first-name'] ) ? sanitize_text_field( $_POST['nws-resident-first-name'] ) : '';
			$_last_name 	= ( $_POST['nws-resident-last-name'] ) ? sanitize_text_field( $_POST['nws-resident-last-name'] ) : '';
			$_id_number		= ( $_POST['nws-resident-id-number'] ) ? sanitize_text_field( $_POST['nws-resident-id-number'] ) : '';
			$_street_nr 	= ( $_POST['nws-resident-str-nr'] ) ? sanitize_text_field( $_POST['nws-resident-str-nr'] ) : '';
			$_complex		= ( $_POST['nws-resident-complex'] ) ? sanitize_text_field( $_POST['nws-resident-complex'] ) : '';
			$_street		= ( $_POST['nws-resident-street'] ) ? sanitize_text_field( $_POST['nws-resident-street'] ) : '';
			$_email 		= ( $_POST['nws-resident-email'] ) ? sanitize_email( $_POST['nws-resident-email'] ) : '';
			$_email2 		= ( $_POST['nws-resident-email2'] ) ? sanitize_email( $_POST['nws-resident-email2'] ) : '';
			$_home_phone 	= ( $_POST['nws-resident-home-phone'] ) ? sanitize_text_field( str_replace( '-', '', str_replace( ' ', '', $_POST['nws-resident-home-phone'] ) ) ) : '';
			$_cell_phone 	= ( $_POST['nws-resident-cell'] ) ? sanitize_text_field( str_replace( '-', '', str_replace( ' ', '', $_POST['nws-resident-cell'] ) ) ) : '';
			$_cell_phone2 	= ( $_POST['nws-resident-cell2'] ) ? sanitize_text_field( str_replace( '-', '', str_replace( ' ', '', $_POST['nws-resident-cell2'] ) ) ) : '';
			$_business		= ( $_POST['nws-resident-business'] ) ? sanitize_text_field( $_POST['nws-resident-business'] ) : 'no';
			$_resident_type	= ( $_POST['nws-resident-type'] ) ? sanitize_text_field( $_POST['nws-resident-type'] ) : 'resident';
			$_security		= ( $_POST['nws-resident-security'] ) ? sanitize_text_field( $_POST['nws-resident-security'] ) : 'na';
			$_nhw_member 	= ( $_POST['nws-resident-nhw-member'] ) ? sanitize_text_field( $_POST['nws-resident-nhw-member'] ) : 'no';
			$_nhw_patrol 	= ( $_POST['nws-resident-nhw-patrol'] ) ? sanitize_text_field( $_POST['nws-resident-nhw-patrol'] ) : 'no';
			$_nhw_zello 	= ( $_POST['nws-resident-nhw-zello'] ) ? sanitize_text_field( $_POST['nws-resident-nhw-zello'] ) : 'no';
			$_nhw_whatsapp 	= ( $_POST['nws-resident-nhw-whatsapp'] ) ? sanitize_text_field( $_POST['nws-resident-nhw-whatsapp'] ) : 'no';
			$_nhw_facebook 	= ( $_POST['nws-resident-nhw-facebook'] ) ? sanitize_text_field( $_POST['nws-resident-nhw-facebook'] ) : 'no';

			$merge_vars = array(
				'email' => $_email,
				'first_name' => $_first_name,
				'last_name' => $_last_name,
				'street' => $_street,
				'nhw_member' => ucwords( $_nhw_member ),
				'nhw_patroller' => ucwords( $_nhw_patrol ),
				'whatsapp' => ucwords( $_nhw_whatsapp ),
				'zello' => ucwords( $_nhw_zello ),
				'facebook' => ucwords( $_nhw_facebook ),
				'business' => ucwords( $_business ),
			);
			$subscribed = nhw_subscribe_mailchimp( $merge_vars );

			// If a secondary email is set, subsribe that as well with the same details.
			if ( ! empty( $_email2 ) ) {
				$merge_vars['email'] = $email2;
				$subscribed = nhw_subscribe_mailchimp( $merge_vars );
			}

			update_post_meta( $post_id, '_first_name', $_first_name );
			update_post_meta( $post_id, '_last_name', $_last_name );
			update_post_meta( $post_id, '_id_number', $_id_number );
			update_post_meta( $post_id, '_street_nr', $_street_nr );
			update_post_meta( $post_id, '_complex', $_complex );
			update_post_meta( $post_id, '_street', $_street );
			update_post_meta( $post_id, '_email', $_email );
			update_post_meta( $post_id, '_email2', $_email2 );
			update_post_meta( $post_id, '_home_phone', $_home_phone );
			update_post_meta( $post_id, '_cell_phone', $_cell_phone );
			update_post_meta( $post_id, '_cell_phone2', $_cell_phone2 );
			update_post_meta( $post_id, '_business', $_business );
			update_post_meta( $post_id, '_resident_type', $_resident_type );
			update_post_meta( $post_id, '_security', $_security );
			update_post_meta( $post_id, '_nhw_member', $_nhw_member );
			update_post_meta( $post_id, '_nhw_patrol', $_nhw_patrol );
			update_post_meta( $post_id, '_nhw_zello', $_nhw_zello );
			update_post_meta( $post_id, '_nhw_whatsapp', $_nhw_whatsapp );
			update_post_meta( $post_id, '_nhw_facebook', $_nhw_facebook );

			// Set the name/surname as post title and name
			// Be sure to first unhook this function to avoid an infinite loop
			remove_action( 'save_post', array( $this, 'save' ) );
			$resident_post = array(
				'ID'          	=> $post_id,
				'post_title' 	=> $_first_name . ' ' . $_last_name,
				'post_name' 	=> sanitize_title( $_first_name . ' ' . $_last_name ),
			);
			wp_update_post( $resident_post );
			add_action( 'save_post', array( $this, 'save' ) );
		}

		/**
		 * Get single instance of this class
		 * @return object NWS_Members
		 */
		public static function get_instance() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		} // End get_instance()

		/**
		 * Define the columns to display on the admin screen
		 * @param array
		 */
		public function set_admin_columns( $columns ) {
			return array(
				'cb' => '<input type="checkbox" />',
				//'thumb' => __( 'Image' ),
				'name' => __( 'Name' ),
				'surname' => __( 'Surname' ),
				'street' => __( 'Address' ),
				'patrols' => __( 'Patrols' ),
				'zello' => __( 'Zello' ),
				'whatsapp' => __( 'WhatsApp' ),
				'facebook' => __( 'Facebook' ),
			);
		} // End set_admin_columns()

		/**
		 * Show the custom fields on the admin edit.php page of the post type
		 * @param  string $column
		 * @param  int $post_id
		 * @return void
		 */
		public function admin_columns( $column, $post_id ) {
			global $post;
			switch ( $column ) {
				case 'thumb' :
					echo '<a href="' . get_edit_post_link( $post->ID ) . '">' . the_post_thumbnail( 'thumbnail' ) . '</a>';
				break;
				case 'name' :
					$is_nhw_member = get_post_meta( $post_id, '_nhw_member', true );
					if ( 'yes' == $is_nhw_member ) {
						echo "&nbsp;";
						echo '<span class="dashicons dashicons-visibility" alt="NHW Member"></span>';
					}
					echo '<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . get_post_meta( $post_id, '_first_name', true ) . '</a>';
					_post_states( $post );
				break;
				case 'surname' :
					echo '<a href="' . admin_url( 'post.php?post=' . $post_id . '&action=edit' ) . '">' . get_post_meta( $post_id, '_last_name', true ) . '</a>';
				break;
				case 'street' :
					$street_field = '';
					$street_field .= get_post_meta( $post_id, '_street_nr', true );
					$complex = get_post_meta( $post_id, '_complex', true );
					if ( ! empty( $complex ) ) {
						$street_field .= ' ' . $complex . ',';
					}
					$street_field .= ' ' . get_post_meta( $post_id, '_street', true );
					echo $street_field;
				break;
				case 'patrols' :
					echo strtoupper(  get_post_meta( $post_id, '_nhw_patrol', true ) );
				break;
				case 'zello' :
					echo strtoupper(  get_post_meta( $post_id, '_nhw_zello', true ) );
				break;
				case 'whatsapp' :
					echo strtoupper(  get_post_meta( $post_id, '_nhw_whatsapp', true ) );
				break;
				case 'facebook' :
					echo strtoupper(  get_post_meta( $post_id, '_nhw_facebook', true ) );
				break;
				default :
					echo '&nbsp;';
				break;
			}
		} // End admin_columns()

		/**
		 * Function for handling a import from a custom table, this is only used once
		 * @return void
		 */
		public function check_handle_imports() {
			if ( ! isset( $_GET['nhw-member-import'] ) ) {
				return;
			}

			if ( 'member-import' == $_GET['nhw-member-import'] ) {
				global $wpdb;
				$results = $wpdb->get_results( 'SELECT * FROM member_import WHERE 1 = 1', OBJECT );
				foreach ( $results as $result ) {
					if ( ! isset( $result->member_data ) ) {
						echo 'ERROR - Missing Member Data: ';
						print_r( $result );
						echo '<br/>';
						continue;
					}

					$data = json_decode( $result->member_data );
					if ( ! $data ) {
						echo 'ERROR - JSON Formatting Invalid: ';
						print_r( $result->member_data );
						echo '<br/>';
						continue;
					}

					// Lets create the resident
					$args = array(
						'post_title' => $data->_first_name . ' ' . $data->_last_name,
						'post_status' => 'publish',
						'post_type' => 'resident',
						'post_name' => sanitize_title( $data->_first_name . ' ' . $data->_last_name ),
					);
					$resident_id = wp_insert_post( $args );

					if ( 0 == $resident_id ) {
						echo 'ERROR - Failed to create post: ';
						print_r( $data );
						echo '<br/>';
						continue;
					}

					foreach ( $data as $key => $value ) {
						add_post_meta( $resident_id, $key, $value );
					}
					echo 'Done inserting: #' . $resident_id . ' - ' . $data->_first_name . ' ' . $data->_last_name . '<br/>';
				}
			}

			if ( 'mail-update' ==  $_GET['nhw-member-import'] ) {
				$args = array(
					'post_type' => 'resident',
					'posts_per_page' => -1,
					'post_status' => 'any',
				);
				$query = new WP_Query( $args );
				if ( $query->have_posts() ) :
					while ( $query->have_posts() ) :
						$query->the_post();
						$post_id = get_the_id();
						$first_name 	= get_post_meta( $post_id, '_first_name', true );
						$last_name 		= get_post_meta( $post_id, '_last_name', true );
						$street_name	= get_post_meta( $post_id, '_street', true );
						$email 			= get_post_meta( $post_id, '_email', true );
						$email2 		= get_post_meta( $post_id, '_email2', true );
						$business		= get_post_meta( $post_id, '_business', true );
						$is_nhw_member 	= get_post_meta( $post_id, '_nhw_member', true );
						$is_patroller 	= get_post_meta( $post_id, '_nhw_patrol', true );
						$is_zello		= get_post_meta( $post_id, '_nhw_zello', true );
						$is_whatsapp 	= get_post_meta( $post_id, '_nhw_whatsapp', true );
						$is_facebook 	= get_post_meta( $post_id, '_nhw_facebook', true );

						$merge_vars = array(
							'email' => $email,
							'first_name' => $first_name,
							'last_name' => $last_name,
							'street' => $street_name,
							'nhw_member' => ucwords( $is_nhw_member ),
							'nhw_patroller' => ucwords( $is_patroller ),
							'whatsapp' => ucwords( $is_whatsapp ),
							'zello' => ucwords( $is_zello ),
							'facebook' => ucwords( $is_facebook ),
							'business' => ucwords( $business ),
						);
						$subscribed = nhw_subscribe_mailchimp( $merge_vars );
						if ( $subscribed ) {
							echo $email . ' Subscribed: ' . json_encode( $merge_vars ) . '<br/>' . "\n";
						}

						// If a secondary email is set, subsribe that as well with the same details.
						if ( ! empty( $email2 ) ) {
							$merge_vars['email'] = $email2;
							$subscribed = nhw_subscribe_mailchimp( $merge_vars );
							if ( $subscribed ) {
								echo $email2 . ' Subscribed: ' . json_encode( $merge_vars ) . '<br/>' . "\n";
							}
						}
					endwhile;
					wp_reset_postdata();
				endif;
			}
			die();
		} // End check_handle_import()

		/**
		 * Add filter options to the admin page for additional search options
		 * @return void
		 */
		public function restrict_manage_posts() {
			global $typenow;

			if ( 'resident' == $typenow ) {
				$street_filter = isset( $_GET['street'] ) ? $_GET['street'] : '';
				$member_filter = isset( $_GET['member_status'] ) ? $_GET['member_status'] : '';
				sort( $this->streets );
			?>
				<select name="member_status">
					<option value=""><?php _e( 'All residents' ); ?>
					<option value="yes" <?php selected( $member_filter, 'yes', true );?>><?php _e( 'NHW Members' ); ?>
					<option value="no" <?php selected( $member_filter, 'no', true );?>><?php _e( 'Non NHW Members' ); ?>
				</select>
				<select name="street">
					<option value=""><?php _e( 'Show all streets' ); ?></option>
					<?php
						foreach ( $this->streets as $street ) {
							echo '<option value="' . $street . '" ' . selected( $street_filter, $street, false ) . '>' . $street . '</option>';
						}
					?>
				</select>
			<?php
			}
		} // End restrict_manage_posts()

		/**
		 * Handle the new filter dropdowns to return the correct data
		 * @param  object $query
		 * @return object
		 */
		public function filters_query( $query ) {
			global $typenow;

			if ( 'resident' == $typenow ) {
				$match = false;
				if ( isset( $_GET['street'] ) && ! empty( $_GET['street'] ) ) {
					$match = true;
					$query->query_vars['meta_query'] = array(
						array(
							'key'   => '_street',
							'value' => sanitize_text_field( $_GET['street'] ),
							'compare' => '='
						)
					);
				}

				if ( isset( $_GET['member_status'] ) && ! empty( $_GET['member_status'] ) ) {
					$meta_query = array(
						'key'   => '_nhw_member',
						'value' => sanitize_text_field( $_GET['member_status'] ),
						'compare' => '='
					);
					if ( $match ) {
						$query->query_vars['meta_query'][] = $meta_query;
					} else {
						$query->query_vars['meta_query'] = array(
							$meta_query
						);
					}
				}
			}
			return $query;
		} // End filters_query()
	}
endif;