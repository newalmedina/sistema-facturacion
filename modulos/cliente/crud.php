<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();

//guardar
if (isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"];
    $apellidos = $_POST["apellidos"];
    $codigoIdentificacion = $_POST["tipoIdentificacion"];
    $identificacion = $_POST["identificacion"];
    $fechanacimiento = $_POST["fechanacimiento"];
    $correo = $_POST["correo"];
    $sexo = $_POST["sexo"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];


    //verificar si existe o no usuarios para apartir de hay crear el ID
    $sql = "select * from clientes";

    $execute = mysqli_query($con, $sql);

    if ($execute) {

        if (mysqli_affected_rows($con)  > 0) {
            $sql = "SELECT max(substring(cod_cliente,7))+1 as serie FROM facturingpos.clientes";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $cod_cliente = "CLI-" . date("y") . $row2["serie"];
        } else {
            $cod_cliente = "CLI-" . date("y") . "100";
        }
    }

    $sql = "INSERT INTO clientes (cod_cliente, nombre, apellidos, identificacion,fecha_nacimiento, correo, telefono, direccion, sexo, cod_identificacion)";
    $sql .= "VALUES ('$cod_cliente', '$nombre', '$apellidos', '$identificacion','$fechanacimiento', '$correo', '$telefono', '$direccion', '$sexo', $codigoIdentificacion);";
    $execute = mysqli_query($con, $sql);

    if ($execute) {

        $_SESSION["success"] = "Guardado";
        //retroceso es para impedir cuando damos atras vuelva y guarde
        $_SESSION["retroceso"] = "modulos/usuario/add.php";

        ?>
        <script>
            location.href = 'index.php?directorio=cliente&pagina=index.php';
        </script>
    <?php
        } else {
            echo mysqli_error($con);
            $_SESSION["error"] = "guardar";
            ?>
        <script>
            location.href = 'index.php?directorio=cliente&pagina=index.php';
        </script>
    <?php
        }
    }
    if (isset($_POST["actualizar"])) {
        $nombre = $_POST["nombre"];
        $apellidos = $_POST["apellidos"];
        $codigoIdentificacion = $_POST["tipoIdentificacion"];
        $identificacion = $_POST["identificacion"];
        $fechanacimiento = $_POST["fechanacimiento"];
        $correo = $_POST["correo"];
        $sexo = $_POST["sexo"];
        $direccion = $_POST["direccion"];
        $telefono = $_POST["telefono"];
        $cod_cliente = $_POST["cod_cliente"];


        $sql = "UPDATE clientes SET nombre = '$nombre', apellidos = '$apellidos',";
        $sql .=  "identificacion = '$identificacion', fecha_nacimiento = '$fechanacimiento',";
        $sql .= "correo = '$correo', telefono = '$telefono', direccion = '$direccion',";
        $sql .= "sexo = '$sexo', cod_identificacion = '$codigoIdentificacion' WHERE cod_cliente ='$cod_cliente'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {



            $_SESSION["success"] = "Actualizado";
            //retroceso es para impedir cuando damos atras vuelva y guarde


            ?>
        <script>
            location.href = 'index.php?directorio=cliente&pagina=edit.php&id=<?php echo $cod_cliente ?>';
        </script>
    <?php
        } else {
            echo mysqli_error($con);
        }
    }
    //eliminar
    if (isset($_GET["eliminar"])) {
        $id = $_GET["id"];


        //borrar registro
        $sql = "delete  from clientes where cod_cliente='$id'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {


            $_SESSION["success"] = "Borrado";
            // header("location: url=index.php?directorio=usuario&pagina=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=cliente&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "borrar";
            echo "<div class='alert alert-danger'>Error al borrar el registro" . mysqli_error($con) . "</div>";
            //header("location: url=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=cliente   &pagina=index.php';
        </script>
<?php
    }
}
//listar
if (isset($_GET["listar"])) {
    listar();
} else {
    listar();
}
function listar()
{
    global $con;
    $sql = "select * from clientes";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {


            echo "<tr><td>" . $row["cod_cliente"] . "</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            echo "<td>" . $row["apellidos"] . "</td>";
            echo "<td>" . $row["identificacion"] . "</td>";
            echo "<td>" . $row["correo"] . "</td>";

            $id = $row["cod_cliente"];
            echo "<td><a  class='btn btn-primary mr-1 ' href='index.php?directorio=cliente&pagina=edit.php&id=$id'><i class='fas fa-pencil-alt'></i></a>";
            echo "<button id='' class='btn btn-danger eliminar' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button></td></tr>";
        }
    }
}
desconectar($con);
