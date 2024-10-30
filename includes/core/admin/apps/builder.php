<?php

namespace WPV_ME\Core\Admin;

use WPV_ME\Core\Admin;
use WPV_ME\Core\FileSystem;
use WPV_ME\Core\Permissions;
use WPV_ME\Core\Utils;

class Builder {
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
		add_action( 'wp_ajax_wpvme_get_by_meta_key', [ $this, 'get_by_meta_key' ], 10 );
		add_action( 'wp_ajax_wpvme_update_post_data', [ $this, 'update_post_data' ], 10 );
		add_action( 'wp_ajax_wpvme_save_builder_data', [ $this, 'save_builder_data' ], 10 );
		add_action( 'wp_ajax_wpvme_reset_upload_directory', [ $this, 'reset_upload_directory' ], 10 );
	}

	public function instantiate( $screen ) {

		if ( ! $screen ) {
			return;
		}

		if ( $screen->id === 'me_maps' ) {
			// actions
			add_action( 'edit_form_top', [ $this, 'render_map_builder' ] );
			add_action( 'admin_enqueue_scripts', [ $this, 'admin_scripts' ], 11, 1 );
		}
	}

	public function admin_scripts() {
		$this->enqueue_scripts( 'map' );
		$this->enqueue_styles( 'map' );
		wp_enqueue_media();
		wp_enqueue_editor();
		wp_dequeue_script( 'autosave' );
	}

	private function get_initial_data() {
		$meta_keys = apply_filters(
			'wpv_me_db_builder_meta_keys',
			[
				'map_engine',
				'map_entities',
				'map_settings',
				'map_global_styles',
				'styles',
			]
		);

		$data = [];

		foreach ( $meta_keys as $meta_key ) {
			$data[ $meta_key ] = get_post_meta( get_the_ID(), $meta_key, true );
		}

		$data = apply_filters( 'wpv_me_map_builder_initial_data', $data );

		return $data;
	}

	private function get_gm_api_key() {
		$options = get_option( 'wpvme_settings' );
		if ( isset( $options['gmap_api_key'] ) ) {
			return $options['gmap_api_key'];
		}
		return '';
	}

	private function enqueue_scripts( $name ) {
		$filepath   = 'dist/' . $name;
		$asset_file = Utils::get_asset_file( $filepath );
		$post_id    = get_the_ID();
		$handle     = 'wpv-me-' . $name . '-script';

		array_push( $asset_file['dependencies'], 'lodash' );

		wp_register_script(
			$handle,
			WPVME_URL . $filepath . '.js',
			$asset_file['dependencies'],
			$asset_file['version'],
			true
		);

		$wme_variable                   = Utils::get_default_global_variables();
		$wme_variable['data']           = $this->get_initial_data();
		$wme_variable['map_name']       = get_the_title( $post_id );
		$wme_variable['post_id']        = $post_id;
		$wme_variable['post_status']    = get_post_status( $post_id );
		$wme_variable['post_permalink'] = get_permalink( $post_id );
		$wme_variable['me_map_types']   = $this->me_filters();
		$wme_variable['data']           = $this->get_initial_data();
		$wme_variable['gm_libraries']   = [ 'places', 'geometry', 'drawing' ];
		$wme_variable['gm_api_key']     = $this->get_gm_api_key();
		$wme_variable                   = apply_filters( 'wme_builder_page_variable', $wme_variable );

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

	public function save_builder_data() {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$data = [
			'map_entities'      => '',
			'map_settings'      => '',
			'map_global_styles' => '',
			'styles'            => '',
		];

		$post_id = sanitize_text_field( $_POST['post_id'] );

		$post = get_post( $post_id );

		if ( ! $post ) {
			wp_send_json_error( __( 'Map not found', 'map-engine' ) );
		}

		$post_status       = sanitize_text_field( $_POST['post_status'] );
		$post_title        = sanitize_text_field( $_POST['post_title'] );
		$map_entities      = json_decode( stripslashes( sanitize_text_field( $_POST['map_entities'] ) ) );
		$map_settings      = json_decode( stripslashes( sanitize_text_field( $_POST['map_settings'] ) ) );
		$map_global_styles = json_decode( stripslashes( sanitize_text_field( $_POST['map_global_styles'] ) ) );
		$styles            = json_decode( stripslashes( sanitize_text_field( $_POST['styles'] ) ) );

		if ( ! $post_status ) {
			wp_send_json_error( __( 'Map status not found', 'map-engine' ) );
		}

		if ( ! $post_title ) {
			wp_send_json_error( __( 'Map name not found', 'map-engine' ) );
		}

		if ( ! $map_entities ) {
			wp_send_json_error( __( 'Map entities not found', 'map-engine' ) );
		}

		if ( ! $map_settings ) {
			wp_send_json_error( __( 'Map settings not found', 'map-engine' ) );
		}

		if ( ! $map_global_styles ) {
			wp_send_json_error( __( 'Map global styles not found', 'map-engine' ) );
		}

		if ( ! $styles ) {
			wp_send_json_error( __( 'Map styles not found', 'map-engine' ) );
		}

		$data['map_entities']      = $map_entities;
		$data['map_settings']      = $map_settings;
		$data['map_global_styles'] = $map_global_styles;
		$data['styles']            = $styles;

		$data = apply_filters( 'wpv_me_map_builder_save_data', $data );

		foreach ( $data as $key => $value ) {
			update_post_meta( $post_id, $key, $value );
		}

		$post->post_status = $post_status;
		$post->post_title  = $post_title;

		wp_update_post( $post );

		// delete the css file from upload directory
		$filename = WPVME_UPLOAD_CSS_BASE_NAME . $post_id . '.css';
		FileSystem::delete_file_from_css_directory( $filename );

		wp_send_json_success();
	}

	public function render_map_builder( $post ) {

		if ( $post->post_type !== 'me_maps' ) {
			return;
		}

		?>
		<div id="me-map-builder-root">
			loading...
		</div>    
		<?php
	}

	public function reset_upload_directory() {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$post_id  = sanitize_text_field( $_POST['post_id'] );
		$filename = WPVME_UPLOAD_CSS_BASE_NAME . $post_id . '.css';

		FileSystem::delete_file_from_css_directory( $filename );
		wp_send_json_success();
	}

	public function get_by_meta_key() {

		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$meta_key = sanitize_text_field( $_POST['meta_key'] );

		$meta_values = get_post_meta( sanitize_text_field( $_POST['post_id'] ), $meta_key, true );

		wp_send_json_success( $meta_values );
	}

	public function update_post_data() {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		Permissions::before_running_ajax();

		$post_id = sanitize_text_field( $_POST['post_id'] );
		$key     = sanitize_text_field( $_POST['key'] );
		$value   = sanitize_text_field( $_POST['value'] );

		$post = get_post( $post_id );

		if ( ! $post ) {
			wp_send_json_error( 'Map not found' );
		}

		$post->{$key} = $value;

		wp_update_post( $post );

		wp_send_json_success();
	}

	private function me_filters() {
		return apply_filters(
			'me_map_types',
			[
				'Default'                      => 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
				'OpenTopoMap'                  => 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
				'Wikimedia Labs OSM'           => 'http://{s}.tiles.wmflabs.org/osm/{z}/{x}/{y}.png',
				'Wikimedia Labs OSM No Labels' => 'http://{s}.tiles.wmflabs.org/osm-no-labels/{z}/{x}/{y}.png',
				'Black and White'              => 'http://{s}.tiles.wmflabs.org/bw-mapnik/{z}/{x}/{y}.png',
				'Stamen Toner'                 => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner/{z}/{x}/{y}{r}.png',
				'Stamen Background'            => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-background/{z}/{x}/{y}{r}.png',
				'Stamen Lite'                  => 'https://stamen-tiles-{s}.a.ssl.fastly.net/toner-lite/{z}/{x}/{y}{r}.png',
				'Stadia Outdoor'               => 'https://tiles.stadiamaps.com/tiles/outdoors/{z}/{x}/{y}{r}.png',
				'Esri World Imagery'           => 'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',

			]
		);
	}
}
