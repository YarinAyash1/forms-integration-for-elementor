<?php 
/*
*
*	***** Forms Integration - Elementor *****
*
*	This file initializes all FI Core components
*	
*/

// If this file is called directly, abort. //

if ( ! defined( 'WPINC' ) ) {die;} // end if

// Define Our Constants
define('FI_CORE_INC' ,dirname( __FILE__ ).'inc/');

define('FI_CORE_JS' ,plugins_url( 'assets/js/', __FILE__ ));

/*
*  Includes
*/ 
    
// Load the Integrations
if ( file_exists( FI_CORE_INC . 'load-integrations.php' ) ) {
	require_once FI_CORE_INC . 'load-integrations.php';
} 

// Load the JS File
function cefi_register_core_js(){
	// Register Core Plugin JS	
	wp_enqueue_script('core', FI_CORE_JS . 'integrations-from-core.js','jquery',time(),true);
};
add_action( 'wp_enqueue_scripts', 'cefi_register_core_js' );    