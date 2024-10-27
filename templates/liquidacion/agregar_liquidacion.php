<?php
// Incluir el archivo de conexión
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

$user = $_SESSION['correo_usuario'];

// Obtener la conexión
$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Función para liberar recursos y cerrar la conexión
function cerrarConexion($stmts, $conn)
{
    foreach ($stmts as $stmt) {
        if ($stmt !== false) {
            sqlsrv_free_stmt($stmt);
        }
    }
    sqlsrv_close($conn);
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <link rel="stylesheet" href="../../assets/css/registrar_empleado.css">
    <script>
        // Función para validar el formulario
        function validarFormulario(event) {
            const form = document.querySelector('form');
            if (!form.checkValidity()) {
                event.preventDefault(); // Evitar el envío del formulario
                event.stopPropagation();

                // Mostrar mensaje de error
                const alertError = document.getElementById('alertError');
                const textAlert = document.getElementById('textAlert');
                textAlert.textContent = 'Por favor, completa todos los campos.';
                alertError.style.display = 'block'; // Mostrar el mensaje de error
            } else {
                // ocultar la alerta
                const alertError = document.getElementById('alertError');
                alertError.style.display = 'none';
            }
            form.classList.add('was-validated'); // Agrega clase para mostrar los estilos de validación
        }
    </script>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../marca.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Agregar Liquidación</h1>
            </div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white rounded-top">
                Formulario de Liquidación
            </div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p id="textAlert" class="text-center"></p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate onsubmit="validarFormulario(event)">
                    <div>
                        <label for="dpi_pasaporte" class="form-label">DPI/Pasaporte del Empleado:</label>
                        <input type="text" class="form-control" name="dpi_pasaporte" placeholder="DPI o Pasaporte" required>
                        <div class="invalid-feedback">Por favor, ingresa el DPI o Pasaporte.</div>
                    </div>
                    <div class="mt-3">
                        <label for="fecha_fin_contrato" class="form-label">Fecha Fin del Contrato:</label>
                        <input type="date" class="form-control" name="fecha_fin_contrato" required>
                        <div class="invalid-feedback">Por favor, ingresa la fecha de fin de contrato.</div>
                    </div>
                    <button type="submit" style="margin-top:30px;" class="btn btn-primary w-100">Calcular Liquidación</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

function calcularLiquidacionEmpleado($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capturar DPI/Pasaporte y fecha de fin de contrato del formulario
        $dpi_pasaporte = $_POST["dpi_pasaporte"];
        $fecha_fin_contrato = $_POST["fecha_fin_contrato"];

        $sp_params = array(
            array($dpi_pasaporte, SQLSRV_PARAM_IN),
            array($fecha_fin_contrato, SQLSRV_PARAM_IN)
        );

        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_crear_liquidacion(?, ?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            echo '<script>alert("La liquidación ha sido registrada exitosamente."); window.location.href = "../../liquidacion.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}

calcularLiquidacionEmpleado($conn);
?>
