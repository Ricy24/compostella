<?php
// Conexión a la base de datos
$host = 'localhost'; // Ajusta esto según tu configuración
$dbname = 'pizzeria';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener la lista de tablas
    $query = $pdo->query("SHOW TABLES");
    $tables = $query->fetchAll(PDO::FETCH_COLUMN);

    // Eliminar datos de todas las tablas excepto 'productos'
    foreach ($tables as $table) {
        if ($table != 'productos') {
            $pdo->exec("DELETE FROM `$table`");
            echo "Datos eliminados de la tabla: $table\n";
        }
    }

    echo "Operación completada.";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
