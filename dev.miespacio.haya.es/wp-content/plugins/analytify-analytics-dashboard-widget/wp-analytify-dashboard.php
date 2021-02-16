<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/*
 * Plugin Name: Analytify - Gooogle Analytics Dashboard Widget
 * Plugin URI: https://analytify.io/add-ons/google-analytics-dashboard-widget-wordpress/
 * Description: It is a Free Add-on for Analytify plugin to show Google Analytics widget at WordPress dashboard. This is developed on the requests of our users.
 * Version: 1.1.5
 * License: GPLv3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Author: Team Analytify
 * Author URI: https://analytify.io/
 * Text Domain: analytify-analytics-dashboard-widget
 * Domain Path: /languages
 */

define( 'ANALYTIFY_DASHBOARD_VERSION', '1.1.5' );
define( 'ANALYTIFY_DASHBOARD_ROOT_PATH', dirname( __FILE__ ) );

add_action( 'plugins_loaded', 'wp_install_analytify_dashboard', 20 );

/**
 * Run onf plugins Loaded.
 *
 * @since 1.0.0
 */
function wp_install_analytify_dashboard() {

	global $pagenow;

	if ( 'index.php' != $pagenow && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) { // Run only on main dashboard page.
		return;
	}

	add_action( 'admin_enqueue_scripts', 'pa_dashboard_layout_script' );

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {
		add_action( 'admin_notices', 'pa_install_free_dashboard' );
		add_action( 'wp_dashboard_setup','add_analytify_widget' );
		return;
	}

	if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		add_action( 'admin_notices', 'pa_activate_free_dashboard' );
		add_action( 'wp_dashboard_setup','add_analytify_widget' );
		return;
	}

	include_once ANALYTIFY_DASHBOARD_ROOT_PATH . '/analytify-dashboard-addon.php';
	new Analytify_Dashboard_Addon;
}

/**
 * Check If Analytify Free is download.
 *
 * @since 1.0.0
 */
function pa_install_free_dashboard() {
	$action = 'install-plugin';
	$slug   = 'wp-analytify';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

	$message = sprintf('%1$s <br /><a href="%2$s">%3$s</a>' , esc_html__( 'Analytify Core is required to run Analytify dashboard widget.', 'analytify-analytics-dashboard-widget' ), $link, esc_html__( 'Click here to Install Analytify(Core)', 'analytify-analytics-dashboard-widget' ) );

 	analytify_widget_notice( $message, 'wp-analytify-danger' );
}

function pa_dashboard_layout_script() {
	wp_enqueue_script( 'analytify-dashboard-layout', plugins_url( '/assets/js/wp-analytify-dashboard-layout.js', __FILE__ ), false, ANALYTIFY_DASHBOARD_VERSION );
}

/**
 * Active Analytify Free.
 *
 * @since 1.0.0
 */
function pa_activate_free_dashboard() {

	$action = 'activate';
	$slug   = 'wp-analytify/wp-analytify.php';
	$link   = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );

	$message = 	sprintf( '%1$s <br /><a href="%2$s">%3$s</a>' , esc_html__( 'Analytify Core is required to run Analytify dashboard widget.', 'analytify-analytics-dashboard-widget' ), $link, esc_html__( 'Click here to activate Analytify Core plugin.', 'analytify-analytics-dashboard-widget' ) );

	analytify_widget_notice( $message, 'wp-analytify-danger' );

}


/**
 * Add dashboard widget with warning message.
 *
 * @since 1.0.3
 */
function add_analytify_widget() {
	global $wp_meta_boxes;

	wp_add_dashboard_widget( 'analytify-dashboard-addon-warning', __( 'Google Analytics Dashboard By Analytify', 'analytify-analytics-dashboard-widget' ), 'wpa_general_dashboard_area', null , null );

	// Place the widget at the top.
	$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];		
	$widget_instance  = array( 'analytify-dashboard-addon-warning' => $normal_dashboard[ 'analytify-dashboard-addon-warning' ] );
	unset( $normal_dashboard[ 'analytify-dashboard-addon-warning' ] );
	$sorted_dashboard                             = array_merge( $widget_instance, $normal_dashboard );
	$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
}

/**
 * Dashboard Widget
 *
 * @since 1.0.3
 */
function wpa_general_dashboard_area( $var, $dashboard_id ) {

	if ( ! file_exists( WP_PLUGIN_DIR . '/wp-analytify/analytify-general.php' ) ) {

		$action = 'install-plugin';
		$slug   = 'wp-analytify';
		$link  = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'update.php' ) ), $action . '_' . $slug );

		echo '<div class="analytify-activation-cards">
        <div class="analytify-activation-card-header">
            <img src="' . plugins_url( 'assets/images/analytify-logo-135x24.png', __FILE__ ) . '">
        </div>
        <div class="analytify-activation-card-body">
            <p>'. __( 'Analytify core is required to use this Analytics widget.', 'analytify-analytics-dashboard-widget' ) . '</p>
            <a href="'. $link .'" class="anaytity-active-card-button">'. __( 'Install Analytify Core', 'analytify-analytics-dashboard-widget' ) . '</a>
        </div>
		</div>';

		return;

	} else if ( ! in_array( 'wp-analytify/wp-analytify.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {

		$action = 'activate';
		$slug   = 'wp-analytify/wp-analytify.php';
		$link = wp_nonce_url( add_query_arg( array( 'action' => $action, 'plugin' => $slug ), admin_url( 'plugins.php' ) ), $action . '-plugin_' . $slug );
		
		echo '<div class="analytify-activation-cards">
        <div class="analytify-activation-card-header">
            <img src="' . plugins_url( 'assets/images/analytify-logo-135x24.png', __FILE__ ) . '">
        </div>
        <div class="analytify-activation-card-body">
            <p>'. __( 'Analytify core is required to use this Analytics widget.', 'analytify-analytics-dashboard-widget' ) . '</p>
            <a href="'. $link .'" class="anaytity-active-card-button">'. __( 'Activate Analytify Core', 'analytify-analytics-dashboard-widget' ) . '</a>
        </div>
		</div>';
		
	}

}

/**
 * Add custom admin notice
 * @param  string $message Custom Message
 * @param  string $class   wp-analytify-success,wp-analytify-danger
 *
 * @since 1.0.3
 */
 function analytify_widget_notice( $message, $class ) {
		echo '<div class="wp-analytify-notification '. $class .'">
							<a class="" href="#" aria-label="Dismiss the welcome panel"></a>
							<div class="wp-analytify-notice-logo">
								<img src="' . plugins_url( 'assets/images/logo.svg', __FILE__ ) . '" alt="">
							</div>
							<div class="wp-analytify-notice-discription">
								<p>' . $message .'</p>
							</div>
				</div>';
 }


/**
 * Add Css for admin notice
 *
 * @since 1.0.3
 */
 add_action( 'admin_enqueue_scripts', 'analytify_widget_scripts' );
 function analytify_widget_scripts( $page ) {
	 if ( 'index.php' == $page ) {
		 wp_enqueue_style( 'analytify-widget-admin', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), ANALYTIFY_DASHBOARD_VERSION );
	 }
 }

/**
 * Load TextDoamin
 *
 * @since 1.0.2
 */
function wp_analytify_dashboard_widget_load_text_domain(){
	$plugin_dir = basename( dirname( __FILE__ ) );
	load_plugin_textdomain( 'analytify-analytics-dashboard-widget', false , $plugin_dir . '/languages/' );
}
add_action( 'init', 'wp_analytify_dashboard_widget_load_text_domain' );

?>
