<?php
/**
 * Theme functions and definitions
 *
 * @package HelloElementorChild
 */
function hello_elementor_child_enqueue_scripts() {

	wp_enqueue_style( 'hello-elementor-child-styles', get_stylesheet_directory_uri() . '/style.css', array( 'hello-elementor-theme-style' ), '1.0.0' );
	wp_enqueue_script( 'hello-main-js', get_stylesheet_directory_uri() . '/js/main.js', array( 'jquery' ), '1.0.0' );
	
}

add_action( 'wp_enqueue_scripts', 'hello_elementor_child_enqueue_scripts' );

/**
 * Upgrade Limit uploads size
 *
 * @author Good Rebels
 */
function filter_site_upload_size_limit( $size ) {
    
    if ( ! current_user_can( 'manage_options' ) ) {
        // 20 MB.
        $size = 20 * 1024 * 1024;
    }
    return $size;
}

add_filter( 'upload_size_limit', 'filter_site_upload_size_limit', 20 );
add_filter( 'big_image_size_threshold', '__return_false' );

/**
 * Translate texts
 *
 * @author Good Rebels
 */
add_filter( 'gettext', 'translate_text' );
add_filter( 'ngettext', 'translate_text', 20 );

function translate_text( $translated ) {
    $text = array(
      'No new notifications' => 'No hay nuevas notificaciones',
      'See all notifications' => 'Ver todas las notificaciones.'
    );

    $translated = str_ireplace( array_keys( $text ), $text, $translated );

    return $translated;
}

/**
 * Disable upload notifications plugin
 *
 * @author Good Rebels
 */
function filter_plugin_updates( $value ) {
    unset( $value->response['um-notifications/um-notifications.php'] );    
    return $value;
}

add_filter( 'site_transient_update_plugins', 'filter_plugin_updates' );