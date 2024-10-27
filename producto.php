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
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['BuscarProducto'])) {
    $criterio = $_POST['BuscarProducto'];
}

// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Procedimiento almacenado para listar productos con filtros
$sql = "{CALL sp_listar_producto(?, ?)}";
$params = array(
    array($criterio, SQLSRV_PARAM_IN),
    array($fk_id_empresa, SQLSRV_PARAM_IN)
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
    <title>Lista de Productos</title>
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
        .table th {
            width: 15%; /* Ajustar el ancho de las columnas */
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
                <h1 class="fs-3 mb-0 fw-bold">Lista de Productos</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <form method="POST" action="">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" name="BuscarProducto" placeholder="Buscar por nombre, marca, categoría." value="<?php echo isset($_POST['BuscarProducto']) ? $_POST['BuscarProducto'] : ''; ?>">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                    <a type="button" href="templates/producto/agregar_producto.php" class="btn btn-outline-secondary">Agregar Producto</a>
                </div>
            </form>
            
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Cantidad</th>
                            <th>Precio</th>
                            <th>Estado</th>
                            <th>Descripción</th>
                            <th>Marca</th>
                            <th>Categoría</th>
                            <th>Empresa</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($stmt)) : ?>
                            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                                <tr>
                                    <td><?php echo $row['id_producto']; ?></td>
                                    <td><?php echo $row['nombre']; ?></td>
                                    <td><?php echo $row['cantidad']; ?></td>
                                    <td><?php echo $row['precio']; ?></td>
                                    <td><?php echo $row['estado']; ?></td>
                                    <td><?php echo $row['descripcion']; ?></td>
                                    <td><?php echo $row['Marca']; ?></td>
                                    <td><?php echo $row['Categoria']; ?></td>
                                    <td><?php echo $row['Nombre']; ?></td>
                                    <td>
                                        <div class="dropdown">
                                            <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                Acciones
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="templates/producto/editar_producto.php?id=<?php echo $row['id_producto']; ?>"><i class="fas fa-pencil-alt"></i> Editar</a></li>
                                                <li><a class="dropdown-item" href="templates/producto/vender_producto.php?id=<?php echo $row['id_producto']; ?>"><i class="fas fa-shopping-cart"></i> Vender</a></li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-3">No se encontró ningún producto</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
