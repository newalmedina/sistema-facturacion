<!DOCTYPE html>
<html>
<?php
session_start();
require_once "funcionalidades.php";
?>

<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>Facturing P.O.S.</title>


</head>
<?php
require 'librerias.php';
if (isset($_SESSION["usuario"])) {
  require "cabezote.php";
  ?>

  <body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">
      <?php

        require "menu.php";
        ?>
      <!-- Main content -->
      <section class="content">
        <div class="container-fluid">

          <!-- Content Wrapper. Contains page content -->
          <div class="content-wrapper">

            <?php

              mensaje();

              if (isset($_GET["directorio"]) && isset($_GET["pagina"])) {




                require "modulos/" . $_GET["directorio"] . "/" . $_GET["pagina"];
              } else {
                require "inicio.php";
              }
              ?>


          </div><!-- /.container-fluid -->
      </section>
      <!-- /.content -->
    </div>
    <script>
      var permisos = <?php echo $_SESSION["usuario"]["provilegios"] ?>;
      if (permisos == 2) {
        $("#user_permisos").hide();
        $("#categoria_permisos").hide();
        $("#productos_permisos").hide();
        $("#rep_venta_permisos").hide();
        $(".eliminar").hide();
      }
      if (permisos == 3) {
        $("#user_permisos").hide();
        $("#cliente_permisos").hide();
        $("#venta_permisos").hide();
        $(".eliminar").hide();
      }
    </script>
  <?php
    require "footer.php";
    echo "</body>";
  } else {


    require "modulos/login/login.php";
  }
  ?>


</html>