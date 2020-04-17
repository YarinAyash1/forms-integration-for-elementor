<?php
namespace ElementorPro\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Form_Record;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class FromIntegration_019 extends Action_Base {

	private $ajax_handler;
	
	public function get_name() {
		return 'fi_019';
	}

	public function get_label() {
		return __( '019 SMS', 'elementor' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'019_section',
			[
				'label' => __( '019 SMS', 'elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
		$widget->add_control(
			'019_username',
			[
				'label' => __( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter 019 username. (required*)', 'elementor' )
			]
		);

		$widget->add_control(
			'019_password',
			[
				'label' => __( 'Password', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter 019 password. (required*)', 'elementor' )
			]
		);

		$widget->add_control(
			'019_phone',
			[
				'label' => __( 'Phone Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter phone number. (required*)', 'elementor' )
			]
		);

		$widget->add_control(
			'019_title',
			[
				'label' => __( 'SMS Title', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter sms title.', 'elementor' ),
				'placeholder' => __( 'SMS from WP', 'elementor' ),
				'default' => __( 'SMS from WP', 'elementor' )
			]
		);

		$widget->add_control(
			'019_message_option',
			[
				'label' => __( 'Select Message Option', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'data' => __( ' Form DATA', 'elementor-pro' ),
					'custom' => __( ' Custom Message', 'elementor-pro' ),
				],
			]
		);

		$widget->add_control(
			'019_message',
			[
				'label' => __( 'Custom Message', 'elementor-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'description' => __( 'Enter your custom message to send', 'elementor-pro' ),
				'condition' => [
					'019_message_option' => 'custom',
				],
			]
		);

		$widget->end_controls_section();
	}

	public function on_export( $element ) {}

	public function run( $record, $ajax_handler ) {
		$this->ajax_handler = $ajax_handler;

		$settings = $record->get( 'form_settings' );

		$subscriber = $this->create_integration_object( $record );
		$basic_fields = array_filter( $subscriber );

		if($settings['019_username'] != '' && $settings['019_password'] != '' && $settings['019_phone'] != ''){
			$post_response = $this->post( $basic_fields );
		}

	}

	private function create_integration_object( Form_Record $record ) {

		$fields_record = $record->get( 'fields' );
		$settings = $record->get( 'form_settings' );
		$data = array();
		// form settings
        $data['username'] = $settings['019_username'];
		$data['password'] = $settings['019_password'];
		$data['phone'] = $settings['019_phone'];
		$data['title'] = $settings['019_title'];

		if($settings['019_message_option'] == 'custom'){
			$data['message'] = $settings['019_message'];
		}
		else{
			$data['message'] = 'New Lead:';
			foreach($fields_record as $key => $value){
				$data['message'] .= '
				'.$key . ' - ' . $value['value'];
			}
		}
		$data['message'] = 'New Lead:';
		foreach($fields_record as $key => $value){
			$data['message'] .= '
'.$key . ' - ' . $value['value'];
		}
		return $data;
	}
	
	private function post( $json_data ) {
        $url = "https://019sms.co.il/api";
        $xml ='
        <?xml version="1.0" encoding="UTF-8"?>
		<sms>
			<user>
				<username>'.$json_data['username'].'</username>
				<password>'.$json_data['password'].'</password>
			</user>
			<source>'.$json_data['title'].'</source>
			<destinations>
				<phone>'.$json_data['phone'].'</phone>
			</destinations>
			<message>'.$json_data['message'].'</message>
		</sms>';
		
        $CR = curl_init();
        curl_setopt($CR, CURLOPT_URL, $url);
        curl_setopt($CR, CURLOPT_POST, 1);
        curl_setopt($CR, CURLOPT_FAILONERROR, true);
        curl_setopt($CR, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($CR, CURLOPT_HTTPHEADER, array("charset=utf-8"));
		
        $result = curl_exec($CR);
		$error = curl_error($CR);

		if (!empty($error) || $this->getArrayFromXML($result)['status'] == '3'){
			$this->add_admin_error($this->getArrayFromXML($result)['message']);
		}
		else{

		}

	}
	private function add_admin_error( $message ) {
		if ( current_user_can( 'edit_post', $_POST['post_id'] ) ) {
			$this->ajax_handler->add_admin_error_message( $message );
		}
	}
	private function getArrayFromXML($response) {
		$xml = simplexml_load_string($response);
		$json = json_encode($xml);
		$arr = json_decode($json,true);
		
		return $arr;
	}
}

\ElementorPro\Modules\Forms\Module::instance()->add_form_action( 'fi_019', new FromIntegration_019() );


