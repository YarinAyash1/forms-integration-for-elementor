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

class Forms_Integration {

	public $settings;
	public $integrations;
    /**
     *  __construct
     *
     *  This function will setup the class functionality
     *
     * @type    function
     * @since    1.0.0
     *
     * @param    void
     *
     * @return    void
     */
	public function __construct(){
		$this->settings = array(
			'version' => '1.0.0',
			'integrations' 	=> dirname( __FILE__ ).'/inc/integrations/',
			'js'	=> plugins_url( 'assets/js/', __FILE__ )
		);
		
		add_action('wp_enqueue_scripts', array( $this, 'register_scripts_styles' ));
		add_action( 'elementor_pro/init', array( $this, 'register_integrations' ));
		$this->integrations = array(
			'cardcom' => array(
				'filename' => 'cardcom-integration'
			),
			'019' => array(
				'filename' => '019-integration'
			)
		);
	}

	public function register_scripts_styles(){
		wp_enqueue_script('fi-core', $this->settings['js'] . 'integrations-from-core.js', 'jquery', time(), true);
	}

	public function register_integrations(){
		foreach($this->integrations as $integration){
			if ( file_exists( $this->settings['integrations'] . $integration['filename'].'.php' ) ) {
				require_once($this->settings['integrations'] . $integration['filename'].'.php');
			}
		}
	}
}

new Forms_Integration();