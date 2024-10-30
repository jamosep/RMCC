<%@page contentType="text/html" pageEncoding="UTF-8"%>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Bienvenido a RMCC</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .welcome-container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            text-align: center;
            max-width: 500px;
            width: 90%;
        }
        h1 {
            color: #2196F3;
            margin-bottom: 20px;
        }
        .welcome-message {
            color: #666;
            margin-bottom: 20px;
        }
        .loading {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #2196F3;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>
    <script>
        window.onload = function() {
            // Obtener la URL de redirección de la sesión
            var redirectUrl = '<%= session.getAttribute("finalRedirect") %>';
            
            // Redirigir después de 3 segundos
            setTimeout(function() {
                window.location.href = redirectUrl;
            }, 3000);
        }
    </script>
</head>
<body>
    <div class="welcome-container">
        <h1>¡Bienvenido a RMCC!</h1>
        <div class="welcome-message">
            <p>¡Hola, <%= session.getAttribute("username") %>!</p>
            <p>Estamos preparando tu sesión...</p>
        </div>
        <div class="loading"></div>
    </div>
</body>
</html>