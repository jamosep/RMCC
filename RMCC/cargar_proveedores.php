<?php
// Configuración de la conexión a la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "proveedoresbd";

// Crear conexión
$conn = new mysqli($servername, $username, "", $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta para obtener proveedores
$sql = "SELECT id, nombre FROM proveedores";
$result = $conn->query($sql);
?>

<div id="formRecepcion" class="form-recepcion">
    <h2>Formulario de Nueva Recepción de Material</h2>
    <form action="cargar_proveedores.php" method="POST" enctype="multipart/form-data">
        <label for="proveedor">Nombre del Proveedor:</label>
        <select id="proveedor" name="proveedor">
            <?php
            if ($result->num_rows > 0) {
                // Generar opciones para cada proveedor
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row["id"]) . "'>" . htmlspecialchars($row["nombre"]) . "</option>";
                }
            } else {
                echo "<option value=''>No hay proveedores</option>";
            }
            ?>
        </select>
        <input type="submit" value="Enviar">
    </form>
</div>

<?php
// Cerrar conexión
$conn->close();
?>
