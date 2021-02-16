/**
 * Función para añadir un cero a la izquierda
 *
 * PAD LEFT
 * -------------------------------------------
 */
function padLeft( n ){
	return ( '00' + n ).slice(-2);
}

/**
 * Formatear fecha a dd/mm/yyyy
 *
 * FORMAT DATE
 * -------------------------------------------
 */
function formatDate(){        
	var currentDate = new Date;
    var dateFormat  = [ 
    	padLeft( currentDate.getDate() ),
        padLeft( currentDate.getMonth() + 1 ),
        currentDate.getFullYear()
    ].join('/');

 	return dateFormat
}

/**
 * Obtener parámetros de la fecha actual
 *
 * GET PARAMS DATE
 * -------------------------------------------
 */
function getParamsDate(){        
	var currentDate = new Date;
    var paramsDate  = {
    	'day' 	: currentDate.getDate(),
        'month' : currentDate.getMonth() + 1,
        'year'  : currentDate.getFullYear(),
        'hour'	: currentDate.getHours(),
        'mins'	: currentDate.getMinutes(),
        'sec'	: currentDate.getSeconds()
    };

 	return paramsDate
}

/**
 * Función para obtener parametros de la query URL
 *
 * GET QUERY PARAMS
 * -------------------------------------------
 */
function getQueryParams( name, url ) {
    if ( !url ) {
    	url = window.location.href;
    }
    name = name.replace(/[\[\]]/g, '\\$&');
    var regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)'),
        results = regex.exec(url);
    if ( !results ) {
    	return false;
    }
    if ( !results[2] ) {
    	return decodeURIComponent( results[2].replace(/\+/g, ' ') );
    }
}

/**
 * Función para obtener todos los parametros de la query URL
 *
 * GET ALL QUERY PARAMS
 * -------------------------------------------
 */
function getAllQueryParams( url ) {
    if ( !url ) {
    	url = window.location.href;
    }
    
    if( url.indexOf('?') > -1 ) {
		url = url.substring( url.indexOf('?') + 1, url.length );

		var splitParams = url.split('&');
		var arrayParams = {};

		splitParams.each( function() {
			var keyVal = $(this).split('=');

			arrayParams[keyVal[0]] = keyVal[1];
		});

		return arrayParams;

	} else {
		return false;
	}
}

/**
 * Función para actualizar parametros en la query de la URL
 *
 * UPDATE QUERY PARAMS
 * -------------------------------------------
 */
function updateQueryParams( uri, key, value ) {
	var re = new RegExp("([?&])" + key + "=.*?(&|#|$)", "i");
	if( value === undefined ) {
		if( uri.match( re ) ) {
		    return uri.replace( re, '$1$2' );
		} else {
		    return uri;
		}
	} else {
		if( uri.match( re ) ) {
		    return uri.replace( re, '$1' + key + "=" + value + '$2' );
		} else {
		    var hash =  '';
		    if( uri.indexOf('#') !== -1 ) {
		        hash = uri.replace(/.*#/, '#');
		        uri = uri.replace(/#.*/, '');
		    }
		    var separator = uri.indexOf('?') !== -1 ? "&" : "?";    
		    return uri + separator + key + "=" + value + hash;
		}
	}  
}

/**
 * Función para limpiar url
 *
 * SANITAZE URL
 * -------------------------------------------
 */
function sanitazeURL( url ) {
	url || ( url = window.location.href );

	if( url.indexOf('#') > -1 ) {
		url = url.substring( 0, url.indexOf('#') );
	}
	if( url.indexOf('?') > -1 ) {
		url = url.substring( 0, url.indexOf('?') );
	}

	return url;
}

/**
 * Función para limpiar string de caracteres especiales
 *
 * CLEAR SPECIAL CHARACTERS
 * -------------------------------------------
 */
function clearSpecialCharacters( string ){
	var regExp = string.toLowerCase();
	regExp = regExp.replace( new RegExp( "\\s", 'g' ), "-" );		// Reemplazar espacios por -
	regExp = regExp.replace( new RegExp( "\\+", 'g' ), "" );		// Reemplazar + por nada
	regExp = regExp.replace( new RegExp( "\\-\\-", 'g' ), "-" );	// Reemplazar -- por -
	regExp = regExp.replace( new RegExp( "\\,", 'g' ), "" );		// Reemplazar , por nada	
	regExp = regExp.replace( new RegExp( "[àáâãäå]", 'g' ), "a" );	// Reemplazar tildes de a
	regExp = regExp.replace( new RegExp( "æ", 'g' ), "ae" );		// Reemplazar æ por ae
	regExp = regExp.replace( new RegExp( "ç", 'g' ), "c" );			// Reemplazar ç por c
	regExp = regExp.replace( new RegExp( "Ł", 'g' ), "l" );			// Reemplazar Ł por l
	regExp = regExp.replace( new RegExp( "ó", 'g' ), "o" );			// Reemplazar ó por o
	regExp = regExp.replace( new RegExp( "ź", 'g' ), "z" );			// Reemplazar ź por z
	regExp = regExp.replace( new RegExp( "[ń]", 'g' ), "n" );		// Reemplazar ń por n
	regExp = regExp.replace( new RegExp( "[èéêë]", 'g' ), "e" );	// Reemplazar tildes de e
	regExp = regExp.replace( new RegExp( "[ìíîï]", 'g' ), "i" );	// Reemplazar tildes de i
	regExp = regExp.replace( new RegExp( "ñ", 'g' ), "n" );         // Reemplazar ñ
	regExp = regExp.replace( new RegExp( "[òóôõö]", 'g' ), "o" );	// Reemplazar tildes de o
	regExp = regExp.replace( new RegExp( "œ", 'g' ), "oe" );		// Reemplazar œ por oe
	regExp = regExp.replace( new RegExp( "[ùúûü]", 'g' ), "u" );	// Reemplazar tildes de u
	regExp = regExp.replace( new RegExp( "[ýÿ]", 'g' ), "y" );		// Reemplazar tildes de y
	regExp = regExp.replace( new RegExp( "®", 'g' ), "" );			// Eliminar ® de registrado

	return regExp;
}

/**
 * Función para imprimir HTML
 *
 * PRINT
 * -------------------------------------------
 */
function print(doc) {
	
    var dualScreenLeft = window.screenLeft != undefined ? window.screenLeft : window.screenX;
    var dualScreenTop = window.screenTop != undefined ? window.screenTop : window.screenY;

    var width  = window.innerWidth ? window.innerWidth : document.documentElement.clientWidth ? document.documentElement.clientWidth : screen.width;
    var height = window.innerHeight ? window.innerHeight : document.documentElement.clientHeight ? document.documentElement.clientHeight : screen.height;

    var systemZoom = width / window.screen.availWidth;
	var left = width / 2 / systemZoom + dualScreenLeft;
	var top  = height / 2 / systemZoom + dualScreenTop;

	var options = 'menubar=no,resizable,scrollbars=yes,status=1';

    windowObjectReference = window.open( doc, '_blank', options );

}

/**
 * Función para formatear número a 2 decimales
 *
 * FORMAT PARSE FLOAT
 * -------------------------------------------
 */
function formatParseFloat( imp, decimals ) {
	decimals || ( decimals = 2 );

	if( typeof imp == 'string' ) {
		imp = imp.replace( ',', '.' );
	}

	imp = Number(imp).toFixed(decimals);

	if( isNaN(imp) ) { 
		return Number(0).toFixed(2); 
	} else { 
		return imp; 
	}
}

/**
 * Función para comprobar el tipo de variable
 *
 * CHECK TYPE VAR
 * -------------------------------------------
 */
function isVariableType( variable, type ) {
	type || ( type = 'string' );

	var validate = false;

	switch( type ) {
		case 'string': 	 validate = typeof variable === type ? true : false; 														break; // Comprobar string
		case 'number': 	 validate = typeof variable === type ? true : false; 														break; // Comprobar número
		case 'boolean':  validate = typeof variable === type ? true : false; 														break; // Comprobar boolean
		case 'function': validate = typeof variable === type ? true : false; 														break; // Comprobar función
		case 'object': 	 validate = typeof variable === type ? true : false; 														break; // Comprobar objeto
		case 'email': 	 validate = /\S+@\S+\.\S+/.test(variable) ? true : false; 													break; // Comprobar email
		case 'tel': 	 validate = /^([9,7,6]{1})+([0-9]{8})$/.test(variable) ? true : false; 										break; // Comprobar teléfono
		case 'date': 	 validate = /^(0?[1-9]|[12][0-9]|3[01])[\/\-](0?[1-9]|1[012])[\/\-]\d{4}$/.test(variable) ? true : false; 	break; // Comprobar fecha
	}

	return validate;
}

/* ----------------------------------
 * CREAR COOKIES
 * Función para crear cookies
 * Params: Nombre, valor y expiración
 * ----------------------------------
 */ 
function setCookie( cname, cvalue, exseconds ) {
    exseconds || ( exseconds = 3600 );
    
    var dateCur = new Date();
    dateCur.setTime( dateCur.getTime() + ( exseconds * 1000 ) );

    var expires = "expires=" + dateCur.toUTCString();
    document.cookie = cname + "=" + cvalue + ";" + expires + ";path=/";
}

/* ------------------------------------------------
 * ELIMINAR COOKIES
 * Función para eliminar cookies con el name pasado
 * No devuelve resultado
 * ------------------------------------------------
 */   
function removeCookie( cname ){
    setCookie( cname, '', -1 );
}
        
/* --------------------------------------------
 * LEER COOKIES
 * Función para leer cookies con el name pasado
 * Devuelve empty en caso contrario
 * --------------------------------------------
 */
function getCookie( cname ) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return '';
}
        
/* ------------------------------------------------
 * DETECTAR COOKIES
 * Función para detectar cookies con el name pasado
 * Devuelve true o false
 * ------------------------------------------------
 */
function detectCookie( cname ) {
    var name = cname + "=";
    var decodedCookie = decodeURIComponent(document.cookie);
    var ca = decodedCookie.split(';');
    for(var i = 0; i <ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0 && (name.length != c.length))  {
            return true;
        }
    }
    return false;
}