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
$id_espacio = $_POST['id'];
$numero = $_POST['numero'];
$estado = $_POST['estado'];
$id_piso =  $_POST['id_piso'];
$id_user_new = $_POST['id_user_new'];
$id_user_actual = $_POST['id_user_actual'];
if(isset($_POST['rol'])){
    $rol = true;
}else{
    $rol = false;
}


 
if($id_espacio!="" && $id_piso != "" && $id_user_new !="" && $id_user_actual!="" && $numero!=""){

    $sql="UPDATE `tb_espacio` 
    SET `numero` = '$numero', 
    `estado` = '$estado', 
    `id_piso` = '$id_piso', 
    `id_usuario` = '$id_user_new' 
    WHERE `tb_espacio`.`id_espacio` = $id_espacio";
     if ($conn->query($sql) === TRUE) {
        echo "Record updated successfully";
        if($id_user_new===$id_user_actual){
            // No actualizar
            if($rol===true){
                echo 'actualizo rol a 4 mimso usuario <br>';
                actualizar_rol_usuer(4, $id_user_new, $conn);
            }else{
                actualizar_rol_usuer(2, $id_user_new, $conn);
            }
        }else{
            // Actualizar rol del usuario viejo y nuevodel espacio
            actualizar_rol_usuer(3, $id_user_actual, $conn);
            // Actualizar el rol del nuevo usuario asignado sera 2 o 4 segun el checkbox del fomulario
            if($rol===true){
                echo 'actualizo rol a 4 diferente usuario <br>';
                actualizar_rol_usuer(4, $id_user_new, $conn);
            }else{
                actualizar_rol_usuer(2, $id_user_new, $conn);
            }
        }
    } else {
        echo "Error updating record: " . $conn->error;
    }
 
}
/**
 * Desasociar el espacio relacionado aun usuario al cambia a rol 3
 */
function actualizar_rol_usuer($rolcode, $iduser, $conn){
	include_once('../includes/conn.php');
    $sql = "UPDATE `tb_usuario` SET `rol` = '$rolcode' WHERE `tb_usuario`.`id_usuario` = $iduser";
				 echo $sql;

				 if ($conn->query($sql) === TRUE) {
					echo "Record updated successfully";
				} else {
					echo "Error updating record: " . $conn->error;
				}
}
 
?>