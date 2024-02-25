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
<section class="content-header">

  <h1>

    Tablero

    <small>Panel de Control</small>

  </h1>



</section>

<section class="content">
  <div class="row">

    <div class="col-md-3 col-sm-6 col-xs-6">

      <div class="small-box bg-primary">

        <div class="inner text-center">

          <h3>$<?php echo number_format($ventas["total"], 2, '.', ','); ?></h3>

          <p>Ventas</p>
          <hr>
          <a href="index.php?directorio=venta&pagina=index.php" class="text-white info">Mas info <small><i class="fas fa-arrow-circle-right"></i></small></a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-6">

      <div class="small-box bg-success">

        <div class="inner text-center">

          <h3><?php echo $categorias["cantidad"]; ?></h3>

          <p>Categorias</p>
          <hr>
          <a href="index.php?directorio=categoria&pagina=index.php" class="text-white info">Mas info <small><i class="fas fa-arrow-circle-right"></i></small></a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-6">

      <div class="small-box bg-warning ">

        <div class="inner text-center text-white ">

          <h3><?php echo $clientes["cantidad"]; ?></h3>

          <p>Clientes</p>
          <hr>
          <a href="index.php?directorio=cliente&pagina=index.php" class="text-white info">Mas info <small><i class="fas fa-arrow-circle-right"></i></small></a>
        </div>
      </div>
    </div>
    <div class="col-md-3 col-sm-6 col-xs-6 ">

      <div class="small-box bg-danger ">

        <div class="inner text-center">

          <h3><?php echo $productos["cantidad"]; ?></h3>

          <p>Productos</p>
          <hr>
          <a href="index.php?directorio=producto&pagina=index.php" class="text-white info">Mas info <small><i class="fas fa-arrow-circle-right"></i></small></a>
        </div>
      </div>
    </div>
  </div>
  <div class="row">



    <div id="" class=" col-md-12 card card-info bg-info pt-1">

      <h3 class="card-title  text-white"> <i class="fas fa-th mr-2"></i>Grafico de Ventas</h3>

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
    function listarUltimosProductos()
    {
      global $con;
      $sql = "SELECT * FROM facturingpos.productos order by cod_producto desc  limit 10";
      $execute = mysqli_query($con, $sql);

      if ($execute) {
        while ($row = mysqli_fetch_array($execute)) {


          echo "<tr>";

          if ($row["foto"] != "") {
            echo '<td> <img class="img-fluid"  src="' . $row["foto"] . '" style="width: 40px; height:40px;" alt=""></td>';
          } else {
            echo '<td> <img class="img-fluid"  src="img/anonymous.png " style="width: 40px; height:40px;" alt=""></td>';
          }
          echo "<td class='text-info'>" . $row["nombre"] . "</td>";
          echo "<td  class='text-right'><small  class='text-white bg-warning p-1 rounded' >$ " .  number_format($row["precio"], 2, '.', ',') . "</small></td></tr>";
        }
      }
    }
    ?>
    <!--PRODUCTOS RECIENTES-->
    <div class=" col-md-6 ">
      <!-- Input addon -->
      <div class=" card card-info border-top border-primary">
        <div class="card-header">
          <h3 class="card-title">Productos Recientes</h3>
        </div>
        <div class="card-body">
          <table class="table">

            <?php
            listarUltimosProductos();
            ?>

          </table>
          <hr>
          <div class="text-center"> <a href="index.php?directorio=producto&pagina=index.php">Ver Todos los productos</a></div>
        </div>
        <!-- /.card-body -->
      </div>
    </div>


</section>
<script>
  <?php
  $sql = "SELECT Date_format(fecha,'%Y-%m') as  fechaMes, sum(total_conIva) as total from facturingpos.ventas group by fechaMes order by fechaMes asc";

  $execute = mysqli_query($con, $sql);

  ?>
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