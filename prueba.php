<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Menú - Pizzería</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        <h1 class="text-center mb-4">Bienvenido, <?php echo htmlspecialchars($nombre); ?> (Mesa: <?php echo htmlspecialchars($mesa); ?>)</h1>

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

        <!-- Resto del código del carrito... -->
    </div>

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
</body>
</html>