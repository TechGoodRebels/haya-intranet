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
    	'HayaOrgNivel1',
        'HayaOrgNivel2',
        'HayaOrgNivel3',
        'HayaOrgNivel4',
        'HayaOrgNivel5',
        'memberof'
    );
    $filter = '(&(objectCategory=Person)(sAMAccountName=*)(memberOf:1.2.840.113556.1.4.1941:=CN=servicio_vpn_empleados,OU=Servicios,OU=Grupos,OU=Usuarios,DC=corp,DC=int))';

    $search       = !empty( $_REQUEST['employee'] ) ? $_REQUEST['employee'] : false;
    $office       = !empty( $_REQUEST['office'] ) ? $_REQUEST['office'] : 'all';
    $dept         = !empty( $_REQUEST['department'] ) ? $_REQUEST['department'] : 'all';

    $pathPhotos   = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/uploads/photos/';
    $urlPhotos    = home_url() . '/wp-content/uploads/photos/';

    $output       = '';

    $ldap_results = false;
    $cacheResult  = apc_fetch( 'ldap_results' );

    if( !$cacheResult || $cacheResult == NULL ) {

        // Conexión al servidor LDAP
        $ad = ldap_connect( "ldap://$ldaphost" );

        if( $ad ) {

            if( @ldap_bind( $ad, $username, $userpassword ) ) {

                $result         = ldap_search( $ad, $basedn, $filter, $searchAttr );
                $countResult    = ldap_count_entries( $ad, $result );
                $parseResult    = ldap_get_entries( $ad, $result );
                
                if( $countResult > 0 ) {

                    $ldap_results = $parseResult;
                    apc_store( 'ldap_results', $parseResult, 3600 * 4 );

                }

            }
        }

        ldap_close( $ad );

    } else { $ldap_results = $cacheResult; }

    if( $ldap_results ) {

        $data             = array();
        $listEmailsUser   = array();
        $listOffices      = array();
        $listDepartaments = array();
        $arrayAddress     = init_array_address();

        $countResult     = $countResult ?? count( $ldap_results );
        $countDataResult = 0;

        for( $o = 0; $o < $countResult; $o++ ) {

            if( !empty( $ldap_results[$o]['hayaoficina'][0] ) && !in_array( $ldap_results[$o]['hayaoficina'][0], $listOffices ) ) {
                array_push( $listOffices, $ldap_results[$o]['hayaoficina'][0] );
            }

            if( !empty( $ldap_results[$o]['department'][0] ) && !in_array( $ldap_results[$o]['department'][0], $listDepartaments ) ) {
                array_push( $listDepartaments, $ldap_results[$o]['department'][0] );
            }

            if( !empty($ldap_results[$o]) && $ldap_results[$o]['count'] > 0 && $ldap_results[$o]['givenname'][0] != '' && $ldap_results[$o]['mail'][0] != '' ) {
                if( !empty( $search ) ) {
                    if( !stristr( utf8_encode( $ldap_results[$o]['givenname'][0] ).' '.utf8_encode( $ldap_results[$o]['sn'][0] ), $search ) ) {
                        continue;
                    }
                }

                $hayaOficina     = utf8_encode( $ldap_results[$o]['hayaoficina'][0] );
                $hayaDepartament = utf8_encode( $ldap_results[$o]['department'][0] );

                if( $office != 'all' && $office != $hayaOficina ) {
                    continue;
                }

                if( $dept != 'all' && $dept != $hayaDepartament ) {
                    continue;
                }
                
                $data[$countDataResult] = $ldap_results[$o];
                $countDataResult++;

                // create_employee_data( $ldap_results[$o] );

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
        $output .= '<option value="all" '.( $office == 'all' ? 'selected' : '' ).'>- Oficina -</option>';

        foreach( $listOffices as $opt ) {
            $currentOffice = utf8_encode( $opt );
            $output .= '<option value="'.$currentOffice.'"';
            $output .= $office == $currentOffice ? ' selected' : ''; 
            $output .= '>'.get_address( $currentOffice, $arrayAddress ).'</option>';
        }

        $output .= '</select>';
        $output .= '</div>';
        $output .= '<div class="col-form col-department-directory">';
        $output .= '<select class="input-department-directory" name="department">';
        $output .= '<option value="all" '.( $dept == 'all' ? 'selected' : '' ).'>- Departamento -</option>';

        foreach( $listDepartaments as $opt ) {
            $currentDepartament = utf8_encode( $opt );
            $output .= '<option value="'.$currentDepartament.'"';
            $output .= $dept == $currentDepartament ? ' selected' : ''; 
            $output .= '>'.$currentDepartament.'</option>';
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

                $bg_image = $default_avatar_uri;

                if( $idUserCurrentDirectory ) {
                    um_fetch_user( $idUserCurrentDirectory->ID );
                    $bg_image = um_get_user_avatar_url();
                } 

                if( $bg_image === $default_avatar_uri ) {
                    if( file_exists( $pathPhotos . $data[$i]['mail'][0] .'.png' ) ) {
                        $bg_image = $urlPhotos . $data[$i]['mail'][0] .'.png';
                    }
                }

                if( $bg_image === $default_avatar_uri ) { 
                    $listEmailsUser[] = $data[$i]['mail'][0]; 
                }
                    
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

                $addressOffice = get_address( utf8_encode( $data[$i]['hayaoficina'][0] ), $arrayAddress );

                if( $addressOffice != $data[$i]['hayaoficina'][0] ) {
                    $partsOffice         = explode( ' ', $data[$i]['hayaoficina'][0] );
                    $suitcase            = $partsOffice[count( $partsOffice ) - 1];
                    $customAddressOffice = $addressOffice . ' (' . $suitcase . ')';
                } else {
                    $customAddressOffice = $data[$i]['hayaoficina'][0];
                }

                $output .= '<p>'.utf8_encode( $data[$i]['hayaoficina'][0] ).'</p>';
                $output .= '<div class="data-directory"';
                $output .= ' data-fullname="'.utf8_encode( $data[$i]['givenname'][0] ).' '.utf8_encode( $data[$i]['sn'][0] ).'"';
                $output .= ' data-title="'.utf8_encode( $data[$i]['title'][0] ).'"';
                $output .= ' data-tel="'.$data[$i]['telephonenumber'][0].'"';
                $output .= ' data-email="'.$data[$i]['mail'][0].'"';
                $output .= ' data-department="'.utf8_encode( $data[$i]['department'][0] ).'"';
                $output .= ' data-office="'.$customAddressOffice.'"';
                $output .= ' data-ext="'.utf8_encode( $data[$i]['mobile'][0] ).'"';
                $output .= ' data-nivel1="'.utf8_encode( $data[$i]['hayaorgnivel1'][0] ).'"';
                $output .= ' data-nivel2="'.utf8_encode( $data[$i]['hayaorgnivel2'][0] ).'"';
                $output .= ' data-nivel3="'.utf8_encode( $data[$i]['hayaorgnivel3'][0] ).'"';
                $output .= ' data-nivel4="'.utf8_encode( $data[$i]['hayaorgnivel4'][0] ).'"';
                $output .= ' data-nivel5="'.utf8_encode( $data[$i]['hayaorgnivel5'][0] ).'"';
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
                $output .= $dept ? '&department='.$dept : '';
                $output .= '"><li>'.$p.'</li></a>';
            }

            $output .= '</ul>';
            $output .= '</div>';

        }

        $output .= '</div>';

        $output .= '<script type="text/javascript">';
        if( $ldap_results ) { 
            $output .= 'autoCompleteDirectory = [';

                foreach( $ldap_results as $ac ) {

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

                        if( !empty( $ac['department'][0] ) ) {
                            $label .= 'Departamento: ' .utf8_encode(  $ac['department'][0] ). '. ';
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

    } else {

        $output .= '<h6>No se han encontrado resultados.</h6>';
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

function get_address( $suitcase = '', $address = array() ) {
    return !empty( $address[$suitcase] ) ? $address[$suitcase] : $suitcase;
}

function init_array_address() {
    $address = array();

    $address['Alicante 5051-10003']         = 'Edificio Marsamar C/ México, 20-5, Alicante, 03008';
    $address['Almería 5051-10010']          = 'Avenida Cabo de Gata, 23 Almería, 04007';
    $address['Ávila 5051-10015']            = 'Jardín del Recreo, 4 Ávila, 05001';
    $address['Badajoz 5051-10041']          = 'C.N. MEDEA -C/ Ricardo Fernández de la Puente, 29, Badajoz, 06001';
    $address['Barcelona 5051-10017']        = 'Avda. Josep Tarradellas, 34-36, Barcelona, 08029';
    $address['Bilbao 5051-10071']           = 'Calle Rodriguez Arias, 23-5, Bilbao  Vizcaya 48011';
    $address['Ciudad Real']                 = 'Calle General Aguilera 10, 1º Planta Ciudad Real, 13001';
    $address['Gran Canaria 5051-10007']     = 'Pelota, 17 esq. Cl Armas (Barrio de Vegueta), Las Palmas de Gran Canaria, 35001';
    $address['Jerez Frontera 5051-10049']   = 'C/ Bizcocheros, 2, Duplicado - Oficina 12, Jerez de la Frontera, Cadiz, 11402';
    $address['La Coruña 5051-10052']        = 'Rua Enrique Mariñas Romero Periodista, 36, La Coruña   La Coruña 15009';
    $address['Logroño 5051-10014']          = 'Calle Vara del Rey, 42 piso 4º, Logroño, La Rioja 26002';
    $address['Madrid 5051-7000']            = 'Calle Medina de Pomar, 27, Madrid, 28042';
    $address['Madrid-Serrano 5051-10019']   = 'Calle Serrano 47, 3º Madrid, 28001';
    $address['Málaga 5051-10009']           = 'Alameda de Colón, 15 Málaga, 29001';
    $address['Murcia 5051-10011']           = 'Avenida Alfonso X el sabio, 11, Local H Entresuelo, Murcia, 30008';
    $address['Palma de Mallorca']           = 'C/ Tous y Maroto, 10 (Centro Negocios Veri), Palma de Mallorca, Baleares, 07001';
    $address['Santander 5051-10064']        = 'Calle Lealtad, 14, 5ª, Santander, Cantabria, 39002';
    $address['Sevilla 5051-10008']          = 'Calle Buhaira 28 Sevilla, 41018';
    $address['Tarragona 5051-10066']        = 'Carrer d\'En Granada, 16, Tarragona, 43003';
    $address['Tenerife 5051-10062']         = 'Paseo Milicias de Garachico, 1; Local 31, Santa Cruz de Tenerife, 38002';
    $address['Terrassa 5051-10045']         = 'Rambla d\'Ègara, 340  Terrassa, Barcelona, 08221';
    $address['Valencia 5051-5000']          = 'Av. Cardenal Benlloch, 67, Valencia, 46021';
    $address['Valladolid 5051-10070']       = 'Calle Constitución, 5-2, Valladolid, 47001';
    $address['Vic 5051-10047']              = 'Carrer de Figueres, 12 poligon Sot del Pradals, Vic Barcelona, 08500';
    $address['Zaragoza 5051-10072']         = 'CN Plaza España C/Martires, 2, 4º Planta, Oficina 42, Zaragoza, 50003';

    return $address;
}