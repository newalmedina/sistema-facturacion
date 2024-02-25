<?php

if (isset($_SESSION["retroceso"])) {
  ?>
  <script>
    location.href = 'index.php?directorio=categoria&pagina=add.php';
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
        <h1 class="m-0 text-dark">Administracion de Categorias</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="index.php?directorio=categoria&pagina=index.php">Administracion de Categorias</a></li>
          <li class="breadcrumb-item active">Nueva Categoria</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>

<div class="card">
  <div class="card-header">
    <h3 class="card-title">NUEVA CATEGORIA</h3>
    <a href="index.php?directorio=categoria&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

  </div>
  <!-- /.card-header -->
  <form role="form" action="index.php?directorio=categoria&pagina=crud.php" method="POST" enctype="multipart/form-data">
    <div class="card-body">
      <div class="form-group">
        <div class="row">
          <div class="col-md-6">
            <label for="nombre">Nombre</label>
            <input type="text" required class="form-control" id="nombre" name="nombre" maxlength="30">
          </div>
        </div>
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