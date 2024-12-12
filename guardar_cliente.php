<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pizzeria";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar si los datos fueron enviados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = $conn->real_escape_string($_POST['nombre']);
    $mesa = intval($_POST['mesa']);

    // Redirigir al menú pasando los datos del cliente a través de la URL
    header("Location: menu.php?nombre=$nombre&mesa=$mesa");
    exit();
}

$conn->close();
?>
