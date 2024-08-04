<?php
// Sanitizar la entrada de datos
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data));
}

// Redirigir a una nueva URL
function redirect($url) {
    header("Location: $url");
    exit();
}

// Mostrar mensajes de error de la base de datos
function showDbError($stmt) {
    if($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
}

// Verificar si un usuario está logueado
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Obtener el nombre de usuario por su ID
function getUsernameById($id) {
    $conn = getConnection();
    $sql = "SELECT username FROM Usuario WHERE id_usuario = ?";
    $params = array($id);
    $stmt = sqlsrv_query($conn, $sql, $params);

    showDbError($stmt);

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    return $user ? $user['username'] : null;
}
?>
