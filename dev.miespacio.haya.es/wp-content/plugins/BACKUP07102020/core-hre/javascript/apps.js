(function($) {

	$(document).ready(function() {

		var contentSortable = $( '.parent-apps .row-apps' );

		contentSortable.sortable({
			cursor: 'move',
			stop: function( e, ui ) {
				reorderAppsIndex();
			}
		});
		
    	contentSortable.disableSelection();

		$('.btn-hide-app').click(function(){

			var parent    = $(this).parents('.col-apps');
			var appID	  = $(this).attr('data-id');
			var actionAPP = $(this).attr('data-type');

			$.ajax({
	           type : 'post',
	           url :  '/wp-admin/admin-ajax.php', 
	           data : {
	               action 	  : 'show_hide_apps',
	               app_type   : 'app',
	               app_id 	  :  appID,
	               app_action :  actionAPP
	           },
	           error: function( response ){
	               console.log( response );
	           },
	           success: function( response ) {
	           		if( response.type == 'Success' ) {
	               		parent.addClass('hidden-app');

	               		if( actionAPP == 'hide' ) {
	               			$('.link_hide_apps').html('<a href="/aplicaciones/ocultas/" class="btn-app btn-link-hide-apps" role="button"><i aria-hidden="true" class="far fa-eye-slash"></i> VER MIS APLICACIONES OCULTAS</a>');
	               		}
	           		} else {
	           			alert('Ha ocurrido un error inesperado. Si continua, por favor, contacte con el administrador.');
	           		}
	               console.log( response.message );
	           }
	       });

		});

		$('.btn-popup-help').click(function(){
			
			var pathTicketing = $(this).attr('data-ticketing');
			var linkTicketing = $(this).attr('data-url');

			$('.popup-apps .content-popup-apps').html(pathTicketing);

			if( linkTicketing.length > 0 ) {
				$('.popup-apps .btn-link-app').removeClass('hide').attr('href', linkTicketing);
			}
			else {
				$('.popup-apps .btn-link-app').addClass('hide');
			}

			$('.overlay-apps').addClass('active');

		});

		$('.btn-link-manual').click(function(){
			
			var data    = $(this).attr('data-manuals');
			var manuals = data.split(';');
			var content = '<ul class="list-manuals">';

			console.log(manuals);

			$.each( manuals, function(i, val){
				if( val.length > 0 ) {
					var item = val.split(',');

					content += '<li>';
					content += '<a href="'+item[1]+'" target="_blank">'+item[0]+' <i class="fas fa-download"></i></a>';
					content += '</li>';
				}
			});

			content += '</ul>';

			$('.popup-apps .content-popup-apps').html(content);
			$('.popup-apps .btn-link-app').addClass('hide');

			$('.overlay-apps').addClass('active');

		});

	} );
	
} (jQuery));

function reorderAppsIndex() {
	var colsApp  = jQuery('.col-apps');
	var arrayIDs = new Array();

	if( colsApp.length > 0 ) {

		colsApp.each( function(){
			var i = jQuery(this).attr('data-appid');
			arrayIDs.push(i);
		});

		var stringIDs = arrayIDs.join();

		jQuery.ajax({
           type : 'post',
           url :  '/wp-admin/admin-ajax.php', 
           data : {
               action 	  : 'reorder_apps_action',
               app_type   : 'app',
               app_ids 	  :  stringIDs
           },
           error: function( response ){
               console.log( response );
           },
           success: function( response ) {
           		if( response.type != 'Success' ) {
           			alert('Ha ocurrido un error inesperado. Si continua, por favor, contacte con el administrador.');
           		}
               console.log( response.message );
           }
       });
		
	}

	console.log(stringIDs);
}