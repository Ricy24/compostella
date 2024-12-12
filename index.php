<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pizzería</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #ff6b6b 0%, #ff4e4e 100%);
            padding: 20px;
        }

        .container {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 360px;
            padding: 20px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .logo {
            width: 100px;
            height: 100px;
            background-color: #ff4e4e;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 40px;
        }

        h1 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.5rem;
        }

        p {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .btn {
            background-color: #ff4e4e;
            color: white;
            border: none;
            padding: 15px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s ease;
            width: 100%;
            margin-top: 15px;
        }

        .btn:hover {
            background-color: #ff6b6b;
        }

        input {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            font-size: 1rem;
            background-color: #f9f9f9;
        }

        .mesa-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 10px;
            margin-top: 15px;
        }

        .mesa-btn {
            background-color: #f0f0f0;
            border: none;
            padding: 15px;
            border-radius: 10px;
            cursor: pointer;
            transition: background-color 0.3s ease;
            font-size: 1rem;
        }

        .mesa-btn:hover {
            background-color: #ff4e4e;
            color: white;
        }

        .step {
            display: none;
        }

        .step.active {
            display: block;
            animation: slideIn 0.5s ease;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">🍕</div>
        <div id="step1" class="step active">
            <h1>Bienvenido a Pizzería Compostella</h1>
            <p>Ingresa tu nombre para comenzar</p>
            <input type="text" id="nombre" placeholder="Tu nombre" required>
            <button class="btn" onclick="goToStep(2)">Siguiente</button>
        </div>
        <div id="step2" class="step">
            <h1>Selecciona tu mesa</h1>
            <div class="mesa-grid">
                <button class="mesa-btn" onclick="submitForm(1)">Mesa 1</button>
                <button class="mesa-btn" onclick="submitForm(2)">Mesa 2</button>
                <button class="mesa-btn" onclick="submitForm(3)">Mesa 3</button>
                <button class="mesa-btn" onclick="submitForm(4)">Mesa 4</button>
                <button class="mesa-btn" onclick="submitForm(5)">Mesa 5</button>
                <button class="mesa-btn" onclick="submitForm(6)">Mesa 6</button>
            </div>
        </div>
        <form id="form" action="guardar_cliente.php" method="POST" style="display:none;">
            <input type="hidden" id="hidden-nombre" name="nombre">
            <input type="hidden" id="hidden-mesa" name="mesa">
        </form>
    </div>

    <script>
        function goToStep(step) {
            document.querySelectorAll('.step').forEach(s => s.classList.remove('active'));
            document.getElementById('step' + step).classList.add('active');
        }

        function submitForm(mesa) {
            const nombre = document.getElementById('nombre').value;
            if (!nombre) {
                alert('Por favor, ingresa tu nombre.');
                return;
            }
            document.getElementById('hidden-nombre').value = nombre;
            document.getElementById('hidden-mesa').value = mesa;
            document.getElementById('form').submit();
        }
    </script>
</body>
</html>
