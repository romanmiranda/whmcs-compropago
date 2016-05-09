<?php
/**
 * Copyright 2015 Compropago.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
/**
 * Compropago WHMCS plugin
 * @author Eduardo Aguilar <eduardo.aguilar@compropago.com>
 */


if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

use Compropago\Sdk\Client;
use Compropago\Sdk\Service;
use Compropago\Sdk\Controllers\Views;

/**
 * Funcion que despluega los campos de configuracion para el modulo de ComproPago
 *
 * @return array
 */
function compropago_config()
{;
    return array(
        "FriendlyName" => array(
            "Type"         => "System",
            "Value"        =>"ComproPago (Oxxo, 7Eleven, Coppel, etc.)"
        ),
        "publickey" => array(
            "FriendlyName" => "Public Key",
            "Type"         => "text",
            "Size"         => "30",
            "Description"  => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion'>
                               https://www.compropago.com/panel/configuracion</a>.",
        ),
        "privatekey" => array(
            "FriendlyName" => "Private Key",
            "Type"         => "text",
            "Size"         => "30",
            "Description"  => "Esta llave esta disponible en <a href='https://www.compropago.com/panel/configuracion'>
                               https://www.compropago.com/panel/configuracion</a>.",
        ),
        "mode" => array(
            "FriendlyName" => "Active Mode",
            "Type"         => "yesno",
            "Description"  => "Seleccione si esta en modo activo o modo de pruebas."
        ),
        "webhook" => array(
            "FriendlyName" => "Webhook",
            "Type"         => "textarea",
            "Descripcion"  => "Copie esta direccion y agreguela en el panel de compropago en la seccion
                               <a href='https://www.compropago.com/panel/webhooks'>Webhooks</a>",
            "Default"      => $_SERVER['SERVER_NAME']."/modules/gateways/callback/compropago.php"
        ),
        "showlogo" => array(
            "FriendlyName" => "Mostrar Logos",
            "Type"         => "yesno",
            "Description"  => "Mostrar los logos de las tiendas o un SelectBox"
        ),
        "description" => array(
            "FriendlyName" => "Descripcion",
            "Type"         => "text",
            "Description"  => "Descripcion del servicio",
            "Default"      => "ComproPago, pagos en Oxxo, 7Eleven, Coppel y mas."
        ),
        "instructions" => array(
            "FriendlyName" => "Instrucciones",
            "Type"         => "text",
            "Description"  => "Instrucciones para la seleccion de tienda",
            "Default"      => "Antes de finalizar seleccione la tienda donde desea realizar su pago"
        ),
    );
}

/**
 * Generar retroalimentacion de errores posibles
 *
 * @param Service $service
 * @param $publickey
 * @param $privatekey
 * @param $mode
 * @return null|string
 */
function hook_retro(Service $service, $publickey, $privatekey, $mode)
{
    $error = null;


    if(!empty($publickey) && !empty($privatekey)){
        if($mode=='yes'){
            $moduleLive=true;
        }else {
            $moduleLive=false;
        }

        try{
            //eval keys
            if(!$compropagoResponse = $service->evalAuth()){
                $error = 'Invalid Keys, The Public Key and Private Key must be valid before using this module.';
            }else{
                if($compropagoResponse->mode_key != $compropagoResponse->livemode){
                    $error = 'Your Keys and Your ComproPago account are set to different Modes.';
                }else{
                    if($moduleLive != $compropagoResponse->livemode){
                        $error = 'Your Store and Your ComproPago account are set to different Modes.';
                    }else{
                        if($moduleLive != $compropagoResponse->mode_key){
                            $error = 'ComproPago ALERT:Your Keys are for a different Mode.';
                        }else{
                            if(!$compropagoResponse->mode_key && !$compropagoResponse->livemode){
                                $error = 'WARNING: ComproPago account is Running in TEST Mode, NO REAL OPERATIONS';
                            }
                        }
                    }
                }
            }
        }catch (Exception $e) {
            $error = $e->getMessage();
        }
    }else{
        $error = 'The Public Key and Private Key must be set before using ComproPago';
    }

    return $error;

}

/**
 * Ejecucion del proceso de pago
 *
 * @param $params
 * @return null
 */
function compropago_link($params)
{
    $code = null;

    $config = array(
        "publickey"     => $params['publickey'],
        "privatekey"    => $params['privatekey'],
        "live"          => $params['mode']
    );

    try{
        $client = new Client($config);
        $service = new Service($client);

        $error = hook_retro($service,$params['publickey'],$params['privatekey'],$params['mode']);

        if(!empty($error)){
            throw new Exception($error);
        }

        $invoiceid      = $params['invoiceid']."-".md5(sprintf("%s %d", $params['companyname'], $params['invoiceid']));
        $return_url     = $params['returnurl'];
        $description    = $params["description"];
        $amount         = $params['amount'];

        $firstname      = $params['clientdetails']['firstname'];
        $lastname       = $params['clientdetails']['lastname'];
        $email          = $params['clientdetails']['email'];

        $full_name = "$firstname $lastname";

        $data = array(
            ":publickey:"       => $params['publickey'],
            ":customer_name:"   => $full_name,
            ":customer_email:"  => $email,
            ":product_price:"   => $amount,
            ":product_id:"      => $invoiceid,
            ":product_name:"    => $description,
            ":success_url:"     => $return_url,
            ":failed_url:"      => $return_url
        );

        $code = Views::loadView('button',null,'path');

        foreach($data as $key => $value){
            $code = str_replace($key,$value,$config);
        }
    }catch(Exception $e){
        $code = "<div style='width: 100%; padding: 1em; color: #FFFFFF; overflow: hidden; background-color: #33C3F0'>
                {$e->getMessage()}</div>";
    }
    return $code;
}

