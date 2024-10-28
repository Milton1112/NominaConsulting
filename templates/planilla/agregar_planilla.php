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

// Obtener las empresas
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa;";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);

function insertarSalarioMensual($conn, $fk_id_empresa) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $anio = $_POST["anio"];
        $mes = $_POST["mes"];

        $sp_params = array(
            array($fk_id_empresa, SQLSRV_PARAM_IN),
            array($anio, SQLSRV_PARAM_IN),
            array($mes, SQLSRV_PARAM_IN)
        );

        $sp_stmt = sqlsrv_query($conn, "{CALL sp_insertar_salarioMensual(?, ?, ?)}", $sp_params);

        if ($sp_stmt) {
            echo '<script>alert("Salario mensual agregado exitosamente."); window.location.href = "../../../planilla_salario.php";</script>';
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));
        }
        sqlsrv_free_stmt($sp_stmt);
    }
}

insertarSalarioMensual($conn, $fk_id_empresa);
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
            <a href="../../../planilla_salario.php" class="btn btn-outline-light d-flex align-items-center">Regresar</a>
            <div class="text-center flex-grow-1"><h1>Agregar Salario Mensual</h1></div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header bg-primary text-white">Formulario de Salario Mensual</div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p class="text-center">Por favor, completa todos los campos.</p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate onsubmit="validarFormulario(event)">
                    <div>
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" class="form-control" name="anio" placeholder="Año" required>
                        <div class="invalid-feedback">Por favor, ingresa un año.</div>
                    </div>

                    <div>
                        <label for="mes" class="form-label">Mes</label>
                        <input type="number" class="form-control" name="mes" placeholder="Mes (1-12)" required>
                        <div class="invalid-feedback">Por favor, ingresa un mes.</div>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 mt-3">Agregar Salario Mensual</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>

<?php
sqlsrv_free_stmt($stmtEmpresa);
sqlsrv_close($conn);
?>
