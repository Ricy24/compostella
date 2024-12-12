<?php

class Productos {
    private $conexion;

    public function __construct($conexion) {
        $this->conexion = $conexion;
    }

    // Obtener todos los productos
    public function obtenerProductos() {
        $sql = "SELECT * FROM productos"; 
        $resultado = $this->conexion->query($sql);

        $productos = [];
        while ($row = $resultado->fetch_assoc()) {
            $productos[] = $row;
        }

        return $productos;
    }

    // Obtener un producto por su ID
    public function obtenerProductoPorId($id) {
        $sql = "SELECT * FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $resultado = $stmt->get_result();
        return $resultado->fetch_assoc();
    }

    // Agregar un nuevo producto
    public function agregarProducto($nombre, $descripcion, $precio, $stock) {
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock) VALUES (?, ?, ?, ?)";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssdi", $nombre, $descripcion, $precio, $stock);
        return $stmt->execute();
    }

    // Actualizar un producto y redirigir a index.php
    public function actualizarProducto($id, $nombre, $descripcion, $precio, $stock) {
        $sql = "UPDATE productos SET nombre = ?, descripcion = ?, precio = ?, stock = ? WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("ssdis", $nombre, $descripcion, $precio, $stock, $id);
        
        if ($stmt->execute()) {
            // Redirige a index.php después de la actualización exitosa
            header("Location: index.php?producto_actualizado=true");
            exit();
        } else {
            // Si hay un error, redirige a index.php con un error
            header("Location: index.php?producto_actualizado=false");
            exit();
        }
    }

    // Eliminar un producto
    public function eliminarProducto($id) {
        $sql = "DELETE FROM productos WHERE id = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }
}

?>
