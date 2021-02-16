<?php 

function reorder_apps_action(){
	
	$user_id     = CORE_HRE_USER_ID;

	$app_ids 	 = $_POST['app_ids'] ?? false;
	$type 		 = $_POST['app_type'] ?? false;

	if( $type == 'app' ) {
		$metaKey = '_reorder_apps_by_'.$user_id;
	} else {
		$metaKey = '_reorder_utilities_by_'.$user_id;
	}

	$metaUserApp = get_user_meta( $user_id, $metaKey, true );

	if( $app_ids && $type ) {

		update_user_meta( $user_id, $metaKey, $app_ids );

		wp_send_json( array( 
			'type'	  => 'Success',
			'message' => __( 'Aplicaciones reordenadas con éxito' ) 
		) );

	} else {
		wp_send_json( array( 
			'type'	  => 'Error',
			'message' => __( 'No se han pasado apps como parámetro' ) 
		) );
	}

}

add_action( 'wp_ajax_reorder_apps_action', 'reorder_apps_action' );