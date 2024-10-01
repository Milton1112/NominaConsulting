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

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id_empresa = $_GET['id'];

    // Consulta para obtener los datos del empleado
    $sql = "SELECT * FROM Empresa WHERE id_empresa = ?";
    $params = array($id_empresa);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $nombre = $row['nombre'];
        $telefono = $row['numero_telefono'];
        $direccion = $row['direccion_empresa'];
        $correo = $row['correo_empresa'];
        $fecha = $row['fecha_inicio'];
        // Convertir el formato de la fecha a 'Y-m-d' si es un objeto de fecha o en otro formato
        if ($fecha instanceof DateTime) {
            $fecha = $fecha->format('Y-m-d');
        } else {
            $fecha = date('Y-m-d', strtotime($fecha));
        }
        
    }else {
        echo "No se encontró el empleado.";
        exit;
    }

} else {
    die("No se proporcionó el ID.");
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
        if ($stmt !== false && $stmt !== null) {
            sqlsrv_free_stmt($stmt); // Asegurarse de que $stmt no sea null
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
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../salario.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Actualizar Empresa</h1>
            </div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white rounded-top">
                Formulario de Empresa
            </div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p id="textAlert" class="text-center"></p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate>
                    <input type="hidden" name="id_Salario" value="<?php echo $id_salario; ?>">
                    
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $nombre; ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label for="fecha" class="form-label">Fecha inicio</label>
                        <input type="date" class="form-control" name="fecha" value="<?php echo isset($fecha) ? $fecha : ''; ?>" required>
                    </div>
                </div>

                <div>
                     <div>
                        <label for="telefono" class="form-label">Numero de telefono</label>
                        <input type="number" class="form-control" name="telefono" value="<?php echo $telefono; ?>" required>
                    </div>

                    <div>
                        <label for="direccion" class="form-label">Dirección</label>
                        <input type="text" class="form-control" name="direccion" value="<?php echo $direccion; ?>" required>
                    </div>

                </div>

                <div>
                    <div>
                        <label for="email" class="form-label">Correo electronico</label>
                        <input type="email" class="form-control" name="email" value="<?php echo $correo; ?>" required>
                    </div>
                </div>

                    <button type="submit" style="margin-top: 20px;" class="btn btn-primary w-100" onclick="validarFormulario(event)">Actualizar Expediente</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="firebase-config.js"></script>
    <script src="expediente.js"></script>
</body>

</html>
