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

if(isset($_GET['id']) && $_GET['id'] != ""){
	$sql="UPDATE `tb_usuario` SET `estado` = '2' WHERE `tb_usuario`.`id_usuario` = ".$_GET['id']."";
	$conn->query($sql);
	$sql="UPDATE `tb_espacio` SET `estado` = '3' WHERE `tb_espacio`.`id_usuario` = ".$_GET['id']."";
	$conn->query($sql);
			echo '
				<script type="text/javascript">
					alert("Regsitro Eliminado");
					window.location="user.php";
				</script>
			';
}



?>