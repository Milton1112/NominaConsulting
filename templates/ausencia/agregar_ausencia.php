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

    // Consulta para obtener los datos del empleado y su salario base
    $sql = "
        SELECT nombres + ' ' + apellidos AS Nombre
        FROM Empleado 
        WHERE id_empleado = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $nombres = $row['Nombre'];
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
                    <div>
                        <label for="nombres" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombres" value="<?php echo $nombres; ?>" readonly>
                    </div>
                </div>
                <!-- Línea separadora -->
                <hr>

                <!-- Fecha de Solicitud -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="fecha_inicio" class="form-label">Fecha de Inicio:</label>
                        <input type="date" class="form-control" name="fecha_inicio" id="fecha" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha_fin" class="form-label">Fecha de Fin:</label>
                        <input type="date" class="form-control" name="fecha_fin" id="fecha2" required>
                    </div>
                </div>

                <div>
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-control" name="estado" id="estado" required>
                            <option value="Pendiente">Suspendido</option>
                            <option value="Aprobado">vacaciones</option>
                            <option value="Aprobado">Permiso</option>
                        </select>
                    </div>

                <div class="row mb-3" style="margin-top:20px;">
                    <div class="col-12">
                        <label for="motivo" class="form-label">Motivo:</label>
                        <textarea class="form-control" name="motivo" id="motivo" rows="4" maxlength="255" placeholder="Escribe el motivo aquí..."></textarea>
                        <small class="form-text text-muted">Máximo 255 caracteres.</small>
                    </div>
                </div>


                <!-- Botón para enviar -->
                <button type="submit" class="btn btn-primary w-100">Actualizar Empleado</button>
            </form>
        </div>
    </div>
</div>

<script>
// Establecer la fecha mínima en el campo de fecha para que no se puedan seleccionar fechas pasadas
document.addEventListener('DOMContentLoaded', function() {
    var today = new Date().toISOString().split('T')[0]; // Obtener la fecha actual en formato YYYY-MM-DD
    document.getElementById('fecha').setAttribute('min', today); // Establecer el valor mínimo
    document.getElementById('fecha2').setAttribute('min', today); // Establecer el valor mínimo
});
</script>


<?php

function agregarInfoAusencia($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = $_POST['id'];
        $fechaInicio = $_POST['fecha_inicio']; 
        $fechaFin = $_POST['fecha_fin']; 
        $motivo = $_POST['motivo'];
        $estado = $_POST['estado'];

        // Preparar la consulta del procedimiento almacenado
        $sql = "{CALL sp_insertar_ausencia(?, ?, ?, ?, ?)}";
        $params = array(
            array($fechaInicio, SQLSRV_PARAM_IN),
            array($fechaFin, SQLSRV_PARAM_IN),
            array($motivo, SQLSRV_PARAM_IN),
            array($id, SQLSRV_PARAM_IN),
            array($estado, SQLSRV_PARAM_IN)

        );

        // Ejecutar el procedimiento almacenado
        $stmt = sqlsrv_query($conn, $sql, $params);

        // Verificar si la ejecución fue exitosa
        if ($stmt) {
            echo '<script>alert("Al empleado se le agrego su Permiso."); window.location.href = "../../ausencia.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($stmt);
    }
}

agregarInfoAusencia($conn);

?>