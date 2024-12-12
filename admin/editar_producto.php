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

// Inicializar variables
$id = $nombre = $precio = $categoria = '';
$error = '';

// Verificar si se ha pasado un ID
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Obtener datos del producto
    $consulta = "SELECT * FROM productos WHERE id = ?";
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows == 1) {
        $producto = $resultado->fetch_assoc();
        $nombre = $producto['nombre'];
        $precio = $producto['precio'];
        $categoria = $producto['categoria'];
    } else {
        $error = "Producto no encontrado";
    }
}

// Procesar formulario de edición
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $id = intval($_POST['id']);
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);

    // Preparar consulta de actualización
    $consulta = "UPDATE productos SET nombre = ?, precio = ?, categoria = ? WHERE id = ?";
    
    // Preparar statement
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("sdsi", $nombre, $precio, $categoria, $id);

    // Ejecutar
    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: productos.php?mensaje=Producto+actualizado+exitosamente");
        exit();
    } else {
        $error = "Error al actualizar el producto: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto</title>
    <style>
        body { 
            font-family: Arial, sans-serif; 
            max-width: 500px; 
            margin: 20px auto; 
            padding: 20px; 
            background: #f4f4f4; 
        }
        .formulario {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .campo {
            margin-bottom: 15px;
        }
        .campo label {
            display: block;
            margin-bottom: 5px;
        }
        .campo input, 
        .campo select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .btn {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #FFC107;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .error {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="formulario">
        <h2>Editar Producto</h2>
        
        <?php if(!empty($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <input type="hidden" name="id" value="<?php echo $id; ?>">
            
            <div class="campo">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
            </div>

            <div class="campo">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" value="<?php echo $precio; ?>" required>
            </div>

            <div class="campo">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <option value="pizza" <?php echo ($categoria == 'pizza') ? 'selected' : ''; ?>>Pizza</option>
                    <option value="bebida" <?php echo ($categoria == 'bebida') ? 'selected' : ''; ?>>Bebida</option>
                </select>
            </div>

            <button type="submit" class="btn">Actualizar Producto</button>
        </form>
        
        <p><a href="productos.php">Volver al listado</a></p>
    </div>
</body>
</html>
<?php
$conexion->close();
?>