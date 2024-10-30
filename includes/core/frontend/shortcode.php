<?php

namespace WPV_ME\Core\Frontend;

use WPV_ME\Core\FileSystem;
use WPV_ME\Core\Utils;
use WPV_ME\Integrations\Google_Map;

class Shortcode {
	private static $instance;
	private $width         = '32px';
	private $border_width  = '3px';
	private $border_color  = '#fff';
	private $global_styles = [];

	public function __construct() {

		// actions

		// filters

		// shortcode
		add_shortcode( 'mapengine', [ $this, 'shortcode' ] );

		// ajax
		add_action( 'wp_ajax_wpvme_frontend_map_data', [ $this, 'get_frontend_map_data' ] );
		add_action( 'wp_ajax_nopriv_wpvme_frontend_map_data', [ $this, 'get_frontend_map_data' ] );

		// top bar menu
		add_action( 'admin_bar_menu', [ $this, 'add_toolbar_items' ], 100 );
		
	}

	public function get_frontend_map_data() {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
		}

		$map_id  = sanitize_text_field( $_POST['map_id'] );
		$results = Google_Map::instance()->get_map_data( $map_id );

		$data = apply_filters( 'wpv_me_frontend_before_sending_map_data', Utils::prepare_data_before_send_on_frontend( $results ) );

		wp_send_json_success( $data );
	}

	public function add_toolbar_items( $admin_bar ) {
		if ( ! is_admin() ) {
			$admin_bar->add_menu(
				[
					'id'    => 'me-maps',
					'title' => 'Map Engine',
					'href'  => '#',
					'meta'  => [
						'title' => __( 'Map Engine', 'map-engine' ),
						'class' => 'map-engine-top-bar',
					],
				]
			);
		}
		global $post;
		if ( $post && $post->post_type === 'me_maps' ) {
			$admin_bar->remove_menu( 'view' );
		}
	}

	public function shortcode( $atts ) {
		$atts        = shortcode_atts(
			[
				'id' => '',
			],
			$atts,
			'mapengine'
		);
		$post_id     = $atts['id'];
		$post_status = get_post_status( $post_id );
		$map_html    = '';

		if ( 'publish' === $post_status ) {
			$map_html = $this->get_map_html( $post_id, $atts );
		} else {
			// can be seen by admin only
			if ( current_user_can( 'manage_options' ) ) {
				$map_html = '<div class="wpv-me-map-container">' . __( 'Map is not published', 'map-engine' ) . '</div>';
			}
		}

		return $map_html;
	}

	public function get_map_html( $post_id, $atts ) {
		if ( $post_id ) {
			$map_engine   = get_post_meta( $post_id, 'map_engine', true );
			$map_settings = get_post_meta( $post_id, 'map_settings', true );

			if ( isset( $map_engine ) && $map_engine ) {
				$map_name = get_the_title( $post_id );
				$this->load_assets( $map_engine, $post_id, $map_settings );
				$map_html = '<div id=' . WPVME_MAP_CONTAINER_BASE_ID_NAME . '-' . $post_id . " class='wpv-me-map-container wpv-me-map-container-" . $map_engine . "' data-map-name='" . $map_name . "' data-map-id='" . $post_id . "' data-map-type='" . $map_engine . "'>" . '</div>';
			}

			return $map_html;
		}

		return '<div>No data found! for this map</div>';
	}

	private function load_assets( $map_engine, $post_id, $map_Settings ) {

		if ( $map_engine === 'google' ) {
			// google maps api js
			wp_enqueue_script( 'me-' . $map_engine . '-maps-script' );
		} else {
			// os maps api js
			wp_enqueue_script( 'me-' . $map_engine . '-maps-script' );
			wp_enqueue_style( 'me-' . $map_engine . '-maps-style' );
			if ( property_exists( $map_Settings, 'fullscreenControl' ) && $map_Settings->fullscreenControl ) {
				// leaflet map full screen control js css
				wp_enqueue_style( 'wpv-me-frontend-os-map-fullscreen-style' );
				wp_enqueue_script( 'me-os-maps-fullscreen-script' );
			}
		}

		// engine script
		wp_enqueue_script( 'wpv-me-frontend-' . $map_engine . '-map-script' );
		// common style
		wp_enqueue_style( 'wpv-me-frontend-common-map-style' );
		// glide script
		wp_enqueue_script( 'wpv-me-frontend-glide-script' );
		// glide style
		wp_enqueue_style( 'wpv-me-frontend-glide-style' );
		wp_enqueue_style( 'wpv-me-frontend-glide-theme-style' );

		// map css generation
		$filepath     = 'css/' . WPVME_UPLOAD_CSS_BASE_NAME . $post_id . '.css';
		$file_handler = 'wpv-me-frontend-map-style-' . $post_id;
		// TODO:: delete or comment this in production
		// temp delete code
		// $filename = WPVME_UPLOAD_CSS_BASE_NAME . $post_id . '.css';
		// FileSystem::delete_file_from_css_directory( $filename );
		// temp delete code ends

		// check if post css file already exists
		$is_file_exists = FileSystem::has_file( $filepath );

		if ( $is_file_exists ) {
			FileSystem::register_file( $filepath, $file_handler );
			wp_enqueue_style( $file_handler );
			return;
		};

		$is_css_file_generated = $this->generate_css( $post_id, $filepath, $file_handler );

		if ( $is_css_file_generated ) {
			// enqueue newly generated css file via file handler
			wp_enqueue_style( $file_handler );
		}
	}


	private function generate_css( $post_id, $filepath, $file_handler ) {
		$is_root_directory = FileSystem::has_root_directory();

		if ( ! $is_root_directory ) {
			FileSystem::create_root_directory();
		}

		$is_css_directory = FileSystem::has_directory( 'css' );

		if ( ! $is_css_directory ) {
			FileSystem::create_css_directory();
		}

		$styles_str  = '';
		$styles      = (array) get_post_meta( $post_id, 'styles', true );
		$markers     = $styles['markers'];
		$polygons    = $styles['polygons'];
		$styles_str .= $this->prepare_general_styles( $post_id );
		$styles_str .= $this->prepare_global_style( $post_id );
		$styles_str .= $this->prepare_marker_styles( $markers );
		$styles_str .= $this->prepare_polygon_styles( $polygons, $post_id, false );

		FileSystem::create_file( $filepath, $styles_str );
		FileSystem::register_file( $filepath, $file_handler );
		return true;
	}

	private function prepare_polygon_styles( $styles, $post_id, $is_global = false ) {
		return $this->prepare_popup_styles( $styles, $is_global, $post_id, 'polygon' );
	}

	private function prepare_general_styles( $post_id ) {
		$map_settings          = (array) get_post_meta( $post_id, 'map_settings', true );
		$map_height            = $map_settings['height'];
		$map_max_width         = $map_settings['maxWidth'];
		$map_align             = $map_settings['mapAlign'];
		$map_container_id_name = WPVME_MAP_CONTAINER_BASE_ID_NAME . '-' . $post_id;
		$container_style       = 'width: 100%;';

		if ( $map_height !== '' ) {
			$container_style .= ' height: ' . $map_height . 'px;';
		}

		if ( $map_max_width !== '' ) {
			$container_style .= ' max-width: ' . $map_max_width . 'px;';
		}

		$container_style .= $map_align === 'center' ? 'margin: auto' : '';
		$container_style .= $map_align === 'left' ? 'margin-right: auto !important; margin-left: unset !important' : '';
		$container_style .= $map_align === 'right' ? 'margin-left: auto !important; margin-right: unset !important' : '';
		$general_styles   = '
            #' . $map_container_id_name . ' {
                ' . $container_style . '
            }
        ';
		return $general_styles;
	}

	private function prepare_popup_styles( $styles, $is_global = false, $post_id = null, $entity_name = 'marker' ) {

		$pseudo_elements = [
			'arrow' => [
				'element' => 'after',
			],
		];
		$styles_str      = '';
		// prepare marker styles
		foreach ( $styles as $id => $value ) {
			$popup_styles = (array) $value->popup->styles;

			$marker_id = $is_global ? '.wme-global-style-' . $post_id . '-' . $entity_name : '#wme-' . $entity_name . '-' . $id;
			foreach ( $popup_styles as $key => $style ) {
				$popup_style_str = '';
				$style           = $popup_styles[ $key ];
				foreach ( $style as $s_k => $s_v ) {
					if ( $s_k !== 'padding' && $s_k !== 'border-radius' && $s_k !== 'showArrow' ) {
						$popup_style_str .= $s_k . ':' . $s_v . ';';
					}
				}
				$classname = '.wme-popup-' . $key;
				if ( array_key_exists( $key, $pseudo_elements ) ) {
					$classname = $classname . ':' . $pseudo_elements[ $key ]['element'];
				}

				if ( $popup_style_str && $key !== 'arrow' ) {
					$styles_str .= $marker_id . ' ' . $classname . '{' . $popup_style_str . '}';
				}

				if ( $key === 'arrow' ) {
					$styles_str .= $marker_id . ' ' . $classname . ' { background: 
						linear-gradient(-45deg,' . $s_v . '  50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
					}';
				}
			}

			if ( array_key_exists( 'card', $popup_styles ) ) {
				$border_radius = (array) $popup_styles['card']->{'border-radius'};
				$padding       = (array) $popup_styles['card']->{'padding'};
				$styles_str   .= Utils::prepare_popup_border_radius_css( $marker_id, $border_radius );
				$styles_str   .= Utils::prepare_popup_padding_css( $marker_id, $padding );
			}
		}
		return $styles_str;
	}

	private function prepare_global_style( $post_id ) {
		$styles              = (array) get_post_meta( $post_id, 'map_global_styles', true );
		$this->global_styles = $styles;
		$markers             = $styles['markers'];
		$polygons            = $styles['polygons'];

		$styles_str  = $this->prepare_marker_styles( [ 'global' => $markers ], true, $post_id, 'marker' );
		$styles_str .= $this->prepare_popup_styles( [ 'global' => $polygons ], true, $post_id, 'polygon' );

		return $styles_str;
	}

	private function prepare_marker_styles( $styles, $is_global = false, $post_id = null, $entity_name = 'marker' ) {

		$pseudo_elements = [
			'arrow' => [
				'element' => 'after',
			],
		];
		$markers_css     = '';

		// prepare marker styles
		foreach ( $styles as $id => $value ) {
			$marker_styles = (array) $value->marker->styles;
			$popup_styles  = (array) $value->popup->styles;
			$shape_type    = '';
			if ( property_exists( $value->marker->properties, 'iconType' ) ) {
				$shape_type = $value->marker->properties->iconType;
			}
			$global_marker = $this->global_styles['markers']->marker;
			if ( ! $shape_type ) {
				$shape_type = $global_marker->properties->iconType;

			}
			$marker_style_str = '';

			if ( array_key_exists( 'width', $marker_styles ) ) {
				$this->width = $marker_styles['width'];
			} else {
				if ( property_exists( $global_marker->styles, 'width' ) ) {
					$this->width = $global_marker->styles->width;
				} else {
					$this->width = '32px';
				}
			}

			if ( array_key_exists( 'border-width', $marker_styles ) ) {
				$this->border_width = $marker_styles['border-width'];
			} else {
				if ( property_exists( $global_marker->styles, 'border-width' ) ) {
					$this->border_width = $global_marker->styles->{'border-width'};
				} else {
					$this->border_width = '3px';
				}
			}
			if ( array_key_exists( 'border-color', $marker_styles ) ) {
				$this->border_color = $marker_styles['border-color'];
			} else {
				if ( property_exists( $global_marker->styles, 'border-color' ) ) {
					$this->border_color = $global_marker->styles->{'border-color'};
				} else {
					$this->border_color = '#fff';
				}
			}

			foreach ( $marker_styles as $key => $style ) {
				$marker_style_str .= $key . ':' . $style . ';';
			}

			$marker_id = $is_global ? '.wme-global-style-' . $post_id . '-' . $entity_name : '#wme-marker-' . $id;

			foreach ( $popup_styles as $key => $style ) {
				$popup_style_str = '';
				$style           = $popup_styles[ $key ];

				foreach ( $style as $s_k => $s_v ) {
					// skip padding and border radius because they can have different values based on their type
					if ( $s_k !== 'padding' && $s_k !== 'border-radius' && $s_k !== 'showArrow' ) {
						$popup_style_str .= $s_k . ':' . $s_v . ';';
					}
				}
				$classname = '.wme-popup-' . $key;
				if ( array_key_exists( $key, $pseudo_elements ) ) {
					$classname = $classname . ':' . $pseudo_elements[ $key ]['element'];
				}

				if ( $popup_style_str && $key !== 'arrow' ) {
					$markers_css .= $marker_id . ' ' . $classname . '{' . $popup_style_str . '}';
				}

				// linear-gradient(-45deg,#104A3687 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%)
				if ( $key === 'arrow' ) {
					$markers_css .= $marker_id . ' ' . $classname . ' { background: 
						linear-gradient(-45deg,' . $s_v . '  50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
					}';
				}
			}

			if ( array_key_exists( 'card', $popup_styles ) ) {
				$border_radius = (array) $popup_styles['card']->{'border-radius'};
				$padding       = (array) $popup_styles['card']->{'padding'};
				$markers_css  .= Utils::prepare_popup_border_radius_css( $marker_id, $border_radius );
				$markers_css  .= Utils::prepare_popup_padding_css( $marker_id, $padding );
			}

			if ( $shape_type === 'rect' ) {
				$markers_css .= $marker_id . ' .wme-marker-rect::before { 
					outline: ' . $this->border_color . ' solid ' . $this->border_width . ';
					bottom: calc( -' . $this->width . ' / 10);
				}';
			}

			if ( $marker_style_str ) {
				$markers_css .= $marker_id . ' .wme-marker {' . $marker_style_str . '}';
			}

			$markers_css = $this->prepare_marker_popup_styles( $marker_id, $markers_css, $value->popup, $marker_styles, $shape_type );
		}

		return $markers_css;
	}

	private function prepare_marker_popup_styles( $marker_id, $markers_css, $popup_styles, $marker_styles, $shape_type = 'marker' ) {
		$properties         = $popup_styles->properties;
		$position           = $properties->position;
		$global_marker_type = $this->global_styles['markerType'];

		if ( ! $position ) {
			$position = $this->global_styles['markers']->popup->properties->position;
		}

		if ( property_exists( $properties, 'is_custom_marker_icon' ) && $properties->is_custom_marker_icon ) {
			$shape_type = 'marker';
		}
		
		switch ( $position ) {
			case 'anchored':
				$markers_css .= $marker_id . ' 
                        .wme-popup.wme-popup-not-anchored { 
                            display: none;
							left: unset;
							right: unset;
                    }';
				break;
			case 'right':
				if ( $shape_type === 'circle' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: 0%; 
                            left: calc(' . $this->width . '/ 2 + 7.7px + 7.7px + ' . $this->border_width . ');
                            transform: translate(0,50%);
							right: unset;
                    }';
				}
				if ( $shape_type === 'rect' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: ' . Utils::get_rect_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['bottom'] . '; 
                            left: ' . Utils::get_rect_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['left'] . ';
                            transform: translate(0,50%);
							
                    }';
				}
				if ( $shape_type === 'marker' || $shape_type === 'markerAlt' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: ' . Utils::get_marker_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['bottom'] . '; 
                            left: ' . Utils::get_marker_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['left'] . ';
                            transform: translate(0,50%);
                    }';
				}
				$markers_css .= $marker_id . ' 
                        .wme-popup::after { 
                            bottom: calc(50% + -5px); 
                            left: -5px;
                    }';
				break;
			case 'left':
				if ( $shape_type === 'circle' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: 0%; 
                            right: calc(' . $this->width . '/ 2 + 7.7px + 7.7px + ' . $this->border_width . ');
                            transform: translate(0,50%);
							left: unset;
                    }';
				}
				if ( $shape_type === 'rect' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: ' . Utils::get_rect_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['bottom'] . '; 
                            right: ' . Utils::get_rect_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['left'] . ';
                            transform: translate(0,50%);
							left: unset;
                    }';
				}
				if ( $shape_type === 'marker' || $shape_type === 'markerAlt' ) {
					$markers_css .= $marker_id . ' 
                        .wme-popup { 
                            bottom: ' . Utils::get_marker_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['bottom'] . '; 
                            right: ' . Utils::get_marker_popup_left_right_style( $marker_styles, $this->width, $this->border_width )['left'] . ';
                            transform: translate(0,50%);
                    }';
				}
				$markers_css .= $marker_id . ' 
                    .wme-popup::after { 
                        top: calc(50% + -5px); 
                        right: -5px;
                }';
				break;

			default:
				if ( $shape_type === 'circle' || $global_marker_type === 'image' ) {
					$markers_css .= $marker_id . ' .wme-popup{ bottom: calc(' . $this->width . '/ 2 + 7.7px + 7.7px + ' . $this->border_width . '); }';
				}
				if ( $shape_type === 'rect' && $global_marker_type !== 'image' ) {
					$markers_css .= $marker_id . ' .wme-popup{ bottom: ' . Utils::get_rect_popup_top_style( $marker_styles, $this->width, $this->border_width ) . ' }';
				}
				if ( ( $shape_type === 'marker' || $shape_type === 'markerAlt' || $shape_type === '' ) && $global_marker_type !== 'image' ) {
					$markers_css .= $marker_id . ' .wme-popup{ bottom: ' . Utils::get_marker_popup_top_style( $marker_styles, $this->width, $this->border_width ) . ' }';
				}
				break;
		}

		return $markers_css;
	}
}
