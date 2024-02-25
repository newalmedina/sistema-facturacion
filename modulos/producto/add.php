<?php

if (isset($_SESSION["retroceso"])) {
  ?>
  <script>
    location.href = 'index.php?directorio=producto&pagina=add.php';
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
        <h1 class="m-0 text-dark">Administracion de Productos</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="index.php?directorio=producto&pagina=index.php">Administracion de Productos</a></li>
          <li class="breadcrumb-item active">Nuevo Producto</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">NUEVO PRODUCTO</h3>
    <a href="index.php?directorio=producto&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

  </div>
  <!-- /.card-header -->
  <form role="form" action="index.php?directorio=producto&pagina=crud.php" method="POST" enctype="multipart/form-data">
    <div class="card-body">
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="nombre">Nombre</label>
            <input type="text" required class="form-control" id="nombre" name="nombre" maxlength="30">
          </div>
          <div class="col-md-3">
            <label for="precio">Precio</label>
            <input type="number" required class="form-control" id="precio" name="precio" maxlength="11">
          </div>
          <div class="col-md-3">
            <label for="costo">Costo</label>
            <input type="number" required class="form-control" id="costo" name="costo" maxlength="11">
          </div>
          <div class="col-md-3">
            <label for="stock">Stock</label>
            <input type="number" required class="form-control" id="stock" name="stock" maxlength="11">
          </div>
          <div class="col-sm-6">
            <label>Categoria</label>
            <select id="categoria" required name="cod_categoria" class="custom-select">
              <option value=''>Seleccione</option>
              <?php
              require_once "modulos/conexion/conexion.php";

              $con = conectar();
              $sqlcategoria = "select * from categorias ";
              $executecategoria = mysqli_query($con, $sqlcategoria);

              if ($executecategoria) {
                while ($rowcategoria = mysqli_fetch_array($executecategoria)) {
                  echo "<option value='" . $rowcategoria["cod_categoria"] . "'>" . $rowcategoria["nombre"] . "</option>";
                }
              }
              ?>
            </select>
          </div>

          <div class="col-md-12">
            <label for="descripcion">Descripcion</label>
            <textarea name="descripcion" class="form-control" id="descripcion" maxlength="100"></textarea>
          </div>
          <div class="col-sm-6 col-md-12">
            <label for="foto">Foto</label><br>
            <input type="file" class="" id="foto" name="foto" accept="image/*" onchange=" validarImagen(this)">
          </div>
          <div class="col-md-3"><br>
            <img class="img-fluid" id="mostrarFoto" src="" style="width: 200px; height:200px; display:none;" alt="">
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
  $('#categoria').select2();
</script>