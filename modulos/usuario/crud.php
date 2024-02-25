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
    $estatus = $_POST["estatus"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $password = $_POST["password"];
    $encriptarPass = crypt($password, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
    $privilegios = $_POST["privilegios"];

    //verificar si existe o no usuarios para apartir de hay crear el ID
    $sql = "select * from usuarios";

    $execute = mysqli_query($con, $sql);

    if ($execute) {

        if (mysqli_affected_rows($con)  > 0) {
            $sql = "SELECT max(substring(cod_usuario,7))+1 as serie FROM facturingpos.usuarios";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $cod_usuario = "USU-" . date("y") . $row2["serie"];
        } else {
            $cod_usuario = "USU-" . date("y") . "100";
        }
    }

    $sql = "INSERT INTO usuarios (cod_usuario, nombre, apellidos, identificacion,fecha_nacimiento, pass, correo, telefono, direccion, cod_privilegio, sexo, estatus, cod_identificacion)";
    $sql .= "VALUES ('$cod_usuario', '$nombre', '$apellidos', '$identificacion','$fechanacimiento','$encriptarPass', '$correo', '$telefono', '$direccion',$privilegios, '$sexo', $estatus, $codigoIdentificacion);";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        //*codigo para imagen
        if ($_FILES["foto"]["name"] != "") { //verificar si hay imagen  seleccionada

            //extraer ultimo id inserccion
            $sql = "SELECT max(cod_usuario) as cod_usuario FROM facturingpos.usuarios";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $ultimoId =  $row2["cod_usuario"];

            $foto = $_FILES["foto"]["name"];
            $ruta = $_FILES["foto"]["tmp_name"];
            $imagen = "modulos/usuario/fotos/" . rand(100, 999) . $foto;
            copy($ruta, $imagen);

            //obtener el ultimo id para guardar la imagen
            $sql = "update usuarios set foto ='$imagen'where cod_usuario='$ultimoId'";

            $execute = mysqli_query($con, $sql);
            if (!$execute) {
                echo "Error al guardar la foto " . mysqli_error($con);
            }
        }

        $_SESSION["success"] = "Guardado";
        //retroceso es para impedir cuando damos atras vuelva y guarde
        $_SESSION["retroceso"] = "modulos/usuario/add.php";

        ?>
        <script>
            location.href = 'index.php?directorio=usuario&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "guardar";
            ?>
        <script>
            location.href = 'index.php?directorio=usuario&pagina=index.php';
        </script>
    <?php
        }
    }
    if (isset($_POST["actualizar"])) {
        $nombre = $_POST["nombre"];
        $cod_usuario = $_POST["cod_usuario"];
        $apellidos = $_POST["apellidos"];
        $codigoIdentificacion = $_POST["tipoIdentificacion"];
        $identificacion = $_POST["identificacion"];
        $fechanacimiento = $_POST["fechanacimiento"];
        $correo = $_POST["correo"];
        $sexo = $_POST["sexo"];
        $estatus = $_POST["estatus"];
        $direccion = $_POST["direccion"];
        $telefono = $_POST["telefono"];
        $password = $_POST["password"];
        $encriptarPass = crypt($password, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
        $privilegios = $_POST["privilegios"];

        $sql = "UPDATE usuarios SET nombre = '$nombre', apellidos = '$apellidos',";
        $sql .=  "identificacion = '$identificacion', fecha_nacimiento = '$fechanacimiento',";
        $sql .= "correo = '$correo', telefono = '$telefono', direccion = '$direccion',";
        $sql .= "cod_privilegio = $privilegios, sexo = '$sexo', estatus = $estatus,";
        $sql .= " cod_identificacion = '$codigoIdentificacion' WHERE cod_usuario ='$cod_usuario'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {
            //actualizar pass si no esta vacio
            if ($password != "") {
                $encriptarPass = crypt($password, '$2a$07$asxx54ahjppf45sd87a5a4dDDGsystemdev$');
                $sql = "update usuarios set pass ='$encriptarPass'where cod_usuario='$cod_usuario'";
                $execute = mysqli_query($con, $sql);
            }
            //*codigo para imagen
            if ($_FILES["foto"]["name"] != "") { //verificar si hay imagen  seleccionada

                //buscar foto existente para eliminar
                $sql = "select foto  from usuarios where cod_usuario='$cod_usuario'";
                $execute = mysqli_query($con, $sql);
                $row = mysqli_fetch_array($execute);
                $imagen = $row["foto"];

                //eliminar imagen si existe
                if ($imagen != "")
                    unlink($imagen);
                /************* */

                $foto = $_FILES["foto"]["name"];
                $ruta = $_FILES["foto"]["tmp_name"];
                $imagen = "modulos/usuario/fotos/" . rand(100, 999) . $foto;
                copy($ruta, $imagen);

                //Actualizar img
                $sql = "update usuarios set foto ='$imagen'where cod_usuario='$cod_usuario'";

                $execute = mysqli_query($con, $sql);
                if (!$execute) {
                    echo "Error al guardar la foto " . mysqli_error($conexion);
                }
            }

            $_SESSION["success"] = "Actualizado";
            //retroceso es para impedir cuando damos atras vuelva y guarde


            ?>
        <script>
            location.href = 'index.php?directorio=usuario&pagina=edit.php&id=<?php echo $cod_usuario ?>';
        </script>
    <?php
        }
    }
    //eliminar
    if (isset($_GET["eliminar"])) {
        $id = $_GET["id"];
        //verificar que tenga foto
        $sql = "select foto  from usuarios where cod_usuario='$id'";
        $execute = mysqli_query($con, $sql);
        $row = mysqli_fetch_array($execute);
        $imagen = $row["foto"];

        //borrar registro
        $sql = "delete  from usuarios where cod_usuario='$id'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {
            //eliminar imagen si existe
            if ($imagen != "")
                unlink($imagen);
            /************* */


            $_SESSION["success"] = "Borrado";
            // header("location: url=index.php?directorio=usuario&pagina=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=usuario&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "borrar";
            echo "<div class='alert alert-danger'>Error al borrar el registro" . mysqli_error($con) . "</div>";
            //header("location: url=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=usuario&pagina=index.php';
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
    $sql = "select * from usuarios";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {


            echo "<tr><td>" . $row["cod_usuario"] . "</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            echo "<td>" . $row["apellidos"] . "</td>";
            echo "<td>" . $row["identificacion"] . "</td>";
            echo "<td>" . $row["correo"] . "</td>";
            if ($row["estatus"] == 1) {

                $estatus = "Activo";
                echo "<td class='bg-success '>" . $estatus . "</td>";
            } else {
                $estatus = "Desactivado";
                echo "<td class='bg-warning '>" . $estatus . "</td>";
            }
            if ($row["foto"] != "") {
                echo '<td> <img class="img-fluid"  src="' . $row["foto"] . '" style="width: 40px; height:40px;" alt=""></td>';
            } else {
                echo '<td> <img class="img-fluid"  src="img/noimage.jpg " style="width: 40px; height:40px;" alt=""></td>';
            }
            $id = $row["cod_usuario"];
            echo "<td><a  class='btn btn-primary mr-1 ' href='index.php?directorio=usuario&pagina=edit.php&id=$id'><i class='fas fa-pencil-alt'></i></a>";
            echo "<button  class='btn btn-danger eliminar' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button></td></tr>";
        }
    }
}
desconectar($con);
