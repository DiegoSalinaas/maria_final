
<?php

include_once './controladores/usuarioSession.php';
$error_sesion = "";
$usuario = new UsuarioSession();

if ($usuario->usuarioLogeado()) {
    
        include_once 'main.php';
    


//    echo "usuario logeado";
} else if (isset($_POST['usuario']) && isset($_POST['pass'])) {


    if ($usuario->existeUsuario($_POST['usuario'], $_POST['pass'])) {
      
        
            include_once 'main.php';
        
    } else {

        if (isset($_POST['usuario'])) {
            $intentos = $usuario->dameIntentos($_POST['usuario']);
            $limite = $usuario->dameLimiteIntentos($_POST['usuario']);

            if ($intentos == $limite) {
                $error_sesion = 'Usuario bloqueado o no existe, contacta con el administrador';
                
                include_once 'login.php';
                
            } else {

                if ($intentos >= $limite) {
                    $usuario->bloquearUsuario($_POST['usuario']);
                    $error_sesion = 'Usuario bloqueado contacta con el administrador';
                    include_once 'login.php';
                } else {
//                    $_SESSION['usuario_intento'] = $_POST['usuario'];
                    $restantes = $limite - $intentos;
                    $error_sesion = 'Contraseña incorrecta. Tienes ' . $restantes . ' intentos.';
                    $intentos++;
                    $usuario->actualizatIntentos($_POST['usuario'], $intentos);
                }
                include_once 'login.php';
            }

        }

    }
} else {
//    echo "login";


    include_once 'login.php';
}
?>

