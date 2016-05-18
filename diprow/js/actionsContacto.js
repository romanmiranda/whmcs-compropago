var Actions = {
    nombre: document.querySelector("#name"),
    asunto: document.querySelector("#asunto"),
    correo: document.querySelector("#correo"),
    telcon: document.querySelector("#telefono"),
    mensaje: document.querySelector("#mensaje"),

    __init__: function(){
        document.querySelector("#enviar").onclick = Actions.clickContactForm;
    },
    
    clickContactForm: function(){
        if(Actions.validateData()) {
            var data = {
                name: Actions.nombre.value,
                asunto: Actions.asunto.value,
                correo: Actions.correo.value,
                telefono: Actions.telcon.value,
                mensaje: Actions.mensaje.value
            };

            $.ajax({
                url: "Controllers/ContactoController.php",
                type: "post",
                data: data,
                success: function(response){
                    console.log(response);
                    if(response.stat){
                        $("#contact-alert").removeClass("alert");
                        $("#contact-alert").addClass("success");
                        $("#message-alert").html(response.msg);
                        $("#contact-alert").css("display","block");
                    }else{
                        $("#contact-alert").removeClass("success");
                        $("#contact-alert").addClass("alert");
                        $("#message-alert").html(response.msg);
                        $("#contact-alert").css("display","block");
                    }
                }
            });

            Actions.cleanInputs();
        }
    },
    
    validateData: function(){

        var flag = true;
        var textError = "";
        
        if(Actions.nombre.value == ""){
            textError = "El campo 'Nombre' debe ser completado.";
            flag = false;
        }else if(Actions.asunto.value == ""){
            textError = "El campo 'Asunto' debe ser completado.";
            flag = false;
        }else if(Actions.correo. value == "" || !Actions.validateCorreo(Actions.correo.value)){
            textError = "El correo no es valido.";
            flag = false;
        }else if(Actions.telcon.value == "" || isNaN(Actions.telcon.value)){
            textError = "Se deve de ingresar un telefono para posterior contacto.";
            flag = false;
        }else if(Actions.mensaje.value == ""){
            textError = "Se debe agregar una descripción a este mensaje";
            flag = false;
        }
        
        if(flag){
            return true;
        }else{
            document.querySelector("#error-text").innerHTML = textError;
            $('#myModal').foundation('reveal', 'open');
        }
    }, 
    
    validateCorreo: function ( email ) {
        var expr = /^([a-zA-Z0-9_\.\-])+\@(([a-zA-Z0-9\-])+\.)+([a-zA-Z0-9]{2,4})+$/;
        if ( !expr.test(email) ){
            return false;
        }else{
            return true;
        }
    },

    cleanInputs: function(){
        Actions.nombre.value = "";
        Actions.asunto.value = "";
        Actions.correo.value = "";
        Actions.telcon.value = "";
        Actions.mensaje.value = "";
    }
};

document.onreadystatechange = Actions.__init__;