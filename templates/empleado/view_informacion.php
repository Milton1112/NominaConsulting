<?php
// Incluir los archivos de conexión y funciones
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está activa
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirigir al login si no está logueado
    exit;
}

// Obtener la conexión
$conn = getConnection();

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id_empleado = $_GET['id'];

    // Validar que el ID sea un número entero
    if (!is_numeric($id_empleado)) {
        echo "ID de empleado no válido.";
        exit;
    }

    // Consulta para obtener los datos del empleado con prepared statements
    $sql = "SELECT 
                e.id_empleado, CONCAT(e.nombres, ' ', e.apellidos) AS Nombre,
                e.fecha_contratacion, e.tipo_contrato, e.puesto, e.dpi_pasaporte,
                e.carnet_igss, e.carnet_irtra, e.fecha_nacimiento, e.numero_telefono,
                e.correo_electronico, o.nombre AS Oficina, p.nombre AS Profesion,
                d.nombre AS Departamento, r.nombre AS Rol, es.nombre AS Estado,
                em.nombre AS Empresa
            FROM 
                Empleado e
            INNER JOIN
                Oficina o ON e.fk_id_oficina = o.id_oficina
            INNER JOIN
                Profesion p ON e.fk_id_profesion = p.id_profesion
            INNER JOIN
                Departamento d ON e.fk_id_departamento = d.id_departamento
            INNER JOIN 
                Rol r ON e.fk_id_rol = r.id_rol
            INNER JOIN 
                Estado es ON e.fk_id_estado = es.id_estado 
            INNER JOIN 
                Empresa em ON e.fk_id_empresa = em.id_empresa
            WHERE
                e.id_empleado = ?";
                
    $params = array($id_empleado);
    $stmt = sqlsrv_prepare($conn, $sql, $params);

    // Ejecutar la consulta y obtener el resultado
    if (sqlsrv_execute($stmt) && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Asignar los datos a variables
        $nombres = htmlspecialchars($row['Nombre']);
        $tipo_contrato = htmlspecialchars($row['tipo_contrato']);
        $fecha_contratacion = htmlspecialchars($row['fecha_contratacion']->format('Y-m-d'));
        $puesto = htmlspecialchars($row['puesto']);
        $dpi_pasaporte = htmlspecialchars($row['dpi_pasaporte']);
        $carnet_igss = htmlspecialchars($row['carnet_igss']);
        $carnet_irtra = htmlspecialchars($row['carnet_irtra']);
        $fecha_nacimiento = htmlspecialchars($row['fecha_nacimiento']->format('Y-m-d'));
        $correo_electronico = htmlspecialchars($row['correo_electronico']);
        $numero_telefono = htmlspecialchars($row['numero_telefono']);
        $oficina = htmlspecialchars($row['Oficina']);
        $profesion = htmlspecialchars($row['Profesion']);
        $departamento = htmlspecialchars($row['Departamento']);
        $Rol = htmlspecialchars($row['Rol']);
        $estado = htmlspecialchars($row['Estado']);
        $empresa = htmlspecialchars($row['Empresa']);
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
        <a href="../../empleados.php" class="btn btn-outline-light d-flex align-items-center">
            <i class="bi bi-arrow-left-circle me-2"></i> Regresar
        </a>
        <div class="text-center flex-grow-1">
            <h1 class="fs-3 mb-0 fw-bold">Empleado</h1>
        </div>
    </div>
</header>

<div class="container mt-5 mb-5">
    <div class="card mx-auto rounded" style="max-width: 600px;">
        <div class="card-header text-center bg-primary text-white rounded-top">
            Información del Empleado
        </div>
        <div class="card-body">
            <!-- Información del empleado -->
            <div class="mb-3">
                <label class="form-label">Nombre Completo:</label>
                <p class="form-control-plaintext"><?php echo $nombres; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Tipo de Contrato:</label>
                <p class="form-control-plaintext"><?php echo $tipo_contrato; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de Contratación:</label>
                <p class="form-control-plaintext"><?php echo $fecha_contratacion; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Puesto:</label>
                <p class="form-control-plaintext"><?php echo $puesto; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">DPI/Pasaporte:</label>
                <p class="form-control-plaintext"><?php echo $dpi_pasaporte; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Carnet IGSS:</label>
                <p class="form-control-plaintext"><?php echo $carnet_igss; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Carnet IRTRA:</label>
                <p class="form-control-plaintext"><?php echo $carnet_irtra; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Fecha de Nacimiento:</label>
                <p class="form-control-plaintext"><?php echo $fecha_nacimiento; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Correo Electrónico:</label>
                <p class="form-control-plaintext"><?php echo $correo_electronico; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Número de Teléfono:</label>
                <p class="form-control-plaintext"><?php echo $numero_telefono; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Oficina:</label>
                <p class="form-control-plaintext"><?php echo $oficina; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Profesión:</label>
                <p class="form-control-plaintext"><?php echo $profesion; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Departamento:</label>
                <p class="form-control-plaintext"><?php echo $departamento; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Rol:</label>
                <p class="form-control-plaintext"><?php echo $Rol; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Estado:</label>
                <p class="form-control-plaintext"><?php echo $estado; ?></p>
            </div>
            <div class="mb-3">
                <label class="form-label">Empresa:</label>
                <p class="form-control-plaintext"><?php echo $empresa; ?></p>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
