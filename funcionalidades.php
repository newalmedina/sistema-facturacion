<?php
require_once "modulos/conexion/conexion.php";
$con = conectar();

function mensaje()
{

    global $con;
    /*Mensajes de actualizacion*/
    if (isset($_SESSION["success"])) {

        ?>
        <script>
            var mensaje = "<?= $_SESSION['success'] ?>";
            swal({
                title: mensaje,
                text: "Operacion realizada Correctamente",
                icon: "success",
            });
        </script>
    <?php
            // echo "<br><div class='alert alert-success'>Registro  " . $_SESSION["success"] . " Correctamente</div>";
            unset($_SESSION["success"]);
        } elseif (isset($_SESSION["error"])) {
            ?>
        <script>
            var mensaje = "<?= $_SESSION['error'] ?>";
            swal({
                title: mensaje,
                text: "Operacion fallida",
                icon: "error",
            });
        </script>
    <?php
            echo "<div class='alert alert-danger'>Error al " . $_SESSION["error"] . " el registro" . mysqli_error($con) . "</div>";

            unset($_SESSION["error"]);
        } elseif (isset($_SESSION["existe"])) {
            ?>
        <script>
            var mensaje = "<?= $_SESSION['existe'] ?>";
            swal({
                title: mensaje + " existe",
                text: "Este registro ya existe en la base de datos",
                icon: "error",
            });
        </script>
<?php

        unset($_SESSION["existe"]);
    }
}

if (isset($_POST["validar"])) {
    $validar = $_POST["validar"];
    $campo = $_POST["campo"];
    $tabla = $_POST["tabla"];

    function validar()
    {
        global $con;
        global $validar;
        global $campo;
        global $tabla;
        $sql = "select * from $tabla where $validar='$campo' ";

        $execute = mysqli_query($con, $sql);

        if ($execute) {

            if (mysqli_affected_rows($con)  > 0) {
                echo "<li class='text-danger'>$validar existe en la base de datos ($campo)</li>";
            }
        }
    }

    if ($validar == "correo") {
        if (filter_var($campo, FILTER_VALIDATE_EMAIL)) {
            validar();
        }
    } else {
        validar();
    }
}


desconectar($con);
