<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Verificar si se ha pasado el ID de la venta en la URL
if (!isset($_GET['id'])) {
    die("Error: No se especificó la venta a eliminar.");
}

// Obtener el id de la venta y el id de la empresa desde la sesión
$idVentaTienda = (int) $_GET['id'];
$idEmpresa = (int) $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Parámetros para el procedimiento almacenado
$params = array(
    array($idVentaTienda, SQLSRV_PARAM_IN),
    array($idEmpresa, SQLSRV_PARAM_IN)
);

// Ejecutar el procedimiento almacenado para eliminar la venta
$sql = "{CALL sp_eliminar_venta(?, ?)}";
$stmt = sqlsrv_query($conn, $sql, $params);

// Verificar si la consulta fue exitosa
if ($stmt) {
    echo '<script>alert("Venta eliminada exitosamente."); window.location.href = "../../ventas.php";</script>';
} else {
    echo "Error al eliminar la venta:<br>";
    die(print_r(sqlsrv_errors(), true));
}

sqlsrv_free_stmt($stmt); // Liberar recurso
sqlsrv_close($conn); // Cerrar conexión
?>
