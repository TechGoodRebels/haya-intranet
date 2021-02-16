<?php 

function audit_log_session_login( $auth_cookie, $expire, $expiration, $user_id, $scheme, $token ) {
	$wp_current_user = get_userdata( $user_id );
	log_register_sessions( $wp_current_user->user_email, true, 'Login successful.' );
}

add_action( 'set_auth_cookie', 'audit_log_session_login', 10, 6 );

function audit_log_session_fail( $user_login, $error ) {
	$errorMessage = $error->get_error_message();
    log_register_sessions( $user_login, false, "Login failed: $errorMessage." );
}

add_action( 'wp_login_failed', 'audit_log_session_fail', 10, 2 );

function reset_log_session_login() {
	if( !empty($_REQUEST['log']) && $_REQUEST['log'] == 'reset_audit_log' ) {
		unlink( WP_CONTENT_DIR . '/uploads/wp-session-logs/session.log' );
	}
}

add_action( 'init', 'reset_log_session_login' );

function log_register_sessions( $username, $fail, $message ) {

	if( defined( 'CORE_HRE_SESSION_LOG' ) && CORE_HRE_SESSION_LOG ) {

		if( defined( 'WP_CONTENT_DIR' ) ) {
			$directoryLogs = WP_CONTENT_DIR . '/uploads/wp-session-logs/';
		} else {
			$directoryLogs = $_SERVER["DOCUMENT_ROOT"] . '/wp-content/uploads/wp-session-logs/';
		}

		if( !file_exists( $directoryLogs ) ) {
			mkdir( $directoryLogs, 0755, true );
		}

		$file = $directoryLogs . 'session.log';

		if( !empty( $_SERVER['HTTP_CLIENT_IP'] ) ) {
		    $ip = $_SERVER['HTTP_CLIENT_IP'];
		} elseif( !empty( $_SERVER['HTTP_X_FORWARDED_FOR'] ) ) {
		    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		} else {
		    $ip = $_SERVER['REMOTE_ADDR'];
		}

		$currentTime = date( 'Y-m-d h:i:s A' );

		$codeERROR = $fail ? 'success' : 'error';

		$line  	 = "[$currentTime][$codeERROR][$ip] - User: $username, $message ##".PHP_EOL;
		$handler = fopen( $file, 'a' );
		$fwrite  = fwrite( $handler, $line );
		fclose( $handler );

	}

}