<?php
// ListarUsuarios.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *'); // Para permitir CORS si es necesario

try {
    // Conexión a la base de datos usando PDO
    $pdo = new PDO(
        "mysql:host=localhost;dbname=users;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Verificar si la solicitud es de tipo GET
    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        throw new Exception("Método de solicitud no permitido. Use GET.");
    }

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare("SELECT UserID, username, rol FROM usuariosrmcc");
    $stmt->execute();

    // Obtener todos los usuarios
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verificar si se encontraron usuarios
    if (count($usuarios) > 0) {
        echo json_encode([
            "success" => true,
            "data" => $usuarios,
            "total" => count($usuarios)
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "data" => [],
            "message" => "No se encontraron usuarios registrados"
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