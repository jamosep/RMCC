<?php
// crearProveedor.php
header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

try {
    // Conexión a la base de datos
    $pdo = new PDO(
        "mysql:host=localhost;dbname=proveedoresbd;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Verificar si la solicitud es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de solicitud no permitido");
    }

    // Obtener los datos de la solicitud
    $datos = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        $datos = $_POST;
    }

    // Verificar campos requeridos
    if (empty($datos['Nombre'])) {
        throw new Exception("Falta el nombre del proveedor");
    }

    // Insertar en la tabla proveedores
    $stmt = $pdo->prepare("INSERT INTO proveedores (Nombre) VALUES (:nombre)");
    $stmt->execute([':nombre' => $datos['Nombre']]);

    echo json_encode([
        "success" => true,
        "message" => "Proveedor creado exitosamente"
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
