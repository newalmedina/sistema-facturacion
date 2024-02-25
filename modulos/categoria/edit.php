<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();
$id = $_GET["id"];
$sql = "SELECT * FROM categorias   where cod_categoria ='$id'";


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
                    <li class="breadcrumb-item"><a href="index.php?directorio=categoria&pagina=index.php">Administracion de Usarios</a></li>
                    <li class="breadcrumb-item active">Editar Usuario</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> EDITAR CATEGORIA: <i><b class="text-success"><?php echo $row["cod_categoria"] ?></b></i></h3>
        <a href="index.php?directorio=categoria&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

    </div>
    <!-- /.card-header -->
    <form role="form" action="index.php?directorio=categoria&pagina=crud.php" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <input type="text" name="cod_categoria" value="<?php echo $row["cod_categoria"]  ?>">
                        <label for="nombre">Nombre</label>
                        <input type="text" value="<?php echo $row["nombre"]  ?>" required class="form-control" id="nombre" name="nombre" maxlength="30">
                    </div>

                </div>
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