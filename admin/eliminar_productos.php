<?php
// Configuración de conexión
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_de_datos = 'pizzeria';

// Conexión a la base de datos
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Verificar que se haya pasado un ID
if (isset($_GET['id']) && !empty($_GET['id'])) {
    // Limpiar y convertir el ID
    $id = intval($_GET['id']);

    // Preparar consulta de eliminación
    $consulta = "DELETE FROM productos WHERE id = ?";
    
    // Preparar statement
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $id);

    // Ejecutar
    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: productos.php?mensaje=Producto+eliminado+exitosamente");
        exit();
    } else {
        // En caso de error
        header("Location: productos.php?error=No+se+pudo+eliminar+el+producto");
        exit();
    }
} else {
    // Si no se proporcionó un ID
    header("Location: productos.php?error=ID+de+producto+no+válido");
    exit();
}

// Cerrar conexión
$conexion->close();
?>