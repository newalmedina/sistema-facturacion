<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();
$id = $_GET["id"];
$sql = "SELECT cli.*, ide.descripcion as nombreIdentificacion FROM clientes as cli inner join identificacion as ide  on cli.cod_identificacion=ide.cod_identificacion  where cod_cliente ='$id'";


$execute = mysqli_query($con, $sql);
if ($execute) {
    $row = mysqli_fetch_array($execute);
} else {
    echo "error " . mysqli_error($con);
}



?>

<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Administracion de Clientes</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="index.php?directorio=cliente&pagina=index.php">Administracion de Clientes</a></li>
                    <li class="breadcrumb-item active">Editar Cliente</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> EDITAR Cliente: <i><b class="text-success"><?php echo $row["cod_cliente"] ?></b></i></h3>
        <a href="index.php?directorio=cliente&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

    </div>
    <!-- /.card-header -->
    <form role="form" action="index.php?directorio=cliente&pagina=crud.php" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="cod_cliente" value="<?php echo $row["cod_cliente"]  ?>">
                        <label for="nombre">Nombre</label>
                        <input type="text" value="<?php echo $row["nombre"]  ?>" required class="form-control" id="nombre" name="nombre" maxlength="30">
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos">Apellidos</label>
                        <input type="text" value="<?php echo $row["apellidos"]  ?>" required class="form-control" id="apellidos" name="apellidos" maxlength="50">
                    </div>
                    <div class="col-sm-6 col-md-2">


                        <label>Tipo Ident.</label>
                        <select required name="tipoIdentificacion" class="custom-select">
                            <option value='<?php echo $row["cod_identificacion"]  ?>'><?php echo $row["nombreIdentificacion"]  ?></option>
                            <?php

                            $sqlidentificacion = "select * from identificacion ";
                            $executeidentificacion = mysqli_query($con, $sqlidentificacion);

                            if ($executeidentificacion) {
                                while ($rowidentificacion = mysqli_fetch_array($executeidentificacion)) {
                                    echo "<option value='" . $rowidentificacion["cod_identificacion"] . "'>" . $rowidentificacion["descripcion"] . "</option>";
                                }
                            }


                            ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="identificacion">Num. Ident.</label>
                        <input type="text" value="<?php echo $row["identificacion"]  ?>" required class="form-control" id="identificacion" name="identificacion" maxlength="9">
                        <ul id="mensajeError2">
                            <!--Mensaje si el correo existe -->
                        </ul>
                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="fechanacimiento">Fecha Nac.</label>
                        <input type="date" value="<?php echo $row["fecha_nacimiento"]  ?>" required class="form-control" id="fechanacimiento" name="fechanacimiento">
                    </div>
                    <div class="col-md-6">
                        <label for="correo">Correo</label>
                        <input type="email" value="<?php echo $row["correo"]  ?>" required class="form-control" id="correo" name="correo" maxlength="50   ">
                        <ul id="mensajeError">
                            <!--Mensaje si el correo existe -->
                        </ul>

                    </div>
                    <div class="col-sm-6 col-md-2">

                        <label>Sexo</label><br>
                        <?php

                        if ($row["sexo"] == "M") {
                            echo "<input class='' type='radio' id='sexo' value='M' checked name='sexo'> Masculino";
                            echo "<input class='ml-2' type='radio' id='sexo' value='F' name='sexo'> Femenino";
                        } else {
                            echo "<input class='' type='radio' id='sexo' value='M'  name='sexo'> Masculino";
                            echo "<input class='ml-2' type='radio' id='sexo' value='F'checked name='sexo'> Femenino";
                        }
                        ?>

                    </div>

                    <div class="col-sm-6 col-md-2">
                        <label for="fechanacimiento">Telefono</label>
                        <input type="tel" value="<?php echo $row["telefono"]  ?>" required class="form-control" id="telefono" name="telefono" onKeyPress="return soloNumeros(event)" maxlength="9" onKeyPress="return soloNumeros(event)">
                    </div>
                    <div class="col-sm-12 col-md-8">
                        <label for="direccion">Direccion</label>
                        <textarea name="direccion" class="form-control" id="direccion" maxlength="100"><?php echo $row["direccion"]  ?></textarea>
                    </div>
                </div>
            </div>

            <div class="form-group">

            </div>

        </div>

</div>
<!-- /.card-body -->

<div class="card-footer text-center">
    <button type="submit" name="actualizar" class="btn btn-primary">Actualizar</button>
</div>
</form>
<!-- /.card-body -->
</div>
<!-- /.card -->
<script>
    $(document).ready(function() {


        var identidad = '<?php echo $row["identificacion"] ?>';
        var campoIdentidad = $("#identificacion");

        //validando identidad
        campoIdentidad.on('change', function() {
            if (campoIdentidad.val() != identidad) {
                Validar('#identificacion', '#mensajeError2', 'identificacion', 'usuarios');

            }
        });
    });
</script>