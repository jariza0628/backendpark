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
$prioridad = $_POST['prioridad'];
$associar_espacio_id = $_POST['associar_espacio_id'];
$sql="";
if($idusuario!="" && $nombre != "" && $apellido !="" && $rol!="" && $user!=""){
	
	if($rol=='2' or $rol=='3' or $rol=='4'){//validar que los roles sean valido
		
		if($pass1 == "" && $pass2 == ""){
			$sql = "UPDATE `tb_usuario` 
			SET `email` = '$user', 
			`nombre` = '$nombre', 
			`apellido` = '$apellido', 
			`rol` = '$rol',
			`prioridad` = '$prioridad'  
			WHERE `tb_usuario`.`id_usuario` = $idusuario";
 		}elseif($pass1 != "" && $pass2 != "" && $_POST['check']==true && $pass1== $pass2){
			$sql = "UPDATE `tb_usuario` 
			SET `email` = '$user', 
			`nombre` = '$nombre', 
			`apellido` = '$apellido', 
			`clave` = '".md5($pass1)."',
			`rol` = '$rol',
			`prioridad` = '$prioridad'  
			WHERE `tb_usuario`.`id_usuario` = $idusuario";
		}
		if($sql!=""){
			if ($conn->query($sql) === TRUE) {
				echo "Record updated successfully";
				actualizar_espacios_rol($rol, $idusuario, $associar_espacio_id, $conn);

			} else {
				echo "Error updating record: " . $conn->error;
			}
			// echo $sql;
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
/**
 * Desasociar el espacio relacionado aun usuario al cambia a rol 3
 */
function actualizar_espacios_rol($rolcode, $iduser, $associar_espacio_id, $conn){
	include_once('../includes/conn.php');
	if($rolcode == '3'){
		$sql = "SELECT * FROM `tb_espacio` WHERE `id_usuario` = $iduser";
		echo $sql;
		$result = $conn->query($sql);
		if ($result->num_rows > 0) {
			// output data of each row
			while($row = $result->fetch_assoc()) {
				$idespacio = $row['id_espacio'];
 				$sql = "UPDATE `tb_espacio` SET `id_usuario` = NULL ,  `estado` = '3' WHERE `tb_espacio`.`id_espacio` = $idespacio";
				 echo $sql;

				 if ($conn->query($sql) === TRUE) {
					echo "Record updated successfully";
				} else {
					echo "Error updating record: " . $conn->error;
				}
			}
		} else {
			echo "0 results";
		}
		$conn->close();
	}
	/**
	 * Cambia el estado del espacio y el usuario para usuarios tipo 2 ó 4
	 */
	if($rolcode == '2' || $rolcode == '4'){
		$sql = "UPDATE `tb_espacio` SET `id_usuario` = $iduser,  `estado` = '1' WHERE `tb_espacio`.`id_espacio` = $associar_espacio_id;";
		echo $sql;
		if ($conn->query($sql) === TRUE) {
			echo "Record updated successfully";
		} else {
			echo "Error updating record: " . $conn->error;
		}
	}
}
 
?>