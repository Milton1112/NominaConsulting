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

if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Obtener el listado de empresas para el selector
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa;";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);

// Función para procesar el formulario
function generarAguinaldo($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id_empresa = $_POST["fkIdEmpresa"];
        $anio = $_POST["anio"];

        $sp_params = array(
            array($id_empresa, SQLSRV_PARAM_IN),
            array($anio, SQLSRV_PARAM_IN)
        );

        // Ejecutar el procedimiento almacenado para generar el aguinaldo
        $sp_stmt = sqlsrv_query($conn, "{CALL generar_aguinaldo(?, ?)}", $sp_params);

        if ($sp_stmt) {
            echo '<script>alert("Aguinaldo generado exitosamente."); window.location.href = "../../aguinaldo.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($sp_stmt);
    }
}

generarAguinaldo($conn);
?>

<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
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
            <a href="../../aguinaldo.php" class="btn btn-outline-light d-flex align-items-center">Regresar</a>
            <div class="text-center flex-grow-1"><h1>Generar Aguinaldo</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">Formulario de Generación de Aguinaldo</div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p class="text-center">Por favor, completa todos los campos.</p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate onsubmit="validarFormulario(event)">
                    <div>
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" class="form-control" name="anio" placeholder="Ingrese el año" required min="2000" max="2100">
                        <div class="invalid-feedback">Por favor, ingresa un año válido.</div>
                    </div>

                    <div class="mt-3">
                        <label for="fkIdEmpresa" class="form-label">Empresa</label>
                        <select class="form-control" id="fkIdEmpresa" name="fkIdEmpresa" required>
                            <option value="">Seleccione una empresa</option>
                            <?php while ($row = sqlsrv_fetch_array($stmtEmpresa, SQLSRV_FETCH_ASSOC)): ?>
                                <option value="<?= $row["id_empresa"] ?>"><?= $row["nombre"] ?></option>
                            <?php endwhile; ?>
                        </select>
                        <div class="invalid-feedback">Por favor, selecciona una empresa.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Generar Aguinaldo</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
// Liberar el statement y cerrar la conexión
if ($stmtEmpresa) {
    sqlsrv_free_stmt($stmtEmpresa);
}
sqlsrv_close($conn);
?>
