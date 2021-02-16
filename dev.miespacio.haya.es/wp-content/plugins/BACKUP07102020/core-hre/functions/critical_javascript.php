<?php 

function insert_critical_javascript() {
	
	?>
	<script type="text/javascript">
	(function($) {

		jQuery(document).ready(function() {

			jQuery( '#toggle-menu' ).click( function(e) {
				e.preventDefault();
				jQuery( '.aside-bar' ).addClass('open');
			} );

			jQuery( '#close-menu' ).click( function(e) {
				e.preventDefault();
				jQuery( '.aside-bar' ).removeClass('open');
			} );

			jQuery('.um-notification-shortcode .um-notification-header .um-notification-left').text('Avisos y lectura obligatoria');

		} );
		
	} (jQuery));
	</script>
	<?php 
	
}

add_action( 'wp_head', 'insert_critical_javascript' );