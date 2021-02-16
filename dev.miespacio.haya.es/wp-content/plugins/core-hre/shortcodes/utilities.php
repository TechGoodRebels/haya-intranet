<?php 

/** ------------------------
 * Utilities List Shortcode
 * -------------------------
 */
function getUtilitiesList( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'title',
			'filter'   => 'all',
			'order'    => 'ASC'
							
		), $atts );

	$args = array(
		'post_type'        => 'utilities',
  		'post_status'      => 'publish',
  		'orderby'          => $params['orderby'],
  		'order'			   => $params['order'],
  		'posts_per_page'   => -1
	);

	$apps = new WP_Query( $args );

	$output = '';

	if( $apps->have_posts() ) :

		$user_id = get_current_user_id();

		$metaUserApp = get_user_meta( $user_id, '_hide_utilities_by_'.$user_id, true );
		if( !empty($metaUserApp) ) {
			$arrayApps   = explode( ',', $metaUserApp );
		}

		$metaUserOrderApp = get_user_meta( $user_id, '_reorder_utilities_by_'.$user_id, true );
		$arrayOrder 	  = false;

		if( $metaUserOrderApp ) {
			$arrayOrder = explode( ',', $metaUserOrderApp );
		}

		$dataAppsObject = array();

		$appIndex = 0;

		$output .= '<div id="utilities-list" class="wrapper-utilities">';
		$output .= '<div class="row-utilities">';

		while( $apps->have_posts() ) :

			$apps->the_post();

			$id_post   		 = get_the_ID();
			$title     		 = get_the_title();
			$image     		 = wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
			$content   		 = get_the_content();
			$link_utility	 = get_field( 'link_utilidad', $id_post );
			$link_txt_manual = get_field( 'link_text_manual', $id_post );
			$file_manual 	 = get_field( 'archivo_manual', $id_post );
			$link_manual 	 = get_field( 'link_manual', $id_post );
			$app_icon 	     = get_field( 'utility_icon', $id_post );
			$addClass 		 = '';

			if( in_array( $id_post, $arrayApps ) ) $addClass = 'hidden-utility';

			if( $arrayOrder ) {
				if( in_array( $id_post, $arrayOrder ) ) {
					$index = array_search( $id_post, $arrayOrder );
				} else {
					$index = 999 + $appIndex;
				}
			} else {
				$index = $appIndex;
			}

			$dataAppsObject[$index] = array(
				'id_post' 			=> $id_post,
				'title' 			=> $title,
				'image' 			=> $image,
				'content' 			=> $content,
				'link_utility' 		=> $link_utility,
				'link_txt_manual' 	=> $link_txt_manual,
				'file_manual'		=> $file_manual,
				'link_manual' 		=> $link_manual,
				'app_icon' 			=> $app_icon,
				'app_class'		 	=> $addClass
			);

			$appIndex++;

		endwhile;

		wp_reset_query();

		if( !empty( $dataAppsObject ) ) {

			ksort($dataAppsObject);

			foreach( $dataAppsObject as $a ) {
				$output .= '<div class="col-utilities '.$a['app_class'].'" data-appid="'.$a['id_post'].'">';

				$output .= '<div class="card-utility-wrapper">';
				$output .= '<a class="btn-hide-utility" href="#" title="Ocultar" data-id="'.$a['id_post'].'" data-type="hide"><i aria-hidden="true" class="far fa-eye-slash"></i></a>';
				$output .= '<div class="icon-box-utility utility-popup">';
				$output .= $a['app_icon'];
				$output .= '</div>';
				$output .= '<h5 class="title-utility utility-popup">'.$a['title'].'</h5>';

				$manual = '';

				if( !empty( $a['file_manual'] ) ) {
					$manual = $a['file_manual'];
				}

				if( !empty( $a['link_manual'] ) ) {
					$manual = $a['link_manual'];
				}

				$output .= '<div class="data-utility" data-content="'.wpautop( str_replace( '"', '', $a['content'] ), true ).'" data-link="'.$a['link_utility'].'" data-txtmanual="'.$a['link_txt_manual'].'" data-manual="'.$manual.'"></div>';
				$output .= '</div>';

				$output .= '</div>';
			}
		}

		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="link_hide_utilities">';
		if( $metaUserApp ) {
			$output .= '<a href="'.CORE_HRE_URL_UTILITIES.'ocultas/" class="btn-app btn-link-hide-apps" role="button"><i aria-hidden="true" class="far fa-eye-slash"></i> VER MIS UTILIDADES OCULTAS</a>';
		}
		$output .= '</div>';

	else :
		
		$output .= '<h6>' . __( 'No se han encontrado utilidades' ) . '</h6>';

	endif;

	return $output;

}

add_shortcode( 'utilities-list', 'getUtilitiesList' );

/** ------------------------
 * Hide Utilities List Shortcode
 * -------------------------
 */
function getHideUtilitiesList( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'title',
			'filter'   => 'all',
			'order'    => 'ASC'
							
		), $atts );

	$args = array(
		'post_type'        => 'utilities',
  		'post_status'      => 'publish',
  		'orderby'          => $params['orderby'],
  		'order'			   => $params['order'],
  		'posts_per_page'   => -1
	);

	$apps = new WP_Query( $args );

	$output = '';

	if( $apps->have_posts() ) :

		$user_id = get_current_user_id();

		$metaUserApp = get_user_meta( $user_id, '_hide_utilities_by_'.$user_id, true );
		if( !empty($metaUserApp) ) {
			$arrayApps   = explode( ',', $metaUserApp );
		}

		$metaUserOrderApp = get_user_meta( $user_id, '_reorder_utilities_by_'.$user_id, true );
		$arrayOrder 	  = false;

		if( $metaUserOrderApp ) {
			$arrayOrder = explode( ',', $metaUserOrderApp );
		}

		$dataAppsObject = array();

		$appIndex = 0;

		$output .= '<div id="utilities-list" class="wrapper-utilities">';
		$output .= '<div class="row-utilities">';

		while( $apps->have_posts() ) :

			$apps->the_post();

			$id_post   		 = get_the_ID();
			$title     		 = get_the_title();
			$image     		 = wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
			$content   		 = get_the_content();
			$link_utility	 = get_field( 'link_utilidad', $id_post );
			$link_txt_manual = get_field( 'link_text_manual', $id_post );
			$file_manual 	 = get_field( 'archivo_manual', $id_post );
			$link_manual 	 = get_field( 'link_manual', $id_post );
			$app_icon 	     = get_field( 'utility_icon', $id_post );
			$addClass 		     = '';

			if( !in_array( $id_post, $arrayApps ) ) $addClass = 'hidden-utility';

			if( $arrayOrder ) {
				if( in_array( $id_post, $arrayOrder ) ) {
					$index = array_search( $id_post, $arrayOrder );
				} else {
					$index = 999 + $appIndex;
				}
			} else {
				$index = $appIndex;
			}

			$dataAppsObject[$index] = array(
				'id_post' 			=> $id_post,
				'title' 			=> $title,
				'image' 			=> $image,
				'content' 			=> $content,
				'link_utility' 		=> $link_utility,
				'link_txt_manual' 	=> $link_txt_manual,
				'file_manual'		=> $file_manual,
				'link_manual' 		=> $link_manual,
				'app_icon' 			=> $app_icon,
				'app_class'		 	=> $addClass
			);

			$appIndex++;

		endwhile;

		wp_reset_query();

		if( !empty( $dataAppsObject ) ) {

			ksort($dataAppsObject);

			foreach( $dataAppsObject as $a ) {
				$output .= '<div class="col-utilities '.$a['app_class'].'" data-appid="'.$a['id_post'].'">';

				$output .= '<div class="card-utility-wrapper">';
				$output .= '<a class="btn-hide-utility" href="#" title="Mostrar" data-id="'.$a['id_post'].'" data-type="show"><i aria-hidden="true" class="far fa-eye"></i></a>';
				$output .= '<div class="icon-box-utility utility-popup">';
				$output .= $a['app_icon'];
				$output .= '</div>';
				$output .= '<h5 class="title-utility utility-popup">'.$a['title'].'</h5>';

				$manual = '';

				if( !empty( $a['file_manual'] ) ) {
					$manual = $a['file_manual'];
				}

				if( !empty( $a['link_manual'] ) ) {
					$manual = $a['link_manual'];
				}

				$output .= '<div class="data-utility" data-content="'.wpautop( str_replace( '"', '', $a['content'] ), true ).'" data-link="'.$a['link_utility'].'" data-txtmanual="'.$a['link_txt_manual'].'" data-manual="'.$manual.'"></div>';
				$output .= '</div>';

				$output .= '</div>';
			}
		}

		$output .= '</div>';
		$output .= '</div>';

	else :
		
		$output .= '<h6>' . __( 'No se han encontrado utilidades' ) . '</h6>';

	endif;

	return $output;

}

add_shortcode( 'hide-utilities-list', 'getHideUtilitiesList' );

/** ------------------------
 * Menu Utilities List Shortcode
 * -------------------------
 */
function getMenuUtilities( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'title',
			'filter'   => 'all',
			'order'    => 'ASC'
							
		), $atts );

    $queryArgs = array(
        'post_type'        => 'utilities',
        'post_status'      => 'publish',
        'orderby'          => $params['orderby'],
        'order'            => $params['order'],
        'posts_per_page'   => -1
    );

    $add_menu_item = '';

    $apps = new WP_Query( $queryArgs );

    if( $apps->have_posts() ) :

    	$user_id = get_current_user_id();

		$metaUserApp = get_user_meta( $user_id, '_hide_utilities_by_'.$user_id, true );
		if( !empty($metaUserApp) ) {
			$arrayApps   = explode( ',', $metaUserApp );
		}

		$metaUserOrderApp = get_user_meta( $user_id, '_reorder_utilities_by_'.$user_id, true );
		$arrayOrder 	  = false;

		if( $metaUserOrderApp ) {
			$arrayOrder = explode( ',', $metaUserOrderApp );
		}

		$dataAppsObject = array();

		$appIndex = 0;

        while( $apps->have_posts() ) :

            $apps->the_post();

            $id_post         = get_the_ID();
            $title           = get_the_title( $id_post );
            $image           = wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
            $content         = get_the_content( $id_post );
            $link_utility    = get_field( 'link_utilidad', $id_post );
            $link_txt_manual = get_field( 'link_text_manual', $id_post );
            $file_manual 	 = get_field( 'archivo_manual', $id_post );
            $link_manual     = get_field( 'link_manual', $id_post );
            $app_icon        = get_field( 'utility_icon', $id_post );
            $show            = true;

            if( in_array( $id_post, $arrayApps ) ) $show = false;

            if( $arrayOrder ) {
				if( in_array( $id_post, $arrayOrder ) ) {
					$index = array_search( $id_post, $arrayOrder );
				} else {
					$index = 999 + $appIndex;
				}
			} else {
				$index = $appIndex;
			}

			$dataAppsObject[$index] = array(
				'id_post' 			=> $id_post,
				'title' 			=> $title,
				'image' 			=> $image,
				'content' 			=> $content,
				'link_utility' 		=> $link_utility,
				'link_txt_manual' 	=> $link_txt_manual,
				'file_manual'		=> $file_manual,
				'link_manual' 		=> $link_manual,
				'app_icon' 			=> $app_icon,
				'show'		 		=> $show
			);

			$appIndex++;

        endwhile;

        wp_reset_query();

        if( !empty( $dataAppsObject ) ) {

			ksort($dataAppsObject);

			foreach( $dataAppsObject as $a ) {
				if( $a['show'] ) {

	                $add_menu_item .= '<li class="menu-item-utility utility-popup-menu menu-item menu-item-type-custom menu-item-object-custom">';
	                $add_menu_item .= '<a href="#" class="link-utility-menu">';
	                $add_menu_item .=  $a['app_icon'];
	                $add_menu_item .= '<span class="title-utility-menu">'.$a['title'].'</span>';

	                $manual = '';

					if( !empty( $a['file_manual'] ) ) {
						$manual = $a['file_manual'];
					}

					if( !empty( $a['link_manual'] ) ) {
						$manual = $a['link_manual'];
					}

	                $add_menu_item .= '<div class="data-utility-menu" data-content="'.wpautop( str_replace( '"', '', $a['content'] ), true ).'" data-link="'.$a['link_utility'].'" data-txtmanual="'.$a['link_txt_manual'].'" data-manual="'.$manual.'"></div>';
	                $add_menu_item .= '</a>';
	                $add_menu_item .= '</li>';

	            }
			}
		}

    endif;

	return $add_menu_item;

}

add_shortcode( 'utilities-menu', 'getMenuUtilities' );