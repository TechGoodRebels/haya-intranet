jQuery(document).ready(function($) {

	if ($("#analytify-dashboard-addon-hide").is(':checked')) {
		ajax_request();
	}

	$("#analytify-dashboard-addon-hide").on('click', function(event) {
		if ($(this).is(':checked')) {
			ajax_request();
		}
	});

	$(".analytify-widget-form").on('submit', function(event) {
		event.preventDefault();
		ajax_request();
	});
	
	function ajax_request() {

		var s_date = $("#analytify_date_start").val();
		var en_date = $("#analytify_date_end").val();
		var stats_type = $("#analytify_dashboard_stats_type").val();
		
		if ( 'inactive' === analytify_dashboard_widget.pro_active && 'real-time-statistics' === $("#analytify_dashboard_stats_type").val() ) {
			event.stopPropagation();
			return false;
		}

		jQuery.ajax({
			url: ajaxurl,
			type: 'post',
			data: {
				action    : 'analytify_dashboard_addon',
				startDate : s_date,
				endDate   : en_date,
				stats_type: stats_type,
				nonce     : analytify_dashboard_widget.get_stats_nonce
			},
			beforeSend: function() {
				$("#inner_analytify_dashboard").addClass('stats_loading');
			},
			success: function(response) {
				// $("#analytify_dashboard").next().remove();
				$('.analytify-dashboard-inner').html(response);
				// $(response).insertAfter("#analytify_dashboard");
				$("#inner_analytify_dashboard").removeClass('stats_loading');
				wp_analytify_paginated();
			}
		});
	}
	
	if ( 'inactive' === analytify_dashboard_widget.pro_active ) {
		$(document).on('change', '#analytify_dashboard_stats_type', function(e) {		
			if('real-time-statistics' === $(this).val()) {
				$('#analytify-dashboard-addon .analytify-dashboard-inner div:nth-child(1)').hide();
				$('<div class="analytify-dashboard-promo"><div class="analytify_visitors_online analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-online">12</div> <div class="analytify_label">Visitors online</div> </div> <div class="analytify_referral analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-referral">99</div> <div class="analytify_label">Referral</div> </div> <div class="analytify_organic analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-organic">110</div> <div class="analytify_label">ORGANIC</div> </div> <div class="analytify_social analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-social">117</div> <div class="analytify_label">social</div> </div> <div class="analytify_direct analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-direct">47</div> <div class="analytify_label">direct</div> </div> <div class="analytify_new analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-new">31</div> <div class="analytify_label">new</div> </div> <div class="analytify_returning analytify_realtime_status_boxes"> <div class="analytify_general_stats_value" id="pa-returning">5</div> <div class="analytify_label">Returning</div> </div> <a class="analytify_general_stats_btn" href="https://analytify.io/pricing/?utm_source=analytify-lite&utm_medium=dashboard-widget&utm_content=realtime-promo&utm_campaign=pro-upgrade" target="_blank">Upgrade to Analytify Pro to Unlock RealTime Analytics in WordPress.</a></div>').insertAfter('#analytify-dashboard-addon .analytify-dashboard-inner');
			} else {
				$('#analytify-dashboard-addon .analytify-dashboard-inner div').show();
				$('.analytify-dashboard-promo').remove();
			}
		});
	}
	
});