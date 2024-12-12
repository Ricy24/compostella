<?php
session_start(); // Start session to manage cart across page loads

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

// Obtener los datos del cliente de la URL
$nombre = isset($_GET['nombre']) ? $conn->real_escape_string($_GET['nombre']) : '';
$mesa = isset($_GET['mesa']) ? $conn->real_escape_string($_GET['mesa']) : '';

// Inicializar el carrito si no existe
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// Agregar producto al carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_to_cart'])) {
        $producto_id = $conn->real_escape_string($_POST['producto_id']);
        $cantidad = intval($_POST['cantidad']);

        // Obtener detalles del producto
        $sql = "SELECT * FROM productos WHERE id = $producto_id";
        $result = $conn->query($sql);
        $producto = $result->fetch_assoc();

        if ($producto) {
            // Verificar si el producto ya está en el carrito
            $encontrado = false;
            foreach ($_SESSION['cart'] as &$item) {
                if ($item['id'] == $producto_id) {
                    $item['cantidad'] += $cantidad;
                    $encontrado = true;
                    break;
                }
            }

            // Si no está en el carrito, agregarlo
            if (!$encontrado) {
                $_SESSION['cart'][] = [
                    'id' => $producto_id,
                    'nombre' => $producto['nombre'],
                    'precio' => $producto['precio'],
                    'cantidad' => $cantidad
                ];
            }
        }
    }

    // Procesar pedido final
    if (isset($_POST['realizar_pedido'])) {
        // Redirigir a la página de confirmación con los productos del carrito
        $productos_pedido = json_encode($_SESSION['cart']);
        header("Location: confirmacion.php?nombre=" . urlencode($nombre) . "&mesa=" . urlencode($mesa) . "&productos=" . urlencode($productos_pedido));
        exit();
    }

    // Eliminar producto del carrito
    if (isset($_POST['eliminar_producto'])) {
        $producto_id = $conn->real_escape_string($_POST['producto_id']);
        foreach ($_SESSION['cart'] as $key => $item) {
            if ($item['id'] == $producto_id) {
                unset($_SESSION['cart'][$key]);
                break;
            }
        }
        // Reindexar el array
        $_SESSION['cart'] = array_values($_SESSION['cart']);
    }
}

// Obtener productos de la base de datos
$sql_productos = "SELECT * FROM productos";
$result_productos = $conn->query($sql_productos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú - Pizzería</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .carrito {
            margin-top: 20px;
            border: 1px solid #ddd;
            padding: 10px;
        }

        body {
            background-color: #f4f4f4;
            font-family: 'Arial', sans-serif;
        }
        .container {
            max-width: 800px;
            padding: 20px;
        }
        .productos {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .producto-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            transition: transform 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .producto-card:hover {
            transform: scale(1.03);
        }
        .producto-card h3 {
            color: #d32f2f;
            margin-bottom: 15px;
            font-weight: bold;
        }
        .producto-price {
            font-size: 1.2rem;
            color: #4CAF50;
            margin-bottom: 15px;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background-color: #f0f0f0;
            border-radius: 30px;
            padding: 5px 10px;
            margin-bottom: 15px;
        }
        .quantity-control button {
            background: none;
            border: none;
            font-size: 1.5rem;
            cursor: pointer;
            color: #d32f2f;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            transition: background-color 0.3s ease;
        }
        .quantity-control button:hover {
            background-color: rgba(211, 47, 47, 0.1);
        }
        .quantity-control input {
            width: 50px;
            text-align: center;
            border: none;
            background: none;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .btn-add-cart {
            width: 100%;
            background-color: #d32f2f;
            border: none;
            padding: 10px;
            border-radius: 30px;
            transition: background-color 0.3s ease;
        }
        .btn-add-cart:hover {
            background-color: #b71c1c;
        }
        .carrito {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            padding: 20px;
            margin-top: 20px;
        }
        @media (max-width: 576px) {
            .productos {
                grid-template-columns: 1fr;
            }
        }


        
    </style>
</head>
<body>
    <div class="container">
        <h1>Bienvenido, <?php echo htmlspecialchars($nombre); ?> (Mesa: <?php echo htmlspecialchars($mesa); ?>)</h1>

        <div class="container">


        <div class="productos">
            <?php while ($producto = $result_productos->fetch_assoc()): ?>
                <div class="producto-card">
                    <h3><?php echo htmlspecialchars($producto['nombre']); ?></h3>
                    <p class="producto-price">$<?php echo number_format($producto['precio'], 2); ?></p>
                    
                    <form method="POST" action="" class="producto-form">
                        <input type="hidden" name="producto_id" value="<?php echo $producto['id']; ?>">
                        
                        <div class="quantity-control">
                            <button type="button" class="quantity-decrease">-</button>
                            <input type="number" name="cantidad" value="1" min="1" max="10" class="quantity-input" readonly>
                            <button type="button" class="quantity-increase">+</button>
                        </div>
                        
                        <button type="submit" name="add_to_cart" class="btn btn-add-cart">
                            Agregar al Carrito
                        </button>
                    </form>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="carrito">
            <h2>Carrito de Compras</h2>
            <?php if (!empty($_SESSION['cart'])): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Total</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_pedido = 0;
                        foreach ($_SESSION['cart'] as $item): 
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_pedido += $subtotal;
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['nombre']); ?></td>
                                <td>$<?php echo number_format($item['precio'], 2); ?></td>
                                <td><?php echo $item['cantidad']; ?></td>
                                <td>$<?php echo number_format($subtotal, 2); ?></td>
                                <td>
                                    <form method="POST" action="">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['id']; ?>">
                                        <button type="submit" name="eliminar_producto">Eliminar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="3">Total:</td>
                            <td>$<?php echo number_format($total_pedido, 2); ?></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>

                <form method="POST" action="">
                    <button type="submit" name="realizar_pedido">Realizar Pedido</button>
                </form>
            <?php else: ?>
                <p>El carrito está vacío</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<script>
        document.addEventListener('DOMContentLoaded', () => {
            // Quantity control logic
            document.querySelectorAll('.producto-form').forEach(form => {
                const decreaseBtn = form.querySelector('.quantity-decrease');
                const increaseBtn = form.querySelector('.quantity-increase');
                const quantityInput = form.querySelector('.quantity-input');

                decreaseBtn.addEventListener('click', () => {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue > 1) {
                        quantityInput.value = currentValue - 1;
                    }
                });

                increaseBtn.addEventListener('click', () => {
                    let currentValue = parseInt(quantityInput.value);
                    if (currentValue < 10) {
                        quantityInput.value = currentValue + 1;
                    }
                });
            });
        });
    </script>

<?php
$conn->close();
?>