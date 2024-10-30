<?php

namespace WPV_ME\Core;

use WPV_ME\Core\Admin\Builder;
use WPV_ME\Core\Admin\Edit;
use WPV_ME\Core\Admin\Settings;

class Admin {
	private static $instance;

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// actions
		add_action( 'admin_menu', [ $this, 'on_admin_menu' ] );
		add_action( 'wp_loaded', [ $this, 'mount_screens' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ] );
		add_action( 'in_admin_header', [ $this, 'top_bar' ] );

		// filters
		add_filter( 'screen_options_show_screen', [ $this, 'hide_screen_options' ] );
		// Remove notices on Map Engine Pages
		add_action( 'admin_print_scripts', [ $this, 'me_remove_notices' ] ); 
		// ajax
		add_action( 'wp_ajax_wpvme_set_by_meta_key', [ $this, 'set_by_meta_key' ], 10 );

		// add shortcode column to map list

		add_filter( 'manage_me_maps_posts_columns', [ $this, 'add_map_list_column' ] );
		add_action( 'manage_me_maps_posts_custom_column', [ $this, 'add_map_list_column_data' ], 10, 2 );
	}

	public function hide_screen_options() {

		$screens = get_current_screen();

		if ( property_exists( $screens, 'id' ) && in_array( $screens->id, Utils::get_screens(), true ) ) {
			return false;
		}

		return true;
	}


	public function on_admin_menu() {
		$this->remove_map_meta_boxes();
		$this->add_map_menus();
	}

	public function mount_screens() {
		new Edit();
		new Settings();
		new Builder();
	}

	private function is_json( $string ) {
		json_decode( $string );
		return ( json_last_error() === JSON_ERROR_NONE );
	}



	public function set_by_meta_key() {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}
		Permissions::before_running_ajax();

		$meta_key   = sanitize_text_field( $_POST['meta_key'] );
		$meta_value = stripslashes( sanitize_text_field( $_POST['meta_value'] ) );

		if ( $this->is_json( $meta_value ) ) {
			$meta_value = json_decode( $meta_value );
		}

		$updated = update_post_meta( sanitize_text_field( $_POST['post_id'] ), $meta_key, $meta_value );

		if ( $updated ) {
			wp_send_json_success( [ 'message' => 'Updated' ] );
		} else {
			wp_send_json_error( [ 'message' => 'Failed to update ' . $meta_key . 'key value' ] );
		}
	}

	public function admin_scripts() {
		wp_enqueue_style( 'me-admin-style', WPVME_URL . 'assets/css/admin/admin.css', [], WPVME_VERSION );
	}

	public function add_map_menus() {

		add_menu_page(
			__( 'Maps', 'map-engine' ),
			'Maps',
			'manage_options',
			'edit.php?post_type=me_maps',
			'',
			WPVME_URL . 'assets/me-mini.svg',
			50
		);

		add_submenu_page(
			'edit.php?post_type=me_maps',
			__( 'Settings', 'map-engine' ),
			__( 'Settings', 'map-engine' ),
			'manage_options',
			'map-engine-settings',
			[ $this, 'render_settings_page' ]
		);
	}

	public function render_settings_page() {        ?>
		<div id="wme-settings-screen">
			loading...
		</div>
		<?php
	}

	public function remove_map_meta_boxes() {
		remove_meta_box( 'submitdiv', 'me_maps', 'side' );
	}

	public function top_bar() {
		$screens = get_current_screen();

		if ( property_exists( $screens, 'id' ) && in_array( $screens->id, Utils::get_screens( [ 'me_maps' ] ), true ) ) {
			?>
				<div class="me-admin-topbar">
					<div class="me-branding">
						<img src="<?php echo esc_html( WPVME_URL . 'assets/logo-50.png' ); ?>" alt="Map Engine" height="50px" />
					</div>
				</div>    
			<?php
		}
	}

	public function add_map_list_column( $columns ) {
		unset( $columns['date'] );
		$columns['me_shortcode'] = __( 'Shortcode', 'map-engine' );
		$columns['me_map_engine']  = __( 'Engine', 'map-engine' );
		$columns['date']         = __( 'Date', 'map-engine' );
		return $columns;
	}

	public function add_map_list_column_data( $column, $post_id ) {
		switch ( $column ) {
			case 'me_shortcode':
				printf(
					// translators: 1: Map Engine ID
					'<div class="me-shortcode-wrapper"><input type="text" id="meShortCode" name="meShortCode" class="me-shortcode-input" value="[mapengine id=\'%1d\']" readonly/> %2$s',
					esc_html( $post_id ),
					"<span class='dashicons dashicons-admin-page me-shortcode-copy'></span></div>
					<div class='wme-copied-notice' style='opacity: 0;'>
						Copied!
					</div>"
				);
				break;

			case 'me_map_engine':
				printf(
					// translators: 1: Map Engine
					'<span class="me-map-engine-value"> %1$s</div>',
					esc_html( get_post_meta($post_id, 'map_engine', true) ),
				);
				break;
		}
	}

	public function me_remove_notices() {
		global $wp_filter;
		$screen     = get_current_screen();
		$me_screens = [
			'edit-me_maps',
			'maps_page_map-engine-settings',
			'me_maps'
		];

		if ( in_array( $screen->id, $me_screens, true ) ) {
			if ( is_user_admin() ) {
				if ( isset( $wp_filter['user_admin_notices'] ) ) {
					unset( $wp_filter['user_admin_notices'] );
				}
			} elseif ( isset( $wp_filter['admin_notices'] ) ) {
				unset( $wp_filter['admin_notices'] );
			}
			if ( isset( $wp_filter['all_admin_notices'] ) ) {
				unset( $wp_filter['all_admin_notices'] );
			}
		}
	}
}
