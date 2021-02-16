(function($) {

	$(document).ready(function() {
		/**
		 * ACTION EMPLOYEE POPUP
		 * -------------------------------------------
		 */
		$('.empleado-popup').click(function(){
			
			var parent 		 = $(this).parents('.directory-card');
			var image 		 = parent.find('.directory-image-bg').attr('data-bg');
			var dataEmployee = parent.find('.data-directory');
			var name         = dataEmployee.attr('data-fullname') != '' ? dataEmployee.attr('data-fullname') : '-';
			var title        = dataEmployee.attr('data-title') != '' ? dataEmployee.attr('data-title') : '';
			var email        = dataEmployee.attr('data-email') != '' ? dataEmployee.attr('data-email') : '-';
			var tel    	     = dataEmployee.attr('data-tel') != '' ? dataEmployee.attr('data-tel') : '-';
			var office       = dataEmployee.attr('data-office') != '' ? dataEmployee.attr('data-office') : '-';
			var ext          = dataEmployee.attr('data-ext') != '' ? dataEmployee.attr('data-ext') : '-';
			var department   = dataEmployee.attr('data-nivel1') != '' ? dataEmployee.attr('data-nivel1') : dataEmployee.attr('data-department');
			var teams 	     = dataEmployee.attr('data-teams') != '' ? dataEmployee.attr('data-teams') : '-';

			for( var i = 2; i <= 5; i++ ) {
				if( dataEmployee.attr('data-nivel'+i ) != '' ) {
					department += '<br><small>'+dataEmployee.attr('data-nivel'+i )+'</small>';
				}
			}

			$('.popup-directory .title-directory-employee').text(name);
			$('.popup-directory .subtitle-directory-employee').text(title);
			$('.popup-directory .bg-image-directory').css( 'background-image', 'url('+image+')');
			$('.popup-directory .email-directory-employee span').text(email);
			$('.popup-directory .tel-directory-employee span').text(tel);
			$('.popup-directory .office-directory-employee span').text(office);
			$('.popup-directory .ext-directory-employee span').text(ext);
			$('.popup-directory .departament-directory-employee span').html(department);
			$('.popup-directory .teams-directory-employee span a').attr('href', teams);

			if( department == '' ){ 
				$('.popup-directory .departament-directory-employee').addClass('hide');
			}

			$('.overlay-directory').addClass('active');

		});

		/**
		 * GET PHOTOS BY MICROSOFT GRAPH JS
		 * -------------------------------------------
		 */
		if( typeof tokenMG !== 'undefined' ) {
			if( tokenMG !== false && typeof emailsDirectory !== 'undefined' && emailsDirectory.length > 0 ) {
				$.each( emailsDirectory, function( key, val ){
					getAzureUserProfilePic( tokenMG, val );
				});
			}
		}

		/**
		 * AUTOCOMPLETE SEARCH BOX DIRECTORY
		 * -------------------------------------------
		 */
		if( typeof autoCompleteDirectory !== 'undefined' ) {

			$.ui.autocomplete.prototype._renderItem = function( ul, item ) {
	          	var term = this.term.split( ' ' ).join( '|' );
	          	var re = new RegExp( "(" + term + ")", "gi" ) ;
	          	var t = item.label.replace( re, "<strong>$1</strong>" );
	          	return $( "<li></li>" )
	             	.data( "item.autocomplete", item )
	             	.append( "<a>" + t + "</a>" )
	             	.appendTo( ul );
	        }; 

			$( '#directory-active-form .input-search-directory' ).autocomplete({
				source: autoCompleteDirectory,
				limit: 20
			});
		}

	} );
	
} (jQuery));