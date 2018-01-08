<?php
$host_db = 'localhost';
$user_db = 'root';
$pass_db = 'Mysqlparkbd';
$db_name = 'bd_park';


$conn = new mysqli($host_db, $user_db, $pass_db, $db_name);

if ($conn->connect_error) {
 die("La conn falló: " . $conn->connect_error);
}
?>