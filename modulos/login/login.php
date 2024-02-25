  <body class="hold-transition login-page">

    <div class="login-box m-auto">
      <div class="login-logo">
        <img src="img/logo-lineal.png" class='img-fluid' alt="">
      </div>
      <!-- /.login-logo -->
      <div class="card">
        <div class="card-body login-card-body">
          <?php
          if (isset($_SESSION["errorLogin"])) {
            echo "<div class='col-12'>";
            if ($_SESSION["errorLogin"] == "inactivo") {
              echo "<p class='alert alert-warning'>Usuario inactivo <small>, contacte con el administrador de sistma</small></p>";
            } else {
              echo ("<p class='alert alert-danger'>Credenciales incorrectas <small>, introduzcalas de nuevo</small></p>");
            }
            echo "</div>";
            unset($_SESSION["errorLogin"]);
          }
          ?>
          <form action="modulos/login/crud.php" method="post">
            <div class="input-group mb-3">
              <input type="email" name='correo' class="form-control" placeholder="Correo">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-envelope"></span>
                </div>
              </div>
            </div>
            <div class="input-group mb-3">
              <input type="password" name='pass' class="form-control" placeholder="Contraseña">
              <div class="input-group-append">
                <div class="input-group-text">
                  <span class="fas fa-lock"></span>
                </div>
              </div>
            </div>


            <!-- /.col -->
            <div class="col-12">
              <button type="submit" name="acceder" class="btn btn-primary btn-block">Acceder</button>
            </div>

          </form>


          <!-- /.login-card-body -->
        </div>
      </div>
      <!-- /.login-box -->

  </body>
  <script>

  </script>