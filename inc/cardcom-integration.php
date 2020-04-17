<?php
namespace ElementorPro\Modules\Forms\Actions;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Forms\Classes\Action_Base;
use ElementorPro\Modules\Forms\Classes\Form_Record;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class CEFI_CC extends Action_Base {

	private $ajax_handler;
	
	public function get_name() {
		return 'cefi_cc';
	}

	public function get_label() {
		return __( 'CardCom', 'elementor' );
	}

	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'cc_section',
			[
				'label' => __( 'CardCom', 'elementor' ),
				'condition' => [
					'submit_actions' => $this->get_name(),
				],
			]
		);
		
		$widget->add_control(
			'cefi_cc_terminal',
			[
				'label' => __( 'Terminal Number', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter Terminal Number. (required*)', 'elementor' )
			]
		);

		$widget->add_control(
			'cefi_cc_username',
			[
				'label' => __( 'Username', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'description' => __( 'Enter your username. (required*)', 'elementor' )
			]
		);

		$widget->add_control(
			'cefi_cc_operation',
			[
				'label' => __( 'Operation', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __( ' Charge Only', 'elementor-pro' ),
				],
			]
		);

		$widget->add_control(
			'cefi_cc_lang',
			[
				'label' => __( 'Language', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'he',
				'options' => [
					'he' => __( 'Hebrew', 'elementor-pro' ),
					'en' => __( 'English', 'elementor-pro' ),
					'ru' => __( 'Russian', 'elementor-pro' ),
					'ar' => __( 'Arabic', 'elementor-pro' ),
				],
			]
		);

		$widget->add_control(
			'cefi_cc_coin',
			[
				'label' => __( 'Coin', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => '1',
				'options' => [
					'1' => __( 'ILS', 'elementor-pro' ),
					'2' => __( 'USD', 'elementor-pro' ),
					'36' => __( 'AUD', 'elementor-pro' ),
					'124' => __( 'CAD', 'elementor-pro' ),
					'208' => __( 'DKK', 'elementor-pro' ),
					'392' => __( 'JPY', 'elementor-pro' ),
					'554' => __( 'NZD', 'elementor-pro' ),
					'643' => __( 'RUB', 'elementor-pro' ),
					'756' => __( 'CHF', 'elementor-pro' ),
					'826' => __( 'GBP', 'elementor-pro' ),
					'840' => __( 'USD (x2)', 'elementor-pro' ),
					'978' => __( 'EUR', 'elementor-pro' ),
				],
			]
		);

		$widget->add_control(
			'cefi_cc_price',
			[
				'label' => __( 'Price', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'dynamic' => [
					'active' => true,
					
				],
				'label_block' => true,
				'render_type' => 'none',
				'description' => __( 'Enter your price. (required*)', 'elementor' ),
			]
		);

		$widget->add_control(
			'cefi_cc_product_name',
			[
				'label' => __( 'Product Name', 'elementor' ),
				'type' => Controls_Manager::TEXT,
				'default' => 'Product',
				'description' => __( 'Enter your product name.', 'elementor' )
			]
		);

		$widget->add_control(
			'cefi_cc_successurl',
			[
				'label' => __( 'Success URL', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
					
				],
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'description' => __( 'Link for successul url', 'elementor-pro' ),
			]
		);

		$widget->add_control(
			'cefi_cc_errorurl',
			[
				'label' => __( 'Error URL', 'elementor-pro' ),
				'type' => Controls_Manager::URL,
				'dynamic' => [
					'active' => true,
					
				],
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'description' => __( 'Link for error url', 'elementor-pro' ),
			]
		);

		$widget->add_control(
			'cefi_cc_createinvoice',
			[
				'label' => __( 'Create Invoice', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'Yes', 'elementor-pro' ),
				'label_off' => __( 'No', 'elementor-pro' ),
				'return_value' => 'true',
				'default' => 'true',
				'separator' => 'before',
			]
		);

		$widget->add_control(
			'cefi_cc_redirect',
			[
				'label' => __( 'Redirect?', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => __( 'No', 'elementor-pro' ),
				'label_off' => __( 'Yes', 'elementor-pro' ),
				'return_value' => 'true',
				'default' => 'false',
				'separator' => 'before',
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

		if($settings['cefi_cc_terminal'] != '' && $settings['cefi_cc_username'] != '' && $settings['cefi_cc_price'] != ''){
			$post_response = $this->post( $basic_fields );
		}

	}

	private function create_integration_object( Form_Record $record ) {
		$fields_record = $record->get( 'fields' );
		$settings = $record->get( 'form_settings' );
		$data = array();
		
        // form fields 
		foreach($fields_record as $key => $subscriber){
			$data['data'][$key] = $subscriber['value'];
		}
		// form settings
        $data['TerminalNumber'] = $settings['cefi_cc_terminal'];
		$data['UserName'] = $settings['cefi_cc_username'];
		$data['Operation'] = $settings['cefi_cc_operation'];
		$data['Languge'] = $settings['cefi_cc_lang'];
		$data['coin'] = $settings['cefi_cc_coin'];
		$data['price'] = $settings['cefi_cc_price'];
		$data['product_name'] = $settings['cefi_cc_product_name'];
		$data['SuccessRedirectUrl'] = $settings['cefi_cc_successurl']['url'];
		$data['ErrorRedirectUrl'] = $settings['cefi_cc_errorurl']['url'];
		$data['CreateInvoice'] = $settings['cefi_cc_createinvoice'];
		$data['redirect'] = $settings['cefi_cc_redirect'];
        
		return $data;
	}
	
	private function post( $json_data ) {
		$TerminalNumber = $json_data['TerminalNumber']; # Company terminal 
		$UserName = $json_data['UserName'];   # API User
		$CreateInvoice = $json_data['CreateInvoice'];  # to Create Invoice (Need permissions to create invoice )
		$IsIframe = $json_data['redirect'];   # Iframe or Redirect 
		$Operation = $json_data['Operation'];  # = 1 - Bill Only , 2- Bill And Create Token , 3 - Token Only , 4 - Suspended Deal (Order).
		#Create Post Information
		// Account vars
		$vars =  array();
		$vars['TerminalNumber'] = $TerminalNumber;
		$vars['UserName'] = $UserName;
		$vars["APILevel"] = "10";
		$vars['codepage'] = '65001';
		$vars["Operation"] = $Operation;
		
		
		$vars["Languge"] =  $json_data['Languge'];
		$vars["CoinID"] = $json_data['coin'];
		$vars["SumToBill"] = $json_data['price'];
		$vars['ProductName'] = $json_data['product_name'];
		$vars['CardOwnerName'] = $json_data['data']['name'];
		$vars['CardOwnerEmail'] = $json_data['data']['email'];
		$vars['ReqCardOwnerEmail'] = false;
		
		$vars['SuccessRedirectUrl'] = $json_data['SuccessRedirectUrl'];
		$vars['ErrorRedirectUrl'] = $json_data['ErrorRedirectUrl'];

		$vars['IndicatorUrl']  = "http://www.yoursite.com/NotifyURL";
		
		$vars["ReturnValue"] = $_POST['post_id']; // value that will be return and save in CardCom system
		$vars["MaxNumOfPayments"] = "1"; // max num of payments to show  to the user
		
		if ($CreateInvoice)
		{
			// article for invoice vars:  http://kb.cardcom.co.il/article/AA-00244/0
			$vars['IsCreateInvoice'] = "true";
			// customer info
			$vars["InvoiceHead.CustName"] = $json_data['data']['name']; // customer name
			$vars["InvoiceHead.SendByEmail"] = "true"; // will the invoice be send by email to the customer
			$vars["InvoiceHead.Language"] = $json_data['Languge']; // he or en only
			$vars["InvoiceHead.Email"] = $json_data['data']['email']; // value that will be return and save in CardCom system
			$vars["InvoiceLines1.Description"] = $json_data['product_name'];
			$vars["InvoiceLines1.Price"] = $json_data['price'];
			$vars["InvoiceLines1.Quantity"] = "1";
		
		
			// ********   Sum of all Lines Price*Quantity  must be equals to SumToBill ***** //
		}
		
		// Send Data To Bill Gold Server
		$r = $this->PostVars($vars,'https://secure.cardcom.co.il/BillGoldLowProfile.aspx');
		// $r is  ResponseCode;Low Profile Code ; Description
		$exp = explode(';',$r);
		
		 
		# Is Deal OK 
		if ($exp[0] == "0") {
		  # Iframe or  Redicet User : 
			$newurl = "https://secure.cardcom.co.il/External/lowProfileClearing/".$TerminalNumber.".aspx?LowProfileCode=". $exp[1];
 
			if ($IsIframe)	{
				$this->ajax_handler->add_response_data( 'cc_url', $newurl );

			} else {

				$this->ajax_handler->add_response_data( 'redirect_url', $newurl );
			}
		
		}
		# Show Error to developer only
		else
		{
				echo $exp[0].' '.$exp[2];
		}
		
	
		

	}
	private function PostVars($vars,$PostVarsURL)
	{
	  $urlencoded = http_build_query($vars);
	  #init curl connection
	  if( function_exists( "curl_init" )) 
	  { 
		 $CR = curl_init();
		curl_setopt($CR, CURLOPT_URL, $PostVarsURL);
		curl_setopt($CR, CURLOPT_POST, 1);
		curl_setopt($CR, CURLOPT_FAILONERROR, true);
		curl_setopt($CR, CURLOPT_POSTFIELDS, $urlencoded );
		curl_setopt($CR, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($CR, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($CR, CURLOPT_FAILONERROR,true);
		#actual curl execution perfom
		$r = curl_exec( $CR );
		$error = curl_error ( $CR );
		# some error , send email to developer
		if( !empty( $error )) {
	
		  echo $error;
	
		  die();
		}
	   curl_close( $CR );
	   return $r;
	 }
	  else
	 {
	  echo "No curl_init" ;
	  die();
	  }
	}
	private function add_admin_error( $message ) {
		if ( current_user_can( 'edit_post', $_POST['post_id'] ) ) {
			$this->ajax_handler->add_admin_error_message( $message );
		}
	}
}

	\ElementorPro\Modules\Forms\Module::instance()->add_form_action( 'cefi_cc', new CEFI_CC() );

