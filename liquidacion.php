<?php
// Incluir el archivo de conexión
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

$criterio = isset($_POST['buscarLiquidacion']) ? $_POST['buscarLiquidacion'] : null;

// Consultar liquidaciones por año y mes
function listarLiquidacion($conn, $criterio = null) {
    $sp_params = array(
        array($criterio, SQLSRV_PARAM_IN)
    );

    // Llamar al procedimiento almacenado
    $sp_stmt = sqlsrv_query($conn, "{CALL sp_listar_liquidacion(?)}", $sp_params);

    if ($sp_stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $liquidaciones = [];
    while ($row = sqlsrv_fetch_array($sp_stmt, SQLSRV_FETCH_ASSOC)) {
        $year = date_format($row['Fecha_Liquidacion'], "Y");
        $month = date_format($row['Fecha_Liquidacion'], "m");
        $liquidaciones[$year][$month][] = $row;
    }

    sqlsrv_free_stmt($sp_stmt);
    return $liquidaciones;
}

$liquidaciones = listarLiquidacion($conn, $criterio);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        function confirmarEliminacion(event) {
            if (!confirm("¿Está seguro de que desea eliminar esta liquidación?")) {
                event.preventDefault(); // Cancela el envío del formulario si el usuario cancela
            }
        }
    </script>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <h1 class="fs-3 mb-0 fw-bold">Listado de Liquidaciones</h1>
            <a href="templates/liquidacion/agregar_liquidacion.php" class="btn btn-light">Agregar Liquidación</a>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="row mb-3">
                    <div class="col-md-10">
                        <input type="text" class="form-control" name="buscarLiquidacion" placeholder="Buscar por criterio" value="<?php echo htmlspecialchars($criterio); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>

            <?php foreach ($liquidaciones as $year => $months): ?>
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
                                                                    <th>ID Liquidación</th>
                                                                    <th>Empleado</th>
                                                                    <th>Puesto</th>
                                                                    <th>Fecha Fin Contrato</th>
                                                                    <th>Fecha Liquidación</th>
                                                                    <th>Monto Liquidación</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                <?php foreach ($months[$month] as $record): ?>
                                                                    <tr class="shadow-sm rounded bg-light mb-2">
                                                                        <td class="text-center fw-bold"><?php echo $record['id_liquidacion']; ?></td>
                                                                        <td class="fw-bold text-primary"><?php echo $record['Nombre']; ?></td>
                                                                        <td><?php echo $record['Puesto']; ?></td>
                                                                        <td><?php echo date_format($record['fecha_fin_contrato'], "Y-m-d"); ?></td>
                                                                        <td><?php echo date_format($record['Fecha_Liquidacion'], "Y-m-d"); ?></td>
                                                                        <td><?php echo number_format($record['Monto_Liquidacion'], 2); ?></td>
                                                                        <td class="text-center">
                                                                            <form method="POST" action="templates/liquidacion/eliminar_liquidacion.php" style="display:inline;" onsubmit="confirmarEliminacion(event);">
                                                                                <input type="hidden" name="id_liquidacion" value="<?php echo $record['id_liquidacion']; ?>">
                                                                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                                                            </form>
                                                                        </td>
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
sqlsrv_close($conn);
?>
