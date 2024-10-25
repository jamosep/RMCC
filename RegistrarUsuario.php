<?php
// RegistrarUsuario.php
header('Content-Type: application/json'); // Establecer el tipo de contenido como JSON

try {
    // Conexión usando PDO en lugar de mysqli para consistencia
    $pdo = new PDO(
        "mysql:host=localhost;dbname=users;charset=utf8",
        "root",
        "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Verificar si la solicitud es de tipo POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método de solicitud no permitido");
    }

    // Obtener el contenido JSON de la solicitud
    $datos = json_decode(file_get_contents('php://input'), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // Si hay error al decodificar JSON, intentar con POST tradicional
        $datos = $_POST;
    }

    // Verificar campos requeridos
    if (empty($datos['username']) || empty($datos['password']) || empty($datos['rol'])) {
        throw new Exception("Faltan campos obligatorios");
    }

    // Preparar los datos
    $userID = $datos['UserID'];
    $username = $datos['username'];
    $password = password_hash($datos['password'], PASSWORD_DEFAULT);
    $rol = $datos['rol'];

    // Preparar y ejecutar la consulta
    $stmt = $pdo->prepare(
        "INSERT INTO usuariosrmcc (UserID, username, password, rol) 
         VALUES (:userID, :username, :password, :rol)"
    );

    $stmt->execute([
        ':userID' => $userID,
        ':username' => $username,
        ':password' => $password,
        ':rol' => $rol
    ]);

    // Respuesta exitosa
    echo json_encode([
        "success" => true,
        "message" => "Usuario registrado exitosamente"
    ]);

} catch (Exception $e) {
    // Manejo de errores
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "error" => $e->getMessage()
    ]);
}
?>