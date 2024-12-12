<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "pizzeria";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Sanitize and validate order ID
$pedido_id = filter_input(INPUT_GET, 'pedido_id', FILTER_VALIDATE_INT);

if ($pedido_id) {
    try {
        // Prepared statement to get the latest order status
        $stmt_estado = $conn->prepare("
            SELECT estado, fecha 
            FROM estado_pedido 
            WHERE pedido_id = :pedido_id 
            ORDER BY fecha DESC 
            LIMIT 1
        ");
        $stmt_estado->execute(['pedido_id' => $pedido_id]);
        $estado_info = $stmt_estado->fetch(PDO::FETCH_ASSOC);

        // Prepared statement for customer name
        $stmt_cliente = $conn->prepare("SELECT nombre FROM pedidos WHERE id = :pedido_id");
        $stmt_cliente->execute(['pedido_id' => $pedido_id]);
        $cliente_info = $stmt_cliente->fetch(PDO::FETCH_ASSOC);

        // Set default values if no data found
        $estado = $estado_info ? $estado_info['estado'] : "Recibido";
        $fecha = $estado_info ? $estado_info['fecha'] : date('Y-m-d H:i:s');
        $nombre = $cliente_info ? $cliente_info['nombre'] : "Cliente no identificado";

        // If no status exists, insert initial status
        if (!$estado_info) {
            $stmt_inicial = $conn->prepare("
                INSERT INTO estado_pedido (pedido_id, estado, fecha) 
                VALUES (:pedido_id, 'Recibido', NOW())
            ");
            $stmt_inicial->execute(['pedido_id' => $pedido_id]);
        }

    } catch(PDOException $e) {
        error_log("Error fetching order details: " . $e->getMessage());
        $estado = $fecha = "Error al cargar información";
        $nombre = "Cliente no identificado";
    }
} else {
    // Handle case where no valid order ID is provided
    die("ID de pedido no válido");
}

// Count pending orders
try {
    $stmt_pendientes = $conn->query("SELECT COUNT(*) as pendientes FROM estado_pedido WHERE estado = 'Recibido' OR estado = 'En el horno'");
    $pedidos_pendientes = $stmt_pendientes->fetchColumn();
} catch(PDOException $e) {
    error_log("Error counting pending orders: " . $e->getMessage());
    $pedidos_pendientes = 0;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Seguimiento de Pedido</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ... (previous CSS remains the same) ... */
    </style>
</head>
<body>
    <div class="order-tracking-card">
        <div class="order-status-header">
            <h2 class="mb-0">Seguimiento de Pedido #<?php echo htmlspecialchars($pedido_id); ?></h2>
        </div>

        <div class="order-details">
            <div>
                <h4 class="text-muted"><?php echo htmlspecialchars($nombre); ?></h4>
                <p id="orderStatus" class="h5 text-primary">
                    <?php echo htmlspecialchars($estado); ?>
                </p>
            </div>
            <div>
                <small class="text-muted" id="orderDate">
                    <?php echo htmlspecialchars($fecha); ?>
                </small>
            </div>
        </div>

        <div class="status-progress">
            <div class="status-progress-bar" id="progressBar"></div>
        </div>

        <div class="status-steps">
            <div class="status-step" id="receivedStep">Recibido</div>
            <div class="status-step" id="preparingStep">En el horno</div>
            <div class="status-step" id="readyStep">Lista</div>
        </div>

        <div class="estimated-time">
            <i class="fas fa-clock text-primary"></i>
            Tiempo estimado: 15-20 minutos
        </div>
    </div>

    <div id="confetti" class="confetti"></div>

    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.5.1/dist/confetti.browser.min.js"></script>
    <script>
        function updateOrderStatus() {
            const pedidoId = <?php echo json_encode($pedido_id); ?>;
            
            fetch(`actualizar_estado.php?pedido_id=${pedidoId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('orderStatus').textContent = data.estado;
                    document.getElementById('orderDate').textContent = data.fecha;

                    const progressBar = document.getElementById('progressBar');
                    const steps = ['receivedStep', 'preparingStep', 'readyStep'];
                    const statuses = ['Recibido', 'En el horno', 'Lista'];

                    steps.forEach((stepId, index) => {
                        const step = document.getElementById(stepId);
                        step.classList.remove('active');
                        
                        if (data.estado === statuses[index]) {
                            step.classList.add('active');
                            progressBar.style.width = `${(index + 1) * 33.33}%`;

                            if (data.estado === 'Lista') {
                                confetti({
                                    particleCount: 100,
                                    spread: 70,
                                    origin: { y: 0.6 }
                                });
                            }
                        }
                    });
                })
                .catch(error => console.error('Error:', error));
        }

        // Update status every 5 seconds
        setInterval(updateOrderStatus, 5000);

        // Initial status setup
        document.addEventListener('DOMContentLoaded', () => {
            const initialStatus = <?php echo json_encode($estado); ?>;
            const steps = ['receivedStep', 'preparingStep', 'readyStep'];
            const statuses = ['Recibido', 'En el horno', 'Lista'];
            const progressBar = document.getElementById('progressBar');

            steps.forEach((stepId, index) => {
                const step = document.getElementById(stepId);
                
                if (initialStatus === statuses[index]) {
                    step.classList.add('active');
                    progressBar.style.width = `${(index + 1) * 33.33}%`;
                }
            });
        });
    </script>
</body>
</html>

        <style>
        :root {
            --primary-color: #ff6b00;
            --secondary-color: #4CAF50;
            --background-light: #f8f9fa;
        }

        body {
            font-family: 'Nunito', sans-serif;
            background-color: var(--background-light);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 15px;
        }

        .order-tracking-card {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            max-width: 500px;
            width: 100%;
            padding: 25px;
            text-align: center;
        }

        .order-status-header {
            background-color: var(--primary-color);
            color: white;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        .order-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .status-progress {
            width: 100%;
            height: 15px;
            background-color: #e0e0e0;
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .status-progress-bar {
            width: 0;
            height: 100%;
            background-color: var(--secondary-color);
            transition: width 0.5s ease;
        }

        .status-steps {
            display: flex;
            justify-content: space-between;
        }

        .status-step {
            flex: 1;
            text-align: center;
            padding: 10px;
            position: relative;
            color: #888;
        }

        .status-step::before {
            content: '';
            position: absolute;
            width: 15px;
            height: 15px;
            background-color: #e0e0e0;
            border-radius: 50%;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
        }

        .status-step.active {
            color: var(--primary-color);
            font-weight: bold;
        }

        .status-step.active::before {
            background-color: var(--primary-color);
        }

        .estimated-time {
            background-color: #e6f3ff;
            padding: 10px;
            border-radius: 8px;
            margin-top: 15px;
        }

        @media (max-width: 480px) {
            .order-tracking-card {
                padding: 15px;
            }

            .status-steps {
                flex-direction: column;
            }

            .status-step {
                margin-bottom: 10px;
            }
        }

        .confetti {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            z-index: 9999;
        }
    </style>
