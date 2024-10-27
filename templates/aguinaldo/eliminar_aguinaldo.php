<?php
// Incluir archivos de conexión y funciones
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Iniciar la sesión si no está ya activa
}

if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirigir al login si no está logueado
}

// Verificar si se ha recibido un ID de aguinaldo por URL
$id_aguinaldo = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id_aguinaldo === 0) {
    die("ID de aguinaldo inválido.");
}

// Comprobar si se ha enviado el formulario de confirmación
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $eliminar_todos = isset($_POST['eliminar_todos']) ? 1 : 0;

    // Ejecutar el procedimiento almacenado para eliminar el aguinaldo
    $params = array(
        array($id_aguinaldo, SQLSRV_PARAM_IN),
        array($eliminar_todos, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, "{CALL sp_eliminar_aguinaldo(?, ?)}", $params);

    if ($stmt) {
        // Mensaje de éxito
        $result = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
        echo '<script>alert("' . $result['Resultado'] . '"); window.location.href = "../../aguinaldo.php";</script>';
    } else {
        // Mostrar errores en caso de fallo
        echo "Error al ejecutar el procedimiento almacenado:<br>";
        die(print_r(sqlsrv_errors(), true));
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
    exit;
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Eliminar Aguinaldo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../aguinaldo.php" class="btn btn-outline-light d-flex align-items-center">Regresar</a>
            <div class="text-center flex-grow-1"><h1>Eliminar Aguinaldo</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 500px;">
            <div class="card-body">
                <h5 class="card-title text-center">¿Deseas eliminar este aguinaldo o todos los aguinaldos del empleado?</h5>
                <form method="POST" action="">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="eliminar_todos" id="soloUno" value="0" checked>
                        <label class="form-check-label" for="soloUno">
                            Eliminar solo este aguinaldo
                        </label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="eliminar_todos" id="todos" value="1">
                        <label class="form-check-label" for="todos">
                            Eliminar todos los aguinaldos del empleado
                        </label>
                    </div>
                    <button type="submit" class="btn btn-danger w-100 mt-3">Confirmar Eliminación</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
