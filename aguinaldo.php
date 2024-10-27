<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2();
}

$criterio = "";
$fecha_inicio = NULL;
$fecha_fin = NULL;

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

$fk_id_empresa = $_SESSION['fk_id_empresa'];

// Procedimiento almacenado para listar aguinaldos con filtro, fechas y empresa
$sql = "{CALL sp_listar_aguinaldo(?, ?, ?, ?)}";
$params = array(
    array($fk_id_empresa, SQLSRV_PARAM_IN),
    array($criterio, SQLSRV_PARAM_IN),
    array($fecha_inicio, SQLSRV_PARAM_IN),
    array($fecha_fin, SQLSRV_PARAM_IN)
);

$stmt = sqlsrv_query($conn, $sql, $params);

if ($stmt === false) {
    echo '<div class="alert alert-danger">Ocurrió un error al realizar la búsqueda de aguinaldos.</div>';
}

$aguinaldo_data = [];
if (sqlsrv_has_rows($stmt)) {
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $year = $row['fecha']->format('Y');
        $month = $row['fecha']->format('n');
        $aguinaldo_data[$year][$month][] = $row;
    }
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
                <h1 class="fs-3 mb-0 fw-bold">Lista de Aguinaldos</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="buscarEmpleado" placeholder="Buscar por nombre o apellido" value="<?php echo isset($_POST['buscarEmpleado']) ? $_POST['buscarEmpleado'] : ''; ?>" aria-label="Buscar aguinaldo">
                    <input type="date" class="form-control" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>" placeholder="Fecha de inicio">
                    <input type="date" class="form-control" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>" placeholder="Fecha de fin">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="templates/aguinaldo/agregar_aguinaldo.php" class="btn btn-outline-secondary ms-2">Agregar</a>
                </div>
            </form>

            <?php if (!empty($aguinaldo_data)) : ?>
                <?php foreach ($aguinaldo_data as $year => $months) : ?>
                    <div class="accordion mb-3" id="accordionYear<?php echo $year; ?>">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading<?php echo $year; ?>">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $year; ?>" aria-expanded="true" aria-controls="collapse<?php echo $year; ?>">
                                    Año <?php echo $year; ?>
                                </button>
                            </h2>
                            <div id="collapse<?php echo $year; ?>" class="accordion-collapse collapse show" aria-labelledby="heading<?php echo $year; ?>">
                                <div class="accordion-body">
                                    <?php foreach ($months as $month => $rows) : ?>
                                        <div class="accordion mb-3" id="accordionMonth<?php echo $year . $month; ?>">
                                            <div class="accordion-item">
                                                <h2 class="accordion-header" id="headingMonth<?php echo $year . $month; ?>">
                                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMonth<?php echo $year . $month; ?>" aria-expanded="false" aria-controls="collapseMonth<?php echo $year . $month; ?>">
                                                        <?php echo date("F", mktime(0, 0, 0, $month, 1)); ?>
                                                    </button>
                                                </h2>
                                                <div id="collapseMonth<?php echo $year . $month; ?>" class="accordion-collapse collapse" aria-labelledby="headingMonth<?php echo $year . $month; ?>">
                                                    <div class="accordion-body">
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
                                                                <?php foreach ($rows as $row) : ?>
                                                                    <tr class="shadow-sm rounded bg-light mb-2">
                                                                        <td class="text-center fw-bold"><?php echo $row['id_aguinaldo']; ?></td>
                                                                        <td class="fw-bold text-primary"><?php echo $row['Nombre']; ?></td>
                                                                        <td><?php echo $row['monto']; ?></td>
                                                                        <td><?php echo $row['fecha'] instanceof DateTime ? $row['fecha']->format('Y-m-d') : ''; ?></td>
                                                                        <td><?php echo $row['Empresa']; ?></td>
                                                                        <td>
                                                                            <div class="dropdown">
                                                                                <a class="fas fa-ellipsis-v text-dark" href="#" role="button" id="dropdownMenuLink<?php echo $row['id_aguinaldo']; ?>" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                                                                <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink<?php echo $row['id_aguinaldo']; ?>">
                                                                                    <li><a class="dropdown-item" href="templates/aguinaldo/eliminar_aguinaldo.php?id=<?php echo $row['id_aguinaldo']; ?>">Eliminar Aguinaldo</a></li>
                                                                                </ul>
                                                                            </div>
                                                                        </td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="alert alert-warning text-center">No se encontraron aguinaldos que coincidan con el criterio de búsqueda.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php
if ($stmt) {
    sqlsrv_free_stmt($stmt);
}
sqlsrv_close($conn);
?>
