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

$idusuario = $_POST['id'];
$user = $_POST['user'];
$nombre = $_POST['nombre'];
$apellido = $_POST['apellido'];
$rol = $_POST['rol'];
$pass1 = $_POST['pass1'];
$pass2 = $_POST['pass2'];
$sql="";
if($idusuario!="" && $nombre != "" && $apellido !="" && $rol!="" && $user!=""){
	
	if($rol=='2' or $rol=='3' or $rol=='4'){//validar que los roles sean valido
		
		if($pass1 == "" && $pass2 == ""){
			$sql = "UPDATE `tb_usuario` 
			SET `email` = '$user', 
			`nombre` = '$nombre', 
			`apellido` = '$apellido', 
			`rol` = '$rol' 
			WHERE `tb_usuario`.`id_usuario` = $idusuario";
		}elseif($pass1 != "" && $pass2 != "" && $_POST['check']==true && $pass1== $pass2){
			$sql = "UPDATE `tb_usuario` 
			SET `email` = '$user', 
			`nombre` = '$nombre', 
			`apellido` = '$apellido', 
			`clave` = '".md5($pass1)."',
			`rol` = '$rol' 
			WHERE `tb_usuario`.`id_usuario` = $idusuario";
		}
		if($sql!=""){
			$conn->query($sql);
			echo '
				<script type="text/javascript">
					alert("Regsitro actualizado");
					window.location="user.php";
				</script>
			';
		}
		
	}else{
		echo "Error al editar, 1010";
	}
	
}

?>

