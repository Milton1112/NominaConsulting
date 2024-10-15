<?php

include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Inicializar variables de búsqueda
$criterio = "";
$fecha_inicio = NULL;
$fecha_fin = NULL;

// Verificar si el usuario ha enviado una búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['buscarEmpleado'])) {
        $criterio = $_POST['buscarEmpleado'];
    }
    if (isset($_POST['fecha_inicio']) && !empty($_POST['fecha_inicio'])) {
        $fecha_inicio = $_POST['fecha_inicio'];
    }
    if (isset($_POST['fecha_fin']) && !empty($_POST['fecha_fin'])) {
        $fecha_fin = $_POST['fecha_fin'];
    }
}

// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa'];

// Procedimiento almacenado para listar bonos 14 con filtro, fechas y fk_id_empresa
$sql = "{CALL sp_listar_bono14(?, ?, ?, ?)}"; // Ajustar según el SP
$params = array(
    array($fk_id_empresa, SQLSRV_PARAM_IN), // Pasar fk_id_empresa como parámetro
    array($criterio, SQLSRV_PARAM_IN),      // Criterio de búsqueda
    array($fecha_inicio, SQLSRV_PARAM_IN),  // Fecha inicio
    array($fecha_fin, SQLSRV_PARAM_IN)      // Fecha fin
);

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    echo '<div class="alert alert-danger">Ocurrió un error al realizar la búsqueda de bonos.</div>';
}


?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body {
            background-color: #F4F7FC;
        }
        .table thead {
            background-color: #2F2C59;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #DDE2FF;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Lista de Bonos 14</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            
        <form method="POST" action="">
    <div class="input-group mb-3">
        <input type="text" class="form-control" name="buscarEmpleado" placeholder="Buscar por nombre o apellido" value="<?php echo isset($_POST['buscarEmpleado']) ? $_POST['buscarEmpleado'] : ''; ?>" aria-label="Buscar bono">
        <input type="date" class="form-control" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>" placeholder="Fecha de inicio">
        <input type="date" class="form-control" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>" placeholder="Fecha de fin">
        <button type="submit" class="btn btn-primary">Buscar</button>
    </div>
</form>
    
            <?php if (sqlsrv_has_rows($stmt)) : ?>
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="bg-gradient bg-primary text-white rounded">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nombre Empleado</th>
                            <th>Monto</th>
                            <th>Fecha</th>
                            <th>Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                            <tr class="shadow-sm rounded bg-light mb-2">
                                <td class="text-center fw-bold"><?php echo $row['id_bono']; ?></td>
                                <td class="fw-bold text-primary"><?php echo $row['Nombre']; ?></td>
                                <td><?php echo $row['monto']; ?></td>
                                <td><?php echo $row['fecha'] instanceof DateTime ? $row['fecha']->format('Y-m-d') : ''; ?></td>
                                <td><?php echo $row['Empresa']; ?></td>

                                <td>
                                    <div class="dropdown">
                                        <a class="fas fa-ellipsis-v" href="#" role="button" id="dropdownMenuLink<?php echo $row['id_bono']; ?>" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                        <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink<?php echo $row['id_bono']; ?>">
                                            <li><a class="dropdown-item" href="eliminar_bono14.php?id=<?php echo $row['id_bono']; ?>">Eliminar Bono 14</a></li>
                                            <li><a class="dropdown-item" href="descargar_bono_pdf.php?id=<?php echo $row['id_bono']; ?>">Descargar PDF</a></li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            <?php else : ?>
                <div class="alert alert-warning text-center">No se encontraron bonos que coincidan con el criterio de búsqueda.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
