<?php

$conn_materiales = null;
$conn_proveedores = null;

// Verificar si se recibió el ID
if (!isset($_GET['id'])) {
    die("<div class='alert alert-danger'>ID no proporcionado</div>");
}

$id = $_GET['id'];

// Conexión a la base de datos
try {
    // Conexión a la base de datos de materiales
    $conn_materiales = new mysqli("localhost", "root", "", "rmcc_entradasmateriales");
    if ($conn_materiales->connect_error) {
        throw new Exception("Error de conexión a materiales: " . $conn_materiales->connect_error);
    }

    // Conexión a la base de datos de proveedores
    $conn_proveedores = new mysqli("localhost", "root", "", "proveedoresbd");
    if ($conn_proveedores->connect_error) {
        throw new Exception("Error de conexión a proveedores: " . $conn_proveedores->connect_error);
    }
    
    // Agregar al inicio del archivo, después de las conexiones
if (!file_exists('documentos')) {
    mkdir('documentos', 0777, true);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Obtener los datos del formulario
        $lote = $_POST['lote'] ?? '';
        $cantidad = $_POST['cantidad'] ?? '';
        $remision = $_POST['remision'] ?? '';
        $unidad = $_POST['unidad'] ?? '';
        $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? '';
        $tipo_material = $_POST['tipo_material'] ?? '';
        $proveedor_id = $_POST['proveedor'] ?? '';
        $id = $_POST['id'] ?? '';

        // Manejo del documento
        $documento_final = $_POST['documento_actual'] ?? '';
        
        if (isset($_FILES['documentos']) && $_FILES['documentos']['error'] === UPLOAD_ERR_OK) {
            $archivo_nuevo = $_FILES['documentos']['name'];
            $extension = pathinfo($archivo_nuevo, PATHINFO_EXTENSION);
            $ruta_archivo = "documentos/" . $id . "_" . time() . "." . $extension;
            
            if (move_uploaded_file($_FILES['documentos']['tmp_name'], $ruta_archivo)) {
                $documento_final = basename($ruta_archivo);
            }
        }

        // Preparar la consulta de actualización
        $update_sql = "UPDATE recepcion_materiales 
                      SET lote = ?, 
                          cantidad = ?, 
                          remision = ?, 
                          unidad = ?, 
                          fecha_vencimiento = ?, 
                          tipo_material = ?,
                          proveedor_id = ?,
                          documentos = ?
                      WHERE id = ?";

        $update_stmt = $conn_materiales->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Error en la preparación de la consulta: " . $conn_materiales->error);
        }

        // Debug - Imprimir valores antes de la vinculación
        echo "Debug - Valores a actualizar:<br>";
        echo "Lote: $lote<br>";
        echo "Cantidad: $cantidad<br>";
        echo "Remisión: $remision<br>";
        echo "Unidad: $unidad<br>";
        echo "Fecha vencimiento: $fecha_vencimiento<br>";
        echo "Tipo material: $tipo_material<br>";
        echo "Proveedor ID: $proveedor_id<br>";
        echo "Documento: $documento_final<br>";
        echo "ID: $id<br>";

        // Vincular parámetros
        if (!$update_stmt->bind_param("ssssssssi", 
            $lote, 
            $cantidad, 
            $remision, 
            $unidad, 
            $fecha_vencimiento, 
            $tipo_material, 
            $proveedor_id,
            $documento_final,
            $id
        )) {
            throw new Exception("Error al vincular parámetros: " . $update_stmt->error);
        }

        // Ejecutar la actualización
        if (!$update_stmt->execute()) {
            throw new Exception("Error al ejecutar la actualización: " . $update_stmt->error);
        }

        // Redirigir con mensaje de éxito
        echo "
            <div class='alert alert-success'>
                <strong>Registro actualizado exitosamente</strong><br>
                <small style='font-size: 14px; color: #666;'>Redirigiendo a la página principal...</small>
            </div>
            <script>
                setTimeout(function(){
                    window.location.href = 'consultar_registro.php';
                }, 2000);
            </script>
        ";
        exit;

    } catch (Exception $e) {
        echo "<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
// Consulta para obtener los datos del registro actual
$sql = "SELECT rm.*, p.Nombre as nombre_proveedor 
        FROM recepcion_materiales rm
        LEFT JOIN proveedoresbd.proveedores p ON rm.proveedor_id = p.id
        WHERE rm.id = ?";

$stmt = $conn_materiales->prepare($sql);
if (!$stmt) {
throw new Exception("Error en la preparación de la consulta: " . $conn_materiales->error);
}

try {
    // Vincular el parámetro ID
    if (!$stmt->bind_param("i", $id)) {
        throw new Exception("Error al vincular parámetro: " . $stmt->error);
    }
    
    // Ejecutar la consulta
    if (!$stmt->execute()) {
        throw new Exception("Error al ejecutar la consulta: " . $stmt->error);
    }
    
    $result = $stmt->get_result();
    
    if (!$result) {
        throw new Exception("Error al obtener el resultado: " . $stmt->error);
    }
} catch (Exception $e) {
    die("<div class='alert alert-danger'>Error: " . htmlspecialchars($e->getMessage()) . "</div>");
}

// Obtener todos los proveedores para el select
$sql_proveedores = "SELECT id, Nombre FROM proveedores ORDER BY Nombre";
$result_proveedores = $conn_proveedores->query($sql_proveedores);
if (!$result_proveedores) {
throw new Exception("Error al obtener proveedores: " . $conn_proveedores->error);
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
    <title>Editar Recepción de Materiales</title>
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

        .file-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .file-upload .file-input {
            display: none;
        }

        .file-upload .file-label {
            display: inline-block;
            padding: 8px 16px;
            background: #f8f9fa;
            border: 1px solid #ced4da;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            box-sizing: border-box;
        }

        .file-upload .file-label:hover {
            background: #e9ecef;
        }

        .current-file {
            margin-top: 8px;
            font-size: 0.9em;
            color: #666;
        }   

    </style>
</head>
<body>
<?php
if ($result && $row = $result->fetch_assoc()) {
    date_default_timezone_set('America/Mexico_City');
    $fecha_actual = date('d/m/Y');
?>

<div id="formEditarMaterial" class="form-edicion-calidad">
        <div class="header-section">
            <h2>Edición de Recepción de Materiales</h2>
        </div>
        
        <div class="registro-info">
            <span class="registro-numero">Registro #<?php echo htmlspecialchars($row['id']); ?></span>
            <span class="fecha-registro">Fecha: <?php echo $fecha_actual; ?></span>
        </div>
        
        <form method="POST" enctype="multipart/form-data" onsubmit="return confirmarGuardado()" class="form-control">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
        <input type="hidden" name="documento_actual" value="<?php echo htmlspecialchars($row['documentos']); ?>">
        <input type="hidden" id="accion_archivo" name="accion_archivo" value="">
            
            <!-- Sección de Información de la materia prima -->
            <div class="seccion-form">
                <h3>Información de la Materia Prima</h3>
                <div class="campo-form">
                    <label for="nombreMP">Nombre de la Materia Prima:</label>
                    <input type="text" id="nombreMP" 
                           value="<?php echo htmlspecialchars($row['NombreMP']); ?>" 
                           disabled>
                </div>
            </div>

            <!-- Sección de Recepción -->
            <div class="seccion-form">
                <h3>Información de la Recepción</h3>
                
                <div class="grid-campos">
                    <!-- Lote -->
                    <div class="campo-form">
                        <label for="lote">Lote:</label>
                        <input type="text" 
                               id="lote" 
                               name="lote" 
                               value="<?php echo htmlspecialchars($row['lote']); ?>" 
                               required>
                    </div>

                    <!-- Cantidad -->
                    <div class="campo-form">
                        <label for="cantidad">Cantidad:</label>
                        <input type="text" 
                               id="cantidad" 
                               name="cantidad" 
                               value="<?php echo htmlspecialchars($row['cantidad']); ?>" 
                               required>
                    </div>

                    <!-- Remisión -->
                    <div class="campo-form">
                        <label for="remision">Remisión/Factura:</label>
                        <input type="text" 
                               id="remision" 
                               name="remision" 
                               value="<?php echo htmlspecialchars($row['remision']); ?>" 
                               required>
                    </div>

                    <!-- Unidad -->
                    <div class="campo-form">
                        <label for="unidad">Unidad:</label>
                        <input type="text" 
                               id="unidad" 
                               name="unidad" 
                               value="<?php echo htmlspecialchars($row['unidad']); ?>" 
                               required>
                    </div>

                    <!-- Fecha de Vencimiento -->
                    <div class="campo-form">
                        <label for="fecha_vencimiento">Fecha de Vencimiento:</label>
                        <input type="date" 
                               id="fecha_vencimiento" 
                               name="fecha_vencimiento" 
                               value="<?php echo htmlspecialchars($row['fecha_vencimiento']); ?>" 
                               required>
                    </div>

                    <!-- Tipo de Material -->
                    <div class="campo-form">
                        <label for="tipo_material">Tipo de Material:</label>
                        <select id="tipo_material" 
                                name="tipo_material" 
                                required 
                                class="form-select">
                            <?php
                            $tipos_material = [
                                'Materia Prima',
                                'Reactivo',
                                'Consumible',                        
                            ];
                            foreach ($tipos_material as $tipo) {
                                $selected = ($tipo === $row['tipo_material']) ? 'selected' : '';
                                echo "<option value='" . htmlspecialchars($tipo) . "' $selected>" . 
                                     htmlspecialchars($tipo) . "</option>";
                            }
                            ?>
                        </select>
                    </div>

                    <!-- Proveedor -->
                    <div class="campo-form">
            <label for="proveedor">Proveedor:</label>
            <select id="proveedor" 
                    name="proveedor" 
                    required 
                    class="form-select">
                <option value="">Seleccione un proveedor</option>
                <?php
                while ($proveedor = $result_proveedores->fetch_assoc()) {
                    $selected = ($proveedor['id'] == $row['proveedor_id']) ? 'selected' : '';
                    echo "<option value='" . htmlspecialchars($proveedor['id']) . "' $selected>" . 
                         htmlspecialchars($proveedor['Nombre']) . "</option>";
                }
                ?>
            </select>
        </div>
        
        </div>
                    <!-- Documentos -->
                    <div class="campo-form">
                        <label for="documentos">Documentos:</label>
                        <div class="file-upload">
                            <input type="file" 
                                   id="documentos" 
                                   name="documentos" 
                                   class="file-input">
                            <label for="documentos" class="file-label">
                                Seleccionar archivo
                            </label>
                            <?php if ($row['documentos']): ?>
                                <div class="current-file">
                                    Archivo actual: <?php echo htmlspecialchars($row['documentos']); ?>
                                </div>                            
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            
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

// Cerrar las conexiones y liberar recursos
if (isset($stmt)) $stmt->close();
if (isset($conn_materiales)) $conn_materiales->close();
if (isset($conn_proveedores)) $conn_proveedores->close();
?>

<script>
function confirmarGuardado() {
    return confirm('¿Está seguro de guardar los cambios realizados?');
}

// Actualizar el nombre del archivo seleccionado
document.getElementById('documento').addEventListener('change', function(e) {
    var fileName = e.target.files[0] ? e.target.files[0].name : 'Seleccionar archivo';
    e.target.nextElementSibling.textContent = fileName;
});
</script>
</body>
</html>