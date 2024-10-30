<?php

namespace WPV_ME\Core;

use WPV_ME\Core\Frontend\Shortcode;
use WPV_ME\Core\Admin;
use WPV_ME\Core\Frontend;


class CMS {
	private static $instance;

	public static function init() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	public function __construct() {
		// actions
		add_action( 'init', [ $this, 'register_post_type' ] );

		// filters
		add_filter( 'the_content', [ $this, 'render_preview' ] );

		// ajax

		if ( is_admin() ) {
			Admin::init();
		}
		Frontend::init();
	}

	public function render_preview( $content ) {
		$shortcode = new Shortcode();
		global $post;
		if ( $post->post_type !== 'me_maps' ) {
			return $content;
		}
		return $shortcode->get_map_html( $post->ID, [] );
	}

	public function register_post_type() {

		/**
		 * Post Type: Maps.
		 */

		$labels = [
			'name'                     => __( 'Maps', 'map-engine' ),
			'singular_name'            => __( 'Map', 'map-engine' ),
			'menu_name'                => __( 'Maps', 'map-engine' ),
			'all_items'                => __( 'All Maps', 'map-engine' ),
			'add_new'                  => __( 'Add new', 'map-engine' ),
			'add_new_item'             => __( 'Add new Map', 'map-engine' ),
			'edit_item'                => __( 'Edit Map', 'map-engine' ),
			'new_item'                 => __( 'New Map', 'map-engine' ),
			'view_item'                => __( 'View Map', 'map-engine' ),
			'view_items'               => __( 'View Maps', 'map-engine' ),
			'search_items'             => __( 'Search Maps', 'map-engine' ),
			'not_found'                => __( 'No Maps found', 'map-engine' ),
			'not_found_in_trash'       => __( 'No Maps found in trash', 'map-engine' ),
			'parent'                   => __( 'Parent Map:', 'map-engine' ),
			'featured_image'           => __( 'Featured image for this Map', 'map-engine' ),
			'set_featured_image'       => __( 'Set featured image for this Map', 'map-engine' ),
			'remove_featured_image'    => __( 'Remove featured image for this Map', 'map-engine' ),
			'use_featured_image'       => __( 'Use as featured image for this Map', 'map-engine' ),
			'archives'                 => __( 'Map archives', 'map-engine' ),
			'insert_into_item'         => __( 'Insert into Map', 'map-engine' ),
			'uploaded_to_this_item'    => __( 'Upload to this Map', 'map-engine' ),
			'filter_items_list'        => __( 'Filter Maps list', 'map-engine' ),
			'items_list_navigation'    => __( 'Maps list navigation', 'map-engine' ),
			'items_list'               => __( 'Maps list', 'map-engine' ),
			'attributes'               => __( 'Maps attributes', 'map-engine' ),
			'name_admin_bar'           => __( 'Map', 'map-engine' ),
			'item_published'           => __( 'Map published', 'map-engine' ),
			'item_published_privately' => __( 'Map published privately.', 'map-engine' ),
			'item_reverted_to_draft'   => __( 'Map reverted to draft.', 'map-engine' ),
			'item_scheduled'           => __( 'Map scheduled', 'map-engine' ),
			'item_updated'             => __( 'Map updated.', 'map-engine' ),
			'parent_item_colon'        => __( 'Parent Map:', 'map-engine' ),
		];

		$args = [
			'label'                 => __( 'Maps', 'map-engine' ),
			'labels'                => $labels,
			'description'           => '',
			'public'                => false,
			'publicly_queryable'    => true,
			'show_ui'               => true,
			'show_in_rest'          => true,
			'rest_base'             => '',
			'rest_controller_class' => 'WP_REST_Posts_Controller',
			'has_archive'           => false,
			'show_in_menu'          => false,
			'show_in_nav_menus'     => true,
			'delete_with_user'      => false,
			'exclude_from_search'   => true,
			'capability_type'       => 'post',
			'map_meta_cap'          => true,
			'hierarchical'          => false,
			'rewrite'               => [
				'slug'       => 'wpv_maps',
				'with_front' => true,
			],
			'query_var'             => true,
			'supports'              => [ '' ],
			'show_in_graphql'       => false,
			'menu_icon'             => WPVME_URL . 'assets/me-mini.svg',
		];

		register_post_type( 'me_maps', $args );
	}
}
