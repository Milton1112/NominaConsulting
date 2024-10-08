<?php
include_once 'includes/db_connect.php';
include_once 'includes/functions.php';

$conn = getConnection(); // Obtener la conexión a la base de datos

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Verificar si la sesión ya está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start(); // Inicia la sesión si no está ya activa
}

// Verificar si la sesión está activa
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2(); // Redirige al login si no está logueado
}

// Inicializar criterios de búsqueda
$fecha_inicio = null;
$fecha_fin = null;
$nombre = "";
$horas = null;
$tipo = "";

// Verificar si el usuario ha enviado una búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && !isset($_POST['validarEmpleado'])) {
    $fecha_inicio = !empty($_POST['fecha_inicio']) ? $_POST['fecha_inicio'] : null;
    $fecha_fin = !empty($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;
    $nombre = !empty($_POST['nombre']) ? $_POST['nombre'] : null;
    $horas = !empty($_POST['horas']) ? (int)$_POST['horas'] : null;
    $tipo = !empty($_POST['tipo']) ? $_POST['tipo'] : null;
}

// Procedimiento almacenado para listar horas extras con filtros
$sql = "{CALL sp_listar_hora_extra(?, ?, ?, ?, ?)}";
$params = array(
    array($fecha_inicio, SQLSRV_PARAM_IN),
    array($fecha_fin, SQLSRV_PARAM_IN),
    array($nombre, SQLSRV_PARAM_IN),
    array($horas, SQLSRV_PARAM_IN),
    array($tipo, SQLSRV_PARAM_IN)
);

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Validación de DPI/Pasaporte mediante AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['validarEmpleado'])) {
    $dpi_pasaporte = $_POST['dpiPasaporte'];

    // Consulta para buscar el empleado por DPI o Pasaporte
    $sql = "SELECT id_empleado FROM Empleado WHERE dpi_pasaporte = ?";
    $params = array($dpi_pasaporte);
    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(json_encode(['existe' => false]));
    }

    // Verificar si se encontró un empleado
    if ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        // Enviar la respuesta en formato JSON indicando que el empleado existe
        echo json_encode(['existe' => true, 'id_empleado' => $row['id_empleado']]);
    } else {
        // Enviar la respuesta en formato JSON indicando que el empleado no existe
        echo json_encode(['existe' => false]);
    }

    exit; // Finalizar la ejecución del script para evitar mostrar el HTML de la página
}
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body {
            background-color: #F4F7FC;
        }
        .table thead {
            background-color: #2F2C59;
            color: #fff;
        }
        .table-hover tbody tr:hover {
            background-color: #DDE2FF;
        }
        .table th, .table td {
            text-align: center;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Lista de Horas Extras</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="row g-3 mb-3">
                    <div class="col">
                        <input type="text" class="form-control" name="nombre" placeholder="Buscar por nombre" value="<?php echo isset($nombre) ? $nombre : ''; ?>">
                    </div>
                    <div class="col">
                        <input type="number" class="form-control" name="horas" placeholder="Horas" value="<?php echo isset($horas) ? $horas : ''; ?>">
                    </div>
                    <div class="col">
                        <input type="text" class="form-control" name="tipo" placeholder="Tipo" value="<?php echo isset($tipo) ? $tipo : ''; ?>">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" name="fecha_inicio" value="<?php echo isset($fecha_inicio) ? $fecha_inicio : ''; ?>">
                    </div>
                    <div class="col">
                        <input type="date" class="form-control" name="fecha_fin" value="<?php echo isset($fecha_fin) ? $fecha_fin : ''; ?>">
                    </div>
                    <div class="col">
                        <button type="submit" class="btn btn-primary w-100">Buscar</button>
                    </div>
                </div>
            </form>

            <!-- Botón para agregar horas extras -->
            <div class="d-flex justify-content-end mb-3">
                <button class="btn btn-outline-secondary" id="agregarHorasBtn">Agregar horas extras</button>
            </div>

            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Horas</th>
                            <th>Tipo</th>
                            <th>Fecha</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($stmt)) : ?>
                            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                                <tr>
                                    <td><?php echo $row['id']; ?></td>
                                    <td><?php echo $row['Nombre']; ?></td>
                                    <td><?php echo $row['horas']; ?></td>
                                    <td><?php echo $row['tipo']; ?></td>
                                    <td><?php echo $row['fecha']->format('Y-m-d'); ?></td>
                                    <td class="text-center">
                                        <div class="dropdown">
                                            <button class="btn btn-info btn-sm rounded-pill px-3 dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $row['id']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Opciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $row['id']; ?>">
                                                <li>
                                                    <a class="dropdown-item" href="templates/profesion/editar_profesion.php?id=<?php echo $row['id']; ?>">
                                                        <i class="fas fa-pencil-alt"></i> Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="templates/horasExtras/eliminar_horas.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esto?');">
                                                        <i class="fas fa-trash-alt"></i> Eliminar
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="5" class="text-center text-muted py-3">No se encontraron horas extras</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('agregarHorasBtn').addEventListener('click', function() {
    const dpiPasaporte = prompt("Ingrese el DPI o Pasaporte del empleado:");

    if (dpiPasaporte !== null && dpiPasaporte.trim() !== "") {
        // Llamada AJAX para verificar si el empleado existe
        fetch('hora_extra.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: new URLSearchParams({ dpiPasaporte: dpiPasaporte, validarEmpleado: true })
        })
        .then(response => response.json())
        .then(data => {
            if (data.existe) {
                if (confirm("Empleado encontrado. ¿Desea agregar horas extras?")) {
                    // Redirigir a agregar_horas_extra.php con el id del empleado
                    window.location.href = `templates/horasExtras/agregar_horas_extra.php?id=${data.id_empleado}`;
                }
            } else {
                alert('Empleado no encontrado.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error al buscar el empleado.');
        });
    } else {
        alert("Debe ingresar un DPI o Pasaporte válido.");
    }
});

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
