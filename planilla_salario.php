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
$fecha_inicio = null;
$fecha_fin = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscarPlanilla'])) {
    $criterio = $_POST['buscarPlanilla'];
    $fecha_inicio = $_POST['fecha_inicio'] ?? null;
    $fecha_fin = $_POST['fecha_fin'] ?? null;
}

$fk_id_empresa = $_SESSION['fk_id_empresa']; 

$sql = "{CALL sp_listar_planilla(?, ?, ?, ?)}";
$params = array(
    array($criterio, SQLSRV_PARAM_IN),
    array($fk_id_empresa, SQLSRV_PARAM_IN),
    array($fecha_inicio, SQLSRV_PARAM_IN),
    array($fecha_fin, SQLSRV_PARAM_IN)
);

$stmt = sqlsrv_query($conn, $sql, $params);
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

$planilla_data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $year = date('Y', strtotime($row['fecha_contratacion']->format('Y-m-d')));
$month = date('n', strtotime($row['fecha_contratacion']->format('Y-m-d')));

    $planilla_data[$year][$month][] = $row;
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
        <div class="container d-flex justify-content-center">
            <h1 class="fs-3 mb-0 fw-bold">Lista de Planilla</h1>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <input type="text" class="form-control" name="buscarPlanilla" placeholder="Buscar por criterio" value="<?php echo isset($_POST['buscarPlanilla']) ? $_POST['buscarPlanilla'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="fecha_inicio" value="<?php echo isset($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : ''; ?>">
                    </div>
                    <div class="col-md-3">
                        <input type="date" class="form-control" name="fecha_fin" value="<?php echo isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : ''; ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>

            <?php foreach ($planilla_data as $year => $months): ?>
                <div class="accordion mb-3" id="accordionYear<?php echo $year; ?>">
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?php echo $year; ?>">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?php echo $year; ?>" aria-expanded="true" aria-controls="collapse<?php echo $year; ?>">
                                Año <?php echo $year; ?>
                            </button>
                        </h2>
                        <div id="collapse<?php echo $year; ?>" class="accordion-collapse collapse show" aria-labelledby="heading<?php echo $year; ?>">
                            <div class="accordion-body">
                                <?php for ($month = 1; $month <= 12; $month++): ?>
                                    <?php if (isset($months[$month])): ?>
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
                                                                    <th>Nombre</th>
                                                                    <th>Cargo</th>
                                                                    <th>Dependencia</th>
                                                                    <th>Salario Base</th>
                                                                    <th>Descuento IGSS</th>
                                                                    <th>Horas Extras</th>
                                                                    <th>Salario Líquido</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($months[$month] as $row): ?>
                                                                    <tr class="shadow-sm rounded bg-light mb-2">
                                                                        <td class="text-center fw-bold"><?php echo $row['id_empleado']; ?></td>
                                                                        <td class="fw-bold text-primary"><?php echo $row['Nombre']; ?></td>
                                                                        <td><?php echo $row['Cargo']; ?></td>
                                                                        <td><?php echo $row['Dependencia']; ?></td>
                                                                        <td><?php echo number_format($row['salario_base'], 2); ?></td>
                                                                        <td><?php echo number_format($row['Descuento_IGSS'], 2); ?></td>
                                                                        <td><?php echo number_format($row['Horas_Extras'], 2); ?></td>
                                                                        <td><?php echo number_format($row['Liquido'], 2); ?></td>
                                                                    </tr>
                                                                <?php endforeach; ?>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
