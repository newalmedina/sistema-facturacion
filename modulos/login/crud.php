
<?php

session_start();
require_once '../conexion/conexion.php';
$con = conectar();
if (isset($_POST["acceder"])) {
    $correo = $_POST["correo"];
    $pass = $_POST["pass"];
    $encriptarPass = crypt($pass, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');


    $sql = "select * from usuarios where correo='$correo' and pass='$encriptarPass'";

    $execute = mysqli_query($con, $sql);

    if ($execute) {
        $row = mysqli_fetch_array($execute);
        if (mysqli_affected_rows($con)  > 0) {
            if ($row["estatus"] == 0) {
                $_SESSION["errorLogin"] = "inactivo";

                header("Location: ../../index.php");
            } else {
                $_SESSION["usuario"] = array("nombre" => $row["nombre"], "apellidos" => $row["apellidos"], "cod_usuario" => $row["cod_usuario"], "provilegios" => $row["cod_privilegio"], "foto" => $row["foto"]);

                header("Location: ../../index.php");
            }
        } else {
            $_SESSION["errorLogin"] = "incorrecto";

            header("Location: ../../index.php");
        }
    }
}
if (isset($_GET["salir"])) {
    unset($_SESSION["usuario"]);
    header("Location: ../../index.php");
}
desconectar($con);
