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

// Obtener las listas de Oficina, Profesión, Rol y Estado para los dropdowns
$sqlOficina = "SELECT id_oficina, nombre FROM Oficina";
$stmtOficina = sqlsrv_query($conn, $sqlOficina);

$sqlProfesion = "SELECT id_profesion, nombre FROM Profesion";
$stmtProfesion = sqlsrv_query($conn, $sqlProfesion);

$sqlRol = "SELECT id_rol, nombre FROM Rol";
$stmtRol = sqlsrv_query($conn, $sqlRol);

$sqlEstado = "SELECT id_estado, nombre FROM Estado";
$stmtEstado = sqlsrv_query($conn, $sqlEstado);

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id = $_GET['id'];


    // Consulta para obtener los datos del empleado
    $sql = "SELECT * FROM Empleado WHERE id_empleado = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $nombres = $row['nombres'];
        $apellidos = $row['apellidos'];
        $tipo_contrato = $row['tipo_contrato'];
        $fecha_contratacion = $row['fecha_contratacion'];
        $puesto = $row['puesto'];
        $dpi_pasaporte = $row['dpi_pasaporte'];
        $carnet_igss = $row['carnet_igss'];
        $carnet_irtra = $row['carnet_irtra'];
        $fecha_nacimiento = $row['fecha_nacimiento'];
        $correo_electronico = $row['correo_electronico'];
        $numero_telefono = $row['numero_telefono'];
        $fk_id_oficina = $row['fk_id_oficina'];
        $fk_id_profesion = $row['fk_id_profesion'];
        $fk_id_rol = $row['fk_id_rol'];
        $fk_id_estado = $row['fk_id_estado'];
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

                <!-- DPI -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="dpi_pasaporte" class="form-label">DPI/Pasaporte</label>
                        <input type="text" class="form-control" name="dpi_pasaporte" value="<?php echo $dpi_pasaporte; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="fk_id_oficina" class="form-label">Oficina</label>
                        <input type="text" class="form-control" id="fk_id_oficina" name="fk_id_oficina" 
                        value="<?php
                        // Obtener el nombre de la oficina seleccionada
                        while ($rowOficina = sqlsrv_fetch_array($stmtOficina, SQLSRV_FETCH_ASSOC)) {
                            if ($fk_id_oficina == $rowOficina['id_oficina']) {
                                echo $rowOficina['nombre']; // Mostrar el nombre de la oficina seleccionada
                                }
                            }
                            ?>" readonly>
                    </div>
                </div>

                <!-- Línea separadora -->
                <hr>

                <!-- Campos adicionales para las horas extras -->
                <div class="row mb-3">
                    <!-- Horas -->
                    <div class="col-md-6">
                        <label for="horas" class="form-label">Horas Extras</label>
                        <input type="number" class="form-control" name="horas" id="horas" placeholder="Ingrese las horas extras" required>
                    </div>
                    <!-- Tipo (Descripción) -->
                    <div class="col-md-6">
                        <label for="tipo" class="form-label">Tipo (Descripción)</label>
                        <input type="text" class="form-control" name="tipo" id="tipo" placeholder="Ingrese la descripción" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <!-- Fecha de la hora extra -->
                    <div class="col-md-6">
                        <label for="fecha_hora_extra" class="form-label">Fecha de la hora extra</label>
                        <input type="date" class="form-control" name="fecha_hora_extra" id="fecha_hora_extra" required>
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
    document.getElementById('fecha_hora_extra').setAttribute('min', today); // Establecer el valor mínimo
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php

function agregarHoraExtra($conn){
    if ($_SERVER["REQUEST_METHOD"] == "POST"){
        $hora = $_POST["horas"];
        $tipo = $_POST["tipo"];
        $fecha = $_POST["fecha_hora_extra"];
        $id = $_POST["id"];

         // Crear parámetros para el procedimiento almacenado
         $sp_params = array(
            array($hora, SQLSRV_PARAM_IN),
            array($tipo, SQLSRV_PARAM_IN),
            array($fecha, SQLSRV_PARAM_IN),
            array($id, SQLSRV_PARAM_IN)
         );

         // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_insertar_horas_extras(?,?,?,?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            echo '<script>alert("Al empleado se le agrego sus horas extras."); window.location.href = "../../hora_extra.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}

agregarHoraExtra($conn);

?>
