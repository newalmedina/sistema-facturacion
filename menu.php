<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary  elevation-4">
  <!-- Brand Logo -->
  <a href="index.php" class="brand-link">
    <img src="img/logo-lineal.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
    <span class="brand-text font-weight-light">Facturing P.O.S.</span>
  </a>

  <!-- Sidebar -->
  <div class="sidebar">
    <!-- Sidebar user panel (optional) -->


    <!-- Sidebar Menu -->
    <nav class="mt-2">
      <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <!-- Add icons to the links using the .nav-icon class
               with font-awesome or any other icon font library -->
        <li id="user_permisos" class="nav-item">
          <a href="index.php?directorio=usuario&pagina=index.php" class="nav-link">
            <i class="nav-icon fas fa-user"></i>
            <p>
              Usuarios
            </p>
          </a>
        </li>
        <li id="categoria_permisos" class="nav-item">
          <a href="index.php?directorio=categoria&pagina=index.php" class="nav-link">
            <i class="nav-icon fas fa-th"></i>
            <p>
              Categorias
            </p>
          </a>
        </li>
        <li id="productos_permisos" class="nav-item">
          <a href="index.php?directorio=producto&pagina=index.php" class="nav-link">
            <i class="nav-icon fab fa-product-hunt"></i>
            <p>
              Productos
            </p>
          </a>
        </li>
        <li id="cliente_permisos" class="nav-item">
          <a href="index.php?directorio=cliente&pagina=index.php" class="nav-link">
            <i class="nav-icon fas fa-users"></i>
            <p>
              Clientes
            </p>
          </a>
        </li>
        <li id="venta_permisos" class="nav-item has-treeview ">
          <a href="#" class="nav-link ">
            <i class="nav-icon fas fa-list-ul"></i>
            <p>
              Ventas
              <i class="right fas fa-angle-left"></i>
            </p>
          </a>
          <ul class="nav nav-treeview">
            <li class="nav-item">
              <a href="index.php?directorio=venta&pagina=index.php" class="nav-link ">
                <i class="far fa-circle nav-icon"></i>
                <p>Administrar Ventas</p>
              </a>
            </li>
            <li class="nav-item">
              <a href="index.php?directorio=venta&pagina=add.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Crear Ventas</p>
              </a>
            </li>
            <li id="rep_venta_permisos" class="nav-item">
              <a href="index.php?directorio=venta&pagina=reporte.php" class="nav-link">
                <i class="far fa-circle nav-icon"></i>
                <p>Reportar Ventas</p>
              </a>
            </li>
          </ul>
        </li>


      </ul>
    </nav>
    <!-- /.sidebar-menu -->
  </div>
  <!-- /.sidebar -->
</aside>