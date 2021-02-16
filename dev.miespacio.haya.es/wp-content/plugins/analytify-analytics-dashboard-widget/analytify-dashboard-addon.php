<?php

if ( ! class_exists( 'Analytify_Dashboard_Addon' ) ) {

	class Analytify_Dashboard_Addon {

		public function __construct() {

			if ( ! $this->is_access() ) { return; }

			add_action( 'wp_dashboard_setup', array( $this, 'add_analytify_widget' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'pa_dashboard_script' ) );


			if ( $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' ) != '' ) {
				add_action( 'wp_ajax_analytify_dashboard_addon', array( $this, 'analytify_general_stats' ) );

			}
		}

		function pa_dashboard_script() {
			wp_enqueue_script( 'analytify-dashboard-addon', plugins_url( '/assets/js/wp-analytify-dashboard.js', __FILE__ ), false, ANALYTIFY_DASHBOARD_VERSION );
			wp_localize_script( 'analytify-dashboard-addon', 'analytify_dashboard_widget', array(
				'get_stats_nonce' => wp_create_nonce( 'analytify-dashboard-widget-get-stats' ),
				'pro_active' => class_exists( 'WP_Analytify_Pro_Base' ) ? 'active' : 'inactive'
			) );
		}


		public function add_analytify_widget() {
			global $wp_meta_boxes;

			wp_add_dashboard_widget( 'analytify-dashboard-addon', __( 'Google Analytics Dashboard By Analytify', 'analytify-analytics-dashboard-widget' ), array( $this, 'wpa_general_dashboard_area' ), null , null );

			// Place the widget at the top.
			$normal_dashboard = $wp_meta_boxes['dashboard']['normal']['core'];		
			$widget_instance  = array( 'analytify-dashboard-addon' => $normal_dashboard[ 'analytify-dashboard-addon' ] );
			unset( $normal_dashboard[ 'analytify-dashboard-addon' ] );
			$sorted_dashboard                             = array_merge( $widget_instance, $normal_dashboard );
			$wp_meta_boxes['dashboard']['normal']['core'] = $sorted_dashboard;
		}

		/**
		* Create Widget Container.
		*
		* @since 1.0.0
		*/
		public function wpa_general_dashboard_area( $var, $dashboard_id ) {

			$acces_token  = get_option( 'post_analytics_token' );
			if ( isset( $acces_token ) && ! empty( $acces_token ) && get_option( 'pa_google_token' ) ) {

				$previous_date = get_option( 'analytify_dashboard_widget_date' );
				// if previous date is stored.
				if ( isset( $previous_date[0] ) && isset( $previous_date[1] ) ) {
					$__previous = true; // testing code
					$s_date = $previous_date[0];
					$ed_date = $previous_date[1];
				} else {
					$__previous = false; // testing code
					$start_date_val = strtotime( '- 7 days' );
					$end_date_val   = strtotime( 'now' );
					$s_date         = date( 'Y-m-d', $start_date_val );
					$ed_date        = date( 'Y-m-d', $end_date_val );
				}

				if ( $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'profile_for_dashboard', 'wp-analytify-profile', '' ) != '' ) {
					?>
					<div class="analytify_wraper">
						<div class="analytify-activation-card-header">
							<img src="<?php echo plugins_url( 'assets/images/analytify-logo-135x24.png', __FILE__ ); ?>">
						</div>

						<form id="analytify_dashboard" name="analytify_dashboard" method="POST" class="analytify-widget-form">
							<div class="analytify_main_setting_bar">
								<div class="analytify_setting">
									<div class="analytify_select_date">
										<form class="analytify_form_date" action="" method="post">
											<div class="analytify_select_date_fields">
												<input type="hidden" name="st_date" id="analytify_start_val">
												<input type="hidden" name="ed_date" id="analytify_end_val">

												<input type="hidden" name="analytify_date_start" id="analytify_date_start" value="<?php echo isset( $s_date ) ? $s_date : '' ?>">
												<input type="hidden" name="analytify_date_end" id="analytify_date_end" value="<?php echo isset( $ed_date ) ? $ed_date : '' ?>">

												<label for="analytify_start"><?php analytify_e( 'From:', 'wp-analytify' ) ?></label>
												<input type="text" id="analytify_start" value="">
												<label for="analytify_end"><?php analytify_e( 'To:', 'wp-analytify' ) ?></label>
												<input type="text" id="analytify_end" value="">

												<div class="analytify_arrow_date_picker"></div>												
											</div>
											<div class="analytify-dashboard-stats-opts">
												<input type="submit" value="<?php _e( 'View Stats', 'analytify-analytics-dashboard-widget' ) ?>" name="view_data" class="analytify_submit_date_btn">
												<select  id="analytify_dashboard_stats_type">
													<option value="general-statistics"><?php analytify_e( 'General Statistics', 'wp-analytify' ) ?></option>
													<option value="real-time-statistics"><?php analytify_e( 'Real-Time', 'wp-analytify' ) ?></option>
													<option value="top-pages-by-views"><?php _e( 'Top pages', 'analytify-analytics-dashboard-widget' ) ?></option>
													<option value="top-countries"><?php _e( 'Top Countries', 'analytify-analytics-dashboard-widget' ) ?></option>
													<option value="top-cities"><?php _e( 'Top Cities', 'analytify-analytics-dashboard-widget' ) ?></option>
													<option value="keywords"><?php _e( 'Keywords', 'analytify-analytics-dashboard-widget' ) ?></option>
													<option value="social-media"><?php analytify_e( 'Social Media', 'wp-analytify' ) ?></option>
													<option value="top-reffers"><?php analytify_e( 'Top Referrers', 'wp-analytify' ) ?></option>
												</select>
											</div>
											<?php echo WPANALYTIFY_Utils::get_date_list() ?>
										</form>
									</div>
								</div>
							</div>
						<div class="analytify-dashboard-inner"></div>
						<?php echo $this->dashboard_footer(); ?>
					</div>
					<?php
				} else { 
					echo $this->exception_message( 'To see your website stats, please select the profile.' );
				}
			} else { 
				echo $this->exception_message( 'To see your website stats, please connect Analytify to Google Analytics.' );
		 	}
		}

		/**
		* Exception message.
		*
		*/
		function exception_message( $message ) {
			$html = '
			<div class="analytify-activation-cards">
				<div class="analytify-activation-card-header">
						<img src="' . plugins_url( 'assets/images/analytify-logo-135x24.png', __FILE__ ) . '">
				</div>
				<div class="analytify-activation-card-body">
						<p>'. __( 'Connect Analytify to your Google Analytics account to setup Analytics for your website.', 'analytify-analytics-dashboard-widget' ) . '</p>
						<a href="'. menu_page_url( 'analytify-settings', false ) . '" class="anaytity-active-card-button">'. __( 'Configure Analytify', 'analytify-analytics-dashboard-widget' ) . '</a>
				</div>
			</div>';

			// $html = '
			// <div id="analytify_widget_connect"  style="display: block;">
			// 	<h2 class="analytify-widget-logo"><span>'. __( 'Analytify', 'analytify-analytics-dashboard-widget' ) . '</span></h2>
			// 	<div class="analytify-no-authed">
			// 		<h2>'. __( 'Analytify is not Setup', 'analytify-analytics-dashboard-widget' ) . '</h2>
			// 		<p>'. __( $message, 'analytify-analytics-dashboard-widget' ) . '</p>
			// 		<a href="'. menu_page_url( 'analytify-settings', false ) . '" class="analytify-wgt-btn">'. __( 'Configure Analytify', 'analytify-analytics-dashboard-widget' ) . '</a>
			// 	</div>
			// </div>';

			return $html;
		}

		/**
		* Runs on Every Ajax.
		*
		* @since 1.0.0
		*/
		public static function analytify_general_stats() {

			check_ajax_referer( 'analytify-dashboard-widget-get-stats', 'nonce' );

			$start_date_val	= strtotime( '- 7 days' );
			$end_date_val	= strtotime( 'now' );
			$start_date		= isset( $_POST['startDate'] ) ? sanitize_text_field( wp_unslash( $_POST['startDate'] ) ) : date( 'Y-m-d', $start_date_val );
			$end_date		= isset( $_POST['endDate'] ) ? sanitize_text_field( wp_unslash( $_POST['endDate'] ) ) : date( 'Y-m-d', $end_date_val );
			$stats_type		= isset( $_POST['stats_type'] ) ? sanitize_text_field( wp_unslash( $_POST['stats_type'] ) ) : 'general-statistics';
			$wp_analytify	= $GLOBALS['WP_ANALYTIFY'];

			update_option( 'analytify_dashboard_widget_date', array( $start_date, $end_date ), false );

			$acces_token  = get_option( 'post_analytics_token' );
			if ( $acces_token ) {
				?>
				<div class="analytify_wraper">
					<div id="inner_analytify_dashboard">
						<?php
						$_analytify_profile = get_option( 'wp-analytify-profile' );
						$dashboard_profile_id = $_analytify_profile['profile_for_dashboard'];

						if ( 'general-statistics' === $stats_type ) {

							$stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions,ga:bounces,ga:newUsers,ga:entrances,ga:pageviews,ga:sessionDuration,ga:avgTimeOnPage,ga:users', $start_date, $end_date, false, false, false, false, 'widget-show-overall-dashboard' );

							// New vs Returning Users
							$new_returning_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:userType', false, false, false, 'widget-show-default-new-returning-dashboard' );

							if ( $stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/general-stats.php';
								pa_include_general( $wp_analytify, $stats, $new_returning_stats );
							}

						} else if( 'top-pages-by-views' === $stats_type ) {

							$top_page_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:pageviews', $start_date, $end_date, 'ga:PageTitle', '-ga:pageviews', 'ga:pageTitle!=(not set)', 50, 'widget-show-top-pages-dashboard' );

							if ( $top_page_stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-pages-stats.php';
								pa_include_top_pages_stats( $wp_analytify, $top_page_stats );
							}

						} else if( 'top-countries' === $stats_type ){

							$top_countries_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:country' , '-ga:sessions' , 'ga:country!=(not set)', 50, 'widget-show-top-countries-dashboard' );

							if ( $top_countries_stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-countries-stats.php';
								pa_include_countries_pages_stats( $wp_analytify, $top_countries_stats );
							}

						} else if( 'top-cities' === $stats_type ){

							$top_cities_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date , 'ga:city' , '-ga:sessions' , 'ga:city!=(not set)', 50, 'widget-show-top-cities-dashboard' );

							if ( $top_cities_stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-cities-stats.php';
								pa_include_cities_stats( $wp_analytify, $top_cities_stats );
							}

						} else if( 'keywords'=== $stats_type ) {

							$top_keywords_stats = $wp_analytify->pa_get_analytics_dashboard(  'ga:sessions', $start_date, $end_date, 'ga:keyword', '-ga:sessions', false, 50, 'widget-show-top-keywords-dashboard' );

							if ( $top_keywords_stats  ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-keywords-stats.php';
								pa_include_keywords_stats( $wp_analytify, $top_keywords_stats );
							}


						}	else if( 'social-media' === $stats_type ) {

							$top_socialmedia_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:socialNetwork', '-ga:sessions', 'ga:socialNetwork!=(not set)' , 50, 'widget-show-top-socialmedia-dashboard' );

							if ( $top_socialmedia_stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-socialmedia-stats.php';
								pa_include_socialmedia_stats( $wp_analytify, $top_socialmedia_stats );
							}

						} else if( 'top-reffers' === $stats_type ) {

							$top_reffers_stats = $wp_analytify->pa_get_analytics_dashboard( 'ga:sessions', $start_date, $end_date, 'ga:source,ga:medium', '-ga:sessions', false, 50, 'widget-show-top-reffers-dashboard' );

							if ( $top_reffers_stats ) {
								include ANALYTIFY_DASHBOARD_ROOT_PATH . '/views/admin/top-reffers-stats.php';
								pa_include_reffers_stats( $wp_analytify, $top_reffers_stats );
							}

						} else if( class_exists( 'WP_Analytify_Pro_Base' ) && 'real-time-statistics' === $stats_type ) {
							$wp_analytify = $GLOBALS['WP_ANALYTIFY'];
							include ANALYTIFY_PRO_ROOT_PATH . '/views/widget/realtime-stats.php';
							pa_include_realtime_stats( $wp_analytify );
						}

						?>
					</div>
				</div>
				<?php
			}
			wp_die();
		}

		function dashboard_footer() {
			global $submenu;

			$html = '<div class="analytify-dashboard-widget-footer">';
			$dashboard_url = get_admin_url( null, 'admin.php?page=analytify-dashboard' );
			
			$html .= '<a href="' . esc_url( 'https://analytify.io/documentation?utm_source=analytify-lite&utm_medium=dashboard-widget&utm_content=addons&utm_campaign=awareness' ) . '" target="_blank">' . __( 'Documentation ', 'analytify-analytics-dashboard-widget' ) . ' <span class="dashicons dashicons-lightbulb"></span></a>';

			if ( class_exists( 'WP_Analytify_Pro_Base' ) ) {
				$html .= ' | <a href="' . esc_url( 'https://analytify.io/add-ons?utm_source=analytify-lite&utm_medium=dashboard-widget&utm_content=addons&utm_campaign=addons-upgrade' ) . '" target="_blank">' . __( 'Addons', 'analytify-analytics-dashboard-widget' ) . ' <span class="dashicons dashicons-networking"></span></a>';
			} else {
				$html .= ' | <a href="' . esc_url( 'https://analytify.io/pricing?utm_source=analytify-lite&utm_medium=dashboard-widget&utm_content=go-pro&utm_campaign=pro-upgrade' ) . '" class="analytify-dashboard-widget-go-pro" target="_blank">' . __( 'Go Pro', 'analytify-analytics-dashboard-widget' ) . ' <span class="dashicons dashicons-cart"></span></a>';
			}
			
			$html .= ' | <a href="' . esc_html( $dashboard_url ) . '">' . __( 'View Dashboard', 'analytify-analytics-dashboard-widget' ) . ' <span class="dashicons dashicons-chart-bar"></span></a>';
			
			$html .= '</div>';

			return $html;
		}

		/**
		 * Check is user have access to check deshboard.
		 * @return boolean
		 *
		 * @since 1.0.5
		 */
		function is_access() {
			$is_access_level = $GLOBALS['WP_ANALYTIFY']->settings->get_option( 'show_analytics_roles_dashboard','wp-analytify-dashboard', array( 'administrator' ) );

			return	$GLOBALS['WP_ANALYTIFY']->pa_check_roles( $is_access_level );
		}

	} // End of class.
}


/**
* Helper function for translation.
*/
if ( ! function_exists( 'analytify__' ) ) {
	/**
	* Wrapper for __() gettext function.
	* @param  string $string     Translatable text string
	* @param  string $textdomain Text domain, default: wp-analytify
	* @return void
	*/
	function analytify__( $string, $textdomain = 'wp-analytify' ) {
		return __( $string, $textdomain );
	}
}

if ( ! function_exists( 'analytify_e' ) ) {
	/**
	* Wrapper for _e() gettext function.
	* @param  string $string     Translatable text string
	* @param  string $textdomain Text domain, default: wp-analytify
	* @return void
	*/
	function analytify_e( $string, $textdomain = 'wp-analytify' ) {
		echo __( $string, $textdomain );
	}
}
