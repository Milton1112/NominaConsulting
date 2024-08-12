<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/login.css">
</head>
<body>
    <header>
        <label>NOMINA-CONSULTING</label>
    </header>

    <div class="div_center">
        <form method="post" action="">
            <nav class="center-form">
                <nav class="card-form-login">
                    <nav class="card-gray">
                        <img src="assets/images/logo.png" class="logo"/>
                    </nav>

                    <div class="center-form mb-3">
                        <input type="email" name="email" class="input-email form-control" placeholder="name@example.com" autocomplete="email" required>
                    </div>

                    <div class="center-form mb-3">
                        <input type="password" name="password" class="input-pass form-control" placeholder="*******" autocomplete="current-password" required>
                    </div>

                    <button type="submit" name="login" class="login-btn btn btn-primary">Iniciar sesión</button>

                    <div class="center-form mb-3">
                        <button type="button" class="recover-pass btn">¿Olvidaste la contraseña?</button>
                    </div>
                </nav>
            </nav>
        </form>

        <span>Derecho de autor 2024</span>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
include_once 'includes/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    login(); // Llamar a la función de login cuando se envíe el formulario
}

function login(){
    // Variables
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    
    // Obtener la conexión a la base de datos
    $conn = getConnection();
    
    // Encriptar la contraseña antes de enviarla al procedimiento almacenado
    $hashed_password = hash('sha256', $password);
    
    // Preparar la llamada al procedimiento almacenado
    $sql = "{call sp_login(?, ?)}";
    $params = array(
        array(&$email, SQLSRV_PARAM_IN),
        array(&$hashed_password, SQLSRV_PARAM_IN)
    );
    
    // Ejecutar la consulta
    $stmt = sqlsrv_query($conn, $sql, $params);
    
    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }
    
    // Verificar si el usuario fue encontrado
    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
    if ($row) {
        // Usuario encontrado
        echo '<script>alert("Inicio de sesión exitoso, bienvenido ' . $row['username'] . '");</script>';
    } else {
        // Usuario no encontrado
        echo '<script>alert("Credenciales incorrectas, por favor verifica e intenta nuevamente.");</script>';
    }
    
    // Cerrar la declaración y la conexión
    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);
}

function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
