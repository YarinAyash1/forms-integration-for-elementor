<?php 
/*
*
*	***** CardCom - Elementor Forms Integration *****
*
*	This file initializes all CEFI Core components
*	
*/
// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
// Define Our Constants
define('CEFI_CORE_INC',dirname( __FILE__ ).'/assets/inc/');
define('CEFI_CORE_JS',plugins_url( 'assets/js/', __FILE__ ));
/*
*
*  Includes
*
*/ 
    
// Load the Integration
if ( file_exists( CEFI_CORE_INC . 'cefi-integration-elementor.php' ) ) {
	require_once CEFI_CORE_INC . 'cefi-integration-elementor.php';
} 
// Load the JS File
function cefi_register_core_js(){
	// Register Core Plugin JS	
	wp_enqueue_script('cefi-core', CEFI_CORE_JS . 'cefi-core.js','jquery',time(),true);
};
add_action( 'wp_enqueue_scripts', 'cefi_register_core_js' );    