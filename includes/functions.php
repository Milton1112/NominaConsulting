<?php
session_start(); // Asegúrate de que la sesión esté iniciada
// Sanitizar la entrada de datos
function sanitizeInput($data) {
    return htmlspecialchars(strip_tags($data));
}

// Verificar si ha iniciado sesión
function SignIn() {
    if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
        // Si el usuario ha iniciado sesión, redirigir a index.php
        header("Location: index.php");
        exit();
    } else {
        // Si el usuario no ha iniciado sesión, redirigir a login.php
        header("Location: login.php");
        exit();
    }
}
function SignIn2() {
    if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
        // Si el usuario ha iniciado sesión, redirigir a index.php
        header("Location: index.php");
        exit();
    } else {
        // Si el usuario no ha iniciado sesión, redirigir a login.php
        header("Location: ../../login.php");
        exit();
    }
}

function SignIn3() {
    if (isset($_SESSION['usuario_logueado']) && $_SESSION['usuario_logueado'] === true) {
        // Si el usuario ha iniciado sesión, redirigir a index.php
        header("Location: index.php");
        exit();
    } else {
        // Si el usuario no ha iniciado sesión, redirigir a login.php
        header("Location: ../login.php");
        exit();
    }
}

// Cerrar sesión
function SignOut() {
    // Iniciar la sesión si no está ya iniciada
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }

    // Destruir todas las variables de sesión
    $_SESSION = array();

    // Si se desea destruir la sesión completamente, se debe borrar la cookie de sesión
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    // Finalmente, destruir la sesión
    session_destroy();

    // Redirigir al usuario a la página de login u otra página después de cerrar sesión
    header("Location: login.php");
    exit();
}
?>
