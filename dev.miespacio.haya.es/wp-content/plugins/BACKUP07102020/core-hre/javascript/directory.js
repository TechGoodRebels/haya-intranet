(function($) {

	$(document).ready(function() {

		$('.empleado-popup').click(function(){
			
			var parent 		 = $(this).parents('.directory-card');
			var image 		 = parent.find('.directory-image-bg').attr('data-bg');
			var dataEmployee = parent.find('.data-directory');
			var name         = dataEmployee.attr('data-fullname') != '' ? dataEmployee.attr('data-fullname') : '-';
			var email        = dataEmployee.attr('data-email') != '' ? dataEmployee.attr('data-email') : '-';
			var tel    	     = dataEmployee.attr('data-tel') != '' ? dataEmployee.attr('data-tel') : '-';
			var office       = dataEmployee.attr('data-office') != '' ? dataEmployee.attr('data-office') : '-';
			var ext          = dataEmployee.attr('data-ext') != '' ? dataEmployee.attr('data-ext') : '-';
			var departament  = dataEmployee.attr('data-departament') != '' ? dataEmployee.attr('data-departament') : '-';
			var teams 	     = dataEmployee.attr('data-teams') != '' ? dataEmployee.attr('data-teams') : '-';

			$('.popup-directory .title-directory-employee').text(name);
			$('.popup-directory .bg-image-directory').css( 'background-image', 'url('+image+')');
			$('.popup-directory .email-directory-employee span').text(email);
			$('.popup-directory .tel-directory-employee span').text(tel);
			$('.popup-directory .office-directory-employee span').text(office);
			$('.popup-directory .ext-directory-employee span').text(ext);
			$('.popup-directory .departament-directory-employee span').text(departament);
			$('.popup-directory .teams-directory-employee span a').attr('href', teams);

			$('.overlay-directory').addClass('active');

		});

		if( tokenMG !== false && typeof emailsDirectory !== 'undefined' && emailsDirectory.length > 0 ) {
			$.each( emailsDirectory, function( key, val ){
				getAzureUserProfilePic( tokenMG, val );
			});
		}

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