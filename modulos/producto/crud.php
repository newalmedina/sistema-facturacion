<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();

//guardar
if (isset($_POST["guardar"])) {
    $nombre = $_POST["nombre"];
    $descripcion = $_POST["descripcion"];
    $precio = $_POST["precio"];
    $costo = $_POST["costo"];
    $stock = $_POST["stock"];
    $cod_categoria = $_POST["cod_categoria"];

    //verificar si existe o no usuarios para apartir de hay crear el ID
    $sql = "select * from productos";

    $execute = mysqli_query($con, $sql);

    if ($execute) {

        if (mysqli_affected_rows($con)  > 0) {
            $sql = "SELECT max(substring(cod_producto,7))+1 as serie FROM productos";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $cod_producto = "PRO-" . date("y") . $row2["serie"];
        } else {
            $cod_producto = "PRO-" . date("y") . "100";
        }
    }

    $sql = "INSERT INTO productos (cod_producto, nombre, descripcion, precio, costo, stock, cod_categoria)";
    $sql .= "VALUES ('$cod_producto', '$nombre', '$descripcion', $precio, $costo, $stock, '$cod_categoria')";
    //echo $sql;
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        //*codigo para imagen
        if ($_FILES["foto"]["name"] != "") { //verificar si hay imagen  seleccionada

            //extraer ultimo id inserccion
            $sql = "SELECT max(cod_producto) as cod_producto FROM productos";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $ultimoId =  $row2["cod_producto"];

            $foto = $_FILES["foto"]["name"];
            $ruta = $_FILES["foto"]["tmp_name"];
            $imagen = "modulos/producto/fotos/" . rand(100, 999) . $foto;
            copy($ruta, $imagen);

            //obtener el ultimo id para guardar la imagen
            $sql = "update productos set foto ='$imagen'where cod_producto='$ultimoId'";

            $execute = mysqli_query($con, $sql);
            if (!$execute) {
                echo "Error al guardar la foto " . mysqli_error($con);
            }
        }

        $_SESSION["success"] = "Guardado";
        //retroceso es para impedir cuando damos atras vuelva y guarde
        $_SESSION["retroceso"] = "modulos/producto/add.php";

        ?>
        <script>
            location.href = 'index.php?directorio=producto&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "guardar";
            ?>
        <script>
            location.href = 'index.php?directorio=producto&pagina=index.php';
        </script>
    <?php
        }
    }
    if (isset($_POST["actualizar"])) {
        $cod_producto = $_POST["cod_producto"];

        $nombre = $_POST["nombre"];
        $descripcion = $_POST["descripcion"];
        $precio = $_POST["precio"];
        $costo = $_POST["costo"];
        $stock = $_POST["stock"];
        $cod_categoria = $_POST["cod_categoria"];


        $sql = "UPDATE facturingpos.productos SET cod_producto = '$cod_producto',cod_categoria='$cod_categoria', nombre = '$nombre', descripcion = '$descripcion',";
        $sql .=  "precio =$precio, costo =$costo, stock =$stock WHERE cod_producto = '$cod_producto'";
        $execute = mysqli_query($con, $sql);
        //echo $sql . "<br>";
        if ($execute) {

            //*codigo para imagen
            if ($_FILES["foto"]["name"] != "") { //verificar si hay imagen  seleccionada

                //buscar foto existente para eliminar
                $sql = "select foto  from productos where cod_producto='$cod_producto'";
                $execute = mysqli_query($con, $sql);
                $row = mysqli_fetch_array($execute);
                $imagen = $row["foto"];

                //eliminar imagen si existe
                if ($imagen != "")
                    unlink($imagen);
                /************* */

                $foto = $_FILES["foto"]["name"];
                $ruta = $_FILES["foto"]["tmp_name"];
                $imagen = "modulos/producto/fotos/" . rand(100, 999) . $foto;
                copy($ruta, $imagen);

                //Actualizar img
                $sql = "update productos set foto ='$imagen'where cod_producto='$cod_producto'";

                $execute = mysqli_query($con, $sql);
                if (!$execute) {
                    echo "Error al guardar la foto " . mysqli_error($con);
                }
            }
            $_SESSION["success"] = "Actualizado";
            //retroceso es para impedir cuando damos atras vuelva y guarde
            ?>
        <script>
            location.href = 'index.php?directorio=producto&pagina=edit.php&id=<?php echo $cod_producto ?>';
        </script>
    <?php
        } else {
            echo "error: " . mysqli_error($con);
        }
    }
    //eliminar
    if (isset($_GET["eliminar"])) {
        $id = $_GET["id"];

        //borrar registro
        $sql = "delete  from productos where cod_producto='$id'";

        $execute = mysqli_query($con, $sql);
        if ($execute) {



            $_SESSION["success"] = "Borrado";
            // header("location: url=index.php?directorio=usuario&pagina=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=producto&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "borrar";
            echo "<div class='alert alert-danger'>Error al borrar el registro" . mysqli_error($con) . "</div>";
            //header("location: url=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=producto&pagina=index.php';
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
    $sql = "select * from productos";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {


            echo "<tr><td>" . $row["cod_producto"] . "</td>";
            echo "<td>" . $row["nombre"] . "</td>";
            echo "<td>" . $row["precio"] . "</td>";
            echo "<td>" . $row["costo"] . "</td>";
            if ($row["stock"] < 3) {
                echo "<td class='bg-danger '>" . $row["stock"] . "</td>";
            } elseif ($row["stock"] < 10) {
                echo "<td class='bg-warning '>" . $row["stock"] . "</td>";
            } else {
                echo "<td class='bg-success '>" . $row["stock"] . "</td>";
            }
            $id = $row["cod_producto"];
            if ($row["foto"] != "") {
                echo '<td> <img class="img-fluid"  src="' . $row["foto"] . '" style="width: 40px; height:40px;" alt=""></td>';
            } else {
                echo '<td> <img class="img-fluid"  src="img/anonymous.png " style="width: 40px; height:40px;" alt=""></td>';
            }
            echo "<td><a  class='btn btn-primary mr-1 ' href='index.php?directorio=producto&pagina=edit.php&id=$id'><i class='fas fa-pencil-alt'></i></a>";
            echo "<button  class='btn btn-danger eliminar' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button></td></tr>";
        }
    }
}
desconectar($con);
