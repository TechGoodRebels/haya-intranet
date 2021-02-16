<?php 

ignore_user_abort(true);
set_time_limit(0);

$ldap_results = false;

// Comprobamos si hay resultados en caché
$cacheResult = apc_fetch( 'ldap_results' );

if( !$cacheResult || $cacheResult == NULL ) {

	// Variables del servidor LDAP
	$ldaphost       = 'dc.haya.es';                 // Servidor LDAP
	$username       = 'usr_miespacio'; 	            // Usuario maestro
	$userpassword   = 'kUrEIZa)&ej5';               // Password
	$basedn         = 'OU=Usuarios,DC=corp,DC=int'; // Base DN
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
	$filter = '(&(objectCategory=Person)(sAMAccountName=*)(memberOf:1.2.840.113556.1.4.1941:=CN=servicio_vpn_empleados,OU=Servicios,OU=Grupos,OU=Usuarios,DC=corp,DC=int))';

	// Conexión al servidor LDAP
	$ad = ldap_connect( "ldap://$ldaphost" );

	if( $ad ) {

	    if( @ldap_bind( $ad, $username, $userpassword ) ) {

	            $result         = ldap_search( $ad, $basedn, $filter, $searchAttr );
	            $countResult    = ldap_count_entries( $ad, $result );
	            $parseResult    = ldap_get_entries( $ad, $result );

	        if( $countResult > 0 ) {

	        	$ldap_results = $parseResult;

	            apc_store( 'ldap_results', $parseResult, 3600 * 4 );

	        }

	    }

	}

} else { $ldap_results = $cacheResult; }

if( $ldap_results ) {

	$countResult    = $countResult ?? count( $ldap_results );

	// Conectamos con login.microsoftonline.com para obtención del token
	$clientId 		= "0fe0efa6-978f-4f7d-93ae-96bbac956354";
	$clientSecret 	= "32CcD._qL_~KBzj1bz~7oW145hW0m73.v9";
	$resource 		= "https://graph.microsoft.com";
	$scope 			= "User.All.Read";
	$curlURL 		= "https://login.microsoftonline.com/0147bb13-8ab6-47bd-a1da-cb352ced5ac2/oauth2/token";

	$headers = array(
	    "Content-type: application/x-www-form-urlencoded",
	);

	$post_params = array(
		"client_id" 	=> $clientId,                    
		"scope" 		=> $scope,
		"client_secret" => $clientSecret,
		"resource"		=> $resource,
		"grant_type" 	=> "client_credentials",
	);

	$curl = curl_init( $curlURL );

	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $post_params );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, Array( "application/x-www-form-urlencoded" ) );
	curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, true );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

	$response  = json_decode( curl_exec( $curl ) );

	$curlError = curl_error( $curl );

	curl_close( $curl );

	if( !$curlError ) {

		$token  = $response->access_token;
		$expire = $response->expires_in;

		$uploads_dir = $_SERVER['DOCUMENT_ROOT'];
		$filepath    = $uploads_dir .  '/wp-content/uploads/photos/';
 		
 		// Creamos directorio photos si no existe
		if( !file_exists( $filepath ) ){
			mkdir( $filepath, 0755, true );
		}

		for( $i = 0; $i < $countResult; $i++ ) {

			$user_email = $ldap_results[$i]['mail'][0];

			// Ruta de la imagen
			$img_file = $filepath . $user_email . '.png';

			// Si ya existe archivo comprobamos fecha de creación y eliminamos si es anterior al día actual
			if( file_exists( $img_file ) ) {

				$filetime 	 = date ( 'Y-m-d', filemtime( $img_file ) );
				$currenttime = date ( 'Y-m-d' );

				if( $filetime < $currenttime ) 
					unlink( $img_file );
				else 
					continue;
		
			}

			// Si el empleado tiene datos y el mail no está vacío
			if( !empty( $ldap_results[$i] ) && $ldap_results[$i]['count'] > 0 && $user_email != '' ) {

				// Conexión cURL para obtener recurso de foto
				$curlGraph = curl_init();

			    curl_setopt_array( $curlGraph, array(
			        CURLOPT_URL 			=> $resource . "/v1.0/users/".$user_email."/photos/240x240/\$value",
			        CURLOPT_RETURNTRANSFER 	=> true,
			        CURLOPT_ENCODING 		=> "",
			        CURLOPT_MAXREDIRS 		=> 10,
			        CURLOPT_TIMEOUT 		=> 30,
			        CURLOPT_HTTP_VERSION 	=> CURL_HTTP_VERSION_1_1,
			        CURLOPT_CUSTOMREQUEST 	=> "GET",
			        CURLOPT_HTTPHEADER 		=> array(
			            "Authorization: Bearer $token",
			            "cache-control: no-cache"
			        ),
			    ));

			    $respPhoto 	 = curl_exec( $curlGraph );
			    $errGraph	 = curl_error( $curlGraph );

			    $jsonMessage = json_decode( $respPhoto );

			    curl_close( $curlGraph );

			    // Si no ha habido error de conexión o la respuesta contiene error
			    if( !$errGraph && empty( $jsonMessage->error ) ) {

			    	$base64 = base64_encode( $respPhoto );
			    	$bin    = base64_decode( $base64 );

					// Creamos imagen a partir de base64
					$imgCreate = imageCreateFromString( $bin );

					// En caso de fallo al crear imagen: liberamos memoria y saltamos iteración del bucle
					if( !$imgCreate ) { 
						imagedestroy( $imgCreate );
						continue; 
					}

					// Convertir imagen a PNG
					imagepng( $imgCreate, $img_file, 0 );

					// Liberamos memoria
					imagedestroy( $imgCreate );

			    }

			}

		}

	}

}