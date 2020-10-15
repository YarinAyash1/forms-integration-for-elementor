<?php 
/*
Plugin Name: Integrations for Elementor Forms 
Plugin URI: https://github.com/YarinAyash1/cardcom-elementor-forms-integration
Description: Forms Integrations
Version: 1.0.0
Author: Yarin Ayash
Author URI: https://yarinayash.com
Text Domain: from-integrations
*/

// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if

// Let's Initialize Everything
if ( file_exists( plugin_dir_path( __FILE__ ) . 'core-init.php' ) ) {
    require_once( plugin_dir_path( __FILE__ ) . 'core-init.php' );
}
