<?php

/**
 * Plugin Name:  Map Engine
 * Plugin URI:   https://wpmapengine.com
 * Description:  Build Advanced maps for WordPress
 * Version:      0.0.2
 * Author:       WPVibes
 * Author URI:   https://wpvibes.com
 * License:      GPL-3.0-or-later
 * License URI:  https://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:  map-engine
 * Domain Path:  /languages
 *
 */


// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// check if php version is greater or equal to 7.0
if ( version_compare( PHP_VERSION, '7.0', '<' ) ) {
	add_action(
		'admin_notices',
		function() {
			?>
		<div class="notice notice-error">
			<p>
				<?php
					printf(
						// translators: 1: Map Engine
						'%1s requires PHP version 7.0 or higher. Please update your PHP version or contact your hosting provider.',
						'<b>Map Engine</b>'
					)
				?>
			</p>
		</div>
			<?php
		}
	);
	return;
}

define( 'WPVME_VERSION', '0.0.2' );
define( 'WPVME_MIN_PHP', '7.1' );
define( 'WPVME_MIN_WP', '5.0' );
define( 'WPVME_FILE', __FILE__ );
define( 'WPVME_BASE', plugin_basename( WPVME_FILE ) );
define( 'WPVME_PATH', plugin_dir_path( WPVME_FILE ) );
define( 'WPVME_URL', plugin_dir_url( WPVME_FILE ) );
define( 'WPVME_UPLOAD_DIR', wp_upload_dir() );
define( 'WPVME_UPLOAD_ROOT_DIR_NAME', 'map-engine' );
define( 'WPVME_UPLOAD_ROOT_DIR', WPVME_UPLOAD_DIR['basedir'] . '/' . WPVME_UPLOAD_ROOT_DIR_NAME );
define( 'WPVME_UPLOAD_ROOT_PATH', WPVME_UPLOAD_DIR['baseurl'] . '/' . WPVME_UPLOAD_ROOT_DIR_NAME );
define( 'WPVME_UPLOAD_CSS_BASE_NAME', 'map-' );
define( 'WPVME_MAP_CONTAINER_BASE_ID_NAME', 'wpv-me-map-container' );


require_once WPVME_PATH . 'vendor/autoload.php';
require_once WPVME_PATH . 'includes/init.php';
