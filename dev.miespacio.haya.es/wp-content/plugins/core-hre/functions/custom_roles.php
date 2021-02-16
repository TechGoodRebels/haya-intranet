<?php 

function custom_nombres_roles() {
     
    global $wp_roles;
     
    if ( !isset( $wp_roles ) ) {
        $wp_roles = new WP_Roles();
    }
    
    // Renombrar subscriptor por empleado
    $wp_roles->roles['subscriber']['name'] = 'Employee';
    $wp_roles->role_names['subscriber'] = 'Employee';

    // Borrar roles no usados
    remove_role( 'author' );
    remove_role( 'contributor' );
    remove_role( 'wpseo_editor' );
    remove_role( 'wpseo_manager' );
 
}

add_action('init', 'custom_nombres_roles');