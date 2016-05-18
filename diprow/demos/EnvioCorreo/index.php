<!doctype html>
<html class="no-js" lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Inicio</title>

    <link rel="stylesheet" href="../../css/foundation.min.css" />
    <script src="../../js/vendor/modernizr.js"></script>
</head>

<body>
<div class="fixed">
    <nav class="top-bar" data-topbar role="navigation">
        <ul class="title-area">
            <li class="name">
                <h1><a href="inicio"><img src="../../designes/Logo_DIPROW-WEB-BLANCO.svg" alt="DIPROW" style="height: 2em; width: auto;"></a></h1>
            </li>
            <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone -->
            <li class="toggle-topbar menu-icon"><a href="#"><span>Menu</span></a></li>
        </ul>

        <section class="top-bar-section">
            <!-- Left Nav Section -->
            <ul class="left">
                <li><a href="inicio">Inicio</a></li>
                <li><a href="quienessomos">¿Quiénes somos?</a></li>
                <li><a href="porquenosotros">¿Por qué nosotros?</a></li>
                <li><a href="servicios">Servicios</a></li>
                <li><a href="contacto">Contacto</a></li>
            </ul>
        </section>

        <section class="top-bar-section">
            <ul class="right">
                <li class="has-dropdown active"><a href="#">Demos</a>
                    <ul class="dropdown">
                        <li><a href="#">Envio personal de correos</a></li>
                    </ul>
                </li>
            </ul>
        </section>
    </nav>
</div>

<main>

    <div class="row">
        <div class="text-center large-12 columns">
            <h2>Prueva envio de correo ejecutivo con firma dijital.</h2>
            <hr>
        </div>
    </div>

    <div class="row">
        <form class="large-centered medium-centered large-6 medium-6 small-12 columns" action="Controllers/EnvioCorreoController.php" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="large-12 columns">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre">
                </div>
                <div class="large-12 columns">
                    <label for="asunto">Asunto</label>
                    <input type="text" id="asunto" name="asunto">
                </div>
                <div class="large-12 columns">
                    <label for="destino">Destinatario</label>
                    <input type="text" id="destino" name="destino">
                </div>
                <div class="large-12 columns">
                    <label for="mensaje">Mensaje</label>
                    <textarea id="mensaje" name="mensaje" rows="10"></textarea>
                </div>
                <div class="large-12 columns">
                    <input type="submit" class="button tiny expand" id="sendMessge" value="Enviar">
                </div>
            </div>
        </form>
    </div>
</main>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js"></script>
<script src="../../js/foundation.min.js"></script>
<script src="../../js/foundation/foundation.topbar.js"></script>
<script>
    $(document).foundation();
</script>
</body>

</html>