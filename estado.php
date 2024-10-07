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

// Inicializar criterio de búsqueda
$criterio = "";

// Verificar si el usuario ha enviado una búsqueda
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscarEstado'])) {
    $criterio = $_POST['buscarEstado'];
}

// Procedimiento almacenado para listar empleados con filtro y fk_id_empresa
$sql = "{CALL sp_listar_estado(?)}";
$params = array(
    array($criterio, SQLSRV_PARAM_IN)
);

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql, $params);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
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
        .table tbody tr {
            vertical-align: middle;
        }
        .btn-info {
            color: #fff;
            background-color: #17a2b8;
            border-color: #17a2b8;
        }
        .input-group .form-control {
            border-radius: 0.25rem 0 0 0.25rem;
        }
        .input-group .btn {
            border-radius: 0 0.25rem 0.25rem 0;
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
                <h1 class="fs-3 mb-0 fw-bold">Lista de Estados</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="buscarEstado" placeholder="Buscar el estado" value="<?php echo isset($_POST['buscarEstado']) ? $_POST['buscarEstado'] : ''; ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="templates/estados/agregar_estado.php" class="btn btn-outline-secondary ms-2">Agregar</a>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead>
                        <tr class="text-center">
                            <th>ID</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($stmt)) : ?>
                            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                                <tr class="text-center">
                                    <td class="fw-bold"><?php echo $row['id_estado']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-info btn-sm rounded-pill px-3 dropdown-toggle" type="button" id="dropdownMenuButton<?php echo $row['id_estado']; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                                                Opciones
                                            </button>
                                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton<?php echo $row['id_estado']; ?>">
                                                <li>
                                                    <a class="dropdown-item" href="templates/estados/editar_estado.php?id=<?php echo $row['id_estado']; ?>">
                                                        <i class="fas fa-pencil-alt"></i> Editar
                                                    </a>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item text-danger" href="templates/estados/eliminar_estado.php?id=<?php echo $row['id_estado']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este estado?');">
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
                                <td colspan="3" class="text-center text-muted py-3">No se encontraron los roles</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
