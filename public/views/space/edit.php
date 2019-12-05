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
$id_espacio = "";
$numero = "";
$estado = "";
$id_piso = "";
$numero_piso = "";
$id_user = "";
$email = "";
$rol = "";

if(isset($_GET['id'])){
	$sql = 'SELECT 
            e.id_espacio, e.numero, e.estado, e.id_piso, p.numero as piso, e.id_usuario, u.email, u.rol
            FROM `tb_espacio` e 
            INNER JOIN tb_usuario u ON e.`id_usuario` = u.id_usuario 
            INNER JOIN tb_piso p ON e.id_piso = p.id_piso
			WHERE e.estado = 1 and e.id_espacio='.$_GET['id'].'';
			
	$result = $conn->query($sql);

	if ($result->num_rows > 0) {
	    // output data of each row
	    while($row = $result->fetch_assoc()) {
	    
	        //roles 1 = administrador, 2 = usuario con espacio, 3 usuario sin espacio, 4 usuario con espacio conpartido
            $id_espacio = $row['id_espacio'];
            $numero = $row['numero'];
            $estado = $row['estado'];
            $id_piso = $row['id_piso'];
            $numero_piso = $row['piso'];
            $id_user = $row['id_usuario'];
            $email = $row['email'];
            $rol = $row['rol'];
	    }
	} else {
	    echo "0 results";
	}
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
          <h1 class="page-header">Editar Estacionamiento</h1>

          

          <h2 class="sub-header">Estacionamiento: <?php echo $numero ?></h2>
         		<?php 
         		if($numero !="" && $id_user != "" && $id_piso !="" && $email != ""){
                    /**
                     * $id_espacio = "";
                        $numero = "";
                        $estado = "";
                        $id_piso = "";
                        $numero = "";
                        $id_user = "";
                        $email = "";
                        $rol = "";
                     */

         		?>
          		<form class="form-horizontal" action="save_edit.php" method="POST">
                <input type="text" class="form-control" name="id_user_actual"   value="<?php echo $id_user ?>" style="visibility: hidden;">
          		<input type="text" class="form-control" name="id"   value="<?php echo $_GET['id'] ?>" style="visibility: hidden;">
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Numero Asignado</label>
				    <div class="col-sm-4">
				      
				      <input type="text" class="form-control" name="numero" id="" placeholder="Usuario" value="<?php echo $numero ?>" required>
				    </div>
				  </div>
                  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Estado</label>
				    <div class="col-sm-5">
				      <select class="form-control" name="estado" id="estado" >
						  <option value="<?php echo $estado ?>">
                            <?php 
                                if($estado==='1'){
                                echo 'ACTIVO'.' (Actual)';
                                }
                                if($estado==='2'){
                                echo 'INACTIVO'.' (Actual)';
                                }
                            ?>
                            </option>
                            <option value="1">ACTIVO</option>
                            <option value="2">INACTIVO</option>
						</select>
				    </div>
				  </div>
                  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Usuario asignado</label>
				    <div class="col-sm-5">
				      <select class="form-control" name="id_user_new" id="id_user_new" >
						  <option value="<?php echo $id_user ?>">
                            <?php 
                                echo $email. '(Actual)';    
                            ?>
                            </option>
                            <?php
                            $sql = 	$sql="SELECT u.id_usuario, CONCAT(u.nombre, ' ', u.apellido)  as nombre,  u.email, 'No posee' as estacionamiento,  u.prioridad as prioridad
                            FROM `tb_usuario` u WHERE u.estado = 1 AND u.rol=3";
                                 $result = $conn->query($sql);

                                 if ($result->num_rows > 0) {
                                     // output data of each row
                                     while($rows = $result->fetch_assoc()) {
                                      ?>
                                      <option value="<?php echo $rows["id_usuario"] ?>">
                                      <?php echo $rows["email"] ?>
                                        </option>
                                       <?php
                                     }
                                 }
                            ?>
						</select>
				    </div>
                    <div class="col-sm-5">
                         <?php
                            if($rol==='4'){
                                echo '<input type="checkbox" name="rol" checked> Usuario con espacio compartido';
                            }else{
                                echo '<input type="checkbox" name="rol"> Usuario con espacio compartido';
    
                            }
                         ?>            
                     </div>
				  </div>
                  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Piso</label>
				    <div class="col-sm-5">
				      <select class="form-control" name="id_piso" id="id_piso" >
						  <option value="<?php echo $id_piso ?>">
                            <?php 
                                echo $numero_piso.' (Actual)';    
                            ?>
                            </option>
                            <?php
                            $sql = 	$sql="SELECT * FROM `tb_piso` WHERE estado = 1";
                                 $result = $conn->query($sql);

                                 if ($result->num_rows > 0) {
                                     // output data of each row
                                     while($rows = $result->fetch_assoc()) {
                                      ?>
                                      <option value="<?php echo $rows["id_piso"] ?>">
                                      <?php echo $rows["numero"] ?>
                                        </option>
                                       <?php
                                     }
                                 }
                            ?>
						</select>
				    </div>
				  </div>
				 
                   <!--                 
				  <div class="form-group">
				    <label for="inputEmail3" class="col-sm-2 control-label">Rol</label>
				    <div class="col-sm-5">
				      <select class="form-control" name="rol" id="rol" onchange="myFunction()">
						  <option value="<?php echo $rol ?>"><?php echo $name_rol ?></option>
						  <option value="2">Usuario con espacio</option>
						  <option value="3">Usuario Sin espacio</option>
						  <option value="4">Usuario con espacio compartido</option>
						</select>
				    </div>
				  </div> -->
          <!-- Se cargan los espacios libres si el usuario no tiene asignado y el usuario selecion la op 2 o 4 del select -->
          <?php 
          // Solo se muestra si el usuario no tiene espacio
          if($rol == '3'){
          ?>
          <div class="form-group" id="espacioslibres" style="display: none">
				    <label for="inputEmail3" class="col-sm-2 control-label"># Estacionamiento:</label>
				    <div class="col-sm-3">
				      <select class="form-control" name="associar_espacio_id">
              <?php
                $sql = "SELECT * FROM `tb_espacio` WHERE `estado`= '3' AND `id_usuario` IS NULL";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // output data of each row
                    while($row = $result->fetch_assoc()) {
                        ?>
                        <option value="<?php echo $row['id_espacio'] ?>"><?php echo $row['numero'] ?></option>
                        <?php
                    }
                } else {
                  ?>
                  <option value="0">No hay espacios disponibles</option>
                  <?php
                }
              ?>
            

						</select>
				    </div>
				  </div>
          <?php
           } // Fin if rol = 3
          ?>      
			 
				  <div id="pass" style="display: none">
				  <div class="form-group">
				    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
				    <div class="col-sm-10">
				      <input type="password" class="form-control" name="pass1" id="inputPassword3" placeholder="Password">
				    </div>
				  </div>
				   <div class="form-group">
				    <label for="inputPassword3" class="col-sm-2 control-label">Password</label>
				    <div class="col-sm-10">
				      <input type="password" class="form-control" name="pass2" id="inputPassword3" placeholder="Password">
				    </div>
				  </div>
				 </div>
				 
				  <div class="form-group">
				    <div class="col-sm-offset-2 col-sm-10">
				      <button type="submit" class="btn btn-default">Guardar</button>
				    </div>
				  </div>
				</form>
          		<?php 
          		}else{
          			?>
          			<p>Error al cargar los datos del usuario, contacte al administrador.</p>
          			<?php
          		}

          		?>
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
    <script type="text/javascript">
    function showContent() {
        element = document.getElementById("pass");
        check = document.getElementById("check");
        if (check.checked) {
            element.style.display='block';
        }
        else {
            element.style.display='none';
        }
    }
    function myFunction() {
      var x = document.getElementById("rol").value;
       var espacioslibres = document.getElementById("espacioslibres");
      if(x=='2' || x=='4'){
        espacioslibres.style.display='block';
      }else{
        espacioslibres.style.display='none';
      }
     }
	</script>
    <script src="../asset/js/jquery.min.js"></script>
    <script>window.jQuery || document.write('<script src="../../../assets/js/vendor/jquery.min.js"><\/script>')</script>
    <script src="../asset/js/bootstrap.min.js"></script>
    <!-- Just to make our placeholder images work. Don't actually copy the next line! -->
    <script src="../asset/js/holder.min.js"></script>
    <!-- IE10 viewport hack for Surface/desktop Windows 8 bug -->
    <script src="../asset/js/ie10-viewport-bug-workaround.js"></script>
  

</body></html>