<?php 

function get_ssotoken_ib() {

	if( is_user_logged_in() ) {

			$user_id 		= CORE_HRE_USER_ID;
			$email 			= CORE_HRE_USER_EMAIL;
			$first_name     = CORE_HRE_USER_NAME;
			$last_name      = CORE_HRE_USER_LAST_NAME;
			$lang 			= get_locale();
			$country_code 	= 'ES';
			$timestamp 		= date( 'Y-m-d H:i:s' );

			if ( empty( $lang ) ) {
				$lang = 'es';
			}

			$method 		= CORE_HRE_IB_ENCRYPT_METHOD;
			$key 			= CORE_HRE_IB_ENCRYPT_PASSW;

			$data 			= "id=$email";
			$data 		   .= "&email=$email";

			if( !empty( $first_name ) )   { $data .= "&nombre=$first_name"; }
			if( !empty( $last_name ) ) 	  { $data .= "&apellidos=$last_name"; }
			if( !empty( $lang ) ) 		  { $data .= "&locale=$lang"; }
			if( !empty( $country_code ) ) { $data .= "&country_code=$country_code"; }
			if( !empty( $timestamp ) ) 	  { $data .= "&timestamp=$timestamp"; }

			$encrypt = openssl_encrypt( $data, $method, $key, OPENSSL_RAW_DATA );

			if( $encrypt ) {
				$encode64 = base64_encode( $encrypt );
			} else {
				$encode64 = false;
			}

			return $encode64;

	}

}

function getIframeIBRedirection() {
	$token = get_ssotoken_ib();

	if( $token ) {
		$iframe = '<iframe src="'.CORE_HRE_IB_REDIRECTION_URL.'?ssotoken='.$token.'" border="0" style="width:100%!important;height:80vh!important;border:none;" id="inspiring-benefits"></iframe>';

		return $iframe;
	}
}

add_shortcode( 'inspiring-benefits', 'getIframeIBRedirection' );