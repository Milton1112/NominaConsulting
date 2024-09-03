<?php
include_once 'includes/functions.php';
include 'includes/db_connect.php';

$conn = getConnection(); // Punto y coma agregado

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si estamos en la página correcta y si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn(); // Redirige al login si no está logueado
}

// Verificar si el botón de cerrar sesión ha sido presionado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signout'])) {
    SignOut(); // Llama a la función para cerrar la sesión
}

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navbar Bonito</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        .navbar-brand {
            font-size: 1.5rem;
            font-weight: bold;
            color: #fff;
        }
        .navbar-nav .nav-link {
            font-size: 1.2rem;
            color: #fff;
        }
        .btn-logout {
            color: #FFFFFF;
            background-color: #2F2C59;
            border: none;
        }
        .btn-logout:hover {
            background-color: #363271;
            color: #FFFFFF;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-building"></i> NominaConsulting</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user"></i> Empleado</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-store"></i> Tienda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-briefcase"></i> Empresa</a>
                    </li>
                </ul>
            </div>
            <!-- Formulario para cerrar sesión -->
            <form class="form-inline" method="post">
                <button class="btn btn-logout my-2 my-sm-0" type="submit" name="signout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</button>
            </form>
        </div>
    </nav>
</body>
</html>

