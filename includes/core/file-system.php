<?php

namespace WPV_ME\Core;

class FileSystem {

	public static function create_directories( $subdir = [ 'css' ] ) {
		if ( ! is_dir( WPVME_UPLOAD_ROOT_DIR ) ) {
			foreach ( $subdir as $dir ) {
				wp_mkdir_p( WPVME_UPLOAD_ROOT_DIR . '/' . $dir, 0700 );
			}
		}
	}

	public static function create_root_directory() {
		if ( ! is_dir( WPVME_UPLOAD_ROOT_DIR ) ) {
			wp_mkdir_p( WPVME_UPLOAD_ROOT_DIR, 0700 );
		}
	}

	public static function create_css_directory() {
		if ( ! is_dir( WPVME_UPLOAD_ROOT_DIR . '/css' ) ) {
			wp_mkdir_p( WPVME_UPLOAD_ROOT_DIR . '/css', 0700 );
		}
	}

	public static function create_file( $file, $content ) {
		$file_path = WPVME_UPLOAD_ROOT_DIR . '/' . $file;
		if ( ! file_exists( $file_path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_read_file_put_contents
			file_put_contents( $file_path, $content );
		}
	}

	public static function get_file_content( $file ) {
		$file_path = WPVME_UPLOAD_ROOT_DIR . '/' . $file;
		if ( file_exists( $file_path ) ) {
			// phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
			return file_get_contents( $file_path );
		}
	}

	public static function delete_file( $file ) {
		$file_path = WPVME_UPLOAD_ROOT_DIR . '/' . $file;
		if ( file_exists( $file_path ) ) {
			unlink( $file_path );
		}
	}

	public static function delete_root_directory() {
		if ( is_dir( WPVME_UPLOAD_ROOT_DIR ) ) {
			rmdir( WPVME_UPLOAD_ROOT_DIR );
		}
	}

	public static function delete_file_from_css_directory( $file ) {
		$file_path = WPVME_UPLOAD_ROOT_DIR . '/css/' . $file;
		if ( file_exists( $file_path ) ) {
			unlink( $file_path );
		}
	}

	public static function has_file( $file ) {
		$file_path = WPVME_UPLOAD_ROOT_DIR . '/' . $file;
		if ( file_exists( $file_path ) ) {
			return true;
		}
		return false;
	}

	public static function has_directory( $dir ) {
		$dir_path = WPVME_UPLOAD_ROOT_DIR . '/' . $dir;
		if ( is_dir( $dir_path ) ) {
			return true;
		}
		return false;
	}

	public static function has_root_directory() {
		if ( is_dir( WPVME_UPLOAD_ROOT_DIR ) ) {
			return true;
		}
		return false;
	}

	public static function register_file( $filepath, $file_handler ) {
		$upload_path = wp_upload_dir();
		wp_register_style( $file_handler, WPVME_UPLOAD_ROOT_PATH . '/' . $filepath, [], WPVME_VERSION );
	}

}
