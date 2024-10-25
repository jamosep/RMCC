<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recepción de Materiales y Control de Calidad</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Recepción de Materiales y Control de Calidad</h1>
        <nav>
            <a href="#" id="inicioLink">Inicio</a>
            <a href="#" id="chatLink">Chat</a>
            <div class="dropdown">
                <a href="#" id="usuarioLink">Usuario</a>
                <div class="dropdown-content" id="dropdownContent">
                    <a href="#">Ajustes</a>
                    <a href="#">Perfil de Usuario</a>
                    <a href="#">Cerrar Sesión</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Opciones -->
    <div class="container" id="inicioPage">
        <div class="option" id="recepcionMaterial">
            <img src="registro1.png" alt="Nueva Recepción de Material">
            <p>Nuevo Recepción de Material</p>
            <div class="description">Realiza una nueva recepción de materiales las cuales se clasifican entre materia prima, Consumible, Reactivos.</div>
        </div>
        <div class="option" id="registroCalidad">
            <img src="registro2.png" alt="Nuevo Registro de Calidad">
            <p>Nuevo Registro de Calidad</p>
            <div class="description">Realiza el registro de los resultados correspondientes a los análisis realizados a las materias primas.</div>
        </div>
        <div class="option" id="consultarRegistro">
            <img src="registro3.png" alt="Consultar Registro">
            <p>Consultar Registro</p>
            <div class="description">Permite realizar la consulta de los registros de una o varias recepciones y control de calidad.</div>
        </div>
        <div class="option" id="novedades">
            <img src="novedades.png" alt="Novedades">
            <p>Novedades</p>
            <div class="description">Permite realizar el reporte de novedades correspondiente a la recepción o control de calidad.</div>
        </div>
    </div>

    <!-- Formulario de Nueva Recepción de Material -->
    <div id="formRecepcion" class="form-recepcion">
        <h2>Formulario de Nuevo Recepción de Material</h2>
        <form action="guardar_recepcion.php" method="POST" enctype="multipart/form-data">
            <!-- Selección del proveedor -->
            <label for="proveedor">Nombre del Proveedor:</label>
            <select id="proveedor" name="proveedor" required>
                <?php
                // Conexión a la base de datos
                $conn = new mysqli("localhost", "root", "", "proveedoresbd");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener proveedores
                $stmt = $conn->prepare("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay proveedores</option>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </select>

            <!-- Selección de la materia prima -->
            <label for="materiaPrima">Nombre de la Materia Prima:</label>
            <select id="NombreMP" name="NombreMP" required>
                <?php
                // Conexión a la base de datos proveedoresbd
                $conn = new mysqli("localhost", "root", "", "proveedoresbd");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener nombres de Materias Primas
                $stmt = $conn->prepare("SELECT IdMP, NombreMP FROM materiaprima ORDER BY NombreMP ASC");
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $idMP = htmlspecialchars($row["IdMP"]);
                        $nombreMP = htmlspecialchars($row["NombreMP"]);
                        echo "<option value='$idMP'>$idMP - $nombreMP</option>";
                    }
                } else {
                    echo "<option value=''>No hay Materias Primas</option>";
                }

                $stmt->close();
                $conn->close();
                ?>
            </select>

            <label for="lote">Lote:</label>
            <input type="text" id="lote" name="lote" required>

            <label for="cantidad">Cantidad:</label>
            <input type="number" id="cantidad" name="cantidad" required>

            <label for="remision">Número de Remisión/Factura de Compra:</label>
            <input type="text" id="remision" name="remision" required>

            <label for="unidad">Unidad:</label>
            <select id="unidad" name="unidad" required>
                <option value="kg">Kg</option>
                <option value="l">L</option>
                <option value="ud">Ud</option>
            </select>

            <label for="fechaVencimiento">Fecha de Vencimiento:</label>
            <input type="date" id="fechaVencimiento" name="fechaVencimiento">

            <label for="tipoMaterial">Tipo de Material:</label>
            <select id="tipoMaterial" name="tipoMaterial" required>
                <option value="materiaprima">Materia Prima</option>
                <option value="Consumible">Consumible</option>
                <option value="Reactivo">Reactivo</option>
            </select>

            <label for="documentos">Cargar Documentos:</label>
            <input type="file" id="documentos" name="documentos" accept="image/*" capture="camera">

            <div class="form-buttons">
                
                <button type="submit" class="icon-button" id="Guardar">
                    <img src="Iconos/Guardar.png" alt="Guardar" title="Guardar">
                    <p>Guardar Recepción</p>    
                </button>
                <button type="reset" class="icon-button" id="Limpiar">
                    <img src="Iconos/Limpiar.png" alt="Limpiar" title="Limpiar datos">
                    <p>Limpiar Datos</p>    
                </button>
            </div>
        </form>
    </div>

    <!-- Formulario de Nuevo Registro de Calidad -->
    <div id="formRegistroCalidad" class="form-registro-calidad">
        <h2>Formulario de Nuevo Registro de Calidad</h2>
        <form action="registrar_calidad.php" method="POST">
            <label for="materiaPrima">Nombre de la Materia Prima:</label>
            <select id="materiaPrima" name="materia_prima_id" required>
                <option value="">Seleccione una Materia Prima</option>
                <?php
                // Conexión a la base de datos
                $conn = new mysqli("localhost", "root", "", "rmcc_entradasmateriales");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener materias primas no registradas en control_calidad
                $stmt = $conn->prepare("SELECT rm.id, rm.materia_prima_id, rm.NombreMP 
                        FROM recepcion_materiales rm
                        LEFT JOIN control_calidad cc ON rm.id = cc.materia_prima_id
                        WHERE rm.tipo_material = 'Materia Prima' AND cc.materia_prima_id IS NULL
                        ORDER BY rm.id ASC");

                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row["id"]) . "'>" .
                             "REM " . htmlspecialchars($row["id"]) . ": " .
                             htmlspecialchars($row["materia_prima_id"]) . " " .
                             htmlspecialchars($row["NombreMP"]) . "</option>";
                    }
                } else {
                    echo "<option value=''>No hay materias primas disponibles</option>";
                }

                
                $conn->close();
                ?>
            </select>

            <label for="limitesEspecificacion">Límites de Especificación:</label>
            <textarea id="limitesEspecificacion" name="limitesEspecificacion" rows="4" cols="50" required></textarea>

            <label for="resultados">Resultados:</label>
            <div id="resultadosContainer">
                <div class="resultado">
                    <label for="apariencia">Apariencia:</label>
                    <input type="text" id="apariencia" name="apariencia" required>
                </div>
                <div class="resultado">
                    <label for="ph">pH:</label>
                    <input type="text" id="ph" name="ph">
                </div>
                <div class="resultado">
                    <label for="densidad">Densidad:</label>
                    <input type="text" id="densidad" name="densidad">
                </div>
                <div class="resultado">
                    <label for="concentracion">Concentración:</label>
                    <input type="text" id="concentracion" name="concentracion">
                </div>
            </div>

            <label for="observaciones">Observaciones (opcional):</label>
            <textarea id="observaciones" name="observaciones" rows="4" cols="50"></textarea>

            <div class="form-buttons">
            <button type="submit" class="icon-button" id="Guardar">
                    <img src="Iconos/Guardar.png" alt="Guardar" title="Guardar">
                    <p>Guardar Registro de Calidad</p>    
                </button>
                <button type="reset" class="icon-button" id="Limpiar">
                    <img src="Iconos/Limpiar.png" alt="Limpiar" title="Limpiar datos">
                    <p>Limpiar Datos</p>    
                </button>
                
            </div>
        </form>
    </div>

    

 <!-- Formulario de Consulta de Registro -->
<div id="formConsultarRegistro" class="form-consultar_registro">
    <h2>Consulta de Registro</h2>
        <form id="formConsulta" action="consultar_registro.php" method="GET">
                <!-- Campo de búsqueda por código o nombre de material -->
            <label for="nombre">Nombre de Material o Codigo de Material</label> 
                <input type="text" name="material" id="materialInput" placeholder="Digite el código o el nombre del material">
            <label for="proveedor">Seleccione Proveedor</label>
                <select name="proveedor" id="proveedor">
                    <option value="">Todos los proveedores</option>
                <?php
                // Conexión a la base de datos
                $conn = new mysqli("localhost", "root", "", "proveedoresbd");
                if ($conn->connect_error) {
                    die("Conexión fallida: " . $conn->connect_error);
                }

                // Consulta para obtener proveedores
                $sql = "SELECT id, nombre FROM proveedores ORDER BY nombre ASC";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<option value='" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['nombre']) . "</option>";
                    }
                }
                $conn->close();
                ?>
            </select>
            
            <label for="tipo_material">Tipo de Material</label>
                <select id="tipo_material" name="tipo_material">
                    <option value="">Seleccione un tipo de material</option>
                    <option value="Materia Prima">Materia Prima</option>
                    <option value="Reactivo">Reactivo</option>
                    <option value="Consumible">Consumible</option>
                </select>
      <!-- Botones de acción -->
    <div class="form-buttons">
    <button type="submit" class="icon-button" id="Buscar">
        <img src="Iconos/Buscar.png" alt="Buscar" title="Realizar Consulta"> 
        <p>Realizar Consulta</p>   
    </button>
    <button type="reset" class="icon-button" id="Limpiar">
        <img src="Iconos/Limpiar.png" alt="Limpiar" title="Limpiar datos">
        <p>Limpiar Datos</p>    
    </button>
    <style>
        
    .icon-button img {
        width: 48px;
        height: auto;
    }

    .form-buttons {
        display: flex;
        justify-content: center; /* Centra los botones horizontalmente */
        gap: 20px; /* Espacio entre los botones */
    }
    </style>
    </div>
        </form>

        
</div>

<script src="script.js"></script>  
           
</body>
</html>
