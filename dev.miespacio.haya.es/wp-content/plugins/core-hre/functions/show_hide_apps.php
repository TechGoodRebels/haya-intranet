<?php 

function show_hide_apps_action(){
	
	$user_id     = CORE_HRE_USER_ID;

	$app_id 	 = $_POST['app_id'] ?? false;
	$type 		 = $_POST['app_type'] ?? false;
	$showHide 	 = $_POST['app_action'] ?? false;

	if( $type == 'app' ) {
		$metaKey = '_hide_apps_by_'.$user_id;
	} else {
		$metaKey = '_hide_utilities_by_'.$user_id;
	}

	$metaUserApp = get_user_meta( $user_id, $metaKey, true );

	if( $app_id && $type ) {

		if( $showHide == 'hide' ) {

			$parseMetaValue = $app_id;

			if( !empty( $metaUserApp ) ) {
				$arrayApps = explode( ',', $metaUserApp );
				array_push( $arrayApps, $app_id );

				$parseMetaValue = implode( ',', $arrayApps );
			} 

			update_user_meta( $user_id, $metaKey, $parseMetaValue );

		} else {
			$arrayApps = explode( ',', $metaUserApp );
			$keyID     = array_search( $app_id, $arrayApps );

			unset( $arrayApps[$keyID] );

			if( count($arrayApps) > 0 ) {
				$parseMetaValue = implode( ',', $arrayApps );
				update_user_meta( $user_id, $metaKey, $parseMetaValue );
			} else {
				delete_user_meta( $user_id, $metaKey );
			}
		}

		wp_send_json( array( 
			'type'	  => 'Success',
			'message' => __( 'Aplicación mostrada/oculta' ) 
		) );

	} else {
		wp_send_json( array( 
			'type'	  => 'Error',
			'message' => __( 'No se ha pasado ID de app o tipo' ) 
		) );
	}

}

add_action( 'wp_ajax_show_hide_apps', 'show_hide_apps_action' );