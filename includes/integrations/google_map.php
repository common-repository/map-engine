<?php
namespace WPV_ME\Integrations;

use WPV_ME\Integrations\MapBase;

class Google_Map extends MapBase {

	private static $instance = null;
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function get_name() {
		return 'google-map';
	}

	public function get_script_depends() {
		$settings     = get_option( 'wpvme_settings' );
		$gmap_api_key = isset( $settings['gmap_api_key'] ) ? $settings['gmap_api_key'] : '';
		$scripts      = [
			[
				'me-google-maps-script',
				'https://maps.googleapis.com/maps/api/js?key=' . $gmap_api_key . '&libraries=places,geometry,drawing&language=' . get_locale(),
				[],
				[],
				[],
				true,
			],
			[
				'wpv-me-frontend-google-map-script',
				WPVME_URL . 'dist/google.js',
				[],
				[],
				true,
			],
			[
				'wpv-me-frontend-glide-script',
				WPVME_URL . 'assets/js/frontend/static/glide/glide.min.js',
				[],
				[],
				true,
			],
		];
		return $scripts;
	}

	public function get_style_depends() {
		$styles = [
			[
				'wpv-me-frontend-common-map-style',
				WPVME_URL . 'assets/css/frontend/common.css',
			],
			[
				'wpv-me-frontend-glide-style',
				WPVME_URL . 'assets/css/frontend/static/glide/glide.core.min.css',
			],
			[
				'wpv-me-frontend-glide-theme-style',
				WPVME_URL . 'assets/css/frontend/static/glide/glide.theme.min.css',
			],
		];

		return $styles;
	}
}
