<?php
// Verificar si se recibió el ID
if (!isset($_GET['id'])) {
    die("<div class='alert alert-danger'>ID no proporcionado</div>");
}

$id = $_GET['id'];

// Conexión a la base de datos
try {
    $conn = new mysqli("localhost", "root", "", "rmcc_entradasmateriales");
    
    if ($conn->connect_error) {
        throw new Exception("Error de conexión: " . $conn->connect_error);
    }

    // Procesar el formulario cuando se envía
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $apariencia = $_POST['apariencia'];
        $ph = $_POST['ph'];
        $densidad = $_POST['densidad'];
        $concentracion = $_POST['concentracion'];
        $observaciones = $_POST['observaciones'];
        $id = $_POST['id'];

        $update_sql = "UPDATE control_calidad 
                      SET apariencia = ?, 
                          pH = ?, 
                          densidad = ?, 
                          concentracion = ?, 
                          observaciones = ? 
                      WHERE materia_prima_id = ?";

        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Error en la preparación de la actualización: " . $conn->error);
        }

        $update_stmt->bind_param("sssssi", $apariencia, $ph, $densidad, $concentracion, $observaciones, $id);
        
        if ($update_stmt->execute()) {
            echo "<div class='alert alert-success'>Registro actualizado exitosamente</div>";
            // Redirigir después de 2 segundos
            header("refresh:2;url=consultar_registro.php");
            exit;
        } else {
            throw new Exception("Error al actualizar el registro: " . $update_stmt->error);
        }
    }

    // Preparar la consulta para obtener los datos
    $sql = "SELECT cc.*, rm.NombreMP, rm.materia_prima_id as codigo_mp 
    FROM control_calidad cc 
    LEFT JOIN recepcion_materiales rm ON cc.materia_prima_id = rm.id 
    WHERE cc.materia_prima_id = ?";

    
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        throw new Exception("Error en la preparación de la consulta: " . $conn->error);
    }

    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result) {
        throw new Exception("Error al obtener resultados: " . $stmt->error);
    }

} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Control de Calidad</title>
    <style>
        .header-section {
            background-color: #007bff;
            color: white;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .form-edicion-calidad {
            max-width: 800px;
            margin: 2rem auto;
            padding: 2rem;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .registro-info {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            color: #666;
            padding: 0 10px;
        }

        .seccion-form {
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 6px;
        }

        .seccion-form h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e9ecef;
        }

        .grid-campos {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 2rem;
            padding: 10px;
        }

        .campo-form {
            margin-bottom: 1.5rem;
        }

        .campo-form label {
            display: block;
            margin-bottom: 0.5rem;
            color: #495057;
            font-weight: 500;
        }

        .campo-form input,
        .campo-form textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ced4da;
            border-radius: 4px;
            transition: border-color 0.2s;
        }

        .campo-form input[type="number"] {
            -moz-appearance: textfield;
        }

        .campo-form input[type="number"]::-webkit-outer-spin-button,
        .campo-form input[type="number"]::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }

        .campo-form input:focus,
        .campo-form textarea:focus {
            border-color: #80bdff;
            outline: 0;
            box-shadow: 0 0 0 0.2rem rgba(0,123,255,0.25);
        }

        .botones-accion {
            display: flex;
            gap: 2rem;
            justify-content: center;
            margin-top: 2rem;
        }

        .btn {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
            background: transparent;
        }

        .btn:hover {
            background-color: #f8f9fa;
        }

        .icon {
            width: 20px;
            height: 20px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid transparent;
            border-radius: 4px;
        }

        .alert-success {
            color: #155724;
            background-color: #d4edda;
            border-color: #c3e6cb;
        }

        .alert-danger {
            color: #721c24;
            background-color: #f8d7da;
            border-color: #f5c6cb;
        }
    </style>
</head>
<body>
<?php
if ($row = $result->fetch_assoc()) {
    date_default_timezone_set('America/Mexico_City');
    $fecha_actual = date('d/m/Y');
?>
    <div id="formEditarCalidad" class="form-edicion-calidad">
        <div class="header-section">
            <h2>Edición de Control de Calidad</h2>
        </div>
        
        <div class="registro-info">
            <span class="registro-numero">Registro #<?php echo htmlspecialchars($row['materia_prima_id']); ?></span>
            <span class="fecha-registro">Fecha: <?php echo $fecha_actual; ?></span>
        </div>
        
        <form method="POST" onsubmit="return confirmarGuardado()" class="form-control">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['materia_prima_id']); ?>">
            
            <!-- Sección de Información de la Materia Prima -->
            <div class="seccion-form">
                <h3>Información de la Materia Prima</h3>
                <div class="campo-form">
                    <label for="nombreMP">Nombre de la Materia Prima:</label>
                    <input type="text" id="nombreMP" 
                           value="<?php echo htmlspecialchars($row['codigo_mp'] . ' - ' . $row['NombreMP']); ?>" 
                           disabled>
                           
                </div>
            </div>

            <!-- Sección de Análisis -->
            <div class="seccion-form">
                <h3>Parámetros de Análisis</h3>
                
                <div class="grid-campos">
                    <!-- Apariencia -->
                    <div class="campo-form">
                        <label for="apariencia">Apariencia:</label>
                        <input type="text" 
                               id="apariencia" 
                               name="apariencia" 
                               value="<?php echo htmlspecialchars($row['apariencia']); ?>" 
                               required>
                    </div>

                    <!-- pH -->
                    <div class="campo-form">
                        <label for="ph">pH:</label>
                        <input type="text" 
                               id="ph" 
                               name="ph" 
                               value="<?php echo htmlspecialchars($row['pH']); ?>" 
                               required>
                    </div>

                    <!-- Densidad -->
                    <div class="campo-form">
                        <label for="densidad">Densidad (g/mL):</label>
                        <input type="text" 
                               id="densidad" 
                               name="densidad" 
                               value="<?php echo htmlspecialchars($row['densidad']); ?>" 
                               required>
                    </div>

                    <!-- Concentración -->
                    <div class="campo-form">
                        <label for="concentracion">Concentración (%):</label>
                        <input type="text" 
                               id="concentracion" 
                               name="concentracion" 
                               value="<?php echo htmlspecialchars($row['concentracion']); ?>" 
                               required>
                    </div>
                </div>
            </div>

            <!-- Sección de Observaciones -->
            <div class="seccion-form">
                <h3>Observaciones y Notas</h3>
                <div class="campo-form">
                    <label for="observaciones">Observaciones:</label>
                    <textarea id="observaciones" 
                              name="observaciones" 
                              rows="4" 
                              required><?php echo htmlspecialchars($row['observaciones']); ?></textarea>
                </div>
            </div>

            <!-- Botones de Acción -->
            <div class="botones-accion">
                <button type="submit" class="btn">
                    <img src="Iconos/Guardar.png" alt="Guardar" class="icon">
                    <span>Actualizar Registro</span>
                </button>
                
                <button type="button" class="btn" onclick="window.history.back()">
                    <img src="Iconos/Regresar.png" alt="Regresar" class="icon">
                    <span>Regresar</span>
                </button>
            </div>
        </form>
    </div>

<?php
} else {
    echo "<div class='alert alert-danger'>No se encontró el registro solicitado.</div>";
}

// Cerrar la conexión
$stmt->close();
$conn->close();
?>

<script>
    function confirmarGuardado() {
        return confirm('¿Está seguro de guardar los cambios realizados?');
    }
</script>
</body>
</html>