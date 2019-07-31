<?php
/**
 * Se configurara vaciado de tablas temporales
 * Tabla TB_TEMP_USUARIOS
 * 
 * 
 */
date_default_timezone_set('America/Bogota');
$servername = "localhost";
$username = "root";
$password = "Mysqlparkbd";
//$password = "";
$dbname = "BD_PARK";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $dbname);
// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$sql = "TRUNCATE tb_temp_usuario";
$result = mysqli_query($conn, $sql);

$sql = "TRUNCATE tb_reservas";
$result = mysqli_query($conn, $sql);

$sql = "TRUNCATE tb_asignacion_reserva_temp";
$result = mysqli_query($conn, $sql);