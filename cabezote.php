 <!-- Navbar -->
 <nav class="main-header navbar navbar-expand navbar-white navbar-light" style="background:#343a40">
   <!-- Left navbar links -->
   <ul class="navbar-nav">
     <li class="nav-item">
       <a class="nav-link" data-widget="pushmenu" href="#"><i class="fas fa-bars"></i></a>
     </li>

   </ul>
   <ul class="navbar-nav ml-auto btn btndefault" style="background:#343a40">

     <li class="dropdown user user-menu">

       <a href="#" class="dropdown-toggle" data-toggle="dropdown">
         <img src="<?php echo $_SESSION["usuario"]["foto"] ?>" alt="imagen usuario" class="user-image">


         <span class="hidden-xs "><?php echo $_SESSION["usuario"]["nombre"] . " " . $_SESSION["usuario"]["apellidos"]  ?></span>

       </a>

       <!-- Dropdown-toggle -->

       <ul class="dropdown-menu btn" style="background:#343a40">

         <li class="user-body">

           <div class="pull-right">

             <a href="modulos/login/crud.php?salir=salir" class="btn btn-default btn-flat">Salir</a>

           </div>

         </li>

       </ul>

     </li>

   </ul>
   <!-- Right navbar links -->


 </nav>
 <!-- /.navbar -->