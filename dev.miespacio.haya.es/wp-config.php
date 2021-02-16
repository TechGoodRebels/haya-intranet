<?php





/** 
 * Configuración básica de WordPress.
 *
 * Este archivo contiene las siguientes configuraciones: ajustes de MySQL, prefijo de tablas,
 * claves secretas, idioma de WordPress y ABSPATH. Para obtener más información,
 * visita la página del Codex{@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} . Los ajustes de MySQL te los proporcionará tu proveedor de alojamiento web.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** Ajustes de MySQL. Solicita estos datos a tu proveedor de alojamiento web. ** //
/** El nombre de tu base de datos de WordPress */
define('DB_NAME', "haya_intranet_dev");

/** Para que no se actualice algo que luego pueda dar problemas */
/** Tu nombre de usuario de MySQL */
define('DB_USER', "root");

/** Tu contraseña de MySQL */
define('DB_PASSWORD', "root");

/** Host de MySQL (es muy probable que no necesites cambiarlo) */
define('DB_HOST', "localhost");

/** Codificación de caracteres para la base de datos. */
define('DB_CHARSET', 'utf8');

/** Cotejamiento de la base de datos. No lo modifiques si tienes dudas. */
define('DB_COLLATE', '');
define( 'FORCE_SSL_ADMIN', true );

/** Memory limit PHP. */
define( 'WP_MEMORY_LIMIT', '512M' );

/**#@+
 * Claves únicas de autentificación.
 *
 * Define cada clave secreta con una frase aleatoria distinta.
 * Puedes generarlas usando el {@link https://api.wordpress.org/secret-key/1.1/salt/ servicio de claves secretas de WordPress}
 * Puedes cambiar las claves en cualquier momento para invalidar todas las cookies existentes. Esto forzará a todos los usuarios a volver a hacer login.
 *
 * @since 2.6.0
 */
define('AUTH_KEY', '9NI{p:>CaWpUp+@j^0ZY95;#zZa=:SN#P12*%STgJ~=]#i:k<#![u;2OmkRmRyIz');
define('SECURE_AUTH_KEY', 'Z)Fs()7F8aux;B1p7(VgS&,*1Crqm^[g8PNJc~kj@:Lv6jL}^/fC[gr*+N`Qj_]j');
define('LOGGED_IN_KEY', 'tr.cKKBNII4)>PSQ&|2T:}q6`WqB5p9Yp0;kCX0d{~l7F+Wb?K;?c(xCEr6@<$Q&');
define('NONCE_KEY', '||mnVN>e?G@Urb#=tII$5dd6;5`G`egw@ +?};m%;wJ3R*[^: bqk>VaO~r_,7|K');
define('AUTH_SALT', ')Y bNl&{).I0%Q*&,XGjDTBI8W*4c7YP_V(0cV1VU50eVF#9T^uVi`Uil(B9:;.5');
define('SECURE_AUTH_SALT', '?VC,cQ:#eSxkfr>Vg3+l-|-Kbm#?EuYT5tvlIPay9CAomQTXt_lzBs2|g<.+/Rkx');
define('LOGGED_IN_SALT', 'SrT}2Dq<fHlyA3%:@R@S<Zl@V-czREM*)(Y5ge/tWT};VNP%A [.L5l:f6*ko#51');
define('NONCE_SALT', 'J?u.TDjo78~4Gpr>b:OepN(5+j2&FVK`nkmH.[*HF*FjC+~a8s?QCuTWYh$Hm,JI');

/**#@-*/

/**
 * Prefijo de la base de datos de WordPress.
 *
 * Cambia el prefijo si deseas instalar multiples blogs en una sola base de datos.
 * Emplea solo números, letras y guión bajo.
 */
$table_prefix  = 'cm_';

define( 'DISALLOW_FILE_EDIT', true );


//Habilitar papelera para contenido multimedia
//define( 'MEDIA_TRASH', true );

/**
 * Para desarrolladores: modo debug de WordPress.
 *
 * Cambia esto a true para activar la muestra de avisos durante el desarrollo.
 * Se recomienda encarecidamente a los desarrolladores de temas y plugins que usen WP_DEBUG
 * en sus entornos de desarrollo.
 */

/**  define('WP_DEBUG', true);
* define('WP_DEBUG_DISPLAY', false);
*define('WP_DEBUG_LOG', true); 
*/

/* ¡Eso es todo, deja de editar! Feliz blogging */

/** WordPress absolute path to the Wordpress directory. */
define( 'WP_POST_REVISIONS', 1 );
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');