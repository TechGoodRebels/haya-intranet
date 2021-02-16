<?php 

function getUserPhoto() {

	if( is_user_logged_in() && defined( 'CORE_HRE_USER_ID' ) ) {

		global $ultimatemember;

		$user_id 		= CORE_HRE_USER_ID;
		$email 			= CORE_HRE_USER_EMAIL;

		um_fetch_user( $user_id );

		$photo_profile   = um_get_user_avatar_url();
		$full_name  	 = um_profile( 'display_name' );
		$default_avatar  = um_get_default_avatar_uri();
		$url_profile 	 = um_user_profile_url();

		if( $photo_profile == $default_avatar ) {
			$pathPhotos   = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/photos/';
    		$urlPhotos    = home_url() . '/wp-content/uploads/photos/';

    		if( file_exists( $pathPhotos . $email .'.png' ) ) {
                $photo_profile = $urlPhotos . $email .'.png';
            }
		} 

		$output  = '<div class="custom-box-current-user">';
		$output .= '<div class="elementor-author-box">';
		$output .= '<div class="elementor-author-box__avatar">';
		$output .= '<img src="'.$photo_profile.'" alt="'.$full_name.'" class="img-current-user '.( $photo_profile == $default_avatar ? 'no-image' : '' ).'" />';
		$output .= '</div>';
		$output .= '<div class="elementor-author-box__text">';
		$output .= '<div>';
		$output .= '<h5 class="elementor-author-box__name">'.$full_name.'</h5>';
		$output .= '</div>';
		$output .= '<a class="elementor-author-box__button elementor-button elementor-size-xs" href="'.$url_profile.'">Mi perfil</a>';
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';

		return $output;

	}

}

add_shortcode( 'user-current-box', 'getUserPhoto' );