<?php

/** ------------------------
 * Apps List Shortcode
 * -------------------------
 */
function getAppsList( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'menu_order',
			'filter'   => 'all',
			'order'    => 'ASC'
							
		), $atts );

	$args = array(
		'post_type'        => 'apps',
  		'post_status'      => 'publish',
  		'orderby'          => $params['orderby'],
  		'order'			   => $params['order'],
  		'posts_per_page'   => -1
	);

	$parent = $_REQUEST['a'] ?? false;
	$cat    = $_REQUEST['c'] ?? 'all';

	$output = '';

	$cat_args = array(
		'taxonomy'	 => 'categorias_aplicaciones',
	    'orderby'    => 'name',
	    'order'      => 'asc',
	    'hide_empty' => false
	);
	 
	$cat_terms = get_terms( $cat_args );

	if( $parent ) {

		$args['post_parent'] = $parent;

		$output .= '<div><a href="'.CORE_HRE_URL_APPS.'"><i aria-hidden="true" class="fas fa-arrow-left"></i> Volver a todas</a></div>';

	} else {

		$args['post_parent'] = 0;

		if( !empty( $cat_terms ) ) {
			$output .= '<form id="app-categories-form" action="" method="GET" onchange="this.submit()">';
	        $output .= '<div class="col-form col-app-categories">';
	        $output .= '<select class="input-category" name="c">';
	        $output .= '<option value="all" '.( $cat == 'all' ? 'selected' : '' ).'>- Todas las categorías -</option>';
	        foreach( $cat_terms as $t ) :
				$output .= '<option value="'.$t->slug.'"';
				$output .= ( $t->slug == $cat ) ? 'selected' : '';
				$output .= '>'.$t->name.'</option>';
			endforeach;
	        $output .= '</select>';
	        $output .= '</div>';
	        $output .= '</form>';
	    }
	}

	$apps = new WP_Query( $args );

	if( $apps->have_posts() ) :

		$user_id = CORE_HRE_USER_ID;

		$metaUserApp = get_user_meta( $user_id, '_hide_apps_by_'.$user_id, true );
		$arrayApps 	 = array();

		if( $metaUserApp ) {
			$arrayApps = explode( ',', $metaUserApp );
		}

		$metaUserOrderApp = get_user_meta( $user_id, '_reorder_apps_by_'.$user_id, true );
		$arrayOrder 	  = false;

		if( $metaUserOrderApp ) {
			$arrayOrder = explode( ',', $metaUserOrderApp );
		}

		$dataAppsObject = array();

		$output .= '<div id="applications-list" class="wrapper-apps applications-show '.( $parent ? 'child-apps' : 'parent-apps' ).'">';
		$output .= '<div class="row-apps">';

		$appIndex = 0;

		while( $apps->have_posts() ) :

			$apps->the_post();

			$id_post   		= get_the_ID();
			$title     		= get_the_title();
			$image     		= wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
			$content   		= get_the_content();
			$link_app 		= get_field( 'link_app', $id_post );
			$ticketing 	    = get_field( 'ruta_ticketing', $id_post );
			$link_ticketing = get_field( 'link_ticketing', $id_post );
			$app_icon 	    = get_field( 'app_icon', $id_post );
			$category 		= get_the_terms( $id_post, 'categorias_aplicaciones' );

			$manuals        = array();
			$hasManuals     = false;
			$addClass 	    = '';

			for( $m = 1; $m <= 7; $m++ ) {
				$currentManual = get_field( 'manual_'.$m, $id_post );

				if( isset( $currentManual ) && $currentManual['nombre_enlace'] != '' ) {
					$manuals[]  = $currentManual;
					$hasManuals = true;
				}
			}

			if( in_array( $id_post, $arrayApps ) ) $addClass = 'hidden-app';

			else {
				if( $cat != 'all' ) {
					if( !empty( $category ) ) {
						$addClass = 'hidden-app';

						foreach ( $category as $term ) {
							if( $term->slug == $cat ) {
								$addClass = '';
								break;
							}
						}
					} else {
						$addClass = 'hidden-app';
					}
				}
			}

			$hasParent = get_field( 'has_parent', $id_post );

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
				'link_app' 			=> $link_app,
				'has_manuals' 		=> $hasManuals,
				'manuals' 			=> $manuals,
				'ticketing' 		=> $ticketing,
				'link_ticketing' 	=> $link_ticketing,
				'app_icon' 			=> $app_icon,
				'class_app' 		=> $addClass,
				'has_parent' 		=> $hasParent,
				'category'			=> $category
			);

			$appIndex++;

		endwhile;

		wp_reset_query();

		if( !empty( $dataAppsObject ) ) {

			ksort( $dataAppsObject );

			foreach( $dataAppsObject as $a ) {
				$output .= '<div class="col-apps '.$a['class_app'].'" data-appid="'.$a['id_post'].'">';

				$output .= '<div class="card-app-wrapper">';
				$output .= '<a class="btn-hide-app" href="#" title="Ocultar" data-id="'.$a['id_post'].'" data-type="hide"><i aria-hidden="true" class="far fa-eye-slash"></i></a>';
				if( !empty( $a['category'] ) ) {
					$output .= '<div class="category-app">';
					foreach ( $a['category'] as $cat ) {
						$output .= $cat->name.' ';
					}
					$output .= '</div>';
				}
				$output .= '<div class="row-inner-app">';
				$output .= '<div class="col-inner-app">';
				if( !empty( $a['image'] ) ) {
					$output .= '<img class="img-app-box" src="'.$a['image'][0].'" alt="'.$a['title'].'" />';
				} else {
					$output .= '<div class="icon-box-app">';
					$output .= $a['app_icon'];
					$output .= '</div>';
				}
				$output .= '</div>';
				$output .= '<div class="col-inner-app">';
				$output .= '<h5>'.$a['title'].'</h5>';
				$output .= wpautop( $a['content'], true );
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="row-buttons-app">';

				if( empty( $a['has_parent'] ) ) {
					if( !empty( $a['link_app'] ) ) {
						$output .= '<a href="'.$a['link_app'].'" target="_blank" class="btn-app btn-link-app" role="button">ACCEDE A LA APP</a>';
					}
					if( $a['has_manuals'] ) {
						$dataManuals = '';

						foreach ( $a['manuals'] as $manual ) {
							if( $manual['nombre_enlace'] && $manual['manual_archivo'] || $manual['enlace_manual'] ) {
								$dataManuals .= str_replace( ',', '', $manual['nombre_enlace'] ) . ',';
								$dataManuals .= $manual['manual_archivo'] ? str_replace( ';', '', $manual['manual_archivo'] ) : str_replace( ';', '', $manual['enlace_manual'] );
								$dataManuals .= ';';
							}
						}

						$output .= '<a href="#" class="btn-app btn-link-manual" data-manuals="'.$dataManuals.'" role="button">MANUAL</a>';
					}
					if( !empty( $a['ticketing'] ) ) {
						$output .= '<a href="#" class="btn-app btn-popup-help" data-ticketing="'.wpautop( str_replace( '"', '', $a['ticketing'] ), true ).'" data-url="'.$a['link_ticketing'].'" role="button">AYUDA</a>';
					}
				} else {
					$output .= '<a href="'.CORE_HRE_URL_APPS.'?a='.$a['id_post'].'" class="btn-app btn-link-app" role="button">VER APPS</a>';
				}

				$output .= '</div>';
				$output .= '</div>';

				$output .= '</div>';
			}
		}

		$output .= '</div>';
		$output .= '</div>';

		$output .= '<div class="link_hide_apps">';
		if( $metaUserApp ) {
			$output .= '<a href="'.CORE_HRE_URL_APPS.'ocultas/" class="btn-app btn-link-hide-apps" role="button"><i aria-hidden="true" class="far fa-eye-slash"></i> VER MIS APLICACIONES OCULTAS</a>';
		}
		$output .= '</div>';

	else :
		
		$output .= '<h6>' . __( 'No se han encontrado aplicaciones' ) . '</h6>';

	endif;

	return $output;

}

add_shortcode( 'app-list', 'getAppsList' );

/** ------------------------
 * Hide Apps List Shortcode
 * -------------------------
 */
function getHideAppsList( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'menu_order',
			'filter'   => 'all',
			'order'    => 'ASC'
							
		), $atts );

	$args = array(
		'post_type'        => 'apps',
  		'post_status'      => 'publish',
  		'orderby'          => $params['orderby'],
  		'order'			   => $params['order'],
  		'posts_per_page'   => -1
	);

	$parent = $_REQUEST['a'] ?? false;
	$cat    = $_REQUEST['c'] ?? 'all';

	$cat_args = array(
		'taxonomy'	 => 'categorias_aplicaciones',
	    'orderby'    => 'name',
	    'order'      => 'asc',
	    'hide_empty' => false
	);
	 
	$cat_terms = get_terms( $cat_args );

	$output = '';

	if( $parent ) {

		$args['post_parent'] = $parent;

		$output .= '<div><a href="'.CORE_HRE_URL_APPS.'ocultas/"><i aria-hidden="true" class="fas fa-arrow-left"></i> Volver a todas las apps ocultas</a></div>';

	} else {

		$args['post_parent'] = 0;

		if( !empty( $cat_terms ) ) {
			$output .= '<form id="app-categories-form" action="" method="GET" onchange="this.submit()">';
	        $output .= '<div class="col-form col-app-categories">';
	        $output .= '<select class="input-category" name="c">';
	        $output .= '<option value="all" '.( $cat == 'all' ? 'selected' : '' ).'>- Todas las categorías -</option>';
	        foreach( $cat_terms as $t ) :
				$output .= '<option value="'.$t->slug.'"';
				$output .= ( $t->slug == $cat ) ? 'selected' : '';
				$output .= '>'.$t->name.'</option>';
			endforeach;
	        $output .= '</select>';
	        $output .= '</div>';
	        $output .= '</form>';
	    }
	}

	$apps = new WP_Query( $args );

	if( $apps->have_posts() ) :

		$user_id = CORE_HRE_USER_ID;

		$metaUserApp = get_user_meta( $user_id, '_hide_apps_by_'.$user_id, true );
		$arrayApps   = explode( ',', $metaUserApp );

		$metaUserOrderApp = get_user_meta( $user_id, '_reorder_apps_by_'.$user_id, true );
		$arrayOrder 	  = false;

		if( $metaUserOrderApp ) {
			$arrayOrder = explode( ',', $metaUserOrderApp );
		}

		$dataAppsObject = array();

		$output .= '<div id="applications-list" class="wrapper-apps applications-hidden '.( $parent ? 'child-apps' : 'parent-apps' ).'">';
		$output .= '<div class="row-apps">';

		$appIndex = 0;

		while( $apps->have_posts() ) :

			$apps->the_post();

			$id_post   		= get_the_ID();
			$title     		= get_the_title();
			$image     		= wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
			$content   		= get_the_content();
			$link_app 		= get_field( 'link_app', $id_post );
			$ticketing 	    = get_field( 'ruta_ticketing', $id_post );
			$link_ticketing = get_field( 'link_ticketing', $id_post );
			$app_icon 	    = get_field( 'app_icon', $id_post );
			$category 		= get_the_terms( $id_post, 'categorias_aplicaciones' );
			
			$manuals        = array();
			$hasManuals     = false;
			$addClass 	    = '';

			for( $m = 1; $m <= 7; $m++ ) {
				$currentManual = get_field( 'manual_'.$m, $id_post );

				if( isset( $currentManual ) && $currentManual['nombre_enlace'] != '' ) {
					$manuals[]  = $currentManual;
					$hasManuals = true;
				}
			}

			if( !in_array( $id_post, $arrayApps ) ) $addClass = 'hidden-app';

			else {
				if( $cat != 'all' ) {
					if( !empty( $category ) ) {
						$addClass = 'hidden-app';

						foreach ( $category as $term ) {
							if( $term->slug == $cat ) {
								$addClass = '';
								break;
							}
						}
					} else {
						$addClass = 'hidden-app';
					}
				}
			}

			$hasParent = get_field( 'has_parent', $id_post );

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
				'link_app' 			=> $link_app,
				'has_manuals' 		=> $hasManuals,
				'manuals' 			=> $manuals,
				'ticketing' 		=> $ticketing,
				'link_ticketing' 	=> $link_ticketing,
				'app_icon' 			=> $app_icon,
				'class_app' 		=> $addClass,
				'has_parent' 		=> $hasParent,
				'category'			=> $category
			);

			$appIndex++;

		endwhile;

		wp_reset_query();

		if( !empty( $dataAppsObject ) ) {

			ksort($dataAppsObject);

			foreach( $dataAppsObject as $a ) {
				$output .= '<div class="col-apps '.$a['class_app'].'" data-appid="'.$a['id_post'].'">';

				$output .= '<div class="card-app-wrapper">';
				if( !empty( $a['category'] ) ) {
					$output .= '<div class="category-app">';
					foreach ( $a['category'] as $cat ) {
						$output .= $cat->name.' ';
					}
					$output .= '</div>';
				}
				$output .= '<a class="btn-hide-app" href="#" title="Mostrar" data-id="'.$a['id_post'].'" data-type="show"><i aria-hidden="true" class="far fa-eye"></i></a>';
				$output .= '<div class="row-inner-app">';
				$output .= '<div class="col-inner-app">';
				if( !empty( $a['image'] ) ) {
					$output .= '<img class="img-app-box" src="'.$a['image'][0].'" alt="'.$a['title'].'" />';
				} else {
					$output .= '<div class="icon-box-app">';
					$output .= $a['app_icon'];
					$output .= '</div>';
				}
				$output .= '</div>';
				$output .= '<div class="col-inner-app">';
				$output .= '<h5>'.$a['title'].'</h5>';
				$output .= wpautop( $a['content'], true );
				$output .= '</div>';
				$output .= '</div>';
				$output .= '<div class="row-buttons-app">';

				if( empty( $a['has_parent'] ) ) {
					if( !empty( $a['link_app'] ) ) {
						$output .= '<a href="'.$a['link_app'].'" target="_blank" class="btn-app btn-link-app" role="button">ACCEDE A LA APP</a>';
					}
					if( $a['has_manuals'] ) {
						$dataManuals = '';

						foreach ( $a['manuals'] as $manual ) {
							if( $manual['nombre_enlace'] && $manual['manual_archivo'] || $manual['enlace_manual'] ) {
								$dataManuals .= str_replace( ',', '', $manual['nombre_enlace'] ) . ',';
								$dataManuals .= $manual['manual_archivo'] ? str_replace( ';', '', $manual['manual_archivo'] ) : str_replace( ';', '', $manual['enlace_manual'] );
								$dataManuals .= ';';
							}
						}

						$output .= '<a href="#" class="btn-app btn-link-manual" data-manuals="'.$dataManuals.'" role="button">MANUAL</a>';
					}
					if( !empty( $a['ticketing'] ) ) {
						$output .= '<a href="#" class="btn-app btn-popup-help" data-ticketing="'.wpautop( str_replace( '"', '', $a['ticketing'] ), true ).'" data-url="'.$a['link_ticketing'].'" role="button">AYUDA</a>';
					}
				} else {
					$output .= '<a href="'.CORE_HRE_URL_APPS.'ocultas/?a='.$a['id_post'].'" class="btn-app btn-link-app" role="button">VER APPS</a>';
				}

				$output .= '</div>';
				$output .= '</div>';

				$output .= '</div>';
			}
		}

		$output .= '</div>';
		$output .= '</div>';

	else :
		
		$output .= '<h6>' . __( 'No se han encontrado aplicaciones' ) . '</h6>';

	endif;

	return $output;

}

add_shortcode( 'hide-app-list', 'getHideAppsList' );

/** ------------------------
 * Apps Home List Shortcode
 * -------------------------
 */
function getAppsListHome( $atts ) {

	if( !is_elementor_edit_mode() ) {

		$params = shortcode_atts( array(
				'max'	   => 6,
				'orderby'  => 'menu_order',
				'filter'   => 'all',
				'order'    => 'ASC'
								
			), $atts );

		$args = array(
			'post_type'        => 'apps',
	  		'post_status'      => 'publish',
	  		'orderby'          => $params['orderby'],
	  		'order'			   => $params['order'],
	  		'posts_per_page'   => -1,
	  		'post_parent'	   => 0
		);

		$apps = new WP_Query( $args );

		$output = '';

		if( $apps->have_posts() ) :

			$user_id = get_current_user_id();

			$metaUserApp = get_user_meta( $user_id, '_hide_apps_by_'.$user_id, true );
			if( !empty($metaUserApp) ) {
				$arrayApps   = explode( ',', $metaUserApp );
			}

			$metaUserOrderApp = get_user_meta( $user_id, '_reorder_apps_by_'.$user_id, true );
			$arrayOrder 	  = false;

			if( $metaUserOrderApp ) {
				$arrayOrder = explode( ',', $metaUserOrderApp );
			}

			$dataAppsObject = array();

			$output .= '<div id="applications-list-home" class="wrapper-apps">';
			$output .= '<div class="row-apps">';

			$appIndex = 0;

			while( $apps->have_posts() ) :

				$apps->the_post();

				$id_post   		= get_the_ID();
				$title     		= get_the_title();
				$image     		= wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
				$link_app 		= get_field( 'link_app', $id_post );
				$app_icon 	    = get_field( 'app_icon', $id_post );
				$show 		    = true;

				if( !empty($metaUserApp) ) {
					if( in_array( $id_post, $arrayApps ) ) $show = false;
				}

				if( $arrayOrder ) {
					if( in_array( $id_post, $arrayOrder ) ) {
						$index = array_search( $id_post, $arrayOrder );
					} else {
						$index = 999 + $appIndex;
					}
				} else {
					$index = $appIndex;
				}

				if( get_field( 'has_parent', $id_post ) ) {
					$link_app = CORE_HRE_URL_APPS . '?a=' . $id_post;
				}

				$dataAppsObject[$index] = array(
					'id_post' 			=> $id_post,
					'title' 			=> $title,
					'image' 			=> $image,
					'link_app' 			=> $link_app,
					'app_icon' 			=> $app_icon,
					'show'		 		=> $show
				);

				$appIndex++;

			endwhile;

			wp_reset_query();

			$maxAppsHome = intval( $params['max'] );
			$countShow   = 0;


			if( !empty( $dataAppsObject ) ) {

				ksort($dataAppsObject);

				foreach( $dataAppsObject as $a ) {
					if( $countShow >= $maxAppsHome ) {
						break;
					}

					if( $a['show'] ) {
						$output .= '<div class="col-apps-home">';

						$output .= '<div class="card-app-wrapper-home">';
						if( $a['link_app'] ) {
							$output .= '<a href="'.$a['link_app'].'" target="_blank">';
						}
						if( !empty( $a['image'] ) ) {
							$output .= '<img class="img-app-box-home" src="'.$a['image'][0].'" alt="'.$a['title'].'" />';
						} else {
							$output .= '<div class="icon-box-app-home">';
							$output .= $a['app_icon'];
							$output .= '</div>';
						}
						$output .= '<h5>'.$a['title'].'</h5>';
						if( $a['link_app'] ) {
							$output .= '</a>';
						}
						$output .= '</div>';

						$output .= '</div>';

						$countShow++;
					}
				}
			}

			$output .= '</div>';
			$output .= '</div>';

		else :
			
			$output .= '<h6>' . __( 'No se han encontrado aplicaciones' ) . '</h6>';

		endif;

		return $output;

	}

}

add_shortcode( 'app-list-home', 'getAppsListHome' );