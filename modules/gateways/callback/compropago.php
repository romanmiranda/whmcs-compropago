<?php
/**
 * WHMCS Sample Payment Callback File
 *
 * This sample file demonstrates how a payment gateway callback should be
 * handled within WHMCS.
 *
 * It demonstrates verifying that the payment gateway module is active,
 * validating an Invoice ID, checking for the existence of a Transaction ID,
 * Logging the Transaction for debugging and Adding Payment to an Invoice.
 *
 * For more information, please refer to the online documentation.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Developer_Docs
 *
 * @copyright Copyright (c) WHMCS Limited 2015
 * @license http://www.whmcs.com/license/ WHMCS Eula
 */
// Require libraries needed for gateway module functions.
require_once __DIR__ . '/../../../init.php';
require_once __DIR__ . '/../../../includes/gatewayfunctions.php';
require_once __DIR__ . '/../../../includes/invoicefunctions.php';
require_once __DIR__ . '/../../vendor/autoload.php';


// Detect module name from filename.
$gatewayModuleName = basename(__FILE__, '.php');
// Fetch gateway configuration parameters.
$gatewayParams = getGatewayVariables($gatewayModuleName);
// Die if module is not active.
if (!$gatewayParams['type']) {
    die("Module Not Activated");
}


$request = @file_get_contents('php://input');
if(!$jsonObj = json_decode($request)){
    die('Tipo de Request no Valido');
}


$jsonObj = \Compropago\Sdk\Utils\Utils::normalizeResponse($jsonObj);


//webhook Test?
if($jsonObj->id=="ch_00000-000-0000-000000" || $jsonObj->short_id =="000000"){
    die("Probando el WebHook?, <b>Ruta correcta.</b>");
}





$live = ($gatewayParams['mode'] == 'Live') ? true : false;
$publickey = $live ? $gatewayParams['publickey_live'] : $gatewayParams['publickey_test'];
$privatekey = $live ? $gatewayParams['privatekey_live'] : $gatewayParams['privatekey_test'];

$compropagoConfig= array(
    'publickey'     => $publickey,
    'privatekey'    => $privatekey,
    'live'          => $live,
    'contained'     =>'plugin; whmcs 1.1.0 ; WHMCS ; webhook;'

);
// consume sdk methods
try{
    $compropagoClient = new Compropago\Sdk\Client($compropagoConfig);
    $compropagoService = new Compropago\Sdk\Service($compropagoClient);
    // Valid Keys?
    if(!$compropagoResponse = $compropagoService->evalAuth()){
        die("ComproPago Error: Llaves no validas");
    }
    // Store Mode Vs ComproPago Mode, Keys vs Mode & combinations
    if(! Compropago\Sdk\Utils\Store::validateGateway($compropagoClient)){
        die("ComproPago Error: La tienda no se encuentra en un modo de ejecución valido");
    }


    $response = $compropagoService->verifyOrder($jsonObj->id);
    if($response->type=='error'){
        die('Error procesando el número de orden');
    }


    switch ($response->type){
        case 'charge.success':
            $nomestatus = "acceptorder";
            break;
        case 'charge.pending':
            $nomestatus = "pendingorder";
            break;
        case 'charge.declined':
            $nomestatus = "cancelorder";
            break;
        case 'charge.expired':
            $nomestatus = "cancelorder";
            break;
        case 'charge.deleted':
            $nomestatus = "cancelorder";
            break;
        case 'charge.canceled':
            $nomestatus = "cancelorder";
            break;
        default:
            die('Invalid Response type');
    }


}catch (Exception $e) {
    //something went wrong at sdk lvl
    die($e->getMessage());
}


$success = true;
$invoiceId 			= $jsonObj->order_info->order_id;
$paymentAmount 		= $jsonObj->order_info->order_price;
$paymentFee 		= $jsonObj->fee;
$hash 				= $jsonObj->order_info->order_name;
$transactionStatus 	= $nomestatus;
$transactionId      = $_POST['x_trans_id'];



/**
 * Validate callback authenticity.
 *
 * Most payment gateways provide a method of verifying that a callback
 * originated from them. In the case of our example here, this is achieved by
 * way of a shared secret which is used to build and compare a hash.
 */
if ($hash != md5($invoiceId . $_SERVER['SERVER_NAME'] . $paymentAmount . $publickey . $privatekey)) {
    $transactionStatus = 'Hash Verification Failure';
    $success = false;
}

/**
 * Validate Callback Invoice ID.
 *
 * Checks invoice ID is a valid invoice number. Note it will count an
 * invoice in any status as valid.
 *
 * Performs a die upon encountering an invalid Invoice ID.
 *
 * Returns a normalised invoice ID.
 */
$invoiceId = checkCbInvoiceID($invoiceId, $gatewayParams['name']);


/**
 * Accept the order transaction
 */
if ($success) {

    $command = $nomestatus;
    $adminuser = "admin";
    $values['orderid'] = $invoiceId;

    $response = localAPI($command,$values,$adminuser);

    if($response != 'success'){
        die($response);
    }
}
