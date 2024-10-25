<?php
// Conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rmcc_entradasmateriales";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para validar y limpiar la entrada
function validar_entrada($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}

// Esta parte solo se ejecuta si se envía el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener y validar los datos del formulario
    $materia_prima_id = validar_entrada($_POST['materia_prima_id']);
    $limites_especificacion = validar_entrada($_POST['limitesEspecificacion']);
    $apariencia = validar_entrada($_POST['apariencia']);
    $ph = isset($_POST['ph']) ? validar_entrada($_POST['ph']) : '';
    $densidad = isset($_POST['densidad']) ? validar_entrada($_POST['densidad']) : '';
    $concentracion = isset($_POST['concentracion']) ? validar_entrada($_POST['concentracion']) : '';
    $observaciones = isset($_POST['observaciones']) ? validar_entrada($_POST['observaciones']) : '';

    // Convertir campos vacíos a 'N/A'
    $ph = $ph === '' ? 'N/A' : $ph;
    $densidad = $densidad === '' ? 'N/A': $densidad;
    $concentracion = $concentracion === '' ? 'N/A' :$concentracion;
    $observaciones = $observaciones === '' ? 'N/A' : $observaciones;

    // Preparar y ejecutar la consulta SQL
    $sql = "INSERT INTO control_calidad (materia_prima_id, limites_especificacion, apariencia, ph, densidad, concentracion, observaciones) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("issddds", $materia_prima_id, $limites_especificacion, $apariencia, $ph, $densidad, $concentracion, $observaciones);
        
        if ($stmt->execute()) {
            echo "
        <div class='alert alert-success'>
            <strong>Registro de calidad realizado exitosamente</strong><br>
            <small style='font-size: 14px; color: #666;'>Redirigiendo a la página principal...</small>
        </div>
        <script>
            setTimeout(function(){
                window.location.href = 'Index.php';
            }, 2000); // Redirige después de 2 segundos
        </script>
    ";
        } else {
            echo "Error al insertar el registro: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $conn->error;
    }

    $conn->close();
    exit; // Terminar la ejecución aquí para evitar que se renderice el resto del HTML
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Calidad</title>
    <style>
        /* Estilos para el modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h2>Registro de Calidad</h2>
    <form id="calidadForm">
        <!-- Aquí van los campos del formulario -->
        <input type="text" name="limitesEspecificacion" required>
    <input type="text" name="apariencia" required>
    <input type="number" name="ph" step="0.01">
    <input type="number" name="densidad" step="0.0001">
    <input type="number" name="concentracion" step="0.01">
    <textarea name="observaciones"></textarea>
    <button type="submit">Guardar</button>
    </form>

    <!-- Modal -->
    <div id="myModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <p id="modalMessage"></p>
            <button id="modalOk">Aceptar</button>
        </div>
    </div>

    <script>
        document.getElementById('calidadForm').addEventListener('submit', function(e) {
            e.preventDefault();
            var formData = new FormData(this);

            fetch('registrar_calidad.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                var modal = document.getElementById('myModal');
                var span = document.getElementsByClassName("close")[0];
                var modalMessage = document.getElementById('modalMessage');
                var modalOk = document.getElementById('modalOk');

                if (data === 'success') {
                    modalMessage.textContent = "Registro guardado correctamente";
                } else {
                    modalMessage.textContent = "Error al guardar el registro: " + data;
                }

                modal.style.display = "block";

                span.onclick = function() {
                    modal.style.display = "none";
                }

                modalOk.onclick = function() {
                    modal.style.display = "none";
                    if (data === 'success') {
                        document.getElementById('calidadForm').reset(); // Opcional: limpiar el formulario
                    }
                }

                window.onclick = function(event) {
                    if (event.target == modal) {
                        modal.style.display = "none";
                    }
                }
            })
            .catch((error) => {
                console.error('Error:', error);
            });
        });
    </script>
</body>
</html>