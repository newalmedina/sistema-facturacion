<?php

if (isset($_SESSION["retroceso"])) {
  ?>
  <script>
    location.href = 'index.php?directorio=usuario&pagina=add.php';
  </script>
<?php
  unset($_SESSION["retroceso"]);
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
          <li class="breadcrumb-item active">Nuevo Usuario</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">NUEVO USUARIO</h3>
    <a href="index.php?directorio=usuario&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

  </div>
  <!-- /.card-header -->
  <form role="form" action="index.php?directorio=usuario&pagina=crud.php" method="POST" enctype="multipart/form-data">
    <div class="card-body">
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="nombre">Nombre</label>
            <input type="text" required class="form-control" id="nombre" name="nombre" maxlength="30">
          </div>
          <div class="col-md-6">
            <label for="apellidos">Apellidos</label>
            <input type="text" required class="form-control" id="apellidos" name="apellidos" maxlength="50">
          </div>
          <div class="col-sm-6 col-md-2">


            <label>Tipo Ident.</label>
            <select required name="tipoIdentificacion" class="custom-select">
              <option value=''>Seleccione</option>
              <?php
              require_once "modulos/conexion/conexion.php";

              $con = conectar();
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
            <input type="text" onchange="Validar('#identificacion','#mensajeError2','identificacion','usuarios')" required class="form-control" id="identificacion" name="identificacion" maxlength="9">
            <ul id="mensajeError2">
              <!--Mensaje si el correo existe -->
            </ul>
          </div>
          <div class="col-sm-6 col-md-2">
            <label for="fechanacimiento">Fecha Nac.</label>
            <input type="date" required class="form-control" id="fechanacimiento" name="fechanacimiento">
          </div>
          <div class="col-md-6">
            <label for="correo">Correo</label>
            <input type="email" required class="form-control" onchange="Validar('#correo','#mensajeError','correo','usuarios')" id="correo" name="correo" maxlength="50   ">
            <ul id="mensajeError">
              <!--Mensaje si el correo existe -->
            </ul>

          </div>
          <div class="col-sm-6 col-md-2">
            <label>Privilegios</label>
            <select name="privilegios" required class="custom-select">
              <option value="">Seleccione</option>
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
            <input class="" type="radio" id="sexo" value="M" checked name="sexo"> Hombre
            <input class="ml-2" type="radio" id="sexo" value="F" name="sexo"> Mujer
          </div>
          <div class="col-sm-6 col-md-2">
            <label>Estatus</label><br>
            <input class="text-success" type="radio" id="estatus" checked value="1" name="estatus"> Activo
            <input class="ml-2" type="radio" id="estatus" value="0" name="estatus"> Desactivado
          </div>
          <div class="col-sm-6 col-md-2">
            <label for="fechanacimiento">Telefono</label>
            <input type="tel" required class="form-control" id="telefono" name="telefono" onKeyPress="return soloNumeros(event)" maxlength="9" onKeyPress="return soloNumeros(event)">
          </div>
          <div class="col-sm-6 col-md-4">
            <label for="fechanacimiento">Password</label>
            <input type="password" required class="form-control" id="telefono" name="password" maxlength="30">
          </div>

          <div class="col-md-12">
            <label for="direccion">Direccion</label>
            <textarea name="direccion" class="form-control" id="direccion" maxlength="100"></textarea>
          </div>
          <div class="col-sm-6 col-md-12">
            <label for="foto">Foto</label><br>
            <input type="file" class="" id="foto" name="foto" accept="image/*" onchange=" validarImagen(this)">
          </div>
          <div class="col-md-3"><br>
            <img class="img-fluid" id="mostrarFoto" src="" style="width: 200px; height:200px;" alt="">
          </div>
        </div>
      </div>

      <div class="form-group">

      </div>

    </div>

</div>
<!-- /.card-body -->

<div class="card-footer text-center">
  <button type="submit" name="guardar" class="btn btn-primary">Guardar</button>
</div>
</form>
<!-- /.card-body -->
</div>
<!-- /.card -->
<script>
  alerta();
</script>