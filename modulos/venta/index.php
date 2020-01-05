<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">Administracion de ventas</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                    <li class="breadcrumb-item active">Administracion de Ventas</li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">LISTADO DE VENTAS</h3>
        <a href="index.php?directorio=venta&pagina=add.php" class="btn btn-sm btn-info" style="position:absolute; right: 20px;">Nueva Venta</a><br>
    </div>
    <!-- /.card-header -->
    <div class="card-body table-responsive">
        <table id="dtventa" class="table  table-bordered table-striped">
            <thead>
                <tr>

                    <th width="70">Factura </th>
                    <th>Cliente</th>
                    <th>Vendedor</th>
                    <th width="90">Forma Pago</th>
                    <th width="25">Total</th>
                    <th width="180">Fecha</th>
                    <th width="60">ACCIONES</th>
                </tr>
            </thead>
            <tbody>
                <?php
                //listar
                $listar = "listar";
                require_once "modulos/venta/crud.php";


                ?>

            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<!-- /.card -->

<script>
    $(document).ready(function() {
        $('#dtventa').DataTable({
            "language": lenguaEspañola(),
            "order": [
                [0, 'desc']
            ]
        });
    });

    function Eliminar(id) {

        swal({
            title: "Estas seguro de eliminar este registro?",
            text: "Una vez lo hagas no podras recuperarlo",
            icon: "warning",
            buttons: [
                'No',
                'Si'
            ],
            dangerMode: true,
        }).then(function(isConfirm) {
            if (isConfirm) {

                location.href = 'index.php?directorio=venta&pagina=crud.php&eliminar=eliminar&id=' + id;

            }
        })


    }

    function imprimir(id) {
        window.open("modulos/venta/factura/index.php?id=" + id + "", "_blank");

    }
</script>