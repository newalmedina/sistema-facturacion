<?php

if (isset($_SESSION["retroceso"])) {
  ?>
  <script>
    location.href = 'index.php?directorio=venta&pagina=add.php';
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
        <h1 class="m-0 text-dark">Nueva Venta</h1>
      </div><!-- /.col -->
      <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
          <li class="breadcrumb-item"><a href="index.php">Inicio</a></li>
          <li class="breadcrumb-item"><a href="index.php?directorio=venta&pagina=index.php">Administracion de Ventas</a></li>
          <li class="breadcrumb-item active">Nuevo Ventas</li>
        </ol>
      </div><!-- /.col -->
    </div><!-- /.row -->
  </div><!-- /.container-fluid -->
</div>
<!--VENTA DETALLE-->

<div class="row">

  <form action="index.php?directorio=venta&pagina=crud.php" method="POST" onsubmit="return  validarVender()" class=" col-md-12 col-lg-5">
    <!-- Input addon -->
    <div class="card card-info  border-top border-success">
      <div class="card-header">
        <h3 class="card-title">Venta detalles</h3>
      </div>
      <div class="card-body">
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-user"></i></span>
          </div>
          <input type="text" class="form-control" placeholder="Username" disabled value="<?php echo  $_SESSION["usuario"]["nombre"] . " " . $_SESSION["usuario"]["apellidos"]; ?>">
        </div>
        <div class="input-group ">
          <div class="input-group-prepend">
            <span class="input-group-text"><i class="fas fa-users"></i></span>
          </div>
          <select id="cod_cliente" required name=" cliente" class="form-control">

            <option selected="selected" value="">Cliente</option>
            <?php
            require_once "modulos/conexion/conexion.php";

            $con = conectar();
            $sql = "select * from clientes";
            $execute = mysqli_query($con, $sql);

            if ($execute) {
              while ($row = mysqli_fetch_array($execute)) {
                echo "<option value='" . $row["cod_cliente"] . "'>" . $row["nombre"] . " " . $row["apellidos"] . "</option>";
              }
            }

            ?>
          </select>
        </div>
        <hr>
        <!--TABLA AGREGANDO PRODUCTOS-->
        <div id="divaddProductos" class="row">
          <div class="col-md-12">
            <table width="100%" class="">
              <thead class="table  bg-primary">
                <tr>
                  <td>Nombre</td>
                  <td width="15px">Precio</td>
                  <td width="15px">Cantidad</td>
                  <td width="15px">Subtotal</td>
                  <td width="10px"></td>
                </tr>
              </thead>
              <tbody id="addProductos">

              </tbody>
            </table>
          </div>
          <hr class="col-md-12">
        </div>

        <div class="row">
          <div class="col-lg-4">
            <label for="">Subtotal</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  $
                </span>
              </div>
              <input type="text" required id='subtotalSinImpuestoDisable' disabled class="form-control">
              <input type="hidden" name='totalSinIva' id='subtotalSinImpuesto' class="form-control">
            </div>
            <!-- /input-group -->
          </div>
          <div class="col-lg-4">
            <label for="">Impuesto 21%</label>
            <div class="input-group">
              <input type="text" id='impuestoDisable' disabled class="form-control">
              <input type="hidden" name='iva' id='impuesto' class="form-control">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  %
                </span>
              </div>
            </div>
            <!-- /input-group -->
          </div>
          <div class="col-lg-4">
            <label for="">Total</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  $
                </span>
              </div>
              <input type="text" id='totalDisable' disabled class="form-control">
              <input type="hidden" name='totalConIva' id='total' class="form-control">
            </div>
            <!-- /input-group -->
          </div>
        </div>
        <hr>
        <!--Metodo de pago-->
        <div class="row">
          <div class="col-md-12 ">
            <label for="">Metodo de pago</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  $
                </span>
              </div>
              <select class="form-control" required name="metodoPago" id="metodoPago" id="">
                <option value="" selected>Seleccione</option>
                <?php
                $sql = "select * from modo_pago";
                $execute = mysqli_query($con, $sql);

                if ($execute) {
                  while ($row = mysqli_fetch_array($execute)) {
                    echo "<option value='" . $row["cod_modo_pago"] . "'>" . $row["descripcion"] . "</option>";
                  }
                }

                ?>
              </select>
            </div>
            <!-- /input-group -->
          </div>
          <!-- /Efectivo -->
          <div class="col-lg-6" id="pago">
            <label for="">Pago</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  $
                </span>
              </div>
              <input id="pago1" onkeyup="calculodevuelta()" onKeyPress="return soloNumeros(event)" maxlength="10" type="text" class="form-control">
            </div>

          </div>
          <div class="col-lg-6" id="devuelta">
            <label for="">Devuelta</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  $
                </span>
              </div>
              <input id="devuelta1" type="text" disabled class="form-control">
            </div>

            <!-- /input-group -->
          </div>
          <div class="col-lg-10 " id="codigotarjeta">
            <label for="">Codigo transaccion</label>
            <div class="input-group">
              <div class="input-group-prepend">
                <span class="input-group-text">
                  <i class="far fa-credit-card"></i>
                </span>
              </div>
              <input id="codigotarjeta1" required name='codigoTransaccion' placeholder="Codigo Transaccion" onKeyPress="return soloNumeros(event)" type="text" class="form-control">
            </div>

            <!-- /input-group -->
          </div>
        </div>
        <hr>
        <div class=" float-right">
          <input class="btn btn-primary btn-sm " type="submit" name="vender" value="Vender">
        </div>
      </div>
      <!-- /.card-body -->
    </div>
  </form>


  <!--PRODUCTOS-->
  <div class=" col-md-12 col-lg-7">
    <!-- Input addon -->
    <div class="card card-info  border-top border-warning">
      <div class="card-header">
        <h3 class="card-title">Productos</h3>
      </div>
      <div class="card-body">
        <table id="dtventa" class="table  table-bordered table-striped">
          <thead>
            <tr>

              <th width="40">CODIGO </th>
              <th width="10">IMAGEN</th>
              <th>NOMBRE</th>
              <th>PRECIO</th>
              <th>STOCK</th>
              <th width="5" class="text-center"></th>
            </tr>
          </thead>
          <tbody>
            <?php
            $sql = "select * from productos";

            $execute = mysqli_query($con, $sql);

            if ($execute) {
              while ($row = mysqli_fetch_array($execute)) {


                echo "<tr><td>" . $row["cod_producto"] . "</td>";
                $id = $row["cod_producto"];
                if ($row["foto"] != "") {
                  echo '<td class="text-center"> <img class="img-fluid"  src="' . $row["foto"] . '" style="width: 40px; height:40px;" alt=""></td>';
                } else {
                  echo '<td class="text-center"> <img class="img-fluid"  src="img/anonymous.png" style="width: 40px; height:40px;" alt=""></td>';
                }
                echo "<td>" . $row["nombre"] . "</td>";
                echo "<td >" . $row["precio"] . "</td>";
                if ($row["stock"] < 3) {
                  echo "<td class='bg-danger '>" . $row["stock"] . "</td>";
                } elseif ($row["stock"] < 10) {
                  echo "<td class='bg-warning '>" . $row["stock"] . "</td>";
                } else {
                  echo "<td class='bg-success '>" . $row["stock"] . "</td>";
                }
                //$producto = new array($row["cod_producto"], $row["nombre"], $row["precio"], $row["stock"]);
                $codProd = $row["cod_producto"];
                $nomProd = $row["nombre"];
                $precProd = $row["precio"];
                $stockProd = $row["stock"];
                if ($row["stock"] < 1) {
                  echo "<td><button disabled id='btnadd$codProd' class='btn btn-info mr-1 'onclick='agregarProducto(\"#btnadd$codProd\",\"" . $codProd . "\",\"" . $nomProd . "\"," . $precProd . "," . $stockProd . ")'><i class='fas fa-plus'></i></button></td></tr>";
                } else {
                  echo "<td><button id='btnadd$codProd' class='btn btn-info mr-1 'onclick='agregarProducto(\"#btnadd$codProd\",\"" . $codProd . "\",\"" . $nomProd . "\"," . $precProd . "," . $stockProd . ")'><i class='fas fa-plus'></i></button></td></tr>";
                }
              }
            }


            ?>

          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>

<script>
  function calculodevuelta() {
    var pago, total, devuelta;
    pago = $("#pago1").val()
    total = $("#totalDisable").val();
    if (total != "") {
      if (pago > total - 1) {
        if (pago != "") {
          devuelta = pago - total;
          devuelta = devuelta.toFixed(2);

          $("#devuelta1").val(devuelta);
        } else {
          $("#devuelta1").val("");
        }
      } else {
        $("#devuelta1").val("");
      }
    } else {
      $("#devuelta1").val("");
    }

  }

  function validarVender() {
    var subtotales = document.getElementsByName("subtotales");
    if (!subtotales.length) {

      swal({
        title: "!Advertencia",
        text: "No tienes Articulos sececionados",
        icon: "warning",
      });
      return false;
    }
    return true;
  }


  function subtotales() {
    var subtotales = document.getElementsByName("subtotales");
    if (subtotales.length) {
      var resultado = parseFloat(subtotales[0].value);


      for (x = 1; x < subtotales.length; x++)
        resultado = parseFloat(resultado) + parseFloat(subtotales[x].value);

      resultado = resultado.toFixed(2);

      var impuesto = parseFloat(resultado) * 0.21;
      impuesto = impuesto.toFixed(2);

      var resultadoTotal = parseFloat(resultado) + parseFloat(impuesto);
      resultadoTotal = resultadoTotal.toFixed(2);


      $("#subtotalSinImpuestoDisable").val(resultado);
      $("#impuestoDisable").val(impuesto);
      $("#totalDisable").val(resultadoTotal);
      $("#subtotalSinImpuesto").val(resultado);
      $("#impuesto").val(impuesto);
      $("#total").val(resultadoTotal);
    } else {

      $("#subtotalSinImpuestoDisable").val("");
      $("#impuestoDisable").val("");
      $("#totalDisable").val("");
      $("#subtotalSinImpuesto").val("");
      $("#impuesto").val("");
      $("#total").val("");
    }

  }

  function cantidadProducto(idcampoCantidad, stock, precio, campoSubtotal, camposubtotalenable) {
    var cantidad = $(idcampoCantidad);
    if (cantidad.val() < 1) {
      swal({
        title: "!Advertencia",
        text: "Por lo menos la cantidad de producto tiene que ser 1 o mayor",
        icon: "warning",
      });
      cantidad.val("1");
    }
    if (cantidad.val() > stock) {
      swal({
        title: "!Advertencia",
        text: "Solo tienes " + stock + " articulos disponibles no puedes seleccionar mas de lo existente",
        icon: "warning",
      });
      cantidad.val(stock);
    }
    var subtotal = precio * cantidad.val();
    subtotal = subtotal.toFixed(2);
    $(campoSubtotal).val(subtotal);
    $(camposubtotalenable).val(subtotal);

    $("#subtotal").val(subtotal);
    subtotales();
  }

  function agregarProducto(btn, codigo, nombre, precio, stock) {

    $("#divaddProductos").show();
    $(btn).attr("disabled", true);

    var campos = "<tr id='producto" + codigo + "'><td class='text-secondary'>" + nombre + " <input name='codigoProducto[]' type='hidden' value='" + codigo + "'></td>";
    campos += "<td> <input name='precioProducto[]' type='hidden' value='" + precio.toFixed(2) + "' > " + precio.toFixed(2) + "</td>";
    campos += "<td><input name='cantProducto[]' id='cantidad" + codigo + "' onchange='cantidadProducto(\"#cantidad" + codigo + "\"," + stock + "," + precio + ",\"#subtotalDisable" + codigo + "\",\"#subtotalEnable" + codigo + "\")'class='form-control mt-1' value='1' min='1' max='" + stock + "' type='number'></td>";
    campos += "<td> <input name='subTotalProd'id='subtotalEnable" + codigo + "' type='hidden' value='" + precio.toFixed(2) + "' > <input name='subtotales' disabled  id='subtotalDisable" + codigo + "' class='form-control mt-1 subtotalDisable' type='text' value='" + precio.toFixed(2) + "'> </td>";
    campos += "<td><button class='btn btn-danger ' onclick='eliminarProducto(\"#producto" + codigo + "\",\"" + btn + "\" )'><i class='fas fa-times'></i></button></tr>";

    var producto = $("#addProductos").append(campos);


    /*var tr = document.createElement("tr");
    alert(codigo);
    var codigo = document.createElement("label")
    codigo.innerHTML = codigo;
    tr.appendChild(codigo);

    document.getElementById("addProductos").appendChild(tr);
*/
    subtotales();
  }

  function eliminarProducto(producto, btn) {
    $(btn).attr("disabled", false);
    $(producto).remove();
    subtotales();
  }

  $(document).ready(function() {
    $("#divaddProductos").hide();

    $('#dtventa').DataTable({
      "language": lenguaEspañola()
    });

    $('#cod_cliente').select2();


    $('#pago').hide();
    $('#devuelta').hide();
    $('#codigotarjeta').hide();
    $('#metodoPago').on('change', function() {

      if ($('#metodoPago').val().trim() === '') {

        $('#pago').hide();
        $('#devuelta').hide();
        $('#codigotarjeta').hide();
        $('#codigotarjeta1').val("");
        $('#pago1').val("");
      }
      if ($('#metodoPago').val() == 1) {
        $('#pago').show();
        $('#devuelta').show();
        $('#codigotarjeta').hide();
        $('#codigotarjeta1').val("0");
        $('#pago1').val("");
      }
      if ($('#metodoPago').val() == 2) {
        $('#pago').hide();
        $('#devuelta').hide();
        $('#codigotarjeta').show();
        $('#codigotarjeta1').val("");
        $('#pago1').val("0");
      }
    });
  });
</script>