<?php
session_start();
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);

    $conn = getConnection();
    $sql = "SELECT u.id_usuario, u.correo, u.contrasena, e.nombres + ' ' + e.apellidos AS username 
            FROM Usuario u 
            INNER JOIN Empleado e ON u.fk_id_empresa = e.id_empleado 
            WHERE u.correo = ?";
    $params = array($email);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($user) {
        // Debugging information
        echo "Usuario encontrado: ";
        print_r($user);
        echo "<br>";
    } else {
        echo "Usuario no encontrado.<br>";
    }

    if ($user && password_verify($password, $user['contrasena'])) {
        $_SESSION['user_id'] = $user['id_usuario'];
        $_SESSION['username'] = $user['username'];
        redirect('index.php');
    } else {
        $login_error = "Correo o contraseña incorrectos.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <form method="post" action="login.php">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required>
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Iniciar Sesión</button>
    </form>
    <?php if (isset($login_error)): ?>
        <p><?php echo $login_error; ?></p>
    <?php endif; ?>
</body>
</html>
