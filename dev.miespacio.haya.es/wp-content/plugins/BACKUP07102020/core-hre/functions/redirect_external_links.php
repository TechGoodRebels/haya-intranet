<?php 

/*function redirect_external_links() {

	$post_type = get_post_type();
	$types_ext = array(
		'apps',
		'utilities',
		'cursos',
		'vacantes'
	);

  	if( in_array( $post_type, $types_ext ) ) {

  		if( function_exists( 'get_field' ) ) {

		    $post_id = get_the_id();
		    
		    switch ( $post_type ) {
		    	case 'apps': 		$metakey = 'link_app'; 		break;
		    	case 'utilities': 	$metakey = 'link_utilidad'; break;
		    	case 'cursos': 		$metakey = 'link_curso'; 	break;
		    	case 'vacantes': 	$metakey = 'link_vacante'; 	break;
		    }

		    $external_link = get_field( $metakey, $post_id );

		    if( $external_link ) {
			    echo "<script> window.open(".$external_link.", '_blank');</script>";
         		exit;
		    }

		}

  	}
  
}

add_action( 'template_redirect', 'redirect_external_links' ); */

function apps_custom_link_option( $url, $post ) {

    // Page_link gives the ID rather than the $post object.
    if ( 'integer' === gettype( $post ) ) {
        $post_id = $post;
    } else {
        $post_id = $post->ID;
    }

    // Create an array of post types.
    $post_type = get_post_type( $post_id );
	$types_ext = array(
		'apps',
		'utilities',
		'cursos',
		'vacantes',
		'empleados'
	);

	// Check the current post type.
    if ( !in_array( $post_type, $types_ext, true ) ) {
        return $url;
    }

    // Get the custom_link if one exists.
    if( function_exists( 'get_field' ) ) {
	    
	    switch ( $post_type ) {
	    	case 'apps':
	    		$hasParent = get_field( 'has_parent', $post_id );
	    		if( !empty( $hasParent ) ) {
	    			$external_link = CORE_HRE_URL_APPS.'?a='.$post_id;
	    		} else {
	    			$external_link = get_field( 'link_app', $post_id );
	    		}
	    		break;
	    	case 'utilities': 	$external_link = get_field( 'link_utilidad', $post_id ); 	break;
	    	case 'cursos': 		$external_link = get_field( 'link_curso', $post_id );	 	break;
	    	case 'vacantes': 	$external_link = get_field( 'link_vacante', $post_id );  	break;
	    	case 'empleados': 	
	    		$title 		   = get_the_title( $post_id );
	    		$external_link = esc_url( CORE_HRE_URL_DIRECTORY . '?employee='.$title );
	    		break;
	    }

	    if( $external_link ) {
		    $url = $external_link;
	    }

	}

    return $url;
}

/**
 * Add filters for post_link, page_link, and post_type_link to update Custom Link
 */
add_filter( 'post_type_link', 'apps_custom_link_option', 10, 2 );