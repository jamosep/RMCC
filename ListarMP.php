<?php
// ListarMP.php
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

    // Preparar la consulta para obtener todas las materias primas
    $stmt = $pdo->prepare("SELECT IdMP, NombreMP FROM materiaprima");
    $stmt->execute();

    // Obtener todas las materias primas
    $materiasPrimas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($materiasPrimas) > 0) {
        echo json_encode([
            "success" => true,
            "data" => $materiasPrimas,
            "total" => count($materiasPrimas)
        ]);
    } else {
        echo json_encode([
            "success" => true,
            "data" => [],
            "message" => "No se encontraron materias primas"
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
