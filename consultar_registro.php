<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consulta de Registros de Materiales y Calidad</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .icon-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 0;
        }
        .icon-button img {
            width: 32px;
            height: auto;
        }
        .form-buttons {
            margin-top: 20px;
            display: flex;
            gap: 10px;
        }
        .form-buttons button {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }
        .table-container {
            margin-top: 20px;
        }
        .table-container table {
            width: 100%;
            border-collapse: collapse;
        }
        .table-container th, .table-container td {
            border: 1px solid #ccc;
            padding: 10px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Resultado de la búsqueda de registros</h1>
    </header>

    <!-- Formulario de búsqueda -->
    <div class="formconsultarregistro"> 
        <h2>Filtrar Registros</h2>
        <form id="formConsulta" action="consultar_registro.php" method="GET">
            <label for="materialInput">Nombre de Material o Código de Material</label> 
            <input type="text" name="material" id="materialInput" placeholder="Digite el código o el nombre del material">
            
            <label for="proveedor">Seleccione Proveedor</label>
            <select name="proveedor" id="proveedor">
                <option value="">Todos los proveedores</option>
                <?php
                // Conexión a la base de datos
                $conn = new mysqli("localhost", "root", "", "proveedoresbd");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener proveedores
                $stmt = $conn->prepare("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay proveedores</option>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </select>
            
            <label for="tipo_material">Tipo de Material</label>
            <select id="tipo_material" name="tipo_material">
                <option value="">Seleccione un tipo de material</option>
                <option value="Materia Prima">Materia Prima</option>
                <option value="Reactivo">Reactivo</option>
                <option value="Consumible">Consumible</option>
            </select>
            
            <label for="idTextbox">Registro Seleccionado:</label>
            <input type="text" id="idTextbox" readonly placeholder="Seleccione un número de registro">

            <div class="form-buttons">
                <button type="submit" class="icon-button" id="Buscar">
                    <img src="Iconos/Buscar.png" alt="Buscar" title="Realizar Consulta"> 
                    <p>Realizar Consulta</p>   
                </button>
                <button type="reset" class="icon-button" id="Limpiar">
                    <img src="Iconos/Limpiar.png" alt="Limpiar" title="Limpiar datos">
                    <p>Limpiar Datos</p>    
                </button>
                <button type="button" class="icon-button" id="regresar">
                    <img src="Iconos/Regresar.png" alt="Regresar" title="Regresar al Inicio"> 
                    <p>Regresar al Inicio</p>                     
                </button>
                <button type="button" class="icon-button" id="verRegistroBtn" disabled>
                    <img src="Iconos/Ver detalle.png" alt="Ver registro detallado"> 
                    <p>Ver registro detallado</p>                     
                </button>
                <button type="button" class="icon-button" id="editarRegistroMaterial" disabled>
                    <img src="Iconos/editar_material.png" alt="Editar Registro de Material">
                    <p>Editar Registro de Material</p>
                </button>
                <button type="button" class="icon-button" id="editarControlCalidad" disabled>
                    <img src="Iconos/editar_calidad.png" alt="Editar Control de Calidad">
                    <p>Editar Control de Calidad</p>
                </button>
            </div>
        </form>
    </div>

    <!-- Script para gestionar la habilitación de botones -->
    <script>
    document.addEventListener("DOMContentLoaded", function() {
        const verRegistroBtn = document.getElementById('verRegistroBtn');
        const editarRegistroMaterialBtn = document.getElementById('editarRegistroMaterial');
        const editarControlCalidadBtn = document.getElementById('editarControlCalidad');
        const idTextbox = document.getElementById('idTextbox');

        document.querySelectorAll('.registroRadio').forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.checked) {
                    idTextbox.value = this.value;
                    verRegistroBtn.disabled = false;
                    editarRegistroMaterialBtn.disabled = false;
                    editarControlCalidadBtn.disabled = false;
                }
            });
        });

        verRegistroBtn.addEventListener('click', function() {
            const id = idTextbox.value;
            if (id) {
                window.location.href = `detalle_registro.php?id=${id}`;
            }
        });

        editarRegistroMaterialBtn.addEventListener('click', function() {
            const id = idTextbox.value;
            if (id) {
                window.location.href = `editar_recepcion.php?id=${id}`;
            }
        });

        editarControlCalidadBtn.addEventListener('click', function() {
            const id = idTextbox.value;
            if (id) {
                window.location.href = `editar_calidad.php?id=${id}`;
            }
        });

        document.getElementById('regresar').addEventListener('click', function() {
            window.location.href = 'index.php';
        });
    });
    </script>
    
<?php

// Conexión a la base de datos 'rmcc_entradasmateriales'
$conn = new mysqli("localhost", "root", "", "rmcc_entradasmateriales");

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Función para manejar campos como 'N/A' si son -1 o NULL
function manejar_na($valor) {
    return ($valor == -1 || $valor === null || $valor === '0000-00-00') ? 'N/A' : $valor;
}

// Inicializar variables
$material = isset($_GET['material']) ? trim($_GET['material']) : '';
$proveedor = isset($_GET['proveedor']) ? trim($_GET['proveedor']) : '';
$tipo_material = isset($_GET['tipo_material']) ? trim($_GET['tipo_material']) : '';

// Verificar si se ha seleccionado al menos un criterio de búsqueda
if (empty($material) &&  empty($proveedor) && empty($tipo_material)) {
    echo "Por favor, seleccione al menos uno de los criterios de búsqueda.";
} else {
    $sql = "SELECT rm.id, rm.materia_prima_id, rm.NombreMP, rm.proveedor_id, rm.fecha_registro, rm.lote, rm.cantidad, 
            rm.unidad, rm.fecha_vencimiento, rm.tipo_material, cc.observaciones, p.nombre AS nombre_proveedor
            FROM recepcion_materiales rm
            LEFT JOIN control_calidad cc ON rm.materia_prima_id = cc.materia_prima_id
            LEFT JOIN proveedoresbd.proveedores p ON rm.proveedor_id = p.id
            WHERE 1=1";

    // Preparar la consulta dinámica
    $params = [];
    $types = "";

    // Agregar condiciones según los criterios seleccionados
    if (!empty($material)) {
        $sql .= " AND (rm.materia_prima_id LIKE ? OR rm.NombreMP LIKE ?)";
        $params[] = "%$material%";
        $params[] = "%$material%"; // Para buscar en nombre también
        $types .= "ss"; // Se añaden dos tipos de parámetros
    }

    if (!empty($proveedor)) {
        $sql .= " AND rm.proveedor_id = ?";
        $params[] = $proveedor;
        $types .= "s";
    }

    if (!empty($tipo_material)) {
        $sql .= " AND rm.tipo_material = ?";
        $params[] = $tipo_material;
        $types .= "s";
    }

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        // Mostrar resultados en tabla
        if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Código del Material</th>
                            <th>Nombre del Material</th>
                            <th>Nombre del Proveedor</th>
                            <th>Fecha de Entrada</th>
                            <th>Lote</th>
                            <th>Cantidad</th>
                            <th>Unidad</th>
                            <th>Vencimiento</th>
                            <th>Observaciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <input type="radio" class="registroRadio" name="registro_id" value="<?= htmlspecialchars($row['id']) ?>"> 
                                    <?= htmlspecialchars($row['id']) ?>
                                </td>                                
                                <td><?= htmlspecialchars($row['materia_prima_id']) ?></td>
                                <td><?= htmlspecialchars($row['NombreMP']) ?></td>
                                <td><?= htmlspecialchars($row['nombre_proveedor']) ?></td>
                                <td><?= htmlspecialchars($row['fecha_registro']) ?></td>
                                <td><?= htmlspecialchars($row['lote']) ?></td>
                                <td><?= htmlspecialchars($row['cantidad']) ?></td>
                                <td><?= htmlspecialchars($row['unidad']) ?></td>
                                <td><?= manejar_na(htmlspecialchars($row['fecha_vencimiento'])) ?></td>
                                <td><?= manejar_na(htmlspecialchars($row['observaciones'])) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php endif;
    }
}
$conn->close();
?>

</body>
</html>
