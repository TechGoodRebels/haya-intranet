<?php 

/* CONECT MICROSOFT GRAPH SDK */

add_action( 'init', 'update_logo_microsoft_office', 100 );

function update_logo_microsoft_office( $args ) {

    if( empty($_SESSION['token_microsoft']) ) {
        $url = 'https://login.microsoftonline.com/'.CORE_HRE_MICROSOFT_TENANT.'/oauth2/token';

        $curl = curl_init( $url );

        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false);
        curl_setopt( $curl, CURLOPT_HEADER, false);
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt( $curl, CURLOPT_POST, true);
        curl_setopt( $curl, CURLOPT_POSTFIELDS, rawurldecode( http_build_query( array(
            'grant_type'    => 'client_credentials',
            'resource'      => CORE_HRE_MICROSOFT_RESOURCE,
            'client_id'     => CORE_HRE_MICROSOFT_ID,
            'client_secret' => CORE_HRE_MICROSOFT_SECRET,
            'scope'         => CORE_HRE_MICROSOFT_SCOPE
        ) ) ) );

        $json = json_decode( curl_exec( $curl ) );

        $_SESSION['token_microsoft'] = $json->access_token;

        // Close cURL resource
        curl_close( $curl );

    }

    $urlProfile = "https://graph.microsoft.com/v1.0/users/".CORE_HRE_USER_EMAIL."/photos/240x240/\$value";

    $headers = array(
        'Content-Type: application/json',
        sprintf( 'Authorization: Bearer %s', $_SESSION['token_microsoft'] )
    );

    $curl = curl_init( $urlProfile );

    curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

    $formatJSON = json_decode( curl_exec( $curl ), true );

    $urlLogo = $formatJSON['@odata.mediaContentType'].';base64,' . base64_encode($formatJSON['@odata.context']);

    curl_close( $curl );

}