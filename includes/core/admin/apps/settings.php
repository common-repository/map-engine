<?php

namespace WPV_ME\Core\Admin;

use WPV_ME\Core\Admin;
use WPV_ME\Core\Permissions;
use WPV_ME\Core\Utils;

class Settings extends Admin {
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
		add_action( 'wp_ajax_wpvme_save_settings', [ $this, 'save_settings' ], 10 );
		add_action( 'wp_ajax_wpvme_get_settings', [ $this, 'get_settings' ], 10 );
	}

	public function instantiate( $screen ) {
		if ( ! $screen ) {
			return;
		}

		if ( $screen->id === 'maps_page_map-engine-settings' ) {
			// actions
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 20 );
		}
	}

	public function admin_scripts() {
		$this->enqueue_scripts( 'settings' );
		$this->enqueue_styles( 'settings' );
	}

	private function get_initial_data() {
		$options_keys = apply_filters(
			'wpv_me_db_settings_options_keys',
			[
				'wpvme_settings',
			]
		);

		$data = [];
		foreach ( $options_keys as $key ) {
			$data[ $key ] = get_option( $key );
		}

		$data = apply_filters( 'wpv_me_map_settings_initial_data', $data );

		return $data;
	}

	private function enqueue_scripts( $name ) {
		$filepath   = 'dist/' . $name;
		$asset_file = Utils::get_asset_file( $filepath );
		$handle     = 'wpv-me-' . $name . '-script';

		wp_register_script(
			$handle,
			WPVME_URL . $filepath . '.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$wme_variable         = Utils::get_default_global_variables();
		$wme_variable['data'] = $this->get_initial_data();
		$wme_variable         = apply_filters( 'wme_edit_page_variable', $wme_variable );

		wp_localize_script(
			$handle,
			'wpvme',
			$wme_variable
		);

		wp_enqueue_script( $handle );
	}

	private function enqueue_styles( $name ) {
		$filepath   = 'dist/' . $name;
		$asset_file = Utils::get_asset_file( $filepath );
		$handle     = 'wpv-me-' . $name . '-script';
		wp_enqueue_style(
			$handle,
			WPVME_URL . $filepath . '.css',
			[ 'wp-edit-blocks' ],
			$asset_file['version']
		);
	}

	public function get_settings() {

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$settings = get_option( 'wpvme_settings' );

		$settings = apply_filters( 'wpvme_settings_response', $settings );

		if ( is_admin() === true ) {
			wp_send_json_success( $settings );
		} else {
			wp_send_json_error( 'Something went wrong - Not in admin side' );
		}
	}

	public function save_settings() {

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$settings = (array) json_decode( stripslashes( sanitize_text_field( $_POST['settings'] ) ) );

		if ( empty( $settings ) ) {
			wp_send_json_error( 'No settings provided' );
		}

		$settings = apply_filters( 'wpvme_save_settings', $settings );

		update_option( 'wpvme_settings', $settings );
		wp_send_json_success( 'Settings saved' );
	}
}
