<?php 
namespace Controllers;

require_once __DIR__."/../CorePHP/Libraries/autoload.php";

use CorePHP\Core\MailUtils;

class ContactoController{
    
    private $data;
    private $objmail;
    private $response;
    
    public function __construct($data){
        $this->data = $data;
        $this->response = array(
            "stat" => false,
            "msg" => ""
        );
        $this->objmail = new MailUtils();
        
        $this->validate();
    }
    
    private function validate(){
        extract($this->data);
        
        if(isset($name) && !empty($name) && isset($asunto) && !empty($asunto) && isset($correo) && $this->objmail->ValidarMail($correo) && isset($telefono) && !empty($telefono) && isset($mensaje) && !empty($mensaje)){
            
            $this->objmail->from = $correo;
            $this->objmail->to = 'contacto@diprow.esy.es';
            $this->objmail->title = $asunto;
            $this->objmail->is_html = true;
            $this->objmail->encoding = "utf-8";
            $this->objmail->template_keys = array(
                "{{name}}" => $name,
                "{{asunto}}" => $asunto,
                "{{mensaje}}" => $mensaje,
                "{{telefono}}" => $telefono,
                "{{correo}}" => $correo
            );
            
            $this->objmail->FromTemplate("../template/basic.html");            
            
            if($this->objmail->SendEmail()){
                $this->response['stat'] = true;
                $this->response['msg'] = "El correo se ha enviado con exito. Tendra una respuesta a sus pregutas a la brevedad";
            }else{
                $this->response['msg'] = "Ocurrio un error al enviar el correo";
            }
        }else{
            $this->response['msg'] = "Asegurese de llenar todos los campos";
        }

        header("Content-Type: application/json");
        echo json_encode($this->response);
    }
    
}

if($_POST){
    new ContactoController($_POST);
}else{
    header("Content-Type: application/json");
    echo json_encode(array("stat" => false, "err" => "Peticion invalida."));
}
