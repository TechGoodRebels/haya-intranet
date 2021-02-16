(function($) {

	$(document).ready(function() {

		$('.popup .close-popup').click(function(e){

			e.preventDefault();
			var parent = $(this).parents('.overlay-popups');

			parent.removeClass('active');

			if( parent.hasClass('overlay-directory') ) {
				parent.find('.popup-directory .title-directory-employee').text('');
				parent.find('.popup-directory .bg-image-directory').css( 'background-image', 'url(/wp-content/uploads/2020/08/default-user-img.jpg)');
				parent.find('.popup-directory .departament-directory-employee span').text('');
				parent.find('.popup-directory .email-directory-employee span').text('');
				parent.find('.popup-directory .tel-directory-employee span').text('');
				parent.find('.popup-directory .office-directory-employee span').text('');
				parent.find('.popup-directory .ext-directory-employee span').text('');
				parent.find('.popup-directory .teams-directory-employee span a').attr('href', '');
			} else if( parent.hasClass('overlay-apps') ) {
				parent.find('.popup-apps .content-popup-apps').text('');
				parent.find('.popup-apps .btn-link-app').addClass('hide');
			} else if( parent.hasClass('overlay-utilities') ) {
				parent.find('.popup-utilities .title-popup-utilities').text('');
				parent.find('.popup-utilities .content-popup-utilities').html('');
				parent.find('.popup-utilities .link-manual-popup-utilities a').text('').attr('href', '#');
				parent.find('.popup-utilities .btn-link-app').addClass('hide');
				parent.find('.popup-utilities .btn-link-app span').text('');
			}

		});

	} );
	
} (jQuery));