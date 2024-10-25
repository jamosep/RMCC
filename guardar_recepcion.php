<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname_rmcc = "rmcc_entradasmateriales";
$dbname_proveedores = "proveedoresbd";

// Crear conexiones
$conn_rmcc = new mysqli($servername, $username, $password, $dbname_rmcc);
$conn_proveedores = new mysqli($servername, $username, $password, $dbname_proveedores);

// Verificar conexiones
if ($conn_rmcc->connect_error || $conn_proveedores->connect_error) {
    die("Conexión fallida: " . $conn_rmcc->connect_error . " " . $conn_proveedores->connect_error);
}

// Función para formatear el tipo de material
function formatearTipoMaterial($tipo) {
    $tipos = [
        'materiaprima' => 'Materia Prima',
        'reactivo' => 'Reactivo',
        'envase' => 'Envase',
        'empaque' => 'Empaque'
    ];
    
    $tipo_lower = strtolower(str_replace(' ', '', $tipo));
    return isset($tipos[$tipo_lower]) ? $tipos[$tipo_lower] : $tipo;
}

// Obtener datos del formulario
$proveedor_id = isset($_POST['proveedor']) ? $_POST['proveedor'] : null;
$materia_prima_id = isset($_POST['NombreMP']) ? $_POST['NombreMP'] : null;
$lote = isset($_POST['lote']) ? $_POST['lote'] : null;
$cantidad = isset($_POST['cantidad']) ? $_POST['cantidad'] : null;
$remision = isset($_POST['remision']) ? $_POST['remision'] : null;
$unidad = isset($_POST['unidad']) ? $_POST['unidad'] : null;
$fecha_vencimiento = isset($_POST['fechaVencimiento']) ? $_POST['fechaVencimiento'] : null;
$tipo_material = isset($_POST['tipoMaterial']) ? formatearTipoMaterial($_POST['tipoMaterial']) : null;

// Manejo de archivo cargado
$documentos = '';
if (isset($_FILES['documentos']) && $_FILES['documentos']['error'] == UPLOAD_ERR_OK) {
    $documentos = $_FILES['documentos']['name'];
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    $upload_file = $upload_dir . basename($_FILES['documentos']['name']);
    move_uploaded_file($_FILES['documentos']['tmp_name'], $upload_file);
}

// Obtener el nombre de la materia prima desde la base de datos proveedoresbd
$sql_nombre_mp = "SELECT NombreMP FROM materiaprima WHERE IdMP = ?";
$stmt_nombre_mp = $conn_proveedores->prepare($sql_nombre_mp);
$stmt_nombre_mp->bind_param("s", $materia_prima_id);
$stmt_nombre_mp->execute();
$result_nombre_mp = $stmt_nombre_mp->get_result();
$nombreMP = '';
if ($row = $result_nombre_mp->fetch_assoc()) {
    $nombreMP = $row['NombreMP'];
}
$stmt_nombre_mp->close();

// Validar que todos los campos requeridos no estén vacíos
$errores = [];

if (empty($proveedor_id)) {
    $errores[] = "El campo Proveedor es obligatorio.";
}
if (empty($materia_prima_id)) {
    $errores[] = "El campo Materia Prima es obligatorio.";
}
if (empty($lote)) {
    $errores[] = "Lote";
}
if (empty($cantidad)) {
    $errores[] = "Cantidad";
}
if (empty($remision)) {
    $errores[] = "Remision/Factura";
}
if (empty($unidad)) {
    $errores[] = "Unidad";
}
// Validación condicional para la fecha de vencimiento
if (($tipo_material == 'Materia Prima' || $tipo_material == 'Reactivo') && empty($fecha_vencimiento)) {
    $errores[] = "Fecha de vencimiento";
}

// Si hay errores, mostrar los mensajes en una ventana emergente
if (!empty($errores)) {
    echo "<script>alert('Faltan los siguientes campos obligatorios:\\n" . implode("\\n", $errores) . "'); window.history.back();</script>";
    exit();
}

// Preparar y ejecutar la consulta
$sql = "INSERT INTO recepcion_materiales (proveedor_id, materia_prima_id, NombreMP, lote, cantidad, remision, unidad, fecha_vencimiento, tipo_material, documentos)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $conn_rmcc->prepare($sql);

// Comprobar si la consulta fue preparada correctamente
if (!$stmt) {
    die("Error en la preparación de la consulta: " . $conn_rmcc->error);
}

// Debug: Imprimir el tipo de material antes de insertar
error_log("Tipo de material a insertar: " . $tipo_material);

// Enlazar los parámetros
$stmt->bind_param("ssssssssss", $proveedor_id, $materia_prima_id, $nombreMP, $lote, $cantidad, $remision, $unidad, $fecha_vencimiento, $tipo_material, $documentos);

// Ejecutar la consulta
if ($stmt->execute()) {
    echo "
<div class='alert alert-success'>
    <strong>Registro de material realizado exitosamente</strong><br>
    <small style='font-size: 14px; color: #666;'>Redirigiendo a la página principal...</small>
</div>
<script>
    setTimeout(function(){
        window.location.href = 'Index.php';
    }, 2000); // Redirige después de 2 segundos
</script>
";
} else {
    echo "<script>alert('Error al insertar el registro: " . $stmt->error . "'); window.history.back();</script>";
}

// Cerrar las conexiones
$stmt->close();
$conn_rmcc->close();
$conn_proveedores->close();
?>