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

$asignacion_hora =  $_POST['asignacion_hora'];
$inicio_reservas =  $_POST['inicio_reservas'];
$activar_liberacion =  $_POST['activar_liberacion'];
$millas =  $_POST['millas'];
$registro_usuarios =  $_POST['registro_usuarios'];

if($asignacion_hora!="" && $inicio_reservas!="" && $activar_liberacion!="" && $millas!="" && $registro_usuarios!="" ){
    $sql = "UPDATE `tb_parametros` 
    SET `hora_asignacion` = '$asignacion_hora', 
    `hora_reservas` = '$inicio_reservas', 
    `modo_liberacion` = '$activar_liberacion', 
    `millas` = '$millas', 
    `registro_usuario` = '$registro_usuarios' 
    WHERE `tb_parametros`.`id_parametros` = 1";

    	if($sql!=""){
			if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
                echo '
				<script type="text/javascript">
					alert("Regsitro actualizado");
					window.location="index.php";
				</script>
			';

			} else {
                // echo "Error updating record: " . $conn->error;
                echo '
				<script type="text/javascript">
					alert("Error al actualizar, contacte a soporte");
					window.location="index.php";
				</script>
			';
			}
			// echo $sql;
			
		}
}else{
    // Error
    echo '
				<script type="text/javascript">
					alert("Error al actualizar");
					window.location="index.php";

				</script>
			';
}
?>