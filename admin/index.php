<?php
// Incluir la clase Pedidos
include_once 'pedidos.php';
include_once 'productos.php';

// Configuración de conexión
$host = 'localhost';
$usuario = 'root';  // Cambia según tu configuración
$contrasena = '';   // Cambia según tu configuración
$base_de_datos = 'pizzeria';

// Conexión a la base de datos
$conexion = new mysqli($host, $usuario, $contrasena, $base_de_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// [Mantén las clases Pedidos y Productos igual que en el código anterior]

// Controlador principal
class AdminPanel {
    private $conexion;
    private $pedidos;
    private $productos;

    public function __construct($conexion) {
        $this->conexion = $conexion;
        $this->pedidos = new Pedidos($conexion);
        $this->productos = new Productos($conexion);
    }

    // Procesar acciones
    public function procesarAccion() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $accion = $_POST['accion'] ?? '';

            switch ($accion) {
                // Acciones de Pedidos
                case 'actualizar_pedido':
                    $resultado = $this->pedidos->actualizarPedido(
                        $_POST['id'], 
                        $_POST['nombre'], 
                        $_POST['mes'], 
                        $_POST['total']
                    );
                    break;
                
                case 'eliminar_pedido':
                    $resultado = $this->pedidos->eliminarPedido($_POST['id']);
                    break;

                // Acciones de Productos
                case 'agregar_producto':
                    header("Location: agregar_producto.php");
                    exit();
                
                case 'eliminar_producto':
                    header("Location: eliminar_productos.php?id=" . $_POST['id']);
                    exit();
            }
        }
    }

    // Renderizar interfaz de administración
    public function renderizarPanel() {
        // Obtener datos
        $pedidos = $this->pedidos->obtenerPedidos();
        $productos = $this->productos->obtenerProductos();
        ?>
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Panel de Administración - Pizzería</title>
            <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
            <style>
                :root {
                    --primary-color: #e74c3c;
                    --secondary-color: #c0392b;
                    --background-color: #f8f4f1;
                    --text-color: #333;
                }

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Roboto', sans-serif;
                    background-color: var(--background-color);
                    color: var(--text-color);
                    line-height: 1.6;
                }

                .container {
                    width: 95%;
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 20px;
                }

                .admin-header {
                    background-color: var(--primary-color);
                    color: white;
                    padding: 15px 0;
                    text-align: center;
                    margin-bottom: 20px;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
                }

                .admin-header h1 {
                    display: flex;
                    align-items: center;
                    justify-content: center;
                }

                .admin-header h1::before {
                    content: '🍕';
                    margin-right: 15px;
                    font-size: 1.5em;
                }

                .seccion {
                    background: white;
                    border-radius: 8px;
                    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
                    padding: 20px;
                    margin-bottom: 20px;
                }

                .seccion h2 {
                    border-bottom: 3px solid var(--primary-color);
                    padding-bottom: 10px;
                    margin-bottom: 15px;
                    color: var(--secondary-color);
                }

                table {
                    width: 100%;
                    border-collapse: collapse;
                }

                table th {
                    background-color: var(--primary-color);
                    color: white;
                    padding: 12px;
                    text-align: left;
                }

                table td {
                    padding: 10px;
                    border-bottom: 1px solid #ddd;
                }

                table tr:nth-child(even) {
                    background-color: #f2f2f2;
                }

                .btn {
                    display: inline-block;
                    padding: 8px 15px;
                    border-radius: 4px;
                    text-decoration: none;
                    font-weight: bold;
                    transition: background-color 0.3s ease;
                    cursor: pointer;
                    margin: 2px;
                }

                .btn-warning {
                    background-color: #f39c12;
                    color: white;
                }

                .btn-danger {
                    background-color: var(--primary-color);
                    color: white;
                }

                .btn-success {
                    background-color: #2ecc71;
                    color: white;
                }

                .btn:hover {
                    opacity: 0.9;
                }

                @media (max-width: 768px) {
                    .container {
                        width: 100%;
                        padding: 10px;
                    }

                    table {
                        font-size: 14px;
                    }
                }
            </style>

<?php
// Verificar si hay un mensaje de éxito o error en la URL
if (isset($_GET['producto_actualizado'])) {
    if ($_GET['producto_actualizado'] == 'true') {
        echo "<div class='alert alert-success'>¡Producto actualizado con éxito!</div>";
    } else {
        echo "<div class='alert alert-danger'>Hubo un error al actualizar el producto.</div>";
    }
}
?>

        </head>
        <body>
            <div class="admin-header">
                <div class="container">
                    <h1>Panel de Administración de Pizzería</h1>
                </div>
            </div>

            <div class="container">
                <!-- Sección de Pedidos -->
                <div class="seccion">
                    <h2>Gestión de Pedidos</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pedidos as $pedido): ?>
                            <tr>
                                <td><?= $pedido['id'] ?></td>
                                <td><?= htmlspecialchars($pedido['nombre']) ?></td>
                                <td>$<?= number_format($pedido['total'], 2) ?></td>
                                <td><?= $pedido['fecha'] ?></td>
                                <td>
                                    <a href="editar_pedido.php?id=<?= $pedido['id'] ?>" class="btn btn-warning">Editar</a>
                                    <button class="btn btn-danger" onclick="eliminarPedido(<?= $pedido['id'] ?>)">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Sección de Productos -->
                <div class="seccion">
                    <h2>Gestión de Productos</h2>
                    <a href="agregar_producto.php" class="btn btn-success" style="margin-bottom: 15px;">Agregar Nuevo Producto</a>
                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Categoría</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?= $producto['id'] ?></td>
                                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                                <td>$<?= number_format($producto['precio'], 2) ?></td>
                                <td><?= $producto['categoria'] ?></td>
                                <td>
                                    <a href="editar_producto.php?id=<?= $producto['id'] ?>" class="btn btn-warning">Editar</a>
                                    <button class="btn btn-danger" onclick="eliminarProducto(<?= $producto['id'] ?>)">Eliminar</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <script>
                function eliminarPedido(id) {
                    if (confirm('¿Estás seguro de eliminar este pedido?')) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        
                        let inputAccion = document.createElement('input');
                        inputAccion.type = 'hidden';
                        inputAccion.name = 'accion';
                        inputAccion.value = 'eliminar_pedido';
                        form.appendChild(inputAccion);

                        let inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = 'id';
                        inputId.value = id;
                        form.appendChild(inputId);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }

                function eliminarProducto(id) {
                    if (confirm('¿Estás seguro de eliminar este producto?')) {
                        let form = document.createElement('form');
                        form.method = 'POST';
                        form.action = '';
                        
                        let inputAccion = document.createElement('input');
                        inputAccion.type = 'hidden';
                        inputAccion.name = 'accion';
                        inputAccion.value = 'eliminar_producto';
                        form.appendChild(inputAccion);

                        let inputId = document.createElement('input');
                        inputId.type = 'hidden';
                        inputId.name = 'id';
                        inputId.value = id;
                        form.appendChild(inputId);

                        document.body.appendChild(form);
                        form.submit();
                    }
                }
            </script>
        </body>
        </html>
        <?php
    }

    // Método principal
    public function iniciar() {
        $this->procesarAccion();
        $this->renderizarPanel();
    }
}

// Inicializar y ejecutar
$adminPanel = new AdminPanel($conexion);
$adminPanel->iniciar();
?>
