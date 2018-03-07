<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'NWS_Adverts' ) ) :
	class NWS_Adverts {

		public function __construct() {
			add_action( 'init', array( $this, 'register_advert_post_type' ) );
			add_action( 'add_meta_boxes', array( $this, 'add_meta_box' ) );
			add_action( 'save_post', array( $this, 'save' ) );

			add_shortcode( 'nhw_bussiness_adverts_slider', array( $this, 'business_adverts_slider_shortcode' ) );
		}

		public function register_advert_post_type() {
			$labels = array(
				'name'                => __( 'Business Advertisements', 'nws' ),
				'singular_name'       => __( 'Business Advertisement', 'nws' ),
				'add_new'             => _x( 'Add New Business Advertisement', 'nws', 'nws' ),
				'add_new_item'        => __( 'Add New Business Advertisement', 'nws' ),
				'edit_item'           => __( 'Edit Business Advertisement', 'nws' ),
				'new_item'            => __( 'New Business Advertisement', 'nws' ),
				'view_item'           => __( 'View Business Advertisement', 'nws' ),
				'search_items'        => __( 'Search Business Advertisements', 'nws' ),
				'not_found'           => __( 'No Business Advertisements found', 'nws' ),
				'not_found_in_trash'  => __( 'No Business Advertisements found in Trash', 'nws' ),
				'parent_item_colon'   => __( 'Parent Business Advertisement Name:', 'nws' ),
				'menu_name'           => __( 'Business Advertisements', 'nws' ),
			);

			$args = array(
				'labels'               => $labels,
				'hierarchical'        => false,
				'description'         => 'Advertisements for businesses supporting the NHW',
				'taxonomies'          => array(),
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => 'nhw-system',
				'show_in_admin_bar'   => true,
				'menu_position'       => null,
				'menu_icon'           => 'dashicons-store',
				'show_in_nav_menus'   => true,
				'publicly_queryable'  => true,
				'exclude_from_search' => false,
				'has_archive'         => true,
				'query_var'           => true,
				'can_export'          => true,
				'rewrite'			  => array( 'slug' => 'businesses' ),
				'capability_type'     => 'post',
				'supports'            => array(
					'title',
					'thumbnail',
					'editor'
				)
			);

			register_post_type( 'business', $args );
		}

		public function add_meta_box() {
			add_meta_box(
				'nws-advert-info',
				__( 'Business Contact Details', 'nws' ),
				array( $this, 'render_info_meta_box_content' ),
				'business',
				'normal',
				'high'
			);
		}

		public function render_info_meta_box_content() {
			global $post;
			$post_id = $post->ID;

			$email   = get_post_meta( $post_id, '_email', true );
			$phone   = get_post_meta( $post_id, '_phone', true );
			$website = get_post_meta( $post_id, '_website', true );
			$address = get_post_meta( $post_id, '_address', true );

			ob_start();
			?>

			<table id="nws-business-info" class="form-table">
				<tr valign="top">
					<th scope="row"><label for="nws-business-address">Address</label></th>
					<td>
						<textarea name="nws-business-address" id="nws-business-address"><?php echo esc_attr( $address ); ?></textarea>
						<p class="description">Enter the address of the business.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-business-phone">Phone Number</th>
					<td>
						<input type="text" name="nws-business-phone" id="nws-business-phone" class="regular-text" value="<?php echo esc_attr( $phone ); ?>"/>
						<p class="description">Enter the contact number of the business.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-business-email">Email</th>
					<td>
						<input type="text" name="nws-business-email" id="nws-business-email" class="regular-text" value="<?php echo esc_attr( $email ); ?>"/>
						<p class="description">Enter the email address of the business.</p>
					</td>
				</tr>
				<tr valign="top">
					<th scope="row"><label for="nws-business-website">Website</th>
					<td>
						<input type="text" name="nws-business-website" id="nws-business-website" class="regular-text" value="<?php echo esc_attr( $website ); ?>"/>
						<p class="description">Enter the url of the business website.</p>
					</td>
				</tr>
			</table>
			<?php
			wp_nonce_field( 'nws-business-info', 'nws-business-info-nonce' );
			echo ob_get_clean();
		}

		public function save( $post_id ) {
			// Check if our nonce is set.
			if ( ! isset( $_POST['nws-business-info-nonce'] ) ) {
				return $post_id;
			}

			$nonce = $_POST['nws-business-info-nonce'];

			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $nonce, 'nws-business-info' ) ) {
				return $post_id;
			}

			// If this is an autosave, our form has not been submitted,
			// so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
			}

			// Check the user's permissions.
			if ( 'business' == $_POST['post_type'] && ! current_user_can( 'edit_page', $post_id ) ) {
				return $post_id;
			}

			$_address = ( $_POST['nws-business-address'] ) ? sanitize_text_field( $_POST['nws-business-address'] ) : '';
			$_phone = ( $_POST['nws-business-phone'] ) ? sanitize_text_field( str_replace( '-', '', str_replace( ' ', '', $_POST['nws-business-phone'] ) ) ) : '';
			$_email = ( $_POST['nws-business-email'] ) ? sanitize_email( $_POST['nws-business-email'] ) : '';
			$_website = ( $_POST['nws-business-website'] ) ? sanitize_text_field( esc_url( $_POST['nws-business-website'] ) ) : '';

			update_post_meta( $post_id, '_address', $_address );
			update_post_meta( $post_id, '_phone', $_phone );
			update_post_meta( $post_id, '_email', $_email );
			update_post_meta( $post_id, '_website', $_website );
		}

		public function business_adverts_slider_shortcode( $atts ) {
			$args = array(
				'post_type' 		=> 'business',
				'posts_per_page' 	=> -1,
				'orderby'        	=> 'rand',
			);
			$business_query = new WP_Query( $args );
			$slides_li = '';
			while ( $business_query->have_posts() ) {
				$business_query->the_post();
				$slides_li .= '<li><a href="' . get_the_permalink() . '"><img src="' . wp_get_attachment_url( get_post_thumbnail_id() ) . '"/><a></li>';
			}
			ob_start();
			?>
			<div class="flexslider">
				<ul class="slides">
					<?php echo $slides_li; ?>
  				</ul>
			</div>
			<script>
				jQuery(window).load(function() {
    				jQuery('.flexslider').flexslider({
    					animation: "slide",
						animationLoop: true,
						itemWidth: 260,
						itemMargin: 15
    				});
  				});
			</script>
			<?php
			return ob_get_clean();
		}
	}
endif;