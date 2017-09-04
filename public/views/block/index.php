<?php
session_start();
include_once('../includes/conn.php');


if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true) {

} else {
   echo "Debe estar registrado para ingresar.<br>";
   echo "<br><a href='../login/login.php'>Ir a Login</a>";
   

exit;
}

$now = time();

if($now > $_SESSION['expire']) {
session_destroy();

echo "Su sesion a terminado,
<a href='../login/login.php'>Necesita Hacer Login</a>";
exit;
}
?>
<!DOCTYPE html>
<!-- saved from url=(0053)https://getbootstrap.com/docs/3.3/examples/dashboard/ -->
<html lang="es"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="icon" href="https://getbootstrap.com/docs/3.3/favicon.ico">

    <title>Panel Administrativo</title>

    <!-- Bootstrap core CSS -->
    <link href="../asset/css/bootstrap.min.css" rel="stylesheet">

    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <link href="../asset/css/ie10-viewport-bug-workaround.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="../asset/css/dashboard.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=Roboto" rel="stylesheet">
    <!-- Just for debugging purposes. Don't actually copy these 2 lines! -->
    <!--[if lt IE 9]><script src="../../assets/js/ie8-responsive-file-warning.js"></script><![endif]-->
    <script src="../asset/js/ie-emulation-modes-warning.js"></script>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <style type="text/css">
     #mmenu_color{
       background: linear-gradient(to right, rgba(255,255,255,1) 0%, rgba(246,246,246,1) 47%, rgba(204,204,204,1) 100%);
filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#cccccc', GradientType=1 );
     }
     #menu_sombra{
     background-color: #2196F3;
    /* border-color: #080808; */
    height: 60px;
    box-shadow: 0px 2px 3px 0px rgba(0,0,0,0.75);
    padding-top: 6px;
    }
     .navbar-inverse .navbar-brand {
    color: white !important;
    font-size: 20px;
    }
    .navbar-inverse a {
        color: white !important;
        font-size: 20px;
    }
    .navbar-inverse .navbar-collapse, .navbar-inverse .navbar-form {
        border-color: #8c8c8c;
        background-color: #2196f3;
        color: white;
    }

    </style>
  </head>

  <body style="font-family: 'Roboto', sans-serif !important;">

    <nav class="navbar navbar-inverse navbar-fixed-top" id="menu_sombra">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="https://getbootstrap.com/docs/3.3/examples/dashboard/#">Panel Administrativo</a>
        </div>
        <div id="navbar" class="navbar-collapse collapse" aria-expanded="false" style="height: 1px;">
          <ul class="nav navbar-nav navbar-right">
            <li><a href="../panel.php">Inicio</a></li>
            <li><a href="" data-toggle="modal" data-target="#myModal">Ayuda</a></li>
            <li><a href="../login/logout.php">Salir</a></li>
          </ul>
        </div>
      </div>
    </nav>

    <div class="container-fluid">
      <div class="row">
        <div class="col-sm-3 col-md-2 sidebar" id="mmenu_color">
         <?php
            include_once('../menu/menu_nv2.php');
          ?>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Bloques</h1>
          <h2 class="sub-header">Lista de Bloques</h2>
          <div class="table-responsive">

          	<?php
          	$sql = 'SELECT * FROM `tb_bloque`';
			
          	?>
            <table class="table table-hover">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Nombre</th>
                  <th>Estado</th>
                  <th>Edificio</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php
              $result = $conn->query($sql);

					if ($result->num_rows > 0) {
					    // output data of each row
					    while($row = $result->fetch_assoc()) {
					     ?>
					      
					      <tr>
			                  <td><?php echo $row["id_bloque"] ?></td>
			                  <td><?php echo $row["numero"] ?></td>
			                  <td><?php echo "Activo"?></td>
			                  <td><?php echo "Transelca" ?></td>
			                  
			                  <td><a href="#">Editar</a> <a href="#">Eliminar</a></td>
			              </tr>

					     <?php
					    }
					} else {
					    echo "0 results";
					}
			?>
               
              
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <!-- Bootstrap Modal
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <div class="modal fade" tabindex="-1" role="dialog" id="myModal">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Bienvenido al Centro de ayuda</h4>
          </div>
          <div class="modal-body">
            <p>
              Aqui pordra realizar control de la aplicacion, podra modificar contraseña de usuarios y crearlos, ver los lugares disponibles para hoy,
              liberar espacios por suarios y ver estadisticas de uso de los espacios.
            </p>
            <blockquote>
            <ul>
              <li><a href="#">¿Donde puedo liberar espacios de estacionamiento de un usuario?</a></li>
              <li><a href="#">¿Gestionar usaurios, crear o modificar?</a></li>
              <li><a href="#">¿Ver estadisticas de uso?</a></li>
            </ul>
            </blockquote>
            <p><strong>Obtener ayuda personalizada</strong></p>
            <address>
            <strong>Barranquilla TIC.</strong><br>
            Colombia<br>
            <abbr title="Phone">P:</abbr> 304 526 87 23
          </address>

          <address>
            <strong>Jeffer Ariza</strong><br>
            <a href="mailto:jefferariza@outlook.com">jefferariza@outlook.com</a>
          </address>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.fin modal -->
    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="../asset/js/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../asset/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="../asset/js/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../asset/js/ie10-viewport-bug-workaround.js"></script>
  

</body></html>