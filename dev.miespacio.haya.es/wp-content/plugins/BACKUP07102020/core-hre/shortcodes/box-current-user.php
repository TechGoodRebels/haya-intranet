<?php 

function getUserPhoto() {

	if( is_user_logged_in() && defined( 'CORE_HRE_USER_ID' ) ) {

		global $ultimatemember;

		$user_id 		= CORE_HRE_USER_ID;
		$email 			= CORE_HRE_USER_EMAIL;

		um_fetch_user( $user_id );

		$photo_profile   = um_get_user_avatar_url();
		$full_name  	 = um_profile( 'display_name' );
		$default_uri 	 = um_get_default_avatar_uri( $user_id );
		$url_profile 	 = um_user_profile_url();

		$photo = !empty( $photo_profile ) ? $photo_profile : $default_uri; 

		$output  = '<div class="custom-box-current-user">';
		$output .= '<div class="elementor-author-box">';
		$output .= '<div class="elementor-author-box__avatar">';
		$output .= '<img src="'.$photo.'" alt="'.$full_name.'" class="img-current-user '.( empty( $photo_profile ) ? 'no-image' : '' ).'" />';
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