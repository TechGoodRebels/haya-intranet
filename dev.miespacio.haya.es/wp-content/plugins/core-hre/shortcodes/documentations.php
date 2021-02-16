<?php 

function downloads_custom_shortcode() {

    $terms = get_terms( array( 'taxonomy' => 'wpdmcategory' ) );

    $currentCategory = $_REQUEST['f'] ?? false;

    if( !$currentCategory ) {
    	$arrayTerms = array();
    	foreach( $terms as $term ) {
    		array_push( $arrayTerms, $term->slug );
    	}
    	$currentCategory = implode( ',', $arrayTerms );
    }

    $output = do_shortcode( '[wpdm_category id="'.$currentCategory.'" operator="IN" toolbar="1" order_by="title" order="asc" item_per_page="12" template="page-template-extended" cols=1 colspad=1 colsphone=1]' );

    return $output;
}

add_shortcode( 'documentation', 'downloads_custom_shortcode' );

function downloads_tree_folders() {

    $downloadsTerms = get_terms( array( 'taxonomy' => 'wpdmcategory', 'parent' => 0 ) );
    $output 		= '';

    if( !empty( $downloadsTerms ) ) {
        $output .= '<ul class="tree-repository main-menu-repository">';
        foreach( $downloadsTerms as $term ) : 

        	$childs  = has_child_taxonomy( $term->term_id );

            $output .= '<li id="term-'.$term->term_id.'" class="menu-item-repository '.( !empty( $childs ) ? 'has-parent' : '' ).'">';
            $output .= '<a href="'.CORE_HRE_URL_REPOSITORY.'?f='.$term->slug.'"><i class="fas fa-folder-open"></i> <span>'.$term->name.'</span></a>';
            $output .= !empty( $childs ) ? '<span class="submenu-handler"><i class="fas fa-caret-down"></i></span>' : '';
            $output .= '<div class="clearfix"></div>';
            $output .= get_child_taxonomy( $childs, $term->term_id );
            $output .= '</li>';

        endforeach;
        $output .= '<li class="menu-item-repository">';
        $output .= '<a href="'.home_url().'/documentacion/lectura-obligatoria"><i class="fas fa-folder-open"></i> <span>Lectura obligatoria</span></a>';
        $output .= '<div class="clearfix"></div>';
        $output .= '</li>';
        $output .= '</ul>';
    } else {
    	$output = '<h6>No se han encontrado carpetas del repositorio</h6>';
    }

    return $output;
}

add_shortcode( 'downloads-category-menu', 'downloads_tree_folders' );

function has_child_taxonomy( $parent = 0 ) {
	return $childDownloadsTerms = get_terms( array( 'taxonomy' => 'wpdmcategory', 'parent' => $parent ) );
}

function get_child_taxonomy( $childDownloadsTerms = '', $id_term = 0 ) {
	$outputchild = '';
	
    if( !empty( $childDownloadsTerms ) ) {
        $outputchild .= '<ul class="tree-repository sub-menu sub-menu-repository term-'.$id_term.'">';
        foreach( $childDownloadsTerms as $term ) : 

        	$childs  	  = has_child_taxonomy( $term->term_id );

            $outputchild .= '<li id="term-'.$term->term_id.'" class="sub-menu-item-repository '.( !empty( $childs ) ? 'has-parent' : '' ).'">';
            $outputchild .= '<a href="'.CORE_HRE_URL_REPOSITORY.'?f='.$term->slug.'"><span>'.$term->name.'</span></a>';
            $outputchild .= !empty( $childs ) ? '<span class="submenu-handler"><i class="fas fa-caret-down"></i></span>' : '';
            $outputchild .= '<div class="clearfix"></div>';
            $outputchild .= get_child_taxonomy( $childs, $term->term_id );
            $outputchild .= '</li>';

        endforeach; 
        
        $outputchild .= '</ul>';
    }

    return $outputchild;

}

function wpdocs_custom_init() {
    remove_post_type_support( 'wpdmpro', 'editor' );
    remove_post_type_support( 'wpdmpro', 'author' );
    remove_post_type_support( 'wpdmpro', 'excerpt' );
    remove_post_type_support( 'wpdmpro', 'comments' );
    remove_post_type_support( 'wpdmpro', 'trackbacks' );
    remove_post_type_support( 'wpdmpro', 'page-attributes' );
    remove_post_type_support( 'wpdmpro', 'post-formats' );
}

add_action( 'init', 'wpdocs_custom_init', 100 );