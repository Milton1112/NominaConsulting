<?php
include_once '../../includes/db_connect.php'; // Ajusta la ruta según tu estructura de carpetas
include_once '../../includes/functions.php';  // Ajusta la ruta según tu estructura de carpetas

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

$conn = getConnection(); // Obtener la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si se ha proporcionado un ID
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Crear los parámetros para el procedimiento almacenado
    $sql = "{CALL sp_eliminar_categoria(?)}";
    $params = array(
        array($id, SQLSRV_PARAM_IN)
    );

    // Ejecutar el procedimiento almacenado
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si la ejecución fue exitosa
    if ($stmt) {
        echo '<script>alert("La categoría ha sido eliminada correctamente."); window.location.href = "../../categoria.php";</script>';
    } else {
        // Obtener los errores de SQL Server
        $errors = sqlsrv_errors();
        $errorCode = $errors[0]['code'];

        // Verificar si el error es debido a una violación de clave externa (código de error 547)
        if ($errorCode == 547) {
            echo '<script>alert("No se puede eliminar esta categoría porque está relacionada con uno o más productos. Por favor, elimine o actualice las referencias antes de intentarlo nuevamente."); window.location.href = "../../categoria.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r($errors, true));  // Mostrar otros errores de ejecución
        }
    }

    // Liberar el statement
    sqlsrv_free_stmt($stmt);
} else {
    echo "No se proporcionó un ID.";
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
