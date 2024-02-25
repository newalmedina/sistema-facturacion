<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();
$id = $_GET["id"];
$sql = "SELECT usu.*, ide.descripcion as nombreIdentificacion, pri.descripcion as nombrePrivilegios FROM usuarios as usu inner join identificacion as ide  on usu.cod_identificacion=ide.cod_identificacion inner join privilegios as pri  on usu.cod_privilegio=pri.cod_privilegio where cod_usuario ='$id'";


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
                <h1 class="m-0 text-dark">Administracion de Usuarios</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="index.php?directorio=usuario&pagina=index.php">Administracion de Usarios</a></li>
                    <li class="breadcrumb-item active">Editar Usuario</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> EDITAR USUARIO: <i><b class="text-success"><?php echo $row["cod_usuario"] ?></b></i></h3>
        <a href="index.php?directorio=usuario&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

    </div>
    <!-- /.card-header -->
    <form role="form" action="index.php?directorio=usuario&pagina=crud.php" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="cod_usuario" value="<?php echo $row["cod_usuario"]  ?>">
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
                        <label>Privilegios</label>
                        <select name="privilegios" required class="custom-select">
                            <option value="<?php echo $row["cod_privilegio"]  ?>"><?php echo $row["nombrePrivilegios"]  ?></option>
                            <?php
                            $sqlprivilegios = "select * from privilegios ";
                            $executeprivilegios = mysqli_query($con, $sqlprivilegios);

                            if ($executeprivilegios) {
                                while ($rowprivilegios = mysqli_fetch_array($executeprivilegios)) {
                                    echo "<option value='" . $rowprivilegios["cod_privilegio"] . "'>" . $rowprivilegios["descripcion"] . "</option>";
                                }
                            }


                            ?>
                        </select>
                    </div>
                    <div class="col-sm-6 col-md-2">

                        <label>Sexo</label><br>
                        <?php

                        if ($row["sexo"] == "M") {
                            echo "<input class='' type='radio' id='sexo' value='M' checked name='sexo'> Hombre";
                            echo "<input class='ml-2' type='radio' id='sexo' value='F' name='sexo'> mujer";
                        } else {
                            echo "<input class='' type='radio' id='sexo' value='M'  name='sexo'> Hombre";
                            echo "<input class='ml-2' type='radio' id='sexo' value='F'checked name='sexo'> mujer";
                        }
                        ?>

                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label>Estatus</label><br>
                        <?php
                        if ($row["estatus"]) {
                            echo '<input class="text-success" type="radio" id="estatus" checked value="1" name="estatus"> Activo';
                            echo '<input class="ml-2" type="radio" id="estatus" value="0" name="estatus"> Desactivado';
                        } else {
                            echo '<input class="text-success" type="radio" id="estatus"  value="1" name="estatus"> Activo';
                            echo '<input class="ml-2" type="radio" id="estatus" checked value="0" name="estatus"> Desactivado';
                        }
                        ?>

                    </div>
                    <div class="col-sm-6 col-md-2">
                        <label for="fechanacimiento">Telefono</label>
                        <input type="tel" value="<?php echo $row["telefono"]  ?>" required class="form-control" id="telefono" name="telefono" onKeyPress="return soloNumeros(event)" maxlength="9" onKeyPress="return soloNumeros(event)">
                    </div>
                    <div class="col-sm-6 col-md-4">
                        <label for="password">Password</label>
                        <input type="password" placeholder="Solo rellenar si se quiere modificar" class="form-control" id="password" name="password" maxlength="30">
                    </div>

                    <div class="col-md-12">
                        <label for="direccion">Direccion</label>
                        <textarea name="direccion" class="form-control" id="direccion" maxlength="100"><?php echo $row["direccion"]  ?></textarea>
                    </div>
                    <div class="col-sm-6 col-md-12">
                        <label for="foto">Foto</label><br>
                        <input type="file" class="" id="foto" name="foto" accept="image/*" onchange=" validarImagen(this)">

                    </div>
                    <div class="col-md-3"><br>
                        <?php
                        if ($row["foto"] != "") {
                            echo ' <img class="img-fluid" id="mostrarFoto" src="' . $row["foto"] . '" style="width: 200px; height:200px;" alt="">';
                            echo ' <input type="button" class="btn btn-xs btn-secondary" onclick="restauraFoto()" value="restaurar Foto">';
                        } else {
                            echo ' <img class="img-fluid" id="mostrarFoto" src="img/noimage.jpg " style="width: 200px; height:200px;" alt="">';
                        }
                        ?>

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
    function restauraFoto() {
        var foto = '<?php echo $row["foto"] ?>';
        document.getElementById("mostrarFoto").src = foto;
        document.getElementById("foto").value = "";

    }
    $(document).ready(function() {


        var identidad = '<?php echo $row["identificacion"] ?>';
        var campoIdentidad = $("#identificacion");

        var correo = '<?php echo $row["correo"] ?>';
        var campoCorreo = $("#correo");

        var password = '<?php echo $row["pass"] ?>';
        var campoPass = $("#password");

        var foto = '<?php echo $row["foto"] ?>';

        //validando identidad
        campoIdentidad.on('change', function() {
            if (campoIdentidad.val() != identidad) {
                Validar('#identificacion', '#mensajeError2', 'identificacion', 'usuarios');

            }
        });


        //validando correo
        campoCorreo.on('change', function() {
            if (campoCorreo.val() != correo) {
                Validar('#correo', '#mensajeError', 'correo', 'usuarios');
            }
        });

        //restaurar imagen


    });
</script>