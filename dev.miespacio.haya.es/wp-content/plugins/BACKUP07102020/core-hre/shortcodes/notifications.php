<?php 

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

This code sample shows you how to use the API to create
and add custom notifications (for real-time notifications)
plugin.

STEP 1: You need to extend the filter: um_notifications_core_log_types with your
new notification type as follows for example

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

STEP 3: Now you just need to add the notification trigger when a user does some action on
another user profile, I assume you can trigger that in some action hooks
for example when user view another user profile you can hook like this

basically you need to run this in code

$who_will_get_notification : is user ID who will get notification
'new_action' is our new notification type
$vars is array containing the required template tags, user photo and url when that notification is clicked

UM()->Notifications_API()->api()->store_notification( $who_will_get_notification, 'new_action', $vars );

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

            if( !empty($notifications) ) {

            	$table_name = $wpdb->prefix . "um_notifications";
                $type       = 'require_docs';

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

                	$result = $wpdb->get_results( $wpdb->prepare(
						"SELECT *
						FROM {$table_name} 
						WHERE user = %d AND type = %s AND url = %s AND time = %s 
						ORDER BY time DESC",
						$user_id,
						$type,
						$notification['Link'],
                        $dateStart
					) );

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

					if( empty( $result ) ) {
	                   
                        if( $notification['Estado'] != 'B' ) {

    	                    UM()->Notifications_API()->api()->store_notification( um_profile_id(), 'require_docs', $vars );

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
                            );
                        }

	                } else {

                        if( $notification['Estado'] != 'B' ) {
                            if( $result[0]->status != $actionStatus ) {
                                $wpdb->update(
                                    $table_name,
                                    array(
                                        'status'    => $actionStatus,
                                        'time'      => $dateStart
                                    ),
                                    array(
                                        'user'      => $user_id,
                                        'id'        => $result[0]->id
                                    )
                                );
                            }

                        } else {
                            UM()->Notifications_API()->api()->delete_log( $result[0]->id );
                        }

                    }

                }

            }

        }
        
    }

}

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

add_action( 'admin_menu', 'register_notifications_menu_page' );

function register_notifications_menu_page() {
    add_menu_page( 'Envío de Notificaciones', 'Notificaciones', 'manage_options', 'core_notifications', 'admin_page_notifications_callback', 'dashicons-bell' ); 

    add_submenu_page( 'core_notifications', __( 'Listado avisos', 'core-hre' ), __( 'Listado avisos', 'core-hre' ), 'manage_options', 'core_notifications_list', 'notifications_table_page' );
}

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
