<?php
// crearMP.php
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
    if (empty($datos['IdMP']) || empty($datos['NombreMP'])) {
        throw new Exception("Falta el ID o el nombre de la materia prima");
    }

    // Insertar en la tabla materiaprima
    $stmt = $pdo->prepare("INSERT INTO materiaprima (IdMP, NombreMP) VALUES (:id, :nombre)");
    $stmt->execute([
        ':id' => $datos['IdMP'],
        ':nombre' => $datos['NombreMP']
    ]);

    echo json_encode([
        "success" => true,
        "message" => "Materia prima creada exitosamente"
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>
