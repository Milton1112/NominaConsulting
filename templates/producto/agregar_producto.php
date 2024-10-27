<?php 
// Incluir archivos de conexión y funciones
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

// Verificar si la sesión está activa antes de llamar a session_start()
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn2();
}

$user = $_SESSION['correo_usuario'];
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Obtener conexión
$conn = getConnection();

// Verificar conexión
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Obtener las marcas y categorías para el formulario
$sqlMarca = "SELECT id_marca, nombre FROM Marca;";
$stmtMarca = sqlsrv_query($conn, $sqlMarca);

$sqlCategoria = "SELECT id_categoria, nombre FROM Categoria;";
$stmtCategoria = sqlsrv_query($conn, $sqlCategoria);

function insertarProducto($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombre = $_POST["nombre"];
        $cantidad = $_POST["cantidad"];
        $estado = $_POST["estado"];
        $descripcion = $_POST["descripcion"];
        $idMarca = $_POST["idMarca"];
        $idCategoria = $_POST["idCategoria"];
        $idEmpresa = $_SESSION['fk_id_empresa'];

        $sp_params = array(
            array($nombre, SQLSRV_PARAM_IN),
            array($cantidad, SQLSRV_PARAM_IN),
            array($estado, SQLSRV_PARAM_IN),
            array($descripcion, SQLSRV_PARAM_IN),
            array($idMarca, SQLSRV_PARAM_IN),
            array($idCategoria, SQLSRV_PARAM_IN),
            array($idEmpresa, SQLSRV_PARAM_IN)
        );

        $sp_stmt = sqlsrv_query($conn, "{CALL sp_insertar_producto(?, ?, ?, ?, ?, ?, ?)}", $sp_params);

        if ($sp_stmt) {
            echo '<script>alert("Producto creado exitosamente."); window.location.href = "../../producto.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($sp_stmt);
    }
}

insertarProducto($conn);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body {
            background-color: #F4F7FC;
        }
    </style>
    <script>
        function validarFormulario(event) {
            const form = document.querySelector('form');
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                document.getElementById('alertError').style.display = 'block';
            } else {
                document.getElementById('alertError').style.display = 'none';
            }
            form.classList.add('was-validated');
        }
    </script>
</head>
<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../producto.php" class="btn btn-outline-light d-flex align-items-center">Regresar</a>
            <div class="text-center flex-grow-1"><h1>Agregar Producto</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">Formulario de Producto</div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p class="text-center">Por favor, completa todos los campos.</p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate onsubmit="validarFormulario(event)">
                    <div>
                        <label for="nombre" class="form-label">Nombre</label>
                        <input type="text" class="form-control" name="nombre" placeholder="Nombre del producto" required>
                        <div class="invalid-feedback">Por favor, ingresa un nombre.</div>
                    </div>

                    <div>
                        <label for="cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" name="cantidad" placeholder="Cantidad" required>
                        <div class="invalid-feedback">Por favor, ingresa la cantidad.</div>
                    </div>

                    <div>
                        <label for="estado" class="form-label">Estado</label>
                        <input type="text" class="form-control" name="estado" placeholder="Estado del producto" required>
                        <div class="invalid-feedback">Por favor, ingresa el estado.</div>
                    </div>

                    <div>
                        <label for="descripcion" class="form-label">Descripción</label>
                        <textarea class="form-control" name="descripcion" placeholder="Descripción del producto" required></textarea>
                        <div class="invalid-feedback">Por favor, ingresa una descripción.</div>
                    </div>

                    <div>
                        <label for="idMarca" class="form-label">Marca</label>
                        <select class="form-control" id="idMarca" name="idMarca" required>
                            <option value="">Seleccione una marca</option>
                            <?php while ($row = sqlsrv_fetch_array($stmtMarca, SQLSRV_FETCH_ASSOC)): ?>
                                <option value="<?= $row["id_marca"] ?>"><?= $row["nombre"] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona una marca.</div>
                    </div>

                    <div>
                        <label for="idCategoria" class="form-label">Categoría</label>
                        <select class="form-control" id="idCategoria" name="idCategoria" required>
                            <option value="">Seleccione una categoría</option>
                            <?php while ($row = sqlsrv_fetch_array($stmtCategoria, SQLSRV_FETCH_ASSOC)): ?>
                                <option value="<?= $row["id_categoria"] ?>"><?= $row["nombre"] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona una categoría.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Crear Producto</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
