# 🍕 Pizzería Compostella

<div align="center">

![PHP](https://img.shields.io/badge/PHP-777BB4?style=flat-square&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=flat-square&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=flat-square&logo=javascript&logoColor=black)

</div>

> Sistema de **pedidos de pizzería** con seguimiento de estados y panel de administración.

---

## 📋 Descripción

Aplicación web para una pizzería: los clientes consultan el menú, hacen sus pedidos y registran sus datos; el equipo de cocina y administración gestiona los estados de cada pedido en tiempo real.

## ✨ Características

- 🍕 Catálogo / menú de productos
- 🛒 Registro de pedidos con datos del cliente
- 🔄 Cambio de estado del pedido (`estado.php`, `actualizar_estado.php`)
- 🧑‍🍳 Panel de administración y cocina
- ✅ Confirmación de pedidos (`confirmacion.php`)
- 🗑️ Eliminación de pedidos (`eliminar.php`)

## 🛠️ Stack

`PHP` · `MySQL` · `JavaScript` · `HTML/CSS`

## 🚀 Instalación

### Requisitos

- [XAMPP](https://www.apachefriends.org/) (o cualquier servidor con PHP 8+ y MySQL)

### Pasos

1. **Clonar** en `htdocs`:

```bash
cd C:/xampp/htdocs
git clone https://github.com/Ricy24/compostella.git
```

2. **Crear la base de datos** en `http://localhost/phpmyadmin` e importar el esquema.

3. **Configurar la conexión** en [`db/db.php`](db/db.php):

```php
$host = "localhost";
$user = "root";
$pass = "";
$db   = "compostella";
```

4. **Ejecutar**: abre `http://localhost/compostella/`

## 📁 Estructura

```text
compostella/
├── index.php               # Menú principal
├── guardar_cliente.php     # Registro de clientes
├── confirmacion.php        # Confirmación del pedido
├── estado.php              # Seguimiento de estados
├── actualizar_estado.php   # Actualización (admin/cocina)
├── eliminar.php            # Baja de pedidos
├── db/db.php               # Conexión a MySQL
├── admin/                  # Panel de administración
└── img/                    # Imágenes
```

## 📄 Licencia

Proyecto desarrollado con **fines académicos**.
