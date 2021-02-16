<?php 

/** ------------------------
 * Vacancies List Shortcode
 * -------------------------
 */
function getVacanciesList( $atts ) {

	$params = shortcode_atts( array(
			'orderby'  => 'date',
			'filter'   => 'all',
			'order'    => 'DESC'
							
		), $atts );

	$args = array(
		'post_type'        => 'vacantes',
  		'post_status'      => 'publish',
  		'orderby'          => $params['orderby'],
  		'order'			   => $params['order'],
  		'posts_per_page'   => -1
	);

	$vacancies = new WP_Query( $args );

	$output = '';

	if( $vacancies->have_posts() ) :

		$output .= '<div id="custom-posts-list" class="wrapper-vacancies">';
		$output .= '<div class="row-custom-posts">';

		while( $vacancies->have_posts() ) :

			$vacancies->the_post();

			$id_post   		 = get_the_ID();
			$title     		 = get_the_title( $id_post );
			$image     		 = wp_get_attachment_image_src( get_post_thumbnail_id( $id_post ), 'single-post-thumbnail' );
			$content   		 = get_the_excerpt( $id_post );
			$link_course	 = get_field( 'link_vacante', $id_post );
			$featured        = get_field( 'vacante_destacada', $id_post );
			$featured_text 	 = get_field( 'texto_destacado_vacante', $id_post );
			$addClass 		 = '';

			if( $featured ) $addClass = 'featured-post';

			$content = strlen( $content ) > 140 ? substr( $content, 0, 140 ) . '...' : $content;
			$image   = !empty( $image ) ? $image : CORE_HRE_DIR_URL . 'imgs/no-image.jpg';

			$output .= '<div class="col-custom-posts '.$addClass.'" data-appid="'.$id_post.'">';

			$output .= '<div class="card-custom-post">';

			if( $featured ) {
				$output .= '<div class="ribbon-post">'.( !empty($featured_text) ? $featured_text : 'Vacante destacado' ).'</div>';
			}
			$output .= '<div class="bg-image-wrapper">';
			$output .= '<div class="bg-image-post" style="background-image:url('.$image[0].');"></div>';
			$output .= '</div>';
			$output .= '<h3 class="title-custom-post">'.$title.'</h3>';
			$output .= '<p class="content-custom-post">'.$content.'</p>';

			if( $link_course ) {
				$output .= '<a class="link-post" href="'.$link_course.'" target="_blank">IR AL FORMULARIO</a>';
			}

			$output .= '</div>';

			$output .= '</div>';

		endwhile;

		wp_reset_query();

		$output .= '</div>';
		$output .= '</div>';

	else :
		
		$output .= '<h6>' . __( 'No se han encontrado vacantes' ) . '</h6>';

	endif;

	return $output;

}

add_shortcode( 'vacancies-list', 'getVacanciesList' );