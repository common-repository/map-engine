<?php

namespace WPV_ME\Core;

class Utils {

	public static function get_circle_popup_away_style( $marker_styles ) {
	}

	public static function get_rect_popup_top_style( $marker_styles, $size, $border ) {
		$size           = Utils::px_to_number( $size );
		$border         = Utils::px_to_number( $border ) * 2;
		$size_percent   = ( ( 20 % 100 ) / 100 ) * $size;
		$border_percent = ( ( 10 % 100 ) / 100 ) * $border;
		$move_border    = $border + $border_percent;
		$pythagorean    = sqrt(
			pow( $size_percent, 2 ) + pow( $size_percent, 2 )
		);
		$move           = ( (float) round( $pythagorean, 2 ) ) / 2;
		$default_css    = 'left: unset; transform: translate(-50%)';
		if ( array_key_exists( 'width', $marker_styles ) ) {
			return 'calc(' . $marker_styles['width'] . ' + 7.7px + 7.7px + ' . $move . 'px + ' . $move_border . 'px );' . $default_css;
		}
		return 'calc(' . $size . 'px + 7.7px + 7.7px + ' . $move . 'px + ' . $move_border . 'px );' . $default_css;
	}

	public static function get_rect_popup_left_right_style( $marker_styles, $size, $border ) {
		$size         = Utils::px_to_number( $size );
		$border       = Utils::px_to_number( $border );
		$size_percent = ( ( 20 % 100 ) / 100 ) * $size;
		$pythagorean  = sqrt(
			pow( $size_percent, 2 ) + pow( $size_percent, 2 )
		);
		$move         = ( (float) round( $pythagorean, 2 ) ) / 2 - 3;
		return [
			'bottom' => 'calc(' . $size . 'px / 2 + ' . $move . 'px + ' . $border . 'px)',
			'left'   => 'calc(' . $size . 'px / 2 + 7.7px + 7.7px + ' . $border . 'px)',
		];
	}

	public static function get_marker_popup_top_style( $marker_styles, $size, $border ) {
		$size        = Utils::px_to_number( $size );
		$border      = Utils::px_to_number( $border );
		$pythagorean = sqrt(
			pow( $size + $border, 2 ) +
			pow( $size + $border, 2 )
		);
		if ( array_key_exists( 'border-width', $marker_styles ) ) {
			return 'calc(' . $pythagorean . 'px + ' . $marker_styles['border-width'] . ' + 7.7px);';
		}
		return 'calc(' . $pythagorean . 'px + 3px + 7.7px);';
	}

	public static function get_marker_popup_left_right_style( $marker_styles, $size, $border ) {
		$size        = Utils::px_to_number( $size );
		$border      = Utils::px_to_number( $border );
		$pythagorean = sqrt(
			pow( $size + $border, 2 ) +
			pow( $size + $border, 2 )
		);

		return [
			'bottom' => 'calc(' . $pythagorean . 'px / 2 + ' . $border . 'px + 3px)',
			'left'   => 'calc( 7.7px + ' . $pythagorean . 'px / 2 - 3px)',
		];
	}

	public static function px_to_number( $px ) {
		$px = str_replace( 'px', '', $px );
		return (int) $px;
	}

	public static function prepare_popup_border_radius_css( $id, $border_radius ) {
		$str = '';
		if ( array_key_exists( 'top', $border_radius ) && $border_radius['top'] ) {
			$str .= $id . ' .wme-popup { border-top-left-radius:' . $border_radius['top'] . '}';
		}
		if ( array_key_exists( 'right', $border_radius ) && $border_radius['right'] ) {
			$str .= $id . ' .wme-popup { border-top-right-radius:' . $border_radius['right'] . '}';
		}
		if ( array_key_exists( 'bottom', $border_radius ) && $border_radius['bottom'] ) {
			$str .= $id . ' .wme-popup { border-bottom-right-radius:' . $border_radius['bottom'] . '}';
		}
		if ( array_key_exists( 'left', $border_radius ) && $border_radius['left'] ) {
			$str .= $id . ' .wme-popup { border-bottom-left-radius:' . $border_radius['left'] . '}';
		}
		return $str;
	}

	public static function prepare_popup_padding_css( $id, $padding ) {
		$str = '';
		if ( array_key_exists( 'top', $padding ) && $padding['top'] ) {
			$str .= $id . ' .wme-popup .wme-popup-content { padding-top:' . $padding['top'] . '}';
		}
		if ( array_key_exists( 'left', $padding ) && $padding['left'] ) {
			$str .= $id . ' .wme-popup .wme-popup-content { padding-left:' . $padding['left'] . '}';
		}
		if ( array_key_exists( 'right', $padding ) && $padding['right'] ) {
			$str .= $id . ' .wme-popup .wme-popup-content { padding-right:' . $padding['right'] . '}';
		}
		if ( array_key_exists( 'bottom', $padding ) && $padding['bottom'] ) {
			$str .= $id . ' .wme-popup .wme-popup-content { padding-bottom:' . $padding['bottom'] . '}';
		}
		return $str;
	}

	public static function verify_nonce( $is_ajax = true ) {
		if ( ! wp_verify_nonce( $_POST['ajax_nonce'], 'me_ajax_nonce' ) ) {
			if ( $is_ajax ) {
				wp_send_json_error( [ 'message' => __( 'Invalid nonce', 'map-engine' ) ] );
			} else {
				wp_die( esc_html( __( 'Invalid nonce', 'map-engine' ) ) );
			}
		}
	}

	public static function get_asset_file( $filepath ) {
		$asset_path = WPVME_PATH . $filepath . '.asset.php';

		return file_exists( $asset_path )
			? include $asset_path
			: [
				'dependencies' => [],
				'version'      => WPVME_VERSION,
			];
	}

	public static function get_screens( $excludes = [] ) {
		$screens = [ 'edit-me_maps', 'maps_page_map-engine-settings', 'me_maps' ];

		if ( ! empty( $excludes ) ) {
			$screens = array_diff( $screens, $excludes );
		}

		$screens = apply_filters( 'wpvme-screens', $screens );

		return $screens;
	}

	public static function prepare_data_before_send_on_frontend( $data ) {
		$markers_styles                        = $data['styles']->markers;
		$polygons_styles                       = $data['styles']->polygons;
		$global_styles                         = $data['map_global_styles'];
		$global_style_marker_properties        = $global_styles->markers->marker->properties;
		$global_style_marker_popup_properties  = $global_styles->markers->popup->properties;
		$global_style_marker_popup_styles      = $global_styles->markers->popup->styles;
		$global_polygon_fill_style_normal      = $global_styles->polygons->fill->normal;
		$global_polygon_fill_style_hover       = $global_styles->polygons->fill->hover;
		$global_polygon_stroke_style_normal    = $global_styles->polygons->stroke->normal;
		$global_polygon_stroke_style_hover     = $global_styles->polygons->stroke->hover;
		$global_style_polygon_popup_properties = $global_styles->polygons->popup->properties;
		$global_style_polygon_popup_styles     = $global_styles->polygons->popup->styles;
		$data['markers_properties']            = [];
		$data['polygons_properties']           = [];
		unset( $data['styles'] );
		unset( $data['map_global_styles'] );

		foreach ( $markers_styles as $marker_id => $marker_style_value ) {
			$marker_properties                        = $marker_style_value->marker->properties;
			$marker_popup_properties                  = $marker_style_value->popup->properties;
			$marker_popup_styles                      = $marker_style_value->popup->styles;
			$data['markers_properties'][ $marker_id ] = [];

			if ( property_exists( $marker_properties, 'iconUrl' ) && $marker_properties->iconUrl ) {
				$data['markers_properties'][ $marker_id ]['icon_url'] = $marker_properties->iconUrl;
			} else {
				if ( property_exists( $global_style_marker_properties, 'iconUrl' ) ) {
					$data['markers_properties'][ $marker_id ]['icon_url'] = $global_style_marker_properties->iconUrl;
				}
			}

			if ( property_exists( $marker_properties, 'iconType' ) && $marker_properties->iconType ) {
				$data['markers_properties'][ $marker_id ]['icon_type'] = $marker_properties->iconType;
			} else {
				if ( property_exists( $global_style_marker_properties, 'iconType' ) ) {
					$data['markers_properties'][ $marker_id ]['icon_type'] = $global_style_marker_properties->iconType;
				}
			}

			if ( property_exists( $marker_popup_properties, 'position' ) && $marker_popup_properties->position ) {
				$data['markers_properties'][ $marker_id ]['popup_position'] = $marker_popup_properties->position;
			} else {
				if ( property_exists( $global_style_marker_popup_properties, 'position' ) ) {
					$data['markers_properties'][ $marker_id ]['popup_position'] = $global_style_marker_popup_properties->position;
				}
			}

			if ( property_exists( $marker_popup_properties, 'openType' ) && $marker_popup_properties->openType ) {
				$data['markers_properties'][ $marker_id ]['popup_open_type'] = $marker_popup_properties->openType;
			} else {
				if ( property_exists( $global_style_marker_popup_properties, 'openType' ) ) {
					$data['markers_properties'][ $marker_id ]['popup_open_type'] = $global_style_marker_popup_properties->openType;
				}
			}

			if ( property_exists( $marker_popup_styles, 'card' ) && property_exists( $marker_popup_styles->card, 'display' ) && $marker_popup_styles->card->display ) {
				$data['markers_properties'][ $marker_id ]['card_display'] = $marker_popup_styles->card->display;
			} else {
				if ( property_exists( $global_style_marker_popup_styles, 'card' ) && property_exists( $global_style_marker_popup_styles->card, 'display' ) ) {
					$data['markers_properties'][ $marker_id ]['card_display'] = $global_style_marker_popup_styles->card->display;
				}
			}

			if ( property_exists( $marker_popup_styles, 'card' ) && property_exists( $marker_popup_styles->card, 'showArrow' ) && $marker_popup_styles->card->showArrow ) {
				$data['markers_properties'][ $marker_id ]['card_arrow'] = $marker_popup_styles->card->showArrow;
			} else {
				if ( property_exists( $global_style_marker_popup_styles, 'card' ) && property_exists( $global_style_marker_popup_styles->card, 'showArrow' ) ) {
					$data['markers_properties'][ $marker_id ]['card_arrow'] = $global_style_marker_popup_styles->card->showArrow;
				}
			}
		}

		foreach ( $polygons_styles as $polygon_id => $polygon_style_value ) {
			$fill_styles              = $polygon_style_value->fill;
			$fill_style_normal        = $fill_styles->normal;
			$fill_style_hover         = $fill_styles->hover;
			$stroke_styles            = $polygon_style_value->stroke;
			$stroke_style_normal      = $stroke_styles->normal;
			$stroke_style_hover       = $stroke_styles->hover;
			$polygon_popup_properties = $polygon_style_value->popup->properties;
			$polygon_popup_styles     = $polygon_style_value->popup->styles;

			foreach ( $fill_style_normal as $key => $value ) {
				if ( $value ) {
					$data['polygons_properties'][ $polygon_id ]['fill']['normal'][ $key ] = $value;
				} else {
					$data['polygons_properties'][ $polygon_id ]['fill']['normal'][ $key ] = $global_polygon_fill_style_normal->$key;
				}
			}

			foreach ( $fill_style_hover as $key => $value ) {
				if ( $value ) {
					$data['polygons_properties'][ $polygon_id ]['fill']['hover'][ $key ] = $value;
				} else {
					$data['polygons_properties'][ $polygon_id ]['fill']['hover'][ $key ] = $global_polygon_fill_style_hover->$key;
				}
			}

			foreach ( $stroke_style_normal as $key => $value ) {
				if ( $value ) {
					$data['polygons_properties'][ $polygon_id ]['stroke']['normal'][ $key ] = $value;
				} else {
					$data['polygons_properties'][ $polygon_id ]['stroke']['normal'][ $key ] = $global_polygon_stroke_style_normal->$key;
				}
			}

			foreach ( $stroke_style_hover as $key => $value ) {
				if ( $value ) {
					$data['polygons_properties'][ $polygon_id ]['stroke']['hover'][ $key ] = $value;
				} else {
					$data['polygons_properties'][ $polygon_id ]['stroke']['hover'][ $key ] = $global_polygon_stroke_style_hover->$key;
				}
			}

			// create functions

			if ( property_exists( $polygon_popup_properties, 'originPoint' ) && $polygon_popup_properties->originPoint ) {
				$data['polygons_properties'][ $polygon_id ]['origin_point'] = $polygon_popup_properties->originPoint;
			} else {
				if ( property_exists( $global_style_polygon_popup_properties, 'originPoint' ) ) {
					$data['polygons_properties'][ $polygon_id ]['origin_point'] = $global_style_polygon_popup_properties->originPoint;
				}
			}

			if ( property_exists( $polygon_popup_properties, 'position' ) && $polygon_popup_properties->position ) {
				$data['polygons_properties'][ $polygon_id ]['popup_position'] = $polygon_popup_properties->position;
			} else {
				if ( property_exists( $global_style_polygon_popup_properties, 'position' ) ) {
					$data['polygons_properties'][ $polygon_id ]['popup_position'] = $global_style_polygon_popup_properties->position;
				}
			}

			if ( property_exists( $polygon_popup_properties, 'openType' ) && $polygon_popup_properties->openType ) {
				$data['polygons_properties'][ $polygon_id ]['popup_open_type'] = $polygon_popup_properties->openType;
			} else {
				if ( property_exists( $global_style_polygon_popup_properties, 'openType' ) ) {
					$data['polygons_properties'][ $polygon_id ]['popup_open_type'] = $global_style_polygon_popup_properties->openType;
				}
			}

			if ( property_exists( $polygon_popup_styles, 'card' ) && property_exists( $polygon_popup_styles->card, 'display' ) && $polygon_popup_styles->card->display ) {
				$data['polygons_properties'][ $polygon_id ]['card_display'] = $polygon_popup_styles->card->display;
			} else {
				if ( property_exists( $global_style_polygon_popup_styles, 'card' ) && property_exists( $global_style_polygon_popup_styles->card, 'display' ) ) {
					$data['polygons_properties'][ $polygon_id ]['card_display'] = $global_style_polygon_popup_styles->card->display;
				}
			}

			if ( property_exists( $polygon_popup_styles, 'card' ) && property_exists( $polygon_popup_styles->card, 'showArrow' ) && $polygon_popup_styles->card->showArrow ) {
				$data['polygons_properties'][ $polygon_id ]['card_arrow'] = $polygon_popup_styles->card->showArrow;
			} else {
				if ( property_exists( $global_style_polygon_popup_styles, 'card' ) && property_exists( $global_style_polygon_popup_styles->card, 'showArrow' ) ) {
					$data['polygons_properties'][ $polygon_id ]['card_arrow'] = $global_style_polygon_popup_styles->card->showArrow;
				}
			}
		}

		return $data;
	}

	public static function get_default_global_variables() {
		$options = get_option( 'wpvme_settings' );
		$api_key = '';
		if ( isset( $options['gmap_api_key'] ) ) {
			$api_key = $options['gmap_api_key'];
		}
		return [
			'rest_nonce' => wp_create_nonce( 'me_rest_nonce' ),
			'ajax_nonce' => wp_create_nonce( 'me_ajax_nonce' ),
			'ajax_url'   => admin_url( 'admin-ajax.php' ),
			'rest_url'   => get_rest_url(),
			'site_url'   => site_url(),
			'gm_api_key' => $api_key,
			'admin_url'	 => admin_url(),
		];
	}

}
