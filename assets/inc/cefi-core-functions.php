<?php 

class Cardcom
{
    public function cardcom_create_post($url, $args)
    {
        $response = wp_remote_post($url, $args);
        if (is_wp_error($response)) {
            $counter = 3;
            while ($counter > 0) {
                $response = wp_remote_post($url, $args);
                $counter--;
                if (is_wp_error($response)) {
                    error_log('post failed! url : '.$url);
                    $error = $response->get_error_message();
                    $this->HandleError('999', $error, $args);
                } else {
                    break;
                }
            }
        }
        return $response;
    }

    public function cardcom_senitize($params)
    {
        foreach ($params as &$p) {
            $p=substr(strip_tags(preg_replace("/&#x\d*;/", " ", $p)), 0, 200);
        }
        return $params;
    }

    public function cardcom_create_form($data)
    {
        $CreateInvoice = true;  # to Create Invoice (Need permissions to create invoice )
        $IsIframe = true;   # Iframe or Redirect
        #Create Post Information
        // Account vars
        $vars =  array();
        $vars['TerminalNumber'] = $data['TerminalNumber'];
        $vars['UserName'] = $data['UserName'];
        $vars["APILevel"] = "10"; // req
        $vars['codepage'] = '65001'; // unicode
        $vars["Operation"] = $data['Operation'];
        $vars["CreateTokenJValidateType"] = "5";
        $vars["Language"] =  'en';   // page languge he- hebrew , en - english , ru , ar
        $vars["CoinID"] = '2'; // billing coin , 1- NIS , 2- USD other , article :  http://kb.cardcom.co.il/article/AA-00247/0
        $vars["SumToBill"] = $data['price'];// Sum To Bill
        $vars['ProductName'] = $data['product_name']; // Product Name , will how if no invoice will be created.

        
        $vars['SuccessRedirectUrl'] = "https://secure.cardcom.solutions/DealWasSuccessful.aspx"; // Success Page
        $vars['ErrorRedirectUrl'] = "https://secure.cardcom.solutions/DealWasUnSuccessful.aspx?customVar=1234"; // Error Page

        // Other Optional vars :

            // $vars["CancelType"] = "2"; # show Cancel button on start ,
        // $vars["CancelUrl"] ="http://www.yoursite.com/OrderCanceld";
        $vars['IndicatorUrl']  = "http://www.yoursite.com/NotifyURL"; // Indicator Url \ Notify URL . after use -  http://kb.cardcom.co.il/article/AA-00240/0

        $vars["ReturnValue"] = "1234"; // Optional , ,recommended , value that will be return and save in CardCom system
        $vars["MaxNumOfPayments"] = "1"; // max num of payments to show  to the user

        $vars["InvoiceHeadOperation"] = "0"; //  0 = no create & show Invoice.  1 =(default)create Invoice.  2 = show Details Invoice but not create Invoice !

        if ($CreateInvoice) {
            // article for invoice vars:  http://kb.cardcom.co.il/article/AA-00244/0
            $vars['IsCreateInvoice'] = "true";
            // customer info :
            $vars["InvoiceHead.CustName"] = 'Test "customer'; // customer name
            $vars["InvoiceHead.SendByEmail"] = "true"; // will the invoice be send by email to the customer
            $vars["InvoiceHead.Language"] = "he"; // he or en only
            $vars["InvoiceHead.Email"] = "test@gmail.com";

            // products info

            // Line 1
            $vars["InvoiceLines1.Description"] = "itme 1";
            $vars["InvoiceLines1.Price"] = "150";
            $vars["InvoiceLines1.Quantity"] = "2";


            // ********   Sum of all Lines Price*Quantity  must be equals to SumToBill ***** //
        }
        $urlencoded = http_build_query($vars);
        $args = array('body'=>$urlencoded,
            'timeout'=>'5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array()
        );
        $response = cardcom_create_post('https://secure.cardcom.solutions/Interface/LowProfile.aspx', $args);
        if (is_wp_error($response)) {
            return;
        }

        $body = wp_remote_retrieve_body($response);
        return $body;
    }

    public function cardcom_get_token($TerminalNumber, $lowprofilecode, $UserName)
    {
        $vars = array(
            'TerminalNumber'=>$TerminalNumber,
            'LowProfileCode'=>$lowprofilecode,
            'UserName'=>$UserName
        );

        $args = array(
            'body'=> http_build_query($this->cardcom_senitize($vars)),
            'timeout'=>'5',
            'redirection' => '5',
            'httpversion' => '1.0',
            'blocking' => true,
            'headers' => array(),
            'cookies' => array()
        );
    
        $response = cardcom_create_post('https://secure.cardcom.solutions/Interface/BillGoldGetLowProfileIndicator.aspx', $args);
        $body = wp_remote_retrieve_body($response);

        return $body;
    }

    public function cardcom_save_token_cookie($token)
    {
        setcookie("CC_LowProfileCode", $responseArray['LowProfileCode'], time() + 900);
    }
}