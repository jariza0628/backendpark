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

$user = $_POST['user'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$rol = $_POST['rol'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
$prioridad = $_POST['prioridad'];
if( $nombre != "" && $apellido !="" && $rol!="" && $user!=""){
	
	if($rol=='2' or $rol=='3' or $rol=='4'){//validar que los roles sean valido
	
		if($pass1 != "" && $pass2 != "" && $pass1== $pass2){
			//validar si el susuario existe
			$sql="SELECT * FROM `tb_usuario` WHERE `email`= '$user'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {  
			     echo "El usuario que inetenta crear no se encuentra disponible,
				 <a href='new.php'>Intentar con otro</a>";
				exit;
 			}else{

 			
			//insetar usuario sin espacio
 			if($rol=='3'){
 				$sql="INSERT INTO `tb_usuario` (`id_usuario`, `email`, `clave`, `img`, `nombre`, `apellido`, `token`, `rol`, `estado`, `id_edificio` , `prioridad`) VALUES
				(NULL, '$user', '".md5($pass1)."', 'user.png', '$nombre', '$apellido', NULL, '$rol', '1', '1', '$prioridad')";
				if ($conn->query($sql) === TRUE) {
				    echo '
				    <script type="text/javascript">

				    	alert("Usuario registrado (Sin estacionamiento)");
				    	window.location="user.php";
				    	
					</script>
				    ';

				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
		 	}
			

			//insertar usario con espacio
 			if($rol=='2' && isset($_POST['space_free']) && $_POST['space_free']!="" ){
 				$sql="INSERT INTO `tb_usuario` (`id_usuario`, `email`, `clave`, `img`, `nombre`, `apellido`, `token`, `rol`, `estado`, `id_edificio`, `prioridad`) VALUES
			(NULL, '$user', '".md5($pass1)."', 'user.png', '$nombre', '$apellido', NULL, '$rol', '1', '1', '$prioridad')";
			if ($conn->query($sql) === TRUE) {
					$last_id = mysqli_insert_id($conn);
					//asignar espacio que este libre
					$sql="UPDATE `tb_espacio` SET `estado` = '1', `id_usuario` = '$last_id' WHERE `tb_espacio`.`id_espacio` = ".$_POST['space_free']."";
					if ($conn->query($sql) === TRUE) {
					    
					} else {
					    echo "Error updating: " . $conn->error;
					}
				    echo '
				    <script type="text/javascript">
				    	alert("Usuario registrado (Con estacionamiento)");
				    	window.location="user.php";
					</script>
				    ';

				} else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
				}
			
			
			
				
 			}
			


			//insertar usuario con espacio compartido;
			if($rol=='4' && isset($_POST['space_free_com']) && $_POST['space_free_com']!="" ){
 				$sql="INSERT INTO `tb_usuario` (`id_usuario`, `email`, `clave`, `img`, `nombre`, `apellido`, `token`, `rol`, `estado`, `id_edificio`, `prioridad`) VALUES
			(NULL, '$user', '".md5($pass1)."', 'user.png', '$nombre', '$apellido', NULL, '$rol', '1', '1', '$prioridad')";
			if ($conn->query($sql) === TRUE) {
				$last_id = mysqli_insert_id($conn);
				$sql="UPDATE `tb_espacio` SET `estado` = '1', `id_usuario` = '$last_id' WHERE `tb_espacio`.`id_espacio` = ".$_POST['space_free_com']."";
					if ($conn->query($sql) === TRUE) {
					    
					} else {
					    echo "Error updating: " . $conn->error;
					}
				    echo '
				    <script type="text/javascript">
				    	alert("Usuario registrado (Con estacionamiento)");
				    	window.location="user.php";
					</script>
				    ';
				    header ("Location: user.php");
			}else {
				    echo "Error: " . $sql . "<br>" . $conn->error;
			}
			
				
				
 			}

			
			}
		}
		

	}
}

?>