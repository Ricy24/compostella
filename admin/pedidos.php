<?php

class Pedidos {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener todos los pedidos
    public function obtenerPedidos() {
        $sql = "SELECT * FROM pedidos"; // Cambia esto con el nombre de tu tabla
        $resultado = $this->conexion->query($sql);

        $pedidos = [];
        while ($row = $resultado->fetch_assoc()) {
            $pedidos[] = $row;
        }

        return $pedidos;
    }

    // Actualizar un pedido
    public function actualizarPedido($id, $nombre, $mes, $total) {
        $sql = "UPDATE pedidos SET nombre = ?, mes = ?, total = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssdi", $nombre, $mes, $total, $id);
        return $stmt->execute();
    }

    // Eliminar un pedido
    public function eliminarPedido($id) {
        $sql = "DELETE FROM pedidos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>
