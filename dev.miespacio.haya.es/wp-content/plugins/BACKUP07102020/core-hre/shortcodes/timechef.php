<?php 

function getTodaysMenu() {
    $user     = CORE_HRE_USER_TIMECHEF;
    $password = CORE_HRE_PASSW_TIMECHEF;

    if( empty($_SESSION) ) session_start();

    if( empty($_SESSION['token_timechef']) ) {

		$curl = curl_init( CORE_HRE_TOKEN_TIMECHEF );

		curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, false);
		curl_setopt( $curl, CURLOPT_HEADER, false);
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt( $curl, CURLOPT_POST, true);
		curl_setopt( $curl, CURLOPT_POSTFIELDS, rawurldecode( http_build_query( array(
		'username'   => $user,
		'password'   => $password,
		'grant_type' => 'password'
		) ) ) );

		$json = json_decode( curl_exec( $curl ) );

		$_SESSION['token_timechef'] = $json->access_token;

	}
    $numberDaysM = date( 't' );
    $dateStart   = date( 'Y-m-01' );
    $dateEnd     = date( 'Y-m-'.$numberDaysM );

    $urlMenu = 'https://apps.serunion.com/Evan/api/Menu/GetList?IdMenu=-1&IdCeco=L0JE08&IdIdioma=es&FechaIni='.$dateStart.'&FechaFin='.$dateEnd.'&IdTipoComensal=2&IdDieta=-1&IdIngesta=-1&ConIncompatibilidades=1&IdUsuarioRE=2357329&IdServicioRE=7';

    $headers = array(
        'Content-Type: application/json',
        sprintf( 'Authorization: Bearer %s', $_SESSION['token_timechef'] )
    );

    $curl = curl_init( $urlMenu );

    curl_setopt( $curl, CURLOPT_HTTPHEADER, $headers );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );

    $resultMenus = json_decode( curl_exec( $curl ) );

    $output = '';

    if( !empty( $resultMenus ) && !is_object( $resultMenus ) ) {

        $MenuIngestaLst = $resultMenus[0]->MenuIngestaLst;

        if( !empty( $MenuIngestaLst ) ) {

            foreach( $MenuIngestaLst as $ingesta ) {
                $output .= '<h3 class="todays-menu-title elementor-heading-title">MENÚ '.$ingesta->DescIngesta.'</h3>';
                $output .= '<div class="dishes-row-menu">';

                $MenuOrdenLst = $ingesta->MenuOrdenLst;

                foreach ( $MenuOrdenLst as $menu ) {
                    if( $menu->Descripcion != 'BUFFET' ) {
                        $output .= '<div class="dishes-col-menu">';
                        $output .= '<h6 class="dishes-title elementor-heading-title">'.$menu->Descripcion.'</h6>';
                        $output .= '<ul class="dishes-list-menu">';

                        foreach ( $menu->FTClienteLst as $lstMenu ) {
                            $output .= '<li><i class="fas fa-utensils"></i> <span class="desc-dish">'.$lstMenu->DescripcionComercial.'</span>';
                            /*if( !empty( $lstMenu->Incompatibilidades ) ) {
                                $output .= ' <small>(';
                                foreach ( $lstMenu->Incompatibilidades as $inc ) {
                                    $output .= $inc->DescIncompatibilidad . '. ';
                                }
                                $output .= ')</small>';
                            }*/

                            //$output .= '<span class="price-dish">'.$lstMenu->PrecioBruto.' €</span>';
                            $output .= '</li>';
                        }

                        $output .= '</ul>';
                        $output .= '</div>';
                    }
                }

                $output .= '</div>';

            }

        } else {
            $output .= '<p>No hay menú del día para este mes.</p>';
        }

    } else {
        $output .= '<p>No hay menú del día para este mes.</p>';
    }

    curl_close( $curl );

    return $output;
}

add_shortcode( 'menu-del-dia', 'getTodaysMenu' );