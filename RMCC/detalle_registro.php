<?php
// Conexión a la base de datos 'rmcc_entradasmateriales'
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "rmcc_entradasmateriales";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$sql = "
    SELECT rm.id, rm.materia_prima_id, rm.NombreMP, rm.proveedor_id, rm.fecha_registro, rm.lote, rm.cantidad, rm.unidad,
           rm.fecha_vencimiento, rm.tipo_material, rm.remision, rm.fecha_registro AS fecha_entrada,
           cc.apariencia, cc.ph, cc.densidad, cc.concentracion, cc.observaciones, cc.fecha_registro AS fecha_analisis,
           p.nombre AS nombre_proveedor
    FROM recepcion_materiales rm
    LEFT JOIN control_calidad cc ON rm.id = cc.materia_prima_id
    LEFT JOIN proveedoresbd.proveedores p ON rm.proveedor_id = p.id
    WHERE rm.id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();
$result = $stmt->get_result();

// Función actualizada para manejar_pendiente
function manejar_valor($valor, $pendiente = false) {
    // Si no hay un registro en control_calidad, mostrar "Pendiente"
    if ($pendiente) {
        return 'Pendiente';
    }
    
    // Si el valor es null, vacío, o -1, mostrar "N/A"
    if ($valor === null || $valor === '' || floatval($valor) == 0.00 || $valor == '0000-00-00') {
        return 'N/A';
    }



    // Formatear valores numéricos con decimales apropiados
    if (is_numeric($valor)) {
        if (strpos($valor, '.') !== false) {
            // Para valores con decimales, mantener hasta 2 decimales
            return number_format(floatval($valor), 2, '.', '');
        } else {
            // Para números enteros, no agregar decimales
            return $valor;
        }
    }

    // Retornar el valor original si no es un número
    return $valor;
}


// Start HTML output
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Detalles del Registro</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        .pendiente {
            color: orange;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class='container'>";

    if ($row = $result->fetch_assoc()) {
        // Verificar si los parámetros de calidad están presentes (si no, mostrar "Pendiente")
        $pendiente = is_null($row['apariencia']) && is_null($row['ph']) && is_null($row['densidad']) && is_null($row['concentracion']) && is_null($row['observaciones']);
    
        // Detalles del registro encontrado
        echo "<h2>Detalles del Registro: " . htmlspecialchars($row['id']) . "</h2>";
        echo "<table>
            <tr><th>Número de Registro de Material</th><td>" . htmlspecialchars($row['id']) . "</td></tr>
            <tr><th>Código del Material</th><td>" . htmlspecialchars($row['materia_prima_id']) . "</td></tr>
            <tr><th>Nombre del Material</th><td>" . htmlspecialchars($row['NombreMP']) . "</td></tr>
            <tr><th>Proveedor</th><td>" . htmlspecialchars($row['nombre_proveedor']) . "</td></tr>
            <tr><th>Fecha de Entrada</th><td>" . htmlspecialchars($row['fecha_entrada']) . "</td></tr>
            <tr><th>Lote</th><td>" . htmlspecialchars($row['lote']) . "</td></tr>
            <tr><th>Cantidad</th><td>" . htmlspecialchars($row['cantidad']) . "</td></tr>
            <tr><th>Unidad</th><td>" . htmlspecialchars($row['unidad']) . "</td></tr>
            <tr><th>Fecha de Vencimiento</th><td>" . htmlspecialchars(manejar_valor($row['fecha_vencimiento'])) . "</td></tr>
            <tr><th>Apariencia</th><td>" . htmlspecialchars(manejar_valor($row['apariencia'], $pendiente)) . "</td></tr>
            <tr><th>pH</th><td>" . htmlspecialchars(manejar_valor($row['ph'], $pendiente)) . "</td></tr>
            <tr><th>Densidad</th><td>" . htmlspecialchars(manejar_valor($row['densidad'], $pendiente)) . "</td></tr>
            <tr><th>Concentración</th><td>" . htmlspecialchars(manejar_valor($row['concentracion'], $pendiente)) . "</td></tr>
            <tr><th>Observaciones</th><td>" . htmlspecialchars(manejar_valor($row['observaciones'], $pendiente)) . "</td></tr>
        </table>";
}
else {
    echo "<p>No se encontró el registro: $id</p>";
}

echo "<a href='consultar_registro.php' class='back-link'>Realizar nueva búsqueda</a>
    </div>
</body>
</html>";

$conn->close();
?>