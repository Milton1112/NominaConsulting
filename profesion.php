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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['buscarProfesion'])) {
    $criterio = $_POST['buscarProfesion'];
}

// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Procedimiento almacenado para listar profesiones con filtro y fk_id_empresa
$sql = "{CALL sp_listar_profesion(?, ?)}";
$params = array(
    array($criterio, SQLSRV_PARAM_IN),
    array($fk_id_empresa, SQLSRV_PARAM_IN) // Pasar fk_id_empresa como parámetro
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
    </style>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="index.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Lista de Profesiones</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="buscarProfesion" placeholder="Buscar profesión o empresa" value="<?php echo isset($_POST['buscarProfesion']) ? $_POST['buscarProfesion'] : ''; ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a href="templates/profesion/agregar_profesion.php" class="btn btn-outline-secondary">Agregar</a>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="bg-gradient bg-primary text-white rounded">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nombre Profesión</th>
                            <th>Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($stmt)) : ?>
                            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                                <tr class="shadow-sm rounded bg-light mb-2">
                                    <td class="text-center fw-bold"><?php echo $row['id']; ?></td>
                                    <td class="fw-bold text-primary"><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['Empresa']; ?></td>
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
                                                    <a class="dropdown-item text-danger" href="templates/profesion/eliminar_profesion.php?id=<?php echo $row['id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar esta profesión?');">
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
                                <td colspan="4" class="text-center text-muted py-3">No se encontraron profesiones</td>
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
