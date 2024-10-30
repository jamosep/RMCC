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

// Consulta para obtener materias primas
$sql = "SELECT IdMP, NombreMP FROM materiasprimas";
$result = $conn->query($sql);
?>

<div id="formRecepcion" class="form-recepcion">
    <h2>Formulario de Nueva Recepción de Material</h2>
    <form action="cargar_MP.php" method="POST" enctype="multipart/form-data">
        <label for="materiaPrima">Nombre de la Materia Prima:</label>
        <select id="IdMP" name="NombreMP">
            <?php
            if ($result->num_rows > 0) {
                // Generar opciones para cada Materia Prima
                while($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row["IdMP"]) . "'>" . htmlspecialchars($row["NombreMP"]) . "</option>";
                }
            } else {
                echo "<option value=''>No hay Materias Primas</option>";
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
