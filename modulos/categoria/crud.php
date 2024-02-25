<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();

//guardar
if (isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"];

    //verificar si existe o no categorias para apartir de hay crear el ID
    $sql = "select * from categorias";

    $execute = mysqli_query($con, $sql);

    if ($execute) {

        if (mysqli_affected_rows($con)  > 0) {
            $sql = "SELECT max(substring(cod_categoria,7))+1 as serie FROM facturingpos.categorias";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $cod_categoria = "CAT-" . date("y") . $row2["serie"];
        } else {
            $cod_categoria = "CAT-" . date("y") . "100";
        }
    }

    $sql = "INSERT INTO categorias (cod_categoria, nombre)";
    $sql .= "VALUES ('$cod_categoria', '$nombre');";
    $execute = mysqli_query($con, $sql);

    if ($execute) {

        $_SESSION["success"] = "Guardado";
        //retroceso es para impedir cuando damos atras vuelva y guarde
        $_SESSION["retroceso"] = "modulos/categoria/add.php";

        ?>
        <script>
            location.href = 'index.php?directorio=categoria&pagina=index.php';
        </script>
    <?php
        } else {
            echo mysqli_error($con);
            $_SESSION["error"] = "guardar";
            ?>
        <script>
            location.href = 'index.php?directorio=categoria&pagina=index.php';
        </script>
    <?php
        }
    }
    if (isset($_POST["actualizar"])) {
        $nombre = $_POST["nombre"];
        $cod_categoria = $_POST["cod_categoria"];


        $sql = "UPDATE categorias SET nombre = '$nombre' WHERE cod_categoria ='$cod_categoria'";


        $execute = mysqli_query($con, $sql);
        if ($execute) {
            $_SESSION["success"] = "Actualizado";
            //retroceso es para impedir cuando damos atras vuelva y guarde
            ?>
        <script>
            location.href = 'index.php?directorio=categoria&pagina=edit.php&id=<?php echo $cod_categoria ?>';
        </script>
    <?php
        }
    }
    //eliminar
    if (isset($_GET["eliminar"])) {
        $id = $_GET["id"];

        //borrar registro
        $sql = "delete  from categorias where cod_categoria='$id'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {



            $_SESSION["success"] = "Borrado";
            // header("location: url=index.php?directorio=usuario&pagina=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=categoria&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "borrar";
            echo "<div class='alert alert-danger'>Error al borrar el registro" . mysqli_error($con) . "</div>";
            //header("location: url=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=categoria&pagina=index.php';
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
    $sql = "select * from categorias";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {


            echo "<tr><td>" . $row["cod_categoria"] . "</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            $id = $row["cod_categoria"];
            echo "<td><a  class='btn btn-primary mr-1 ' href='index.php?directorio=categoria&pagina=edit.php&id=$id'><i class='fas fa-pencil-alt'></i></a>";
            echo "<button  class='btn btn-danger eliminar ' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button></td></tr>";
        }
    }
}
desconectar($con);
