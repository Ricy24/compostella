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
    die(json_encode(['error' => "Connection failed: " . $e->getMessage()]));
}

// Sanitize and validate order ID
$pedido_id = filter_input(INPUT_GET, 'pedido_id', FILTER_VALIDATE_INT);

if (!$pedido_id) {
    die(json_encode(['error' => 'ID de pedido no válido']));
}

try {
    // Get the current status of the order
    $stmt_current = $conn->prepare("
        SELECT estado 
        FROM estado_pedido 
        WHERE pedido_id = :pedido_id 
        ORDER BY fecha DESC 
        LIMIT 1
    ");
    $stmt_current->execute(['pedido_id' => $pedido_id]);
    $current_status = $stmt_current->fetchColumn();

    // Determine the next status
    $next_status = $current_status;
    $statuses = ['Recibido', 'En el horno', 'Lista'];
    $current_index = array_search($current_status, $statuses);

    if ($current_index !== false && $current_index < count($statuses) - 1) {
        $next_status = $statuses[$current_index + 1];
    }

    // Insert the new status
    $stmt_insert = $conn->prepare("
        INSERT INTO estado_pedido (pedido_id, estado, fecha) 
        VALUES (:pedido_id, :estado, NOW())
    ");
    $stmt_insert->execute([
        'pedido_id' => $pedido_id,
        'estado' => $next_status
    ]);

    // Fetch the latest status information
    $stmt_latest = $conn->prepare("
        SELECT estado, fecha 
        FROM estado_pedido 
        WHERE pedido_id = :pedido_id 
        ORDER BY fecha DESC 
        LIMIT 1
    ");
    $stmt_latest->execute(['pedido_id' => $pedido_id]);
    $latest_status = $stmt_latest->fetch(PDO::FETCH_ASSOC);

    // Return the latest status as JSON
    echo json_encode([
        'estado' => $latest_status['estado'],
        'fecha' => $latest_status['fecha']
    ]);

} catch(PDOException $e) {
    error_log("Error updating order status: " . $e->getMessage());
    die(json_encode(['error' => 'Error al actualizar el estado del pedido']));
}