(function($) {

	$(document).ready(function() {

		if( $('#repository').length > 0 ) {
			$('#repository .panel-footer input[name=skw]').attr('Placeholder', 'Buscar por...');
			$('#repository .panel-footer button[type=submit]').text('Aplicar filtros');
		}

		$('.tree-repository .submenu-handler').click( function(e){
			e.preventDefault();
			var liParent  = $(this).parents('li');
			var idHandler = liParent.attr('id');
			var subMenu   = $('.sub-menu-repository.'+idHandler).slideToggle(500);
		});

	} );
	
} (jQuery));