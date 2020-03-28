<?php

function register_integrations(){
    if ( file_exists( CEFI_CORE_INC . 'cefi-cardcom-integration.php' ) ) {
        require_once CEFI_CORE_INC . 'cefi-cardcom-integration.php';
    }   
}

add_action( 'elementor_pro/init', 'register_integrations');
