<?php

namespace WPV_ME\Core;

class Permissions {
	public static function before_running_ajax() {
		if ( ! current_user_can( 'edit_posts' ) ) {
			wp_send_json_error( 'You do not have permission to create maps' );
		}
	}
}
