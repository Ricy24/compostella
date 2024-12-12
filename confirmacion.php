<?php
session_start(); // Start session to access cart data

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

// Obtener los datos del pedido
$nombre = isset($_GET['nombre']) ? $conn->real_escape_string($_GET['nombre']) : '';
$mesa = isset($_GET['mesa']) ? $conn->real_escape_string($_GET['mesa']) : '';

// Verificar si hay productos en el carrito
$productos = isset($_SESSION['cart']) ? $_SESSION['cart'] : [];

if (empty($productos)) {
    die("No hay productos en el pedido");
}

// Calcular total del pedido
$total_pedido = 0;
foreach ($productos as $producto) {
    $total_pedido += $producto['precio'] * $producto['cantidad'];
}

// Insertar el pedido en la tabla `pedidos`
$sql_pedido = "INSERT INTO pedidos (nombre, mesa, total) VALUES ('$nombre', $mesa, $total_pedido)";

if ($conn->query($sql_pedido) === TRUE) {
    $pedido_id = $conn->insert_id;

    // Insertar productos del pedido
    foreach ($productos as $producto) {
        $producto_id = $conn->real_escape_string($producto['id']);
        $cantidad = $conn->real_escape_string($producto['cantidad']);
        $precio = $conn->real_escape_string($producto['precio']);

        $sql_detalle = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio) 
                        VALUES ($pedido_id, $producto_id, $cantidad, $precio)";
        $conn->query($sql_detalle);
    }

    // Insertar el estado inicial del pedido en la tabla `estado_pedido`
    $estado_inicial = "Recibido"; // Estado inicial del pedido
    $fecha = date("Y-m-d H:i:s");

    // Insertar estado en la tabla `estado_pedido`
    $sql_estado = "INSERT INTO estado_pedido (pedido_id, estado, fecha) VALUES ($pedido_id, '$estado_inicial', '$fecha')";
    
    if ($conn->query($sql_estado) === TRUE) {
        $mensaje_estado = "Pedido confirmado";
        $estado = $estado_inicial; // Estado que se mostrará en la página
    } else {
        $mensaje_estado = "Error al insertar estado del pedido";
        $estado = "No disponible";
    }

    // Limpiar el carrito después de confirmar el pedido
    unset($_SESSION['cart']);

} else {
    // Si hubo error en la inserción del pedido
    $mensaje_estado = "Error al confirmar el pedido";
    $estado = "No disponible";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Confirmación de Pedido</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        :root {
            --primary-color: #007bff;
            --background-color: #f4f4f9;
            --text-color: #333;
            --light-text: #888;
        }
        
        body {
            font-family: 'Roboto', sans-serif;
            background-color: var(--background-color);
            line-height: 1.6;
            color: var(--text-color);
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 15px;
        }
        
        .container {
            width: 100%;
            max-width: 600px;
            margin: 20px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            padding: 25px;
            position: relative;
            overflow: hidden;
        }
        
        .chulo {
            text-align: center;
            margin-bottom: 20px;
        }
        
        .chulo img {
            width: 70px;
            height: auto;
            max-width: 100%;
        }
        
        .mensaje {
            text-align: center;
            font-size: 24px;
            font-weight: bold;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        .informacion {
            text-align: center;
            font-size: 14px;
            color: var(--light-text);
            margin-bottom: 20px;
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
        }
        
        .tabla-pedido {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 20px;
        }
        
        .tabla-pedido th,
        .tabla-pedido td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #eee;
        }
        
        .tabla-pedido th {
            background-color: #f8f9fa;
            font-weight: bold;
            text-transform: uppercase;
            font-size: 12px;
            color: #6c757d;
        }
        
        .tabla-pedido tr:last-child td {
            border-bottom: none;
        }
        
        .total {
            text-align: right;
            font-size: 18px;
            font-weight: bold;
            margin-top: 15px;
            padding: 10px 0;
            border-top: 2px solid #eee;
        }
        
        .botones {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-top: 20px;
        }
        
        .boton {
            display: inline-block;
            padding: 12px 20px;
            background-color: var(--primary-color);
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            transition: background-color 0.3s ease;
        }
        
        .boton:hover {
            background-color: #0056b3;
        }
        
        @media (max-width: 480px) {
            .container {
                width: 100%;
                margin: 10px auto;
                padding: 15px;
                border-radius: 0;
                box-shadow: none;
            }
            
            .tabla-pedido {
                font-size: 14px;
            }
            
            .tabla-pedido th,
            .tabla-pedido td {
                padding: 8px;
            }
            
            .mensaje {
                font-size: 20px;
            }
            
            .total {
                font-size: 16px;
            }
        }
        
        /* Animaciones */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="chulo">
            <img src="img/chulo.png" alt="Chulo Verde">
        </div>

        <div class="mensaje">
            <?php echo $mensaje_estado; ?>
        </div>

        <div class="informacion">
            <p><strong>Fecha:</strong> <?php echo $fecha; ?></p>
            <p><strong>Nombre:</strong> <?php echo htmlspecialchars($nombre); ?></p>
            <p><strong>Mesa:</strong> <?php echo htmlspecialchars($mesa); ?></p>
        </div>

        <table class="tabla-pedido">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($producto['cantidad']); ?></td>
                        <td>$<?php echo number_format($producto['precio'], 2); ?></td>
                        <td>$<?php echo number_format($producto['precio'] * $producto['cantidad'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total">
            Total del Pedido: $<?php echo number_format($total_pedido, 2); ?>
        </div>

        <div class="botones">
            <a href="estado.php?pedido_id=<?php echo $pedido_id; ?>" class="boton">Estado de tu pedido</a>
            <a href="index.php" class="boton">Volver al inicio</a>
        </div>
    </div>

    <script>
        // Prevent double form submission


        // Disable zooming on mobile
        document.addEventListener('touchmove', function(event) {
            if (event.scale !== 1) {
                event.preventDefault();
            }
        }, { passive: false });
    </script>
</body>
</html>