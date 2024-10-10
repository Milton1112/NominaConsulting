<?php
// Incluir el archivo de conexión
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Obtener la conexión
$conn = getConnection();

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Consulta para obtener los datos del empleado
    $sql = "SELECT
		a.id_anticipo AS id, e.nombres + ' ' + e.apellidos AS Nombre,
		a.fecha_solicitud AS Fecha, a.monto_solicitado AS Monto, a.estado
	FROM
		Anticipo a
	INNER JOIN
		Empleado e ON a.fk_id_empleado = e.id_empleado
	WHERE
	    id_anticipo = ?;";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $id = $row['id'];
        $nombres = $row['Nombre'];
        $fecha = $row['Fecha']->format('Y-m-d');
        $monto = $row['Monto'];
        $estado = $row['estado'];
    } else {
        echo "No se encontró el empleado.";
        exit;
    }
} else {
    echo "No se proporcionó el ID del empleado.";
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<header class="bg-primary text-white py-3 shadow-sm">
    <div class="container d-flex justify-content-between align-items-center">
        <a href="../../hora_extra.php" class="btn btn-outline-light d-flex align-items-center">
            <i class="bi bi-arrow-left-circle me-2"></i> Regresar
        </a>
        <div class="text-center flex-grow-1">
            <h1 class="fs-3 mb-0 fw-bold">Anticipo del Empleado</h1>
        </div>
    </div>
</header>

<div class="container mt-5 mb-5">
    <div class="card mx-auto rounded" style="max-width: 600px;">
        <div class="card-header text-center bg-primary text-white rounded-top">
            Información del Anticipo:
        </div>
        <div class="card-body">
            <!-- Formulario de actualización -->
            <form action="" method="POST" novalidate>
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <!-- Nombres y monto -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombre Completo:</label>
                        <input type="text" class="form-control" name="nombres" value="<?php echo $nombres; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="monto" class="form-label">Monto Solicitado:</label>
                        <input type="number" class="form-control" name="monto" value="<?php echo $monto; ?>" readonly>
                    </div>
                </div>

                <!-- Fecha (solo visualización) -->
                <div >
                    <div>
                        <label for="fecha" class="form-label">Fecha de Solicitud:</label>
                        <input type="date" class="form-control" name="fecha" value="<?php echo $fecha; ?>" readonly>
                    </div>
                </div>

                
                <!-- Línea separadora -->
                <hr>

                <!-- Estado con Dropdown -->
                <div class="row mb-3">
                    <div >
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" name="estado" id="estado" required>
                            <option value="Pendiente" <?php echo $estado == 'Pendiente' ? 'selected' : ''; ?>>Pendiente</option>
                            <option value="Aprobado" <?php echo $estado == 'Aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                            <option value="Rechazado" <?php echo $estado == 'Rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                        </select>
                    </div>
                </div>

                <!-- Botón para enviar -->
                <button type="submit" class="btn btn-primary w-100">Actualizar Anticipo</button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
function actualizarAnticipo($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $estado = $_POST["estado"];
        $id = $_POST["id"];

        
         // Crear parámetros para el procedimiento almacenado
         $sp_params = array(
            array($id, SQLSRV_PARAM_IN),
            array($estado, SQLSRV_PARAM_IN)
        );

        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_actualizar_anticipo(?,?)}", $sp_params);

         // Verificar si la ejecución fue exitosa
         if ($sp_stmt) {
            echo '<script>alert("Al empleado se le actualizo su anticipo."); window.location.href = "../../anticipo.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}


actualizarAnticipo($conn);
?>