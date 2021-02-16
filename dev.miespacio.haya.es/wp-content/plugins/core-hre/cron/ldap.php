<?php 

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

            apc_store( 'ldap_results', $parseResult, 3600 * 4 );

        }

    }

}