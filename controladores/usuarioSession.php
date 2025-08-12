<?php

include_once 'conexion/db.php';

class UsuarioSession {

    public function __construct() {
        session_start();
    }

    public function existeUsuario($usuario, $pass) {

        //conversor  a md5
        $passMD5 = md5($pass);
        //instancia de la clase BD para conexiones con la base de datos
        $db = new DB();

        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("SELECT `cod_usuario`, `usuario_alias`, 
            `usu_clave`, `estado_usu`, `cod_sucursal`, `cod_funcionario`, `cod_permiso`,
            `nro_timbrado`, `intentos`, `limite` FROM `usuario` 
                WHERE usuario_alias = :usuario and usu_clave = :pass");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario, 'pass' => $passMD5]);

        if ($query->rowCount()) {

            foreach ($query as $user) {
                $_SESSION['id_user'] = $user['cod_usuario'];
                $_SESSION['rol'] = $user['cod_permiso'];
                $_SESSION['usuario_alias'] = $user['usuario_alias'];
                $_SESSION['nombre_user'] = $user['usuario_alias'];
                $_SESSION['cod_sucursal'] = $user['cod_sucursal'];

                return true;
            }
        } else {
            return false;
        }
    }

    public function bloquearUsuario($usuario) {


        //instancia de la clase BD para conexiones con la base de datos
        $db = new DB();

        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("UPDATE usuario SET estado = 'BLOQUEADO' 
        WHERE  usuario_alias LIKE :usuario");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario]);
    }

    public function actualizatIntentos($usuario, $intentos) {


//        instancia de la clase BD para conexiones con la base de datos
//        echo "<script> alert($usuario); alert($intentos); </script>";
        $db = new DB();
        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("UPDATE usuario SET intentos = :intentos 
        WHERE  usuario_alias LIKE :usuario");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario, 'intentos' => $intentos]);
    }

    public function dameIntentos($usuario) {


        //instancia de la clase BD para conexiones con la base de datos
        $db = new DB();

        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("SELECT intentos FROM usuario  
        WHERE  usuario_alias LIKE :usuario limit 1");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario]);

        if ($query->rowCount()) {

            foreach ($query as $user) {


                return $user['intentos'];
            }
        } else {
            return 0;
        }
    }

    public function dameLimiteIntentos($usuario) {


        //instancia de la clase BD para conexiones con la base de datos
        $db = new DB();

        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("SELECT intentos FROM usuario  
        WHERE  usuario_alias LIKE :usuario");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario]);

        if ($query->rowCount()) {

            foreach ($query as $user) {


                return $user['intentos'];
            }
        } else {
            return 0;
        }
    }

    public function usuarioLogeado() {
        if (isset($_SESSION['id_user'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getNombre() {
        return $_SESSION['nomEmpleado'];
    }

    public function getIdCliente() {
        return $_SESSION['id_user'];
    }

//##############################################################################
//##############################################################################
//##############################PARA ADMINISTRADORES#######################
//##############################################################################
//##############################################################################

    public function existeAdmin($usuario, $pass) {

        //conversor  a md5
        $passMD5 = md5($pass);
        //instancia de la clase BD para conexiones con la base de datos
        $db = new DB();

        //preparamos la sentencia a ser ejecutada
        $query = $db->conectar()->prepare("SELECT nombre_apellido,"
                . "id_usuario FROM usuario WHERE nombre = :usuario "
                . "and clave = :pass;");
        //agregamos los valores a la consulta mediante la ayuda de un diccionario
        $query->execute(['usuario' => $usuario, 'pass' => $passMD5]);

        if ($query->rowCount()) {

            foreach ($query as $user) {
                $_SESSION['nombre_apellido_admin'] = $user['nombre_apellido'];
                $_SESSION['id_usuario'] = $user['id_usuario'];

                return true;
            }
        } else {
            return false;
        }
    }

    /**
     * 
     * @return boolean
     */
    public function adminLogeado() {
        if (isset($_SESSION['id_user'])) {
            return true;
        } else {
            return false;
        }
    }

    public function getNombreAdmin() {
        return $_SESSION['nombre_apellido_admin'];
    }

    public function getIdAdmin() {
        return $_SESSION['id_user'];
    }
}
