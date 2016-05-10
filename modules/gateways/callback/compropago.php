<?php

# Required File Includes
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . "/../../../dbconnect.php";
require_once __DIR__ . "/../../../includes/functions.php";
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';

$GATEWAY = getGatewayVariables('compropago');

if (!$GATEWAY["type"]){
    die("CompropagoNo se encuentra activo");
}

$body = @file_get_contents('php://input');
$event_json = json_decode($body,true);

if( $event_json['type'] = "charge.success" ){
	# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
	$product_price  = $event_json['data']['object']['payment_details'][0]['product_price'];
	$product_id     = $event_json['data']['object']['payment_details'][0]['product_id'];

	$transid        = $event_json['data']['object']['id'];
	$amount         = $event_json['data']['object']['amount'];
	$fee            = $event_json['data']['object']['fee'];
	$status         = $event_json['data']['object']['paid'];

	$_product_id    = explode("-",$product_id);

	$invoiceid      = isset($_product_id[0]) ? $_product_id[0] : 0;

	if( $product_id == $invoiceid."-".md5(sprintf("%s %d", $GATEWAY['companyname'], $invoiceid)) ){

        # Checks invoice ID is a valid invoice number or ends processing
		$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]);

        # Checks transaction number isn't already in the database and ends processing if it does
		checkCbTransID($transid);

		if ($status) {
		    # Successful
            # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
		    addInvoicePayment($invoiceid,$transid,$amount,$fee,'compropago');

            # Save to Gateway Log: name, data array, status
			logTransaction($GATEWAY["name"],$event_json,"Successful");
		} else {
			# Unsuccessful
            # Save to Gateway Log: name, data array, status
		    logTransaction($GATEWAY["name"],$event_json,"Unsuccessful");
		}

	}
}