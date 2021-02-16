<?php 

function getDirectoryActive( $atts ) {

    global $ultimatemember;

    $default_avatar_uri = um_get_default_avatar_uri();

    $params = shortcode_atts( array(
        'per_page'  => 20,
        'cols'      => 5                   
    ), $atts );

    // Variables del servidor LDAP
    $ldaphost       = CORE_HRE_LDAP_HOST;   // Servidor LDAP
    $username       = CORE_HRE_LDAP_USER; 	// Usuario maestro
    $userpassword   = CORE_HRE_LDAP_PASSW;  // Password
    $basedn         = CORE_HRE_LDAP_BASEDN; // Base DN
    $searchAttr     = array( 
    	'givenname', 
    	'sn', 
    	'mail', 
    	'telephonenumber', 
    	'mobile', 
    	'department', 
    	'title', 
    	'hayaoficina',
        'memberof'
    );
    $filter = '(&(objectCategory=Person)(sAMAccountName=*)(memberOf:1.2.840.113556.1.4.1941:=CN=servicio_vpn_empleados,OU=Servicios,OU=Grupos,OU=Usuarios,DC=corp,DC=int))';

    $search         = !empty( $_REQUEST['employee'] ) ? $_REQUEST['employee'] : false;
    $office         = !empty( $_REQUEST['office'] ) ? $_REQUEST['office'] : 'all';

    // Conexión al servidor LDAP
    $ad = ldap_connect( "ldap://$ldaphost" );

    $output = '';

    if( $ad ) {

        if( @ldap_bind( $ad, $username, $userpassword ) ) {

            $cacheResult = apc_fetch( 'ldap_results' );

            if( !$cacheResult || $cacheResult == NULL ) {
                $result         = ldap_search( $ad, $basedn, $filter, $searchAttr );
                $countResult    = ldap_count_entries( $ad, $result );
                $parseResult    = ldap_get_entries( $ad, $result );
            } else {
                $countResult    = count( $cacheResult );
                $parseResult    = $cacheResult;
            }

            $data           = array();
            $listEmailsUser = array();
            $listOffices    = array();

            if( $countResult > 0 ) {

                apc_store( 'ldap_results', $parseResult, 3600 );

                $countDataResult = 0;

                for( $o = 0; $o < $countResult; $o++ ) {

                    if( !empty( $parseResult[$o]['hayaoficina'][0] ) && !in_array( $parseResult[$o]['hayaoficina'][0], $listOffices ) ) {
                        array_push( $listOffices, $parseResult[$o]['hayaoficina'][0] );
                    }

                    if( !empty($parseResult[$o]) && $parseResult[$o]['count'] > 0 && $parseResult[$o]['givenname'][0] != '' && $parseResult[$o]['mail'][0] != '' ) {
                        if( !empty( $search ) ) {
                            if( !stristr( utf8_encode( $parseResult[$o]['givenname'][0] ).' '.utf8_encode( $parseResult[$o]['sn'][0] ), $search ) ) {
                                continue;
                            }
                        }

                        $hayaOficina = utf8_encode( $parseResult[$o]['hayaoficina'][0] );

                        if( $office != 'all' && $office != $hayaOficina ) {
                            continue;
                        }
                        
                        $data[$countDataResult] = $parseResult[$o];
                        $countDataResult++;

                        // create_employee_data( $parseResult[$o] );

                    }

                }

                sort( $listOffices );

                $per_page = $params['per_page'];
                $cols     = $params['cols'];

                if( !empty( $data ) && $countDataResult > $params['per_page'] ) {
                    $pages = ceil( $countDataResult/$params['per_page'] );
                } else {
                    $pages = 1;
                }

                $output .= '<form id="directory-active-form" action="" method="GET">';
                $output .= '<div class="col-form col-search-directory">';
                $output .= '<input type="text" class="input-search-directory" name="employee" value="'.$search.'" placeholder="Buscar por nombre, email o teléfono." />';
                $output .= '</div>';
                $output .= '<div class="col-form col-office-directory">';
                $output .= '<select class="input-office-directory" name="office">';
                $output .= '<option value="all" '.( $office == 'all' ? 'selected' : '' ).'>Oficina</option>';

                foreach( $listOffices as $opt ) {
                    $currentOffice = utf8_encode( $opt );
                    $output .= '<option value="'.$currentOffice.'"';
                    $output .= $office == $currentOffice ? ' selected' : ''; 
                    $output .= '>'.$currentOffice.'</option>';
                }

                $output .= '</select>';
                $output .= '</div>';
                $output .= '<div class="col-form col-submit-directory">';
                $output .= '<input type="submit" class="input-submit-directory" value="Buscar" />';
                $output .= '</div>';
                $output .= '</form>';
                $output .= '<div style="clear: both"></div>';

                $output .= '<div id="directory-active" data-count="'.$countDataResult.'">';
                if( !empty( $_REQUEST['employee'] ) || !empty( $_REQUEST['office'] ) ) {
                    $output .= '<p class="directory-results">Se han encontrado '.$countDataResult.'. <a href="'.CORE_HRE_URL_DIRECTORY.'">Resetear busqueda</a>.</p>';
                }

                $currentPage = $_REQUEST['pag'] ?? 1;

                $offset = ( $currentPage - 1 ) * $params['per_page'];
                $limit  = $currentPage * $params['per_page'];

                $output .= '<div class="pagination-directory-active" id="page-'.$currentPage.'">';
                $output .= '<div class="wrapper-directory-active">';
                $output .= '<div class="row-directory-active">';

                for( $i = $offset; $i < $limit; $i++ ) {

                    $output .= '<div class="col-directory-active">';

                    if( !empty( $data[$i] ) ) {

                        $idUserCurrentDirectory = get_user_by( 'email', $data[$i]['mail'][0] );

                        if( $idUserCurrentDirectory ) {
                            um_fetch_user( $idUserCurrentDirectory->ID );

                            $photo    = um_get_user_avatar_url();
                            $bg_image = $photo ? $photo : $default_avatar_uri;
                        } else {
                            $bg_image = $default_avatar_uri;
                        }

                        if( $bg_image === $default_avatar_uri ) { $listEmailsUser[] = $data[$i]['mail'][0]; }
                            
                        $output .= '<div class="directory-card" data-user="'.$data[$i]['mail'][0].'">';
                        $output .= '<div class="directory-image empleado-popup">';
                        $output .= '<div class="directory-image-bg '.( $bg_image === $default_avatar_uri ? 'no-image' : '' ).'" style="background-image:url('.$bg_image.')" data-bg="'.$bg_image.'"></div>';
                        $output .= '</div>';
                        $output .= '<div class="directory-content">';
                        
                        $output .= '<div class="directory-icon-wrapper">';
                        $output .= '<a class="directory-icon empleado-popup" href="#">';
                        $output .= '<i aria-hidden="true" class="far fa-envelope"></i>';
                        $output .= '</a>';
                        $output .= '</div>';
                        
                        $output .= '<h4 class="elementor-heading-title elementor-size-default empleado-popup">'.utf8_encode( $data[$i]['givenname'][0] ).' '.utf8_encode( $data[$i]['sn'][0] ).'</h4>';
                        $output .= '<p>'.utf8_encode( $data[$i]['hayaoficina'][0] ).'</p>';
                        $output .= '<div class="data-directory"';
                        $output .= ' data-fullname="'.utf8_encode( $data[$i]['givenname'][0] ).' '.utf8_encode( $data[$i]['sn'][0] ).'"';
                        $output .= ' data-tel="'.$data[$i]['telephonenumber'][0].'"';
                        $output .= ' data-email="'.$data[$i]['mail'][0].'"';
                        $output .= ' data-departament="'.utf8_encode( $data[$i]['department'][0] ).'"';
                        $output .= ' data-office="'.utf8_encode( $data[$i]['hayaoficina'][0] ).'"';
                        $output .= ' data-ext="'.utf8_encode( $data[$i]['mobile'][0] ).'"';
                        $output .= ' data-teams="https://teams.microsoft.com/l/chat/0/0?users='.$data[$i]['mail'][0].'"';
                        $output .= '></div>';
                        $output .= '</div>';
                        $output .= '</div>';

                    }

                    $output .= '</div>';

                }

                $output .= '</div>';
                $output .= '</div>';
                $output .= '</div>';

                if( $pages > 1 ) {

                    $output .= '<div class="directory-pagination full-pagination" id="pagination" data-current="1" data-container="pagination-directory-active">';
                    $output .= '<ul class="ul-pagination">';

                    for( $p = 1; $p <= $pages; $p++ ) {
                        $output .= '<a data-pagination="'.$p.'" data-id="pagination" class="pag-link';
                        $output .= $p == $currentPage ? ' active' : '';
                        $output .= '" href="'.CORE_HRE_URL_DIRECTORY.'?pag='.$p;
                        $output .= $search ? '&employee='.$search : '';
                        $output .= $office ? '&office='.$office : '';
                        $output .= '"><li>'.$p.'</li></a>';
                    }

                    $output .= '</ul>';
                    $output .= '</div>';

                }

                $output .= '</div>';

            } else {
                $output .= '<h6>No se han encontrado resultados. <a href="'.CORE_HRE_URL_DIRECTORY.'">Resetear busqueda</a></h6>';
            }

            $output .= '<script type="text/javascript">';
            if( $parseResult ) { 
                $output .= 'autoCompleteDirectory = [';

                    foreach( $parseResult as $ac ) {

                        $output .= '{';

                        $label = '';
                        $value = utf8_encode( $ac['givenname'][0] ).' '.utf8_encode( $ac['sn'][0] );

                        if( !empty( $ac ) ) {
                            if( !empty( $ac['givenname'][0] ) ) {
                                $label .= utf8_encode( $ac['givenname'][0] ).' '.utf8_encode( $ac['sn'][0] ) . ' - ';
                            }

                            if( !empty( $ac['mail'][0] ) ) {
                                $label .= 'Email: ' . $ac['mail'][0] . '. ';
                            }

                            if( !empty( $ac['telephonenumber'][0] ) ) {
                                $label .= 'Tlf: ' . $ac['telephonenumber'][0] . '. ';
                            }
                        }

                        if( empty( $label ) ) $label = $value;

                        $output .= 'label: "'.$label.'", value: "'.$value.'"';

                        $output .= '},';
                        
                    }

                $output .= '];';
                
            } 
            
            if( $listEmailsUser ) {
                $output .= 'emailsDirectory = new Array(';

                foreach( $listEmailsUser as $email ) {

                    if( !empty( $email ) ) {
                        $output .= '"' . $email . '",';
                    }
                        
                }

                $output .= ');';
            }
            $output .= '</script>';

            ldap_close( $ad );

        } else {

            $output .= '<h6>No se ha podido conectar con el directorio.</h6>';
            $output .= '<div class="ldap_error">Ldap_error: ' . ldap_error( $ad ) . '</div>';

        }

    } else {

        $output .= '<h6>No se ha podido conectar con el directorio.</h6>';
        $output .= '<div class="ldap_error">Ldap_error: ' . ldap_error( $ad ) . '</div>';

    }

    return $output;

}

add_shortcode( 'directorio', 'getDirectoryActive' );

function create_employee_data( $data = false ) {

    if( $data ) {
        $title = utf8_encode( $data['givenname'][0] ).' '.utf8_encode( $data['sn'][0] );

        $content = '';

        if( isset($data['mail'][0]) ) 
            $content .= '<strong>Email:</strong> '.$data['mail'][0].'. ';
        if( isset($data['telephonenumber'][0]) ) 
            $content .= '<strong>Tlf:</strong> '.$data['telephonenumber'][0].'. ';
        if( isset($data['mobile'][0]) ) 
            $content .= '<strong>Ext:</strong> '.$data['mobile'][0].'. ';
        if( isset($data['departament'][0]) ) 
            $content .= '<strong>Dpto:</strong> '.utf8_encode( $data['departament'][0] ).'. ';
        if( isset($data['hayaoficina'][0]) ) 
            $content .= '<strong>Oficina:</strong> '.utf8_encode( $data['hayaoficina'][0] ).'. ';

        $post_id = wp_insert_post( array(
           'post_type'      => 'empleados',
           'post_title'     => $title,
           'post_content'   => $content,
           'post_status'    => 'publish',
           'comment_status' => 'closed',   
           'ping_status'    => 'closed'    
        ) );

        return $post_id;

    }

}