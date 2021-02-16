<?php
header('HTTP/1.1 503 Service Temporarily Unavailable');
header('Status: 503 Service Temporarily Unavailable');
header('Retry-After: 3600');
?><!DOCTYPE HTML>
<html xmlns="http://www.w3.org/1999/xhtml" lang="es">
    <head>
        <title>Error de conexión con la base de datos</title>
        
	    <link rel='stylesheet' id='ub-db-error-page-styling-css'  href='https://premiespacio.haya.es/wp-content/plugins/ultimate-branding/inc/modules/front-end/assets/css/db-error-page.css?ver=3.3.1' media='all' />
<style id='ub-db-error-page-styling-inline-css'>
#logo{opacity:1.00}
#logo, #logo {display: block;}
#logo {background: transparent url(/wp-content/uploads/2020/04/logo-haya.png) no-repeat 50% 5%;background-size: contain;margin: 0px auto 25px auto;overflow: hidden;text-indent: -9999px;height: 84px;width: 84px;
}
#logo{margin:0 auto 25px auto;}
.page{background-color: #ddd;width: 600px;-webkit-border-radius: 0;-moz-border-radius: 0;border-radius: 0;}.page h1 {color: #000;}.page .content {color: #888;text-align:left}
</style>

        <script type="text/javascript"></script>
    </head>
    <body class="ultimate-branding-settings-db-error-page">
        <div class="overall">
            <div class="page">
                <div id="logo"></div>
                <div class="content">
                    <h1>Error de conexión con la base de datos</h1>
                    <p>Por favor, contacte con el administrador.</p>

                    
                </div>
            </div>
        </div>
    </body>
</html>