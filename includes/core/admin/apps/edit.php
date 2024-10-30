<?php

namespace WPV_ME\Core\Admin;

use WPV_ME\Core\Admin;
use WPV_ME\Core\Permissions;
use WPV_ME\Core\Utils;

class Edit extends Admin {
	private static $instance;

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// actions
		add_action( 'current_screen', [ $this, 'instantiate' ] );

		// ajax
		add_action( 'wp_ajax_wpvme_create_map', [ $this, 'create_map' ], 10 );
	}

	public function instantiate( $screen ) {

		if ( ! $screen ) {
			return;
		}

		if ( $screen->id === 'edit-me_maps' ) {
			// actions
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 20 );

			$this->render_add_new_map_page();
		}
	}

	public function render_add_new_map_page() {
		?>
			<div class="wme-add-new-map-popup-wrapper" style="display: none">
				<div class="wme-add-new-map-popup-content">
					<header class="wme-add-new-map-popup-header">
						<h2>Select a map engine</h2>
						<span class="wme-anm-close-btn"><svg width="24" height="24" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" role="img" aria-hidden="true" focusable="false"><path d="M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"></path></svg></span>
					</header>
					<div class="wme-add-new-map-popup-body">
						<form>
							<div class="wme-anm-error"></div>
							<div class="wme-anm-name-wrapper">
								<input class="wme-anm-name" required type="text" placeholder="Enter a map name" />
							</div>
							<div class="wme-anm-engine-selector-wrapper">
								<div class="wme-anm-engine-selector" data-engine="google">
									<div></div>
									<span class="wme-anm-label">Google Maps</span>
								</div>
								<div class="wme-anm-engine-selector selected" data-engine="os">
									<div></div>
									<span class="wme-anm-label">Open Street Maps</span>
								</div>
							</div>
							<div class="wme-google-api-notice hide">Google Maps API key is 			missing. Please <a href="<?php echo admin_url() ?>edit.php?			post_type=me_maps&page=map-engine-settings">add api key</a> to 		use this option or proceed with Open Street Maps
							</div>
							<div class="wme-anm-btn-wrapper">
								<button type='submit' class="wme-anm-btn">Create map</button>
							</div>
						</form>
					</div>
				</div>
			</div>
		<?php
	}

	public function admin_scripts() {
		$handle = 'me-admin-edit-page-script';
		wp_register_script(
			$handle,
			WPVME_URL . 'assets/js/admin/edit-page.js',
			[],
			[],
			true
		);

		$wme_variable = Utils::get_default_global_variables();
		$wme_variable = apply_filters( 'wme_edit_page_variable', $wme_variable );

		wp_localize_script(
			$handle,
			'wpvme',
			$wme_variable
		);

		wp_enqueue_script( $handle );

		wp_enqueue_style( 'me-admin-edit-page-style', WPVME_URL . 'assets/css/admin/edit-page.css', [], WPVME_VERSION );
	}

	public function create_map() {

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$name = stripslashes( sanitize_text_field( $_POST['map_name'] ) );

		$engine = stripslashes( sanitize_text_field( $_POST['engine'] ) );

		if ( ! $name ) {
			wp_send_json_error( __( 'Map name is required', 'map-engine' ) );
		}

		if ( ! $engine ) {
			wp_send_json_error( __( 'Map engine is required', 'map-engine' ) );
		}

		$post_id = wp_insert_post(
			[
				'post_type'   => 'me_maps',
				'post_status' => 'draft',
				'post_title'  => $name,
			]
		);

		if ( is_wp_error( $post_id ) ) {
			wp_send_json_error( $post_id->get_error_message() );
		}

		update_post_meta( $post_id, 'map_engine', $engine );

		wp_send_json_success(
			[
				'post_id'      => $post_id,
				'redirect_url' => get_edit_post_link( $post_id, '' ),
			]
		);
	}
}
