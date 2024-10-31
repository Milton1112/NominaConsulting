<?php
// Incluir archivos de conexión y funciones
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2();
}

$user = $_SESSION['correo_usuario'];
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Obtener conexión
$conn = getConnection();

// Verificar conexión
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

function procesarVenta($conn, $fk_id_empresa) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $fecha = $_POST["fecha"];
        $cantidad = $_POST["cantidad"];
        $dpi_pasaporte = $_POST["dpi_pasaporte"];
        $id_producto = $_POST["id_producto"];

        // Parámetros para el procedimiento almacenado
        $params = array(
            array($fecha, SQLSRV_PARAM_IN),
            array($cantidad, SQLSRV_PARAM_IN),
            array($dpi_pasaporte, SQLSRV_PARAM_IN),
            array($id_producto, SQLSRV_PARAM_IN),
            array($fk_id_empresa, SQLSRV_PARAM_IN)
        );

        // Llamar al procedimiento almacenado
        $stmt = sqlsrv_query($conn, "{CALL sp_venta_tienda(?, ?, ?, ?, ?)}", $params);

        // Verificar si se ejecutó correctamente
        if ($stmt) {
            echo '<script>alert("Venta registrada exitosamente y cantidad actualizada."); window.location.href = "../../index.php";</script>';
        } else {
            echo "Error al registrar la venta:<br>";
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($stmt);
    }
}

procesarVenta($conn, $fk_id_empresa);

// Obtener productos de la empresa
$sqlProducto = "SELECT id_producto, nombre FROM Producto WHERE fk_id_empresa = ?";
$paramsProducto = array($fk_id_empresa);
$stmtProducto = sqlsrv_query($conn, $sqlProducto, $paramsProducto);

?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Venta de Productos - Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../oficina.php" class="btn btn-outline-light d-flex align-items-center">Regresar</a>
            <div class="text-center flex-grow-1"><h1>Venta de Producto</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">Formulario de Venta</div>
            <div class="card-body">
                <form action="" method="POST" novalidate>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha</label>
                        <input type="date" class="form-control" name="fecha" required>
                        <div class="invalid-feedback">Por favor, selecciona una fecha.</div>
                    </div>

                    <div class="mb-3">
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad" min="1" required>
                        <div class="invalid-feedback">Por favor, ingresa la cantidad.</div>
                    </div>

                    <div class="mb-3">
                        <label for="dpi_pasaporte" class="form-label">DPI/Pasaporte del Empleado</label>
                        <input type="text" class="form-control" name="dpi_pasaporte" required>
                        <div class="invalid-feedback">Por favor, ingresa el DPI o pasaporte del empleado.</div>
                    </div>

                    <div class="mb-3">
                        <label for="id_producto" class="form-label">Producto</label>
                        <select class="form-control" name="id_producto" required>
                            <option value="">Seleccione un producto</option>
                            <?php while ($row = sqlsrv_fetch_array($stmtProducto, SQLSRV_FETCH_ASSOC)): ?>
                                <option value="<?= $row["id_producto"] ?>"><?= $row["nombre"] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona un producto.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Registrar Venta</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
