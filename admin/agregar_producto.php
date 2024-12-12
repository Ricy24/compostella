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

// Procesar formulario de agregar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recoger datos del formulario
    $nombre = $conexion->real_escape_string($_POST['nombre']);
    $precio = floatval($_POST['precio']);
    $categoria = $conexion->real_escape_string($_POST['categoria']);

    // Preparar consulta
    $consulta = "INSERT INTO productos (nombre, precio, categoria) VALUES (?, ?, ?)";
    
    // Preparar statement
    $stmt = $conexion->prepare($consulta);
    $stmt->bind_param("sds", $nombre, $precio, $categoria);

    // Ejecutar
    if ($stmt->execute()) {
        // Redirigir con mensaje de éxito
        header("Location: productos.php?mensaje=Producto+agregado+exitosamente");
        exit();
    } else {
        $error = "Error al agregar el producto: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Agregar Producto</title>
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
            background-color: #28A745;
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
        <h2>Agregar Nuevo Producto</h2>
        
        <?php if(isset($error)): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="campo">
                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div class="campo">
                <label for="precio">Precio:</label>
                <input type="number" id="precio" name="precio" step="0.01" min="0" required>
            </div>

            <div class="campo">
                <label for="categoria">Categoría:</label>
                <select id="categoria" name="categoria" required>
                    <option value="pizza">Pizza</option>
                    <option value="bebida">Bebida</option>
                </select>
            </div>

            <button type="submit" class="btn">Agregar Producto</button>
        </form>
        
        <p><a href="productos.php">Volver al listado</a></p>
    </div>
</body>
</html>
<?php
$conexion->close();
?>