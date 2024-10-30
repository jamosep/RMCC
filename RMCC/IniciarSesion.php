<?php
// Asegúrate de que este archivo se guarde como "IniciarSesion.php"
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a RMCC</title>
    <!-- Los estilos permanecen igual... -->
    <style>
        /* Los mismos estilos anteriores */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: system-ui, -apple-system, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        }

        .welcome-container {
            text-align: center;
            padding: 2rem;
            max-width: 800px;
            margin: 0 auto;
        }

        .welcome-text {
            margin: 2rem 0;
            line-height: 1.6;
            color: #2c3e50;
        }

        .question {
            font-size: 1.5rem;
            color: #2c3e50;
            margin: 2rem 0;
            text-align: center;
        }

        .cards-container {
            display: flex;
            justify-content: center;
            gap: 2rem;
            perspective: 1000px;
            min-height: 400px;
            padding: 1rem;
        }

        .card {
            position: relative;
            width: 300px;
            height: 400px;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: all 0.5s ease-in-out;
        }

        .card.hide {
            transform: scale(0);
            width: 0;
            margin: 0;
            opacity: 0;
        }

        .card.expand {
            width: 100%;
            max-width: 500px;
        }

        .card:hover {
            transform: scale(1.05);
        }

        .card.flip {
            transform: rotateY(180deg);
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2rem;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .card-front {
            background-color: #fff;
        }

        .login-card .card-front {
            background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }

        .register-card .card-front {
            background: linear-gradient(45deg, #ff9a9e 0%, #fad0c4 100%);
            color: white;
        }

        .card-back {
            background: white;
            transform: rotateY(180deg);
        }

        .form-container {
            width: 100%;
        }

        .form-group {
            margin-bottom: 1rem;
            text-align: left;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #2c3e50;
        }

        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
        }

        .btn {
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1rem;
            transition: background-color 0.3s;
        }

        .btn-primary {
            background-color: #4facfe;
            color: white;
        }

        .btn-secondary {
            background-color: #ff9a9e;
            color: white;
        }

        .btn-back {
            background-color: #95a5a6;
            color: white;
            margin-top: 1rem;
        }

        .inactive {
            filter: grayscale(100%);
            opacity: 0.7;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 1rem;
        }

        .card-title {
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <div class="welcome-container">
        <h1>Bienvenido a RMCC</h1>
        <div class="welcome-text">
            <p>RMCC (Recepción de Materiales y Control de Calidad) es un sistema integral diseñado para optimizar y gestionar eficientemente los procesos de recepción de materiales y control de calidad en su organización.</p>
            <p>Nuestro software permite:</p>
            <ul>
                <li>Gestión eficiente de la recepción de materiales</li>
                <li>Control y seguimiento de calidad</li>
                <li>Generación de reportes detallados</li>
                <li>Trazabilidad completa de materiales</li>
                <li>Gestión de proveedores y materiales</li>
            </ul>
        </div>
        <div class="question">¿Qué le gustaría hacer?</div>
    </div>

    <div class="cards-container">
        <div class="card login-card">
            <div class="card-face card-front">
                <h2 class="card-title">Iniciar Sesión</h2>
                <p>Accede a tu cuenta existente</p>
            </div>
            <div class="card-face card-back">
                <div class="form-container">
                    <h2 class="card-title">Iniciar Sesión</h2>
                    <form action="index.php" method="POST">
                        <div class="form-group">
                            <label for="login-email">Correo electrónico</label>
                            <input type="email" id="login-email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="login-password">Contraseña</label>
                            <input type="password" id="login-password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                    </form>
                    <button class="btn btn-back" onclick="resetCards()">Regresar</button>
                </div>
            </div>
        </div>

        <div class="card register-card">
            <div class="card-face card-front">
                <h2 class="card-title">Crear Usuario</h2>
                <p>Registra una nueva cuenta</p>
            </div>
            <div class="card-face card-back">
                <div class="form-container">
                    <h2 class="card-title">Crear Usuario</h2>
                    <form>
                        <div class="form-group">
                            <label for="register-name">Nombre completo</label>
                            <input type="text" id="register-name" required>
                        </div>
                        <div class="form-group">
                            <label for="register-email">Correo electrónico</label>
                            <input type="email" id="register-email" required>
                        </div>
                        <div class="form-group">
                            <label for="register-password">Contraseña</label>
                            <input type="password" id="register-password" required>
                        </div>
                        <button type="submit" class="btn btn-secondary">Crear Usuario</button>
                    </form>
                    <button class="btn btn-back" onclick="resetCards()">Regresar</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const loginCard = document.querySelector('.login-card');
        const registerCard = document.querySelector('.register-card');

        function handleCardHover(hoveredCard, otherCard) {
            hoveredCard.addEventListener('mouseenter', () => {
                otherCard.classList.add('inactive');
            });

            hoveredCard.addEventListener('mouseleave', () => {
                otherCard.classList.remove('inactive');
            });
        }

        function handleCardClick(clickedCard, otherCard) {
            clickedCard.addEventListener('click', () => {
                if (!clickedCard.classList.contains('flip')) {
                    clickedCard.classList.add('flip', 'expand');
                    otherCard.classList.add('hide');
                }
            });
        }

        function resetCards() {
            loginCard.classList.remove('flip', 'expand');
            registerCard.classList.remove('flip', 'expand');
            loginCard.classList.remove('hide');
            registerCard.classList.remove('hide');
        }

        handleCardHover(loginCard, registerCard);
        handleCardHover(registerCard, loginCard);
        handleCardClick(loginCard, registerCard);
        handleCardClick(registerCard, loginCard);
    </script>
</body>
</html>