<?php
require_once "modulos/conexion/conexion.php";

$con = conectar();
$id = $_GET["id"];
$sql = "SELECT prod.*, cat.nombre as nombreCategoria FROM productos as prod inner join categorias as cat  on prod.cod_categoria=cat.cod_categoria where cod_producto ='$id'";


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
                <h1 class="m-0 text-dark">Administracion de Productos</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="index.php?directorio=producto&pagina=index.php">Administracion de Productos</a></li>
                    <li class="breadcrumb-item active">Editar Productos</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"> EDITAR PRODUCTO: <i><b class="text-success"><?php echo $row["cod_producto"] ?></b></i></h3>
        <a href="index.php?directorio=producto&pagina=index.php" class="btn btn-sm btn-warning" style="position:absolute; right: 20px;">Regresar</a><br>

    </div>
    <!-- /.card-header -->
    <form role="form" action="index.php?directorio=producto&pagina=crud.php" method="POST" enctype="multipart/form-data">
        <div class="card-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <input type="hidden" name="cod_producto" value="<?php echo $row["cod_producto"]  ?>">
                        <label for="nombre">Nombre</label>
                        <input type="text" value="<?php echo $row["nombre"]  ?>" required class="form-control" id="nombre" name="nombre" maxlength="30">
                    </div>
                    <div class="col-md-3">
                        <label for="precio">Precio</label>
                        <input type="number" value="<?php echo $row["precio"]  ?>" required class="form-control" id="precio" name="precio" maxlength="11">
                    </div>
                    <div class="col-md-3">
                        <label for="costo">Costo</label>
                        <input type="number" value="<?php echo $row["costo"]  ?>" required class="form-control" id="costo" name="costo" maxlength="11">
                    </div>
                    <div class="col-md-3">
                        <label for="stock">Stock</label>
                        <input type="number" value="<?php echo $row["stock"]  ?>" required class="form-control" id="stock" name="stock" maxlength="11">
                    </div>
                    <div class="col-sm-6">
                        <label>Categoria</label>
                        <select id="categoria" required name="cod_categoria" class="custom-select">
                            <option value='<?php echo $row["cod_categoria"]  ?>'><?php echo $row["nombreCategoria"]  ?></option>
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
                        <textarea name="descripcion" class="form-control" id="descripcion" maxlength="100"><?php echo $row["descripcion"]  ?></textarea>
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
                            echo ' <img class="img-fluid" id="mostrarFoto" src="img/anonymous.png " style="width: 200px; height:200px;" alt="">';
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
    $('#categoria').select2();
</script>