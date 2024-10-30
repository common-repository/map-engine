<?php
namespace WPV_ME\Integrations;

use WPV_ME\Integrations\MapBase;

class Open_Map extends MapBase {

	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_name() {
		return 'open-map';
	}

	public function get_script_depends() {
		$scripts = [
			[
				'wpv-me-frontend-os-map-script',
				WPVME_URL . 'dist/os.js',
				[],
				'',
				true,
			],
			[
				'me-os-maps-script',
				WPVME_URL . 'assets/js/frontend/static/leaflet.js',
				[],
				'',
				true,
			],
			[
				'me-os-maps-fullscreen-script',
				WPVME_URL . 'assets/js/frontend/static/Control.FullScreen.js',
				[],
				'',
				true,
			],
		];
		return $scripts;
	}

	public function get_style_depends() {
		$styles = [
			[
				'wpv-me-frontend-os-map-style',
				WPVME_URL . 'assets/css/frontend/open-map.css',
			],
			[
				'wpv-me-frontend-os-map-fullscreen-style',
				WPVME_URL . 'assets/css/frontend/static/Control.FullScreen.css',
			],
			[
				'me-os-maps-style',
				WPVME_URL . 'assets/css/frontend/static/leaflet.css',
			],
		];

		return $styles;
	}
}
