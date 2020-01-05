<?php



function conectar()
{
    $host = "localhost:3307";
    $user = "root";
    $password = "";
    $db = "facturingpos";
    $con = @mysqli_connect($host, $user, $password, $db);
    if ($con) {
        //echo "Conexion realizada correctamente";
        mysqli_set_charset($con, "utf8");
    } else {
        echo "<br>Numero de error: " . mysqli_connect_errno();
        echo "<br>Descripcion de error: " . mysqli_connect_error();
    }
    return $con;
}

function desconectar($conexion)
{
    if ($conexion) {
        $cerrada = mysqli_close($conexion);

        if ($cerrada) {
            //echo"<br> conexion cerrada correctamente";
        } else {
            echo "<br> Error al cerrar la conexion";
        }
    } else
        echo 'ERROR:!!! Al abrir la conexion aun no ha sido abierta';
}
