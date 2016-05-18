<?php

namespace demos\EnvioCorreo\Controllers;

require_once __DIR__."/../../../CorePHP/Libraries/autoload.php";

use CorePHP\Core\MailUtils;


class EnvioCorreoController
{

    private $data;
    private $response;
    private $objmail;

    public function __construct($data)
    {
        $this->data = $data;
        $this->response = array(
            "stat" => false,
            "msg" => ""
        );
        $this->objmail = new MailUtils();

        $this->validate();
    }

    private function validate()
    {
        extract($this->data);

        if(isset($nombre) && !empty($nombre) && isset($asunto) && !empty($asunto) && isset($destino) && !empty($destino) && isset($mensaje) && !empty($mensaje)){
            if($this->objmail->ValidarMail($destino)){
                $this->objmail->from = "contacto@diprow.esy.es";
                $this->objmail->to = $destino;
                $this->objmail->title = $asunto;
                $this->objmail->is_html = true;
                $this->objmail->encoding = "utf-8";
                $this->objmail->template_keys = array(
                    ":nombre:" => $nombre,
                    ":asunto:" => $asunto,
                    ":mensaje:" => $mensaje,
                    ":destino:" => $destino
                );

                $this->objmail->FromTemplate("../correo_plantilla/presentacion.html");

                if($this->objmail->SendEmail()){
                    $this->response['stat'] = true;
                    $this->response['msg'] = "Correo enviado correctamente";
                }else{
                    $this->response['msg'] = "Ocurrio un error al enviar el correo.";
                }
            }else{
                $this->response['msg'] = "El correo destinatario no es valido.";
            }
        }else{
            $this->response['msg'] = "La informacion no es valida o esta incompleta, asegurese de llenar todos los campos así como de cargar su firma digital en formato jpeg.";
        }

        header("Location: ../?stat={$this->response['stat']}&msg={$this->response['msg']}");
    }

}

if($_POST){
    new EnvioCorreoController($_POST);
}else{
    header("Location: ../");
}