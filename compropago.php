<?php

function compropago_config() {
    $configarray = array(
     "FriendlyName" => array("Type" => "System", "Value"=>"Compropago"),
     "public_live_key" => array("FriendlyName" => "Live Public Key", "Type" => "text", "Size" => "25", "Description" => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion' title='Compropago API Keys'>https://www.compropago.com/panel/configuracion</a>.", ),
     "private_live_key" => array("FriendlyName" => "Live Secret Key", "Type" => "text", "Size" => "25", "Description" => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion' title='Compropago API Keys'>https://www.compropago.com/panel/configuracion</a>.", ),
	 "public_test_key" => array("FriendlyName" => "Test Public Key", "Type" => "text", "Size" => "20", "Description" => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion' title='Compropago API Keys'>https://www.compropago.com/panel/configuracion</a>.", ),
	 "private_test_key" => array("FriendlyName" => "Test Private Key", "Type" => "text", "Size" => "20", "Description" => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion' title='Compropago API Keys'>https://www.compropago.com/panel/configuracion</a>." , ),
	 "mode" => array("FriendlyName" => "Modo", "Type" => "radio", "Options" => "Live,Test", "Description" => "Seleccione si esta en modo activo o modo de pruebas." , "default" => "Activo" ),
    );
	return $configarray;
}

function compropago_link($params) {
	# Gateway Specific Variables
	$public_live_key = $params['public_live_key'];
	$public_test_key = $params['public_test_key'];

	# Invoice Variables
	$invoiceid =  $params['invoiceid'] . "-" . md5(sprintf("%s %d", $params['companyname'], $params['invoiceid']));
	$description = $params["description"];
    $amount = $params['amount']; # Format: ##.##
    $currency = $params['currency']; # Currency Code

	# Client Variables
	$firstname = $params['clientdetails']['firstname'];
	$lastname = $params['clientdetails']['lastname'];
	$email = $params['clientdetails']['email'];
	$address1 = $params['clientdetails']['address1'];
	$address2 = $params['clientdetails']['address2'];
	$city = $params['clientdetails']['city'];
	$state = $params['clientdetails']['state'];
	$postcode = $params['clientdetails']['postcode'];
	$country = $params['clientdetails']['country'];
	$phone = $params['clientdetails']['phonenumber'];

	# System Variables
	$companyname = $params['companyname'];
	$systemurl = $params['systemurl'];
	$currency = $params['currency'];
	$return_url = $params['returnurl'];

	# Enter your code submit to the gateway...
	$code  = '<form action="https://www.compropago.com/comprobante/" method="post">';
	if( $params['mode'] == 'Live' ){
		$code .= '<input type="hidden" name="public_key" value="' . $public_live_key . '" />';
	}else{
		$code .= '<input type="hidden" name="public_key" value="' . $public_test_key . '" />';
	}
	
	$code .= '<input type="hidden" name="app_client_name" value="WHMCS" />';
	$code .= '<input type="hidden" name="app_client_version" value="1.0" />';
	
	$code .= '<input type="hidden" name="customer_data_blocked" value="false" />';
	$code .= '<input type="hidden" name="customer_name" value="' . $firstname . " " . $lastname . '" />';
	$code .= '<input type="hidden" name="customer_email" value="' . $email . '" />';

	$code .= '<input type="hidden" name="product_price" value="' . $amount . '" />';
	$code .= '<input type="hidden" name="product_id" value="' . $invoiceid . '" />';
	$code .= '<input type="hidden" name="product_name" value="' . $description . '" />';

	$code .= '<input type="hidden" name="success_url" value="' . $return_url . '" />';
	$code .= '<input type="hidden" name="failed_url" value="' . $return_url . '" />';

	$code .= '<p align="center"><input type="image" src="https://www.compropago.com/assets/payment-green-btn.png" alt="Compropago" /></p>';
	$code .= '</form>';
	return $code;
}