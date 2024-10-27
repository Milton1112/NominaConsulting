<?php
// Incluir archivos de conexión y funciones
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

// Verificar si 'fk_id_empresa' está en la sesión
if (!isset($_SESSION['fk_id_empresa'])) {
    die("Error: 'fk_id_empresa' no está definido en la sesión.");
} else {
    $idEmpresa = $_SESSION['fk_id_empresa']; // Asignar el valor de la sesión a la variable $idEmpresa
}

// Verificar si se proporcionó el ID del producto
if (isset($_GET['id'])) {
    $idProducto = $_GET['id'];
    $conn = getConnection();

    // Consulta para obtener los datos del producto
    $sql = "SELECT * FROM Producto WHERE id_producto = ?";
    $params = array($idProducto);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del producto en variables
        $nombre = $row['nombre'];
        $cantidad = $row['cantidad'];
        $precio = $row['precio'];
        $estado = $row['estado'];
        $descripcion = $row['descripcion'];
        $idMarca = $row['fk_id_marca'];
        $idCategoria = $row['fk_id_categoria'];
    } else {
        echo "No se encontró el producto.";
        exit;
    }
} else {
    die("No se proporcionó el ID.");
}

// Obtener las marcas y categorías para el formulario
$sqlMarca = "SELECT id_marca, nombre FROM Marca;";
$stmtMarca = sqlsrv_query($conn, $sqlMarca);

$sqlCategoria = "SELECT id_categoria, nombre FROM Categoria;";
$stmtCategoria = sqlsrv_query($conn, $sqlCategoria);

function actualizarProducto($conn, $idEmpresa) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $idProducto = $_POST["id"];
        $nombre = $_POST["nombre"];
        $cantidad = $_POST["cantidad"];
        $precio = $_POST["precio"];
        $estado = $_POST["estado"];
        $descripcion = $_POST["descripcion"];
        $idMarca = $_POST["idMarca"];
        $idCategoria = $_POST["idCategoria"];

        $sp_params = array(
            array($idProducto, SQLSRV_PARAM_IN),
            array($nombre, SQLSRV_PARAM_IN),
            array($cantidad, SQLSRV_PARAM_IN),
            array($precio, SQLSRV_PARAM_IN),
            array($estado, SQLSRV_PARAM_IN),
            array($descripcion, SQLSRV_PARAM_IN),
            array($idMarca, SQLSRV_PARAM_IN),
            array($idCategoria, SQLSRV_PARAM_IN),
            array($idEmpresa, SQLSRV_PARAM_IN)
        );

        $sp_stmt = sqlsrv_query($conn, "{CALL sp_actualizar_producto(?, ?, ?,?, ?, ?, ?, ?, ?)}", $sp_params);

        if ($sp_stmt) {
            echo '<script>alert("Producto actualizado exitosamente."); window.location.href = "../../producto.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));
        }
    }
}

// Llamar a la función para actualizar el producto
actualizarProducto($conn, $idEmpresa);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../producto.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1"><h1>Actualizar Producto</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">Formulario de Producto</div>
            <div class="card-body">
                <form action="" method="POST" novalidate>
                    <input type="hidden" name="id" value="<?php echo $idProducto; ?>">

                    <div>
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" value="<?php echo $nombre; ?>" required>
                    </div>

                    <div>
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad" value="<?php echo $cantidad; ?>" required>
                    </div>

                    <div>
                        <label for="precio" class="form-label">Precio</label>
                        <input type="number" class="form-control" name="precio" value="<?php echo $precio; ?>" required>
                    </div>

                    <div>
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" name="estado" value="<?php echo $estado; ?>" required>
                    </div>

                    <div>
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" required><?php echo $descripcion; ?></textarea>
                    </div>

                    <div>
                        <label for="idMarca" class="form-label">Marca</label>
                        <select class="form-control" name="idMarca" required>
                            <?php while ($row = sqlsrv_fetch_array($stmtMarca, SQLSRV_FETCH_ASSOC)) : ?>
                                <option value="<?= $row['id_marca']; ?>" <?php echo ($idMarca == $row['id_marca']) ? 'selected' : ''; ?>><?= $row['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div>
                        <label for="idCategoria" class="form-label">Categoría</label>
                        <select class="form-control" name="idCategoria" required>
                            <?php while ($row = sqlsrv_fetch_array($stmtCategoria, SQLSRV_FETCH_ASSOC)) : ?>
                                <option value="<?= $row['id_categoria']; ?>" <?php echo ($idCategoria == $row['id_categoria']) ? 'selected' : ''; ?>><?= $row['nombre']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Actualizar Producto</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
