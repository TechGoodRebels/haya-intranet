<?php 

/** ------------------------
 * Add Popup Employee
 * -------------------------
 */
function addPopupEmployeeFooter() {

    ?>
    <div class="overlay-popups overlay-directory">
        <div class="popup-directory popup">
            <div class="close-popup"><i class="eicon-close"></i></div>
            <div class="row-popup">
                <div class="col-popup bg-image-directory"></div>
                <div class="col-popup content-directory">
                    <h4 class="title-directory-employee">Nombre y Apellidos</h4>
                     <h4 class="subtitle-directory-employee">Título</h4>
                    <div class="row-inner-popup">
                        <div class="col-inner-popup tel-directory-employee">
                            <i aria-hidden="true" class="fas fa-phone-alt"></i> <span>Teléfono</span>
                        </div>
                        <div class="col-inner-popup ext-directory-employee">
                            <i aria-hidden="true" class="fas fa-tty"></i> <span>Ext.</span>
                        </div>
                    </div>
                    <div class="row-inner-popup">
                        <div class="col-inner-popup email-directory-employee">
                            <i aria-hidden="true" class="far fa-envelope"></i> <span>Email</span>
                        </div>
                        <div class="col-inner-popup office-directory-employee">
                            <i aria-hidden="true" class="far fa-building"></i> <span>Oficina</span>
                        </div>
                    </div>
                    <div class="row-inner-popup">
                        <div class="col-inner-popup departament-directory-employee">
                            <i aria-hidden="true" class="fas fa-users"></i> <span>Departamento</span>
                        </div>
                        <div class="col-inner-popup teams-directory-employee">
                            <i aria-hidden="true" class="fab fa-windows"></i> <span><a href="" target="_blank" class="link-teams">Microsoft Teams</a></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php 

}

add_action( 'wp_footer', 'addPopupEmployeeFooter' );

/** ------------------------
 * Add Popup APP
 * -------------------------
 */
function addPopupAppsFooter() {

    ?>
    <div class="overlay-popups overlay-apps">
        <div class="popup-apps popup">
            <div class="close-popup"><i class="eicon-close"></i></div>
            <h5 class="title-popup-apps">Resolvemos tus dudas</h5>
            <p class="content-popup-apps"></p>
            <a href="#" class="btn-app btn-link-app" target="_blank" role="button">IR A TICKETING</a>
        </div>
    </div>
    <?php 

}

add_action( 'wp_footer', 'addPopupAppsFooter' );

/** ------------------------
 * Add Popup Utility
 * -------------------------
 */
function addPopupUtilitiesFooter() {

    ?>
    <div class="overlay-popups overlay-utilities">
        <div class="popup-utilities popup">
            <div class="close-popup"><i class="eicon-close"></i></div>
            <h5 class="title-popup-utilities"></h5>
            <div class="content-popup-utilities"></div>
            <p class="link-manual-popup-utilities">
                <a href="#" target="_blank"></a>
            </p>
            <a href="#" class="btn-app btn-link-app" target="_blank" role="button">IR A <span></span></a>
        </div>
    </div>
    <?php 

}

add_action( 'wp_footer', 'addPopupUtilitiesFooter' );