<?php
$con = conectar();

//valores de las targetas ventas, productos, categorias, clientes
$sql = "SELECT count(cod_producto) as cantidad FROM facturingpos.productos";
$execute = mysqli_query($con, $sql);
$productos = mysqli_fetch_array($execute);

$sql = "SELECT count(cod_categoria) as cantidad FROM facturingpos.categorias";
$execute = mysqli_query($con, $sql);
$categorias = mysqli_fetch_array($execute);

$sql = "SELECT count(cod_cliente) as cantidad FROM facturingpos.clientes";
$execute = mysqli_query($con, $sql);
$clientes = mysqli_fetch_array($execute);

$sql = "SELECT sum(total_conIva) as total FROM facturingpos.ventas";
$execute = mysqli_query($con, $sql);
$ventas = mysqli_fetch_array($execute);

?>


<section class="content">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">Reporte de ventas</h1>
                </div><!-- /.col -->
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
                        <li class="breadcrumb-item active">Reportes de Ventas </li>
                    </ol>
                </div><!-- /.col -->
            </div><!-- /.row -->
        </div><!-- /.container-fluid -->
    </div>
    <div class="row">



        <div id="" class=" col-md-12 card card-info bg-info pt-1">

            <h4 class="text-body text-center"> Ventas Totales: <i class="text-danger">$<?php echo number_format($ventas["total"], 2, '.', ','); ?></i></h4>

            <div id="myfirstchart" class="text-center" style="height: 250px; width:100%;"></div>

        </div>
        <div class=" col-md-6 ">
            <!-- Input addon -->
            <div class="card card-info  border-top border-secondary ">
                <div class="card-header">
                    <h3 class="card-title">Productos mas vendidos</h3>
                </div>
                <div class="card-body">
                    <div>
                        <canvas id="donutChart" style="height:200px; min-height:230px"></canvas>
                    </div>

                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <?php

        ?>
        <!--PRODUCTOS RECIENTES-->
        <div class=" col-md-6 ">
            <!-- Input addon -->
            <div class=" card card-info border-top border-success">
                <div class="card-header">
                    <h3 class="card-title">Vendedores</h3>
                </div>
                <div class="card-body">
                    <div id="bar-chart1" class="text-center" style="height:200px; min-height:230px"></div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>

        <div class=" col-md-12 ">
            <!-- Input addon -->
            <div class=" card card-info border-top border-primary">
                <div class="card-header">
                    <h3 class="card-title">Clientes</h3>
                </div>
                <div class="card-body">
                    <div id="bar-chart2" class="text-center" style="height:200px; min-height:230px"></div>
                </div>
                <!-- /.card-body -->
            </div>
        </div>
</section>
<script>
    //BAR CHART vendedores

    <?php
    $sql = "SELECT usu.nombre as nombre, usu.apellidos as apellidos,  sum(ven.total_conIva) as total from facturingpos.ventas as ven INNER JOIN usuarios as usu on ven.cod_usuario=usu.cod_usuario group by ven.cod_usuario order by total desc";

    $execute = mysqli_query($con, $sql);

    ?>
    var bar = new Morris.Bar({
        element: 'bar-chart1',
        resize: true,
        data: [

            <?php
            while ($row = mysqli_fetch_array($execute)) {
                echo "{y: '" . $row["nombre"]  . " " . $row["apellidos"] . "', a: '" . $row["total"]  . "'},";
            }

            ?>
        ],
        barColors: ['#0af'],
        xkey: 'y',
        ykeys: ['a'],
        labels: ['ventas'],
        hideHover: 'auto',
        preUnits: '$'
    });

    <?php
    $sql = "SELECT Date_format(fecha,'%Y-%m') as  fechaMes, sum(total_conIva) as total from facturingpos.ventas group by fechaMes order by fechaMes asc";

    $execute = mysqli_query($con, $sql);

    ?>
    //BAR CHART Clientes

    <?php
    $sql = "SELECT cli.nombre as nombre, cli.apellidos as apellidos,  sum(ven.total_conIva) as total from facturingpos.ventas as ven INNER JOIN clientes as cli on ven.cod_cliente=cli.cod_cliente group by ven.cod_cliente order by total desc";

    $execute = mysqli_query($con, $sql);

    ?>
    var bar = new Morris.Bar({
        element: 'bar-chart2',
        resize: true,
        data: [

            <?php
            while ($row = mysqli_fetch_array($execute)) {
                echo "{y: '" . $row["nombre"]  . " " . $row["apellidos"] . "', a: '" . $row["total"]  . "'},";
            }

            ?>
        ],
        barColors: ['#82005E'],
        xkey: 'y',
        ykeys: ['a'],
        labels: ['ventas'],

        xLabelAngle: '50',
        preUnits: '$',
        hideHover: 'auto'
    });

    <?php
    $sql = "SELECT Date_format(fecha,'%Y-%m') as  fechaMes, sum(total_conIva) as total from facturingpos.ventas group by fechaMes order by fechaMes asc";

    $execute = mysqli_query($con, $sql);

    ?>

    /**MORRIS LINE */
    new Morris.Line({
        // ID of the element in which to draw the chart.
        element: 'myfirstchart',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        data: [<?php
                while ($row = mysqli_fetch_array($execute)) {

                    echo "{y:'" . $row["fechaMes"] . "', ventas:" . $row["total"] . "},";
                }
                ?>],
        xkey: 'y',
        ykeys: ['ventas'],
        labels: ['ventas'],
        lineColors: ['black'],
        lineWidth: 2,
        hideHover: 'auto',
        gridTextColor: '#fff',
        gridStrokeWidth: 0.4,
        pointSize: 4,
        pointStrokeColors: ['#efefef'],
        gridLineColor: '#efefef',
        gridTextFamily: 'Open Sans',
        preUnits: '$',
        gridTextSize: 10

    });

    //- DONUT CHART -
    //-------------

    <?php
    //articulos mas vendidos 
    $sql = "SELECT count(det.cod_producto) as cantidad, prod.nombre nombre FROM facturingpos.venta_detalle as det inner join productos as prod on det.cod_producto=prod.cod_producto group by nombre order by cantidad desc limit 5";
    $execute = mysqli_query($con, $sql);
    $data = "data: [";
    $labels = "labels: [";
    while ($row = mysqli_fetch_array($execute)) {

        $data .= $row["cantidad"] . ",";;
        $labels .= "'" . $row["nombre"] . "',";
    }
    $data .= "]";
    $labels .= "]";

    ?>
    // Get context with jQuery - using jQuery's .get() method.
    var donutChartCanvas = $('#donutChart').get(0).getContext('2d')
    var donutData = {
        <?php echo $labels; ?>,
        datasets: [{
            <?php echo $data; ?>,
            backgroundColor: ["red", "green", "yellow", "aqua", "purple", "blue", "cyan", "magenta", "orange", "gold"],
        }]
    }
    var donutOptions = {
        maintainAspectRatio: false,
        responsive: true,
        borderWidth: 1,
        legend: {
            display: true,
            position: 'right',

        }

    }
    //Create pie or douhnut chart
    // You can switch between pie and douhnut using the method below.
    var donutChart = new Chart(donutChartCanvas, {
        type: 'doughnut',
        data: donutData,
        options: donutOptions
    })
</script>