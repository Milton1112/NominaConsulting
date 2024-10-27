<?php
// Incluir el archivo de conexión
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); 
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Verificar si se ha enviado el formulario para eliminar la liquidación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id_liquidacion'])) {
    $id_liquidacion = $_POST['id_liquidacion']; // Capturar el ID de la liquidación del formulario

    // Consultar el fk_id_empleado usando el id_liquidacion
    $query = "SELECT fk_id_empleado FROM Liquidacion WHERE id_liquidacion = ?";
    $params = array($id_liquidacion);
    $result = sqlsrv_query($conn, $query, $params);

    if ($result === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC);

    if ($row) {
        $fk_id_empleado = $row['fk_id_empleado'];

        // Llamar al procedimiento almacenado para eliminar la liquidación
        $sp_params = array(
            array($id_liquidacion, SQLSRV_PARAM_IN),
            array($fk_id_empleado, SQLSRV_PARAM_IN)
        );

        $stmt = sqlsrv_query($conn, "{CALL sp_eliminar_liquidacion(?, ?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($stmt === false) {
            die(print_r(sqlsrv_errors(), true));
        } else {
            // Redirigir a la página principal de liquidación
            header("Location: ../../liquidacion.php");
            exit(); // Asegurarse de detener la ejecución del script después de la redirección
        }

        // Liberar recursos
        sqlsrv_free_stmt($stmt);
    } else {
        echo "No se encontró ninguna liquidación con el ID proporcionado.";
    }

    // Liberar recursos
    sqlsrv_free_stmt($result);
}

sqlsrv_close($conn);
?>
