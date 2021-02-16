<?php 

/*
Plugin Name: Core Haya Real Estate
Plugin URI: https://haya.es
Description: Funcionalidades de la intranet. Directorio, integración APIs y personlización de Apps.
Author: Good Rebels
Version: 1.0.0
Author URI: https://www.goodrebels.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	die();
}

// Plugin Paths
define( 'CORE_HRE_DIR_PATH', 	plugin_dir_path( __FILE__ ) );
define( 'CORE_HRE_DIR_URL', 	plugin_dir_url( __FILE__ ) );
define( 'CORE_HRE_SHORTCODES', 	CORE_HRE_DIR_PATH . 'shortcodes/' );
define( 'CORE_HRE_FUNCTIONS', 	CORE_HRE_DIR_PATH . 'functions/' );
define( 'CORE_HRE_CLASSES', 	CORE_HRE_DIR_PATH . 'class/' );
define( 'CORE_HRE_SCRIPTS', 	CORE_HRE_DIR_PATH . 'javascript/' );
define( 'CORE_HRE_STYLES', 		CORE_HRE_DIR_PATH . 'styles/' );

// API Credentials
define( 'CORE_HRE_URL_WEBSERVICES', 		'https://intranet.haya.es/WsControlDocumentacion/Servicios.asmx' );
define( 'CORE_HRE_USER_TIMECHEF', 			'hayarealestate' );
define( 'CORE_HRE_PASSW_TIMECHEF',  		'HaReEs@2020' );
define( 'CORE_HRE_TOKEN_TIMECHEF',  		'https://apps.serunion.com/identityprovider/token' );
define( 'CORE_HRE_LDAP_HOST', 				'dc.haya.es' );
define( 'CORE_HRE_LDAP_USER', 				'usr_miespacio' );
define( 'CORE_HRE_LDAP_PASSW', 				'kUrEIZa)&ej5' );
define( 'CORE_HRE_LDAP_BASEDN', 			'OU=Usuarios,DC=corp,DC=int' );
define( 'CORE_HRE_MICROSOFT_TENANT', 		'0147bb13-8ab6-47bd-a1da-cb352ced5ac2' );
define( 'CORE_HRE_MICROSOFT_ID', 	 		'0fe0efa6-978f-4f7d-93ae-96bbac956354' );
define( 'CORE_HRE_MICROSOFT_SECRET', 		'32CcD._qL_~KBzj1bz~7oW145hW0m73.v9' );
define( 'CORE_HRE_MICROSOFT_RESOURCE', 		'https://graph.microsoft.com' );
define( 'CORE_HRE_MICROSOFT_REDIRECT_URI', 	 home_url() );
define( 'CORE_HRE_MICROSOFT_SCOPE',  		'profile User.Read.All' );
define( 'CORE_HRE_IB_REDIRECTION_URL',  	'https://haya.contigomas.com/signup' );
define( 'CORE_HRE_IB_ENCRYPT_PASSW',  		'1234567812345678' );
define( 'CORE_HRE_IB_ENCRYPT_METHOD',  		'aes-256-ecb' );

// Links
define( 'CORE_HRE_URL_APPS', 	   home_url() . '/aplicaciones/' );
define( 'CORE_HRE_URL_UTILITIES',  home_url() . '/utilidades/' );
define( 'CORE_HRE_URL_DIRECTORY',  home_url() . '/directorio/' );
define( 'CORE_HRE_URL_REPOSITORY', home_url() . '/documentacion/repositorio/' );

// Others
define( 'CORE_HRE_SESSION_LOG', true );

// Table list custom notifications
function core_init_notifications() {

	if( !class_exists( 'WP_List_Table' ) ) {
		require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
	}

	require_once( CORE_HRE_CLASSES . 'notifications-table.class.php' );
}

add_action( 'plugins_loaded', 'core_init_notifications' );

// Get current user data
function get_current_user_data() {

	if( is_user_logged_in() ) {

		$wp_user = wp_get_current_user();

		define( 'CORE_HRE_USER_ID', 		$wp_user->ID );
		define( 'CORE_HRE_USER_EMAIL', 		$wp_user->user_email );
		define( 'CORE_HRE_USER_NAME', 		$wp_user->first_name );
		define( 'CORE_HRE_USER_LAST_NAME', 	$wp_user->last_name );

	}
	
}

add_action( 'init', 'get_current_user_data' );

// Check Elementor Edit Mode
function is_elementor_edit_mode() {
	if ( \Elementor\Plugin::$instance->preview->is_preview_mode() ) {
		return true;
	} else {
		return false;
	}
}

// Custom Search Query
function order_search_by_posttype( $orderby, $query ){
    if ( $query->is_search() ) :

        global $wpdb;
        $orderby =
            "
            CASE WHEN {$wpdb->prefix}posts.post_type = 'apps' THEN '1' 
                 WHEN {$wpdb->prefix}posts.post_type = 'empleados' THEN '2' 
                 WHEN {$wpdb->prefix}posts.post_type = 'wpdmpro' THEN '3' 
                 WHEN {$wpdb->prefix}posts.post_type = 'utilities' THEN '4' 
                 WHEN {$wpdb->prefix}posts.post_type = 'cursos' THEN '5' 
                 WHEN {$wpdb->prefix}posts.post_type = 'vacantes' THEN '6' 
                 WHEN {$wpdb->prefix}posts.post_type = 'page' THEN '7' 
                 WHEN {$wpdb->prefix}posts.post_type = 'post' THEN '8' 
            ELSE {$wpdb->prefix}posts.post_type END ASC, 
            {$wpdb->prefix}posts.post_title ASC";

    endif;

    return $orderby;
}

add_filter( 'posts_orderby', 'order_search_by_posttype', 10, 2 );

// Enqueue Scripts
function core_hre_child_enqueue_scripts() {

	// jQuery UI Register
    wp_enqueue_style( 'jquery-ui', CORE_HRE_DIR_URL . '/styles/jquery-ui.min.css', array(), '1.0.0' );

    // jQuery UI Enqueue
    wp_enqueue_script( 'jquery-ui-autocomplete' );
    wp_enqueue_script( 'jquery-ui-sortable' );

    // Core HRE Scripts Register
	wp_enqueue_script( 'core-apps', 		 	CORE_HRE_DIR_URL . 'javascript/apps.js', 					array( 'jquery-ui-sortable' ), '1.0.0', true );
	wp_enqueue_script( 'core-directory', 		CORE_HRE_DIR_URL . 'javascript/directory.js', 				array( 'jquery-ui-autocomplete' ), '1.0.0', true );
	wp_enqueue_script( 'core-documentation', 	CORE_HRE_DIR_URL . 'javascript/documentation.js', 			array( 'jquery-ui-core' ), '1.0.0', true );
	wp_enqueue_script( 'core-popups', 			CORE_HRE_DIR_URL . 'javascript/pop-ups.js', 				array( 'jquery-ui-core' ), '1.0.0', true );
	wp_enqueue_script( 'core-utilities', 		CORE_HRE_DIR_URL . 'javascript/utilities.js', 				array( 'jquery-ui-sortable' ), '1.0.0', true );
	wp_enqueue_script( 'core-touch', 			CORE_HRE_DIR_URL . 'javascript/touch-punch.min.js', 		array( 'jquery-ui-core' ), '1.0.0', true );
	
}

add_action( 'wp_enqueue_scripts', 'core_hre_child_enqueue_scripts' );

// Shortcodes
require_once CORE_HRE_SHORTCODES . 'apps.php';
require_once CORE_HRE_SHORTCODES . 'directory.php';
require_once CORE_HRE_SHORTCODES . 'documentations.php';
require_once CORE_HRE_SHORTCODES . 'notifications.php';
require_once CORE_HRE_SHORTCODES . 'pop-ups.php';
require_once CORE_HRE_SHORTCODES . 'timechef.php';
require_once CORE_HRE_SHORTCODES . 'utilities.php';
require_once CORE_HRE_SHORTCODES . 'inspiring-benefits.php';
require_once CORE_HRE_SHORTCODES . 'box-current-user.php';
require_once CORE_HRE_SHORTCODES . 'courses.php';
require_once CORE_HRE_SHORTCODES . 'vacancies.php';

// Functions
require_once CORE_HRE_FUNCTIONS . 'critical_javascript.php';
require_once CORE_HRE_FUNCTIONS . 'custom_roles.php';
require_once CORE_HRE_FUNCTIONS . 'show_hide_apps.php';
require_once CORE_HRE_FUNCTIONS . 'reorder_apps.php';
require_once CORE_HRE_FUNCTIONS . 'audit_log.php';
require_once CORE_HRE_FUNCTIONS . 'redirect_external_links.php';
require_once CORE_HRE_FUNCTIONS . 'check_employee_by_user.php';