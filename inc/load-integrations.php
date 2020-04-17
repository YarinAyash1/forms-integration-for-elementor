<?php
function register_integrations(){
    // Cardcom
    if ( file_exists( FI_CORE_INC . 'integrations/cardcom-integration.php' ) ) {
        require_once FI_CORE_INC . 'integrations/cardcom-integration.php';
    }   

    // 019 SMS
    if ( file_exists( FI_CORE_INC . 'integrations/019-integration.php' ) ) {
        require_once FI_CORE_INC . 'integrations/019-integration.php';
    }   
}


add_action( 'elementor_pro/init', 'register_integrations');
