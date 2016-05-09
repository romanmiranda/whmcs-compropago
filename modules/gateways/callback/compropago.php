<?php

# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");

$gatewaymodule = "compropago"; # Enter your gateway module name here replacing template
$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

$body = @file_get_contents('php://input');
$event_json = json_decode($body,true);

if( $event_json['type'] = "charge.success" ){
	# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
	$product_price = $event_json['data']['object']['payment_details'][0]['product_price'];
	$product_id = $event_json['data']['object']['payment_details'][0]['product_id'];

	$transid = $event_json['data']['object']['id'];
	$amount = $event_json['data']['object']['amount'];
	$fee = $event_json['data']['object']['fee'];
	$status = $event_json['data']['object']['paid'];

	$_product_id = explode("-",$product_id);

	$invoiceid = isset($_product_id[0]) ? $_product_id[0] : 0;

	if( $product_id == $invoiceid . "-" . md5(sprintf("%s %d", $GATEWAY['companyname'], $invoiceid ) )){

		$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

		checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

		if ($status) {
		    # Successful
		    addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
			logTransaction($GATEWAY["name"],$event_json,"Successful"); # Save to Gateway Log: name, data array, status
		} else {
			# Unsuccessful
		    logTransaction($GATEWAY["name"],$event_json,"Unsuccessful"); # Save to Gateway Log: name, data array, status
		}

	}
}