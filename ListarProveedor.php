<?php
// ListarProveedor.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Para permitir CORS

try {
    // Conexión a la base de datos
    $pdo = new PDO(
        "mysql:host=localhost;dbname=proveedoresbd;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Verificar si la solicitud es de tipo GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método de solicitud no permitido. Use GET.");
    }

    // Preparar la consulta para obtener todos los proveedores
    $stmt = $pdo->prepare("SELECT id, Nombre FROM proveedores");
    $stmt->execute();

    // Obtener todos los proveedores
    $proveedores = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($proveedores) > 0) {
        echo json_encode([
            "success" => true,
            "data" => $proveedores,
            "total" => count($proveedores)
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "data" => [],
            "message" => "No se encontraron proveedores"
        ]);
    }

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
