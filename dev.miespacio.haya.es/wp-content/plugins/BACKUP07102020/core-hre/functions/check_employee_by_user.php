<?php 

function check_employee_by_user( $auth_cookie, $expire, $expiration, $user_id, $scheme, $token ) {
	$wp_current_user = get_userdata( $user_id );
	
	// Variables del servidor LDAP
    $ldaphost       = CORE_HRE_LDAP_HOST;   // Servidor LDAP
    $username       = CORE_HRE_LDAP_USER; 	// Usuario maestro
    $userpassword   = CORE_HRE_LDAP_PASSW;  // Password
    $basedn         = CORE_HRE_LDAP_BASEDN; // Base DN
    $searchAttr     = array( 
    	'givenname', 
    	'sn', 
    	'mail', 
    	'telephonenumber', 
    	'mobile', 
    	'department', 
    	'title', 
    	'hayaoficina',
    	'memberof'
    );

    $filter = '(&(objectCategory=Person)(mail='.$wp_current_user->user_mail.')(memberOf:1.2.840.113556.1.4.1941:=CN=servicio_vpn_empleados,OU=Servicios,OU=Grupos,OU=Usuarios,DC=corp,DC=int))';

    // Conexión al servidor LDAP
    $ad = ldap_connect( "ldap://$ldaphost" );

    if( $ad ) {

        if( @ldap_bind( $ad, $username, $userpassword ) ) {

            $result         = ldap_search( $ad, $basedn, $filter, $searchAttr );
            $countResult    = ldap_count_entries( $ad, $result );
            $parseResult    = ldap_get_entries( $ad, $result );

            global $wpdb;

            if( $result && $countResult > 0 ) {

            	$title = utf8_encode( $parseResult[0]['givenname'][0] ).' '.utf8_encode( $parseResult[0]['sn'][0] );

            	if( isset( $parseResult[0]['givenname'][0] ) ) {

            		$content = '';

			        if( isset($parseResult[0]['mail'][0]) ) 
			            $content .= '<strong>Email:</strong> '.$parseResult[0]['mail'][0].'. ';
			        if( isset($parseResult[0]['telephonenumber'][0]) ) 
			            $content .= '<strong>Tlf:</strong> '.$parseResult[0]['telephonenumber'][0].'. ';
			        if( isset($parseResult[0]['mobile'][0]) ) 
			            $content .= '<strong>Ext:</strong> '.$parseResult[0]['mobile'][0].'. ';
			        if( isset($parseResult[0]['departament'][0]) ) 
			            $content .= '<strong>Dpto:</strong> '.utf8_encode( $parseResult[0]['departament'][0] ).'. ';
			        if( isset($parseResult[0]['hayaoficina'][0]) ) 
			            $content .= '<strong>Oficina:</strong> '.utf8_encode( $parseResult[0]['hayaoficina'][0] ).'. ';

					$employee = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'empleados' AND post_title = '%s'", $title ) );

					if( empty( $employee ) ) {
						$post_id = wp_insert_post( array(
				           'post_type'      => 'empleados',
				           'post_title'     => $title,
				           'post_content'   => $content,
				           'post_status'    => 'publish',
				           'comment_status' => 'closed',
				           'ping_status'    => 'closed'
				        ) );
					} else {
						wp_update_post( array(
							'ID'			 => $employee[0]->ID,
				            'post_title'     => $title,
				            'post_content'   => $content
				        ) );
					}

				}

            } else {

            	$title 	  = $wp_current_user->first_name . ' ' . $wp_current_user->last_name;
            	$employee = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->posts WHERE post_type = 'empleados' AND post_title = '%s'", $title ) );

            	if( empty( $employee ) ) {
					wp_delete_post( $employee[0]->ID, true );
				}

            }

        }

    }

}

add_action( 'set_auth_cookie', 'check_employee_by_user', 20, 6 );