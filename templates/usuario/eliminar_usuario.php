<?php
// Incluir el archivo de conexión a la base de datos
include_once '../../includes/db_connect.php';

// Verificar si se recibió el parámetro 'id' en la URL
if (isset($_GET['id'])) {
    
    $id = $_GET['id'];

    // Parámetros para el procedimiento almacenado
    $params = array(
        array($id, SQLSRV_PARAM_IN)
    );

    // Llamar al procedimiento almacenado
    $sql = "{call sp_eliminar_usuario(?)}";
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt) {
        echo '<script>alert("Usuario eliminado correctamente."); window.location.href = "../../usuarios.php";</script>';
    } else {
        echo '<script>alert("Error al eliminar el usuario."); window.location.href = "../../usuarios.php";</script>';
    }

    // Liberar el statement
    sqlsrv_free_stmt($stmt);
}

// Cerrar la conexión
sqlsrv_close($conn);
?>
