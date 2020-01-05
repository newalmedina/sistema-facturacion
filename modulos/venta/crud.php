<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();

//guardar
if (isset($_POST["vender"])) {
    $cod_usuario = $_SESSION["usuario"]["cod_usuario"];
    $cliente = $_POST["cliente"];
    $totalSinIva = $_POST["totalSinIva"];
    $iva = $_POST["iva"];
    $totalConIva = $_POST["totalConIva"];
    $metodoPago = $_POST["metodoPago"];
    $codigoTransaccion = $_POST["codigoTransaccion"];


    //$codVenta (ultimaVenta)
    $codigoProducto = $_POST["codigoProducto"];
    $precioProducto = $_POST["precioProducto"];
    $cantProducto = $_POST["cantProducto"];

    /*echo " cod_usuario: " . $cod_usuario . "<br>";
    echo "cliente: " . $cliente . "<br>";
    echo "total sin iva: " . $totalSinIva . "<br>";
    echo "iva: " . $iva . "<br>";
    echo "total con iva: " . $totalConIva . "<br>";
    echo "metodo pago: " . $metodoPago . "<br>";
    echo "cod_trasaccion: " . $codigoTransaccion . "<br><br>";*/

    //verificar si existen ventas
    //verificar si existe o no usuarios para apartir de hay crear el ID
    $sql = "select * from ventas";

    $execute = mysqli_query($con, $sql);

    if ($execute) {

        if (mysqli_affected_rows($con)  > 0) {
            $sql = "SELECT max(substring(cod_venta,7))+1 as serie FROM ventas";
            $execute = mysqli_query($con, $sql);
            $row2 = mysqli_fetch_array($execute);

            $cod_venta = "VEN-" . date("y") . $row2["serie"];
        } else {
            $cod_venta = "VEN-" . date("y") . "100";
        }
    }


    $sql = "INSERT INTO ventas (cod_venta,cod_modo_pago,total_sinIva,iva,total_conIva,cod_usuario,cod_cliente,codigo_transaccion)";
    $sql .= " VALUES ('$cod_venta', $metodoPago, $totalSinIva, $iva, $totalConIva, '$cod_usuario', '$cliente', '$codigoTransaccion')";
    $execute = mysqli_query($con, $sql);
    if ($execute) {

        //agregando ventas detalles
        for ($i = 0; $i < count($codigoProducto); $i++) {
            /* echo "cod_prod: " . $codigoProducto[$i] . "<br>";
        echo "prec_prod: " . $precioProducto[$i] . "<br>";
        echo "cantidad_prod; " . $cantProducto[$i] . "<br>";

        $subtotalProducto = $precioProducto[$i] * $cantProducto[$i];

        echo "subtotal_prod: " . number_format($subtotalProducto, 2, '.', '') . "<br><hr>";*/
            $subtotalProducto = $precioProducto[$i] * $cantProducto[$i];

            $sql2 = "INSERT INTO venta_detalle (cod_venta, cod_producto, precio, cantidad, sub_total)";
            $sql2 .= " VALUES ('$cod_venta', '$codigoProducto[$i]', $precioProducto[$i], $cantProducto[$i],$subtotalProducto )";
            $execute2 = mysqli_query($con, $sql2);
            if ($execute2) {
                //restar lo vendido al stock
                $sql3 = "update productos set stock = stock- $cantProducto[$i] where cod_producto ='$codigoProducto[$i]'";
                $execute3 = mysqli_query($con, $sql3);
                if (!$execute3) {
                    echo "Eror al descontar articulos en stock";
                } else {
                    $_SESSION["success"] = "Vendido";
                    //retroceso es para impedir cuando damos atras vuelva y guarde
                    $_SESSION["retroceso"] = "modulos/venta/index.php";

                    ?>
                    <script>
                        window.open("modulos/venta/factura/index.php?id=<?php echo $cod_venta ?>", "_blank");
                        //window.open('modulos/venta/factura/index.php', '_blank');
                        location.href = 'index.php?directorio=venta&pagina=index.php';
                    </script>
        <?php
                        }
                    } else {
                        echo $cod_venta . ' ';
                        echo "Error al ingresar venta detalles " . mysqli_error($con);
                    }
                }
            } else {
                echo "Error al ingresar venta cabecera" . mysqli_error($con);
            }
        }
        if (isset($_GET["eliminar"])) {
            $id = $_GET["id"];

            $sql = "delete  from venta_detalle where cod_venta='$id'";

            $execute = mysqli_query($con, $sql);


            if ($execute) {
                $sql = "delete  from ventas where cod_venta='$id'";

                $execute = mysqli_query($con, $sql);
                $_SESSION["success"] = "Borrado";
                // header("location: url=index.php?directorio=usuario&pagina=index.php");
                ?>
        <script>
            location.href = 'index.php?directorio=venta&pagina=index.php';
        </script>
    <?php
        } else {
            $_SESSION["error"] = "borrar";
            echo "<div class='alert alert-danger'>Error al borrar el registro" . mysqli_error($con) . "</div>";
            //header("location: url=index.php");
            ?>
        <script>
            location.href = 'index.php?directorio=venta&pagina=index.php';
        </script>
<?php
    }
}
if (isset($_GET["listar"])) {
    listar();
} else {
    listar();
}

function listar()
{
    global $con;
    $sql = "select ven.*,usu.nombre as nombreUsuario, usu.apellidos as apellidoUsuario  , cli.nombre as nombreCliente, cli.apellidos as apellidoCliente, modo.descripcion as modoPago from ventas as ven inner join clientes as cli on ven.cod_cliente = cli.cod_cliente inner join usuarios as usu on ven.cod_usuario = usu.cod_usuario inner join modo_pago as modo on ven.cod_modo_pago = modo.cod_modo_pago ";
    $execute = mysqli_query($con, $sql);

    if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {
            $id = $row["cod_venta"];
            $nombreCliente = $row["nombreCliente"] . " " . $row["apellidoCliente"];
            $nombreVendedor = $row["nombreUsuario"] . " " . $row["apellidoUsuario"];
            $modoPago = $row["modoPago"];
            $total = number_format($row["total_conIva"], 2, '.', ',');
            $fecha = $row["fecha"];
            echo "<tr><td>$id</td><td>$nombreCliente</td><td>$nombreVendedor</td><td>$modoPago</td>";
            echo "<td>$total</td><td>$fecha</td>";
            echo "<td><button  class='btn btn-success  ' onclick='imprimir(\"$id\")'><i class='fas fa-print'></i></button>"
                . " <button  class='btn btn-danger eliminar ' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button></td></tr>";
            // echo "<td><button class='btn btn-danger ' onclick='Eliminar(\"$id\")'><i class='fas fa-times'></i></button><td> </tr>";
        }
    } else {
        echo mysqli_error($con);
    }
}
desconectar($con);

/*

$cod_usuario;
$cod_cliente;
$totalSinIva;
$iva;
$totalConIva;
$metodoPago;
$codigoTransaccion;


//$codVenta (ultimaVenta)
$codigoProducto[];
$precioProducto[];
$cantidadProducto[];
$subtotalProducto[];
*/
