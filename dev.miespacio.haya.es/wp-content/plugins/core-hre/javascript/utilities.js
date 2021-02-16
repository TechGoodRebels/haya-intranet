(function($) {

	$(document).ready(function() {
		/**
		 * UTILITIES SORTABLE
		 * -------------------------------------------
		 */
		var contentSortable = $( '.row-utilities' );

		contentSortable.sortable({
			cursor: 'move',
			update: function( e, ui ) {
				reorderUtilitiesIndex();
			}
		});

    	contentSortable.disableSelection();
    	/**
		 * ACTION HIDE UTILITY
		 * -------------------------------------------
		 */
		$('.btn-hide-utility').click(function() {

			var parent    = $(this).parents('.col-utilities');
			var appID 	  = $(this).attr('data-id');
			var actionAPP = $(this).attr('data-type');

			$.ajax({
	           type : 'post',
	           url :  '/wp-admin/admin-ajax.php', 
	           data : {
	               action 	  : 'show_hide_apps',
	               app_type   : 'utility',
	               app_id 	  :  appID,
	               app_action :  actionAPP
	           },
	           error: function( response ){
	               console.log( response );
	           },
	           success: function( response ) {
	           		if( response.type == 'Success' ) {
	               		parent.addClass('hidden-utility');

	               		if( actionAPP == 'hide' ) {
	               			$('.link_hide_utilities').html('<a href="/utilidades/ocultas/" class="btn-app btn-link-hide-apps" role="button"><i aria-hidden="true" class="far fa-eye-slash"></i> VER MIS UTILIDADES OCULTAS</a>');
	               		}
	           		} else {
	           			alert('Ha ocurrido un error inesperado. Si continua, por favor, contacte con el administrador.');
	           		}
	               console.log( response.message );
	           }
	       });

		});
		/**
		 * ACTION UTILITY POPUP
		 * -------------------------------------------
		 */
		$('.utility-popup').click(function(){
			
			var parent 		= $(this).parents('.card-utility-wrapper');
			var dataUtility = parent.find('.data-utility');
			var title 		= parent.find('.title-utility').text();
			var content 	= dataUtility.attr('data-content');
			var link        = dataUtility.attr('data-link');
			var txtmanual   = dataUtility.attr('data-txtmanual');
			var manual    	= dataUtility.attr('data-manual');

			$('.popup-utilities .title-popup-utilities').text(title);
			$('.popup-utilities .content-popup-utilities').html(content);
			if( manual.length > 0 ) {
				$('.popup-utilities .link-manual-popup-utilities a').attr('href', manual);
				if( txtmanual.length > 0 ) {
					$('.popup-utilities .link-manual-popup-utilities a').text(txtmanual);
				} else {
					$('.popup-utilities .link-manual-popup-utilities a').text('Descargar guía de uso aquí');
				}
			}

			if( link.length > 0 ) {
				$('.popup-utilities .btn-link-app').removeClass('hide').attr('href', link);
				$('.popup-utilities .btn-link-app span').text(title.toUpperCase());
			}
			else {
				$('.popup-utilities .btn-link-app').addClass('hide');
			}

			$('.overlay-utilities').addClass('active');

		});
		/**
		 * ACTION UTILITY POPUP MENU
		 * -------------------------------------------
		 */
		$('.utility-popup-menu').click(function(){
			
			var parent 		= $(this);
			var dataUtility = parent.find('.data-utility-menu');
			var title 		= parent.find('.title-utility-menu').text();
			var content 	= dataUtility.attr('data-content');
			var link        = dataUtility.attr('data-link');
			var txtmanual   = dataUtility.attr('data-txtmanual');
			var manual    	= dataUtility.attr('data-manual');

			$('.popup-utilities .title-popup-utilities').text(title);
			$('.popup-utilities .content-popup-utilities').html(content);
			if( manual.length > 0 ) {
				$('.popup-utilities .link-manual-popup-utilities a').attr('href', manual);
				if( txtmanual.length > 0 ) {
					$('.popup-utilities .link-manual-popup-utilities a').text(txtmanual);
				} else {
					$('.popup-utilities .link-manual-popup-utilities a').text('Descargar guía de uso aquí');
				}
			}

			if( link.length > 0 ) {
				$('.popup-utilities .btn-link-app').removeClass('hide').attr('href', link);
				$('.popup-utilities .btn-link-app span').text(title.toUpperCase());
			}
			else {
				$('.popup-utilities .btn-link-app').addClass('hide');
			}

			$('.overlay-utilities').addClass('active');

		});


	} );
	
} (jQuery));

function reorderUtilitiesIndex() {
	var colsApp  = jQuery('.col-utilities');
	var arrayIDs = new Array();

	if( colsApp.length > 0 ) {

		colsApp.each( function(){
			var i = jQuery(this).attr('data-appid');
			arrayIDs.push(i);
		});

		var stringIDs = arrayIDs.join();

		jQuery.ajax({
           type : 'post',
           url  : '/wp-admin/admin-ajax.php', 
           data : {
               action 	  : 'reorder_apps_action',
               app_type   : 'utility',
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
}