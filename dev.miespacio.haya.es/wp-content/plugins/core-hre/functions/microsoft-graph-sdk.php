<?php 

function connectUserMicrosoft() {

	if( is_user_logged_in() ) {

		global $ultimatemember;

		$user_id 		= CORE_HRE_USER_ID;
		$email 			= CORE_HRE_USER_EMAIL;

		um_fetch_user( $user_id );

		$photo_profile 		= um_get_user_avatar_url();
		$default_avatar_uri = um_get_default_avatar_uri( $user_id );
	
		?>
		<!-- Functions -->
		<script type="text/javascript" src="<?php echo CORE_HRE_DIR_URL; ?>javascript/functions.js"></script>
		<!-- Microsoft Graph SDK -->
		<script type="text/javascript" src="<?php echo CORE_HRE_DIR_URL; ?>javascript/microsoft-graph-sdk.js"></script>
		<script type="text/javascript">

			var tokenMG = false;

			function getAccessToken() {

				var newToken = false;

				jQuery.ajax({
	                "async": false,
	                "crossDomain": true,
	                "url": "https://cors-anywhere.herokuapp.com/https://login.microsoftonline.com/<?php echo CORE_HRE_MICROSOFT_TENANT; ?>/oauth2/token", // Pass your tenant name
	                "method": "POST",
	                "headers": {
	                    "content-type": "application/x-www-form-urlencoded"
	                },
	                "data": {
	                    "grant_type": 	 "client_credentials",
	                    "client_id ": 	 "<?php echo CORE_HRE_MICROSOFT_ID; ?>", 			//Provide your app id
	                    "client_secret": "<?php echo CORE_HRE_MICROSOFT_SECRET; ?>", 		//Provide your client secret genereated from your app
	                    "resource": 	 "<?php echo CORE_HRE_MICROSOFT_RESOURCE; ?>",
	                    "scope ": 		 ["User.All.Read"]
	                },
	                success: function( response ) {
	                    newToken = response.access_token;
	                    setCookie( 'tokenMGA', newToken, response.expires_in );
	                },
		        	error: function( jqXhr, textStatus, errorThrown ) {
		        		console.log( 'jqXhr:', jqXhr );
		        		console.log( 'textStatus:', textStatus );
		        		console.log( 'errorThrown:', errorThrown );
		        	} 

	            });

	            return newToken;
			}

			var cookieToken = getCookie( 'tokenMGA' );
			if( !cookieToken || cookieToken == '' ) {
				tokenMG = getAccessToken();
			} else {
				tokenMG = cookieToken;
			}

			const getAzureMyProfilePic = async ( token ) => {

			  	const client = MicrosoftGraph.Client.init({
			    	authProvider: ( done ) => {
			      		done(null, token);
			    	},
			  	});

			  	try {
			    	const result = await client
			      		.api('/users/<?php echo $email; ?>/photos/240x240/$value')
			      		.responseType(MicrosoftGraph.ResponseType.BLOB)
			      		.get();

			    	const winURL  = window.URL || window.webkitURL;
					const blobUrl = winURL.createObjectURL( result );

					jQuery('.img-current-user:not(.no-image)').attr( 'src', blobUrl );
					jQuery('.gravatar:not(.um-avatar-default)').attr( 'src', blobUrl );

			  	} catch ( err ) {
			    	if( err.statusCode == 404 ) {
			    		console.log( 'Not Found:', 'Foto no encontrada para el usuario actual' );
			    	} else if( err.statusCode == 401 ) {
			    		tokenMG = getAccessToken();
			    	} else {
			    		console.log( 'Error Code:', err.statusCode );
			    		console.log( 'Msg:', err.message );
			    	}
			  	}

			};

			const getAzureUserProfilePic = async ( token, user ) => {

			  	const client = MicrosoftGraph.Client.init({
			    	authProvider: ( done ) => {
			      		done( null, token );
			    	},
			  	});

			  	try {
			    	const result = await client
			      		.api('/users/'+user+'/photos/240x240/$value')
			      		.responseType(MicrosoftGraph.ResponseType.BLOB)
			      		.get();

			    	const winURL  = window.URL || window.webkitURL;
					const blobUrl = winURL.createObjectURL( result );

					jQuery('.directory-card[data-user="'+user+'"] .directory-image-bg').attr( 'data-bg', blobUrl );
					jQuery('.directory-card[data-user="'+user+'"] .directory-image-bg').css( 'background-image', 'url('+blobUrl+')');

			  	} catch ( err ) {
			    	if( err.statusCode == 404 ) {
			    		console.log( 'Not Found:', 'Foto no encontrada para el usuario '+user );
			    	} else if( err.statusCode == 401 ) {
			    		tokenMG = getAccessToken();
			    	} else {
			    		console.log( 'Error Code:', err.statusCode );
			    		console.log( 'Msg:', err.message );
			    	}
			  	}

			};

			(function($){
				$(document).ready(function () {
					<?php if( !$photo_profile || $photo_profile == $default_avatar_uri ) { ?>
		            	getAzureMyProfilePic( tokenMG );
		            <?php } ?>
				});
		    }(jQuery));
		</script>
		<?php 
	
	}
	
}

add_action( 'wp_head', 'connectUserMicrosoft' );