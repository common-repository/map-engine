<?php
namespace WPV_ME\Integrations;

abstract class MapBase {

	private $depended_scripts = [];

	private $depended_styles = [];

	abstract public function get_name();

	public function add_script_depends( $script ) {
		$this->depended_scripts[] = $script;
	}

	public function add_style_depends( $style ) {

		$this->depended_styles[] = $style;
	}

	public function get_script_depends() {
		return $this->depended_scripts;
	}

	public function get_style_depends() {
		return $this->depended_styles;
	}

	final public function enqueue_scripts() {
		foreach ( $this->get_script_depends() as $script ) {
			wp_register_script( $script[0], $script[1], $script[2], $script[3], $script[4] );
			wp_localize_script(
				$script[0],
				'wpvme',
				[
					'ajax_nonce' => wp_create_nonce( 'me_ajax_nonce' ),
					'ajax_url'   => admin_url( 'admin-ajax.php' ),
					'admin_url'  => admin_url(),
				]
			);
		}
		// engine script
		if ( is_user_logged_in() ) {
			wp_enqueue_script( 'wpv-me-script', WPVME_URL . '/assets/js/frontend/mapengine.js', [], WPVME_VERSION, true );
		}
	}

	final public function enqueue_styles() {
		foreach ( $this->get_style_depends() as $style ) {
			wp_register_style( $style[0], $style[1], [], WPVME_VERSION );
		}
	}

	public function get_map_data( $id ) {
		$post_id    = sanitize_text_field( $id );
		$post_metas = [];
		$meta_keys  = [
			'map_engine',
			'map_entities',
			'map_settings',
			'map_global_styles',
			'styles',
		];
		foreach ( $meta_keys as $meta_key ) {
			$post_metas[ $meta_key ] = get_post_meta( $post_id, $meta_key, true );
			$post_metas['id']        = $post_id;
		}

		return $post_metas;
	}

	public function get_map_meta_data( $id ) {
		$map_engine = get_post_meta( $id, 'map_engine', true );
		return $map_engine;
	}
}
