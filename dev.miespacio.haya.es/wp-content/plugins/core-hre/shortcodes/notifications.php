<?php 

/** ------------------------
 * Require Docs Shortcode
 * -------------------------
 */
function getNotificationsDocs() {

    $objNotifications = get_notification_webservices();

    $output = '';

    if( $objNotifications['Respuesta'] == 'OK' ) {
        $notifications = $objNotifications['Notificaciones'];

        if( !empty( $notifications ) ) {

        	$filterStatus = $_REQUEST['status'] ?? 'pendiente';

        	$output .= '<form id="notification-status-form" action="" method="GET">';
            $output .= '<div class="col-form col-status-notification">';
            $output .= '<select class="input-status-notification" name="status">';
            $output .= '<option value="all" '.( $filterStatus == 'all' ? 'selected' : '' ).'>- Todos los estados -</option>';
            $output .= '<option value="pendiente" '.( $filterStatus == 'pendiente' ? 'selected' : '' ).'>Pendiente de confirmar</option>';
            $output .= '<option value="confirmado" '.( $filterStatus == 'confirmado' ? 'selected' : '' ).'>Confirmado</option>';
            $output .= '<option value="caducado" '.( $filterStatus == 'caducado' ? 'selected' : '' ).'>Caducado</option>';
            $output .= '</select>';
            $output .= '</div>';
            $output .= '<div class="col-form col-submit-notification">';
            $output .= '<input type="submit" class="input-submit-notification" value="Filtrar" />';
            $output .= '</div>';
            $output .= '</form>';
            $output .= '<div style="clear: both"></div>';
            $output .= '<div class="list-notifications-docs">';

            foreach( $notifications['NotificacionUsuario'] as $notification ) {

                $hasExpires = strtotime( $notification['FechaPlazo'] ) < strtotime( 'now' ) ?  true : false;

                switch( $notification['Estado'] ) {
                    case 'N':
                    case 'L': 
                    case 'A': $statusDoc = 'Pdte. confirmar';   break;
                    case 'C': $statusDoc = 'Confirmado';        break;
                }

                if( $notification['Estado'] == 'B' ) {
                    continue;
                }

                if( $hasExpires && $statusDoc == 'Pdte. confirmar' ) $statusDoc = 'Caducado';

                if( $filterStatus != 'all' ) {
                    if( $filterStatus == 'pendiente' && $statusDoc != 'Pdte. confirmar' )   continue;
                    if( $filterStatus == 'confirmado' && $statusDoc != 'Confirmado' )       continue;
                    if( $filterStatus == 'caducado' && $statusDoc != 'Caducado' )           continue;
                }

                $dateStart  = date( 'd/m/Y', strtotime( $notification['FechaEnvio'] ) );
                $dateExpire = date( 'd/m/Y', strtotime( $notification['FechaPlazo'] ) );

                switch( $statusDoc ) {
                    case 'Pdte. confirmar': $classStatus = 'pending';   break;
                    case 'Confirmado':      $classStatus = 'confirm';   break;
                    case 'Caducado':        $classStatus = 'expires';   break;
                }

                $message    = strlen( $notification['Mensaje'] ) > 160 ? substr( $notification['Mensaje'], 0, 160 ) . '...' : $notification['Mensaje'];

                $output .= '<div class="notification-doc '.$classStatus.'">';
                
                $output .= '<p class="description-notification-doc">';
                $output .= '<a class="link-notification-doc" href="'.$notification['Link'].'" target="_blank">'.$notification['Titulo'].'</a>';
                $output .= '<em class="date-notification-doc">'.$dateStart.'</em> - '.$message.' ';
                $output .= '</p>';
                $output .= '<p class="description-notification-doc">';
                $output .= '<span class="bubble-notification"><strong><i class="fas fa-user-tie"></i> Area Responsable: </strong>'.$notification['AreaResponsable'].'.</span>';
                $output .= '<span class="bubble-notification"><strong><i class="far fa-hourglass"></i> Fecha Plazo:</strong> '.$dateExpire.'.</span>';
                $output .= '<span class="bubble-notification bubble-status"><strong><i class="fas fa-book"></i> Estado:</strong> '.$statusDoc.'.</span>';
                $output .= '</p>';
                $output .= '</div>';

            }

            $output .= '</div>';

        } else {
            $output .= '<h6>No hay notificationes de lectura obligatoria.</h6>';
        }
    } else {
        $output .= '<h6>No hay notificationes de lectura obligatoria.</h6>';
    }

    return $output;
}

add_shortcode( 'custom-notifications', 'getNotificationsDocs' );

/*

STEP 1: Log types notifications: Require docs & Custom notifications

*/

add_filter( 'um_notifications_core_log_types', 'add_custom_notification_type', 200 );

function add_custom_notification_type( $array ) {
        
    $array['require_docs'] = array(
        'title'         => 'Documento de lectura obligatoria.',                                         // Title for reference in backend settings
        'template'      => '<strong>{doc}</strong> - {message}. Fecha plazo: {expire}. {departament}',  // The template
        'account_desc'  => 'Nuevo documento de lectura obligatoria',                                    // Title for account page (notification settings)
    );

    $array['custom_notifications'] = array(
        'title'         => 'Notificación personalizada.',                                               // Title for reference in backend settings
        'template'      => '<strong>{title}</strong> - {message}. <strong>{departament}</strong>',                                 // The template
        'account_desc'  => 'Notificaciones corporativas',                                               // Title for account page (notification settings)
    );
        
    return $array;
}

/*

STEP 2: Add an icon and color to this new notification type

*/

add_filter( 'um_notifications_get_icon', 'add_custom_notification_icon', 10, 2 );

function add_custom_notification_icon( $output, $type ) {
    
    if ( $type == 'require_docs' ) { 
        $output = '<i class="um-faicon-book" style="color: #BF0086"></i>';
    }

    if ( $type == 'custom_notifications' ) { 
        $output = '<i class="um-icon-android-person" style="color: #001064"></i>';
    }
    
    return $output;
}

/*

STEP 3: Add trigger for custom notifications

*/

add_action( 'template_redirect', 'trigger_new_notification_docs', 100 );

function trigger_new_notification_docs() {

    global $um_notifications;
    global $wpdb;

    if( is_user_logged_in() ) {

    	$wp_current_user = wp_get_current_user();

        $hayaUser = $wp_current_user->user_email;
        $user_id  = $wp_current_user->ID;

        $objNotifications = get_notification_webservices( $hayaUser );

        if( $objNotifications['Respuesta'] == 'OK' ) {

            $notifications = $objNotifications['Notificaciones'];
            
            um_fetch_user( $user_id );

            $photoUser   = um_get_avatar_url( get_avatar( $user_id, 40 ) );
            $displayName = um_user( 'display_name' );

            if( !empty( $notifications ) ) {

            	$table_name = $wpdb->prefix . "um_notifications";
                $type       = 'require_docs';

                // $clearNotifications  = $wpdb->prepare( 'DELETE FROM `' . $wpdb->prefix . 'um_notifications` WHERE user = %d AND type = %s', $user_id, $type );
                // $actionDelete        = $wpdb->query( $clearNotifications );

                $currentNotifications = $wpdb->get_results( $wpdb->prepare(
                    "SELECT * 
                    FROM {$table_name} 
                    WHERE user = %d AND 
                          type = %s",
                    $user_id,
                    $type
                ) );

                foreach( $notifications['NotificacionUsuario'] as $notification ) {

                    $hasExpires = strtotime( $notification['FechaPlazo'] ) < strtotime( 'now' ) ?  true : false;

                    switch( $notification['Estado'] ) {
                        case 'N':
                        case 'L': 
                        case 'A': $statusDoc = 'Pdte. confirmar';   break;
                        case 'C': $statusDoc = 'Confirmado';        break;
                    }

                    if( $hasExpires && $statusDoc == 'Pdte. confirmar' ) $statusDoc = 'Caducado';

                    $dateStart  = date( 'Y-m-d H:i:s', strtotime( $notification['FechaEnvio'] ) );
                    $dateExpire = date( 'd/m/Y', strtotime( $notification['FechaPlazo'] ) );

                    if( $statusDoc == 'Pdte. confirmar' ) $actionStatus = 'unread';
                    else $actionStatus = 'read';

                    $vars                     = array();

                    $vars['photo']            = $photoUser;
                    $vars['doc']              = $notification['Titulo'];
                    $vars['message']          = strlen( $notification['Mensaje'] ) > 140 ? substr( $notification['Mensaje'], 0, 140 ) . '...' : $notification['Mensaje'];
                    $vars['expire']           = $dateExpire;
                    $vars['departament']      = $notification['AreaResponsable'];
                    $vars['notification_uri'] = $notification['Link'];
                    $vars['status']           = $statusDoc;

                    $hasNotification          = notification_exists( $currentNotifications, $notification['Link'], $dateStart );
	                   
                    if( $notification['Estado'] != 'B' && !$hasExpires ) {

                        if( !$hasNotification ) {

                            $content = UM()->Notifications_API()->api()->get_notify_content( $type, $vars );

                            $wpdb->insert(
                                $table_name,
                                array(
                                    'time'      => $dateStart,
                                    'user'      => $user_id,
                                    'status'    => $actionStatus,
                                    'photo'     => $vars['photo'],
                                    'type'      => $type,
                                    'url'       => $notification['Link'],
                                    'content'   => $content
                                )
                            );
                        } else {
                            if( $hasNotification['status'] !== $actionStatus ) {
                                $wpdb->update(
                                    $table_name,
                                    array(
                                        'status'    => $actionStatus
                                    ),
                                    array(
                                        'user'      => $user_id,
                                        'id'        => $hasNotification['ID']
                                    )
                                );
                            }
                        }

                        /* UM()->Notifications_API()->api()->store_notification( um_profile_id(), 'require_docs', $vars );

                        $wpdb->update(
                            $table_name,
                            array(
                                'status'    => $actionStatus,
                                'time'      => $dateStart
                            ),
                            array(
                                'user'      => $user_id,
                                'type'      => $type,
                                'url'       => $notification['Link']
                            )
                        ); */

                    } else {

                        if( $hasNotification ) {
                            $wpdb->delete( $table_name, array( 'id' => $hasNotification['ID'] ) );
                        }

                    }

                }

            }

        }
        
    }

}

/** ---------------------------------
 * Get notifications from Webservices
 * ----------------------------------
 */
function get_notification_webservices( $user = false ) {

    if( !$user ) {
        $user    = CORE_HRE_USER_EMAIL;
    }

    if( strpos( $user, '@' ) > 0 ) $user = substr( $user, 0, strpos( $user, '@' ) );

    $curl        = curl_init();
    $urlService  = CORE_HRE_URL_WEBSERVICES . '/GetComunicacionesUltimaNotificacionUsuario?VsNombreUsuario='.$user;

    curl_setopt( $curl, CURLOPT_URL, $urlService );
    curl_setopt( $curl, CURLOPT_RETURNTRANSFER, 1 );

    $respond = curl_exec( $curl );
    curl_close( $curl );

    if( $respond && $respond !== NULL ) {
        $xmlResult    = simplexml_load_string( $respond );
        $parseToArray = json_decode( json_encode($xmlResult), true );
    } else {
        $parseToArray = array( 'Respuesta' => 'Error' );
    }

    return $parseToArray;

}

/** ------------------------
 * Admin menu item custom notifications
 * -------------------------
 */
add_action( 'admin_menu', 'register_notifications_menu_page' );

function register_notifications_menu_page() {
    add_menu_page( 'Envío de Notificaciones', 'Notificaciones', 'manage_options', 'core_notifications', 'admin_page_notifications_callback', 'dashicons-bell' ); 

    add_submenu_page( 'core_notifications', __( 'Listado avisos', 'core-hre' ), __( 'Listado avisos', 'core-hre' ), 'manage_options', 'core_notifications_list', 'notifications_table_page' );
}

/** ------------------------
 * Admin page Send notifications 
 * -------------------------
 */
function admin_page_notifications_callback() {

    if( $_REQUEST['action'] ) {

        $resultSend = sendCustomNotificationPOST( $_REQUEST );

        if( $resultSend ) { echo '<div id="message"><p><strong>Notificación enviada.</strong></p></div>'; }
        else { echo '<div id="message"><p><strong>Fallo al enviar las notificaciones. Inténtelo de nuevo o contacte con el administrador.</strong></p></div>'; }
    } 

    $roles = get_editable_roles();

    ?>

    <div class="wrap">
        <h2>Envío de notificaciones</h2>
        <form method="post" id="send-custom-notifications">
            <table class="form-table">
                <tr>
                  <td style="width: 10%;"><label><strong>Título :</strong></label></td>
                  <td style="width: 90%;"><input type="text" name="title_notification" style="width: 90%;" /></td>
                </tr>
                <tr>
                  <td style="width: 10%;"><label><strong>Mensaje :</strong></label></td>
                  <td style="width: 90%;"><textarea name="message_notification" rows="8" style="width: 90%;"></textarea></td>
                </tr>
                <tr>
                  <td style="width: 10%;"><label><strong>URL Destino :</strong></label></td>
                  <td style="width: 90%;"><input type="url" name="url_notification" style="width: 90%;" value="<?php echo home_url('/'); ?>" /><br /><small>Siempre en formato URL (https://...)</small></td>
                </tr>
                <tr>
                  <td style="width: 10%;"> <label><strong>Area Responsable :</strong></label></td>
                  <td style="width: 90%;"><input type="text" name="area_notification" style="width: 90%;" /><br /><small>Dejar vacío si no quiere que aparezca</small></td>
                </tr>
                <tr>
                  <td style="width: 10%;"> <label><strong>Rol específico :</strong></label></td>
                  <td style="width: 90%;"><select name="by_rol_notification" style="width: 90%;">
                      <option value="all">Todos</option>
                      <?php 

                      foreach ( $roles as $key => $info ) :
                          echo '<option value="'.$key.'">'.$info['name'].'</option>';
                      endforeach;

                      ?>
                  </select></td>
                </tr>
                <tr>
                    <td colspan="2">
                       <input type="submit" name="submit" id="submit" class="button button-primary" value="Enviar" style="">
                        <input type="hidden" name="action" value="send" /> 
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <?php 

} 

/** ------------------------
 * Send Notifications POST
 * -------------------------
 */
function sendCustomNotificationPOST( $args = false ) {

    global $um_notifications;

    if( $args ) {

        $argsUsers = array(
            'blog_id'      => $GLOBALS['blog_id'],
            'orderby'      => 'login',
            'order'        => 'ASC'
         );

        if( isset( $args['by_rol_notification'] ) && $args['by_rol_notification'] != 'all' ) {
            $argsUsers['role__in'] = array( $args['by_rol_notification'] );
        }

        $users = get_users( $argsUsers );

        foreach( $users as $_WP_User ) {

            if( empty( $args['title_notification'] ) || empty( $args['message_notification'] ) ) {
                break;
            }

            $vars['title']            = $args['title_notification'];
            $vars['message']          = $args['message_notification'];
            $vars['departament']      = $args['area_notification'];
            $vars['notification_uri'] = !empty( $args['url_notification'] ) ? $args['url_notification'] : home_url('/');

            UM()->Notifications_API()->api()->store_notification( $_WP_User->ID, 'custom_notifications', $vars );

        }

        return true;

    } else {
        return false;
    }

}

/** ---------------------------------
 * Display table notifications record
 * ----------------------------------
 */
function notifications_table_page()  { 

    $notification_list_table = new Core_HRE_Notifications();

    ?>
    <div class="wrap">
        <h2><?php _e('Avisos enviados', 'core-hre'); ?></h2>
            <form method="post">
                <input type="hidden" name="page" value="<?php echo $_REQUEST['page'] ?>" />
                <?php

                $notification_list_table->search_box( __( 'Buscar por contenido o url', 'core-hre' ), 'search-notification-form' );
                $notification_list_table->prepare_items();
                $notification_list_table->display();
                
                ?>
            </form>
    </div>
    <?php
    
}

function notification_exists( $object, $url, $time ) {
    $result = false;

    if( is_array( $object ) && !empty( $object ) ) {
        foreach ( $object as $notification ) {
            if( $notification->url == $url && $notification->time == $time ) {
                $result = array(
                    'ID'     => $notification->id,
                    'status' => $notification->status 
                );
                break;
            }
        }
    }

    return $result;
}
