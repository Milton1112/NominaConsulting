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
                he.id_hora_extra, e.nombres, e.apellidos, he.horas, he.tipo, 
                he.fecha
            FROM 
                HorasExtras he
            INNER JOIN
                Empleado e ON he.fk_id_empleado = e.id_empleado
            WHERE
                id_hora_extra = ?;";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $id = $row['id_hora_extra'];
        $nombres = $row['nombres'];
        $apellidos = $row['apellidos'];
        $hora = $row['horas'];
        $tipo = $row['tipo'];
        $fecha = isset($fecha) ? $fecha : date('Y-m-d');


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
            <h1 class="fs-3 mb-0 fw-bold">Horas Extras</h1>
        </div>
    </div>
</header>

<div class="container mt-5 mb-5">
    <div class="card mx-auto rounded" style="max-width: 600px;">
        <div class="card-header text-center bg-primary text-white rounded-top">
            Información del empleado
        </div>
        <div class="card-body">
            <!-- Formulario de actualización -->
            <form action="" method="POST" novalidate>
                <input type="hidden" name="id" value="<?php echo $id; ?>">

                <!-- Nombres y Apellidos -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombres" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombres" value="<?php echo $nombres; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="apellidos" class="form-label">Apellido</label>
                        <input type="text" class="form-control" name="apellidos" value="<?php echo $apellidos; ?>" readonly>
                    </div>
                </div>

                <!-- Línea separadora -->
                <hr>

                <!-- Hora y Tipo -->

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="horas" class="form-label">Horas:</label>
                        <input type="int" class="form-control" name="horas" value="<?php echo $hora; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="tipo" class="form-label">Tipo</label>
                        <input type="text" class="form-control" name="tipo" value="<?php echo $tipo; ?>" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div>
                        <label for="fecha" class="form-label">Fecha:</label>
                        <input type="date" class="form-control" name="fecha" id="fecha_hora_extra" value="<?php echo $fecha; ?>" required>
                    </div>
                </div>
               
                <!-- Botón para enviar -->
                <button type="submit" class="btn btn-primary w-100">Actualizar horas</button>
            </form>
        </div>
    </div>
</div>

<script>
// Establecer la fecha mínima en el campo de fecha para que no se puedan seleccionar fechas pasadas
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0]; // Obtener la fecha actual en formato YYYY-MM-DD
    document.getElementById('fecha_hora_extra').setAttribute('min', today); // Establecer el valor mínimo
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

function actualizarHoraExtra($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $hora = $_POST["horas"];
        $tipo = $_POST["tipo"];
        $fecha = $_POST["fecha"];
        $id = $_POST["id"];

         // Crear parámetros para el procedimiento almacenado
     $sp_params = array(
        array($id, SQLSRV_PARAM_IN),
        array($hora, SQLSRV_PARAM_IN),
        array($tipo, SQLSRV_PARAM_IN),
        array($fecha, SQLSRV_PARAM_IN)
     );

     // Llamar al procedimiento almacenado
    $sp_stmt = sqlsrv_query($conn, "{CALL sp_actualizar_horas_extras(?,?,?,?)}", $sp_params);

    // Verificar si la ejecución fue exitosa
    if ($sp_stmt) {
        echo '<script>alert("Al empleado se le actualizo sus horas extras."); window.location.href = "../../hora_extra.php";</script>';
    } else {
        echo "Error al ejecutar el procedimiento almacenado:<br>";
        die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
    }

    // Liberar recursos
    sqlsrv_free_stmt($sp_stmt);
    }
}

actualizarHoraExtra($conn);
?>