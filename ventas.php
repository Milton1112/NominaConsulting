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
$fechaInicio = null;
$fechaFin = null;

// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa'];

// Verificar si el usuario ha enviado una búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $criterio = $_POST['criterio'] ?? '';
    $fechaInicio = $_POST['fechaInicio'] ?? null;
    $fechaFin = $_POST['fechaFin'] ?? null;
}

// Procedimiento almacenado para listar ventas con filtro
$sql = "{CALL sp_listar_venta(?, ?, ?, ?)}";
$params = array(
    array($criterio, SQLSRV_PARAM_IN),
    array($fk_id_empresa, SQLSRV_PARAM_IN),
    array($fechaInicio, SQLSRV_PARAM_IN),
    array($fechaFin, SQLSRV_PARAM_IN)
);

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Agrupar ventas por mes
$ventasPorMes = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $mes = date_format($row['fecha'], "Y-m"); // Obtener el mes y año
    if (!isset($ventasPorMes[$mes])) {
        $ventasPorMes[$mes] = [];
    }
    $ventasPorMes[$mes][] = $row;
}

sqlsrv_free_stmt($stmt); // Liberar recurso
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ventas de Productos - Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-light">Regresar</a>
            <h1 class="fs-3 mb-0 text-center">Ventas de Productos</h1>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="criterio" placeholder="Buscar por criterio" value="<?= $criterio ?>">
                    <input type="date" class="form-control" name="fechaInicio" value="<?= $fechaInicio ?>">
                    <input type="date" class="form-control" name="fechaFin" value="<?= $fechaFin ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </form>

            <!-- Mostrar ventas en colapsos por mes -->
            <div class="accordion" id="accordionVentas">
                <?php foreach ($ventasPorMes as $mes => $ventas): ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $mes ?>">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse<?= $mes ?>" aria-expanded="false" aria-controls="collapse<?= $mes ?>">
                                Ventas de <?= date("F Y", strtotime($mes . "-01")) ?>
                            </button>
                        </h2>
                        <div id="collapse<?= $mes ?>" class="accordion-collapse collapse" aria-labelledby="heading<?= $mes ?>" data-bs-parent="#accordionVentas">
                            <div class="accordion-body">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Producto</th>
                                            <th>Precio</th>
                                            <th>Categoría</th>
                                            <th>Marca</th>
                                            <th>Cantidad Vendida</th>
                                            <th>Fecha</th>
                                            <th>Monto Total</th>
                                            <th>Empleado</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($ventas as $venta): ?>
                                            <tr>
                                                <td><?= $venta['id_venta_tienda'] ?></td>
                                                <td><?= $venta['Nombre Producto'] ?></td>
                                                <td><?= $venta['Precio'] ?></td>
                                                <td><?= $venta['Categoria'] ?></td>
                                                <td><?= $venta['Marca'] ?></td>
                                                <td><?= $venta['Cantidad Vendida'] ?></td>
                                                <td><?= date_format($venta['fecha'], 'Y-m-d') ?></td>
                                                <td><?= $venta['Monto Total'] ?></td>
                                                <td><?= $venta['Empleado'] ?></td>
                                                <td>
                                                    <a href="templates/venta/eliminar_venta.php?id=<?= $venta['id_venta_tienda'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar esta venta?');">Eliminar</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
sqlsrv_close($conn);
?>
