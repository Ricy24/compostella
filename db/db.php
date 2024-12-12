<?php
// config/database.php
$host = 'localhost';
$usuario = 'root';
$password = '';
$base_datos = 'pizzeria';

try {
    $conexion = new PDO("mysql:host=$host;dbname=$base_datos", $usuario, $password);
    $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
?>