<?php
include_once '../../includes/db_connect.php'; // Ajusta la ruta según tu estructura de carpetas
include_once '../../includes/functions.php';  // Ajusta la ruta según tu estructura de carpetas

$conn = getConnection(); // Obtener la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si el ID está presente en la URL
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Procedimiento almacenado para eliminar el estado
    $sql = "{CALL sp_eliminar_estado(?)}";
    $params = array(
        array($id, SQLSRV_PARAM_IN)
    );

    // Ejecutar la consulta
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si la consulta fue exitosa
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    // Liberar el statement
    sqlsrv_free_stmt($stmt);

    // Redirigir a la lista de estados con un mensaje de éxito
    header("Location: ../../estado.php?mensaje=Estado eliminado correctamente");
    exit();
} else {
    // Si no se proporciona un ID, redirigir a la lista de estados
    header("Location: ../../index.php?mensaje=Error: ID no proporcionado");
    exit();
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
