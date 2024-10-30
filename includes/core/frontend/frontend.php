<?php

namespace WPV_ME\Core;

use WPV_ME\Core\Frontend\Shortcode;
use WPV_ME\Integrations\Google_Map;
use WPV_ME\Integrations\Open_Map;

class Frontend {
	private static $instance;

	public static function init() {

		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// actions
		add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_scripts' ] );
		add_action( 'template_redirect', [ $this, 'redirect' ] );

		// filters
		add_filter( 'post_row_actions', [ $this, 'remove_view_link_cpt' ] );

		// ajax

		// class objects
		new Shortcode();
	}

	public function redirect() {
		if ( is_singular( 'me_maps' ) && ! current_user_can( 'edit_posts' ) ) {
			wp_safe_redirect( site_url(), 301 );
			exit;
		}
	}

	public function remove_view_link_cpt( $action ) {
		if ( get_post_type() === 'me_maps' && isset( $action['view'] ) ) {
			unset( $action['view'] );
		}
		return $action;
	}

	public function enqueue_scripts() {
		Google_Map::instance()->enqueue_scripts();
		Google_Map::instance()->enqueue_styles();
		Open_Map::instance()->enqueue_scripts();
		Open_Map::instance()->enqueue_styles();
	}

}
