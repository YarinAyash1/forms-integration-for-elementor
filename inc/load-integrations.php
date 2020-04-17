<?php

function register_integrations(){
    if ( file_exists( CEFI_CORE_INC . 'cardcom-integration.php' ) ) {
        require_once CEFI_CORE_INC . 'cardcom-integration.php';
    }   
}

add_action( 'elementor_pro/init', 'register_integrations');
