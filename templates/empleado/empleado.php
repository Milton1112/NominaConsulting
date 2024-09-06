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

// Obtener la conexión
$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Función para liberar recursos y cerrar la conexión
function cerrarConexion($stmts, $conn) {
    foreach ($stmts as $stmt) {
        if ($stmt !== false) {
            sqlsrv_free_stmt($stmt);
        }
    }
    sqlsrv_close($conn);
}

// Consulta para obtener las oficinas
$sqlOficina = "SELECT id_oficina, nombre FROM Oficina";
$stmtOficina = sqlsrv_query($conn, $sqlOficina);

// Consulta para obtener las profesiones
$sqlProfesion = "SELECT id_profesion, nombre FROM Profesion";
$stmtProfesion = sqlsrv_query($conn, $sqlProfesion);

// Consulta para obtener los departamentos
$sqlDepartamento = "SELECT id_departamento, nombre FROM Departamento";
$stmtDepartamento = sqlsrv_query($conn, $sqlDepartamento);

// Consulta para obtener los roles
$sqlRol = "SELECT id_rol, nombre FROM Rol";
$stmtRol = sqlsrv_query($conn, $sqlRol);

// Consulta para obtener los estados
$sqlEstado = "SELECT id_estado, nombre FROM Estado";
$stmtEstado = sqlsrv_query($conn, $sqlEstado);

// Consulta para obtener las empresas
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NOMINA-CONSULTING</title>
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
                alert('Por favor, completa todos los campos antes de enviar el formulario.');
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
    <header class="text-center py-3">
        <h1>NOMINA-CONSULTING</h1>
    </header>

    <div class="container mt-5">
        <div class="card mx-auto" style="max-width: 600px;">
            <div class="card-header text-center">
                <h4>Crear Empleado</h4>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate>
                    <!-- Nombres y Apellidos -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="nombres" class="form-label">Nombre</label>
                            <input type="text" class="form-control" name="nombres" placeholder="Ingrese los nombres" required>
                            <div class="invalid-feedback">Por favor, ingresa el nombre.</div>
                        </div>
                        <div class="middle-div mb-3">
                            <label for="apellidos" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellidos" placeholder="Ingrese los apellidos" required>
                            <div class="invalid-feedback">Por favor, ingresa el apellido.</div>
                        </div>
                    </div>
                    
                    <!-- Tipo de Contrato y Puesto -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="tipoContrato" class="form-label">Tipo de Contrato</label>
                            <input type="text" class="form-control" name="tipoContrato" placeholder="Ingrese tipo de contrato" required>
                            <div class="invalid-feedback">Por favor, ingresa el tipo de contrato.</div>
                        </div>

                        <div class="mb-3">
                            <label for="puesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" name="puesto" placeholder="Ingrese puesto" required>
                            <div class="invalid-feedback">Por favor, ingresa el puesto.</div>
                        </div>
                    </div>

                    <!-- DPI y Salario -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="dpiPasaporte" class="form-label">DPI/Pasaporte</label>
                            <input type="text" class="form-control" name="dpiPasaporte" placeholder="Ingrese DPI o Pasaporte" required>
                            <div class="invalid-feedback">Por favor, ingresa el DPI o Pasaporte.</div>
                        </div>
                        <div class="mb-3">
                            <label for="salario" class="form-label">Salario</label>
                            <input type="number" class="form-control" name="salario" placeholder="Ingrese el salario" required>
                            <div class="invalid-feedback">Por favor, ingresa el salario.</div>
                        </div>
                    </div>

                    <!-- IGSS y IRTRA -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="carnetIgss" class="form-label">Carnet IGSS</label>
                            <input type="number" class="form-control" name="carnetIgss" placeholder="Ingrese carnet IGSS" required>
                            <div class="invalid-feedback">Por favor, ingresa el carnet IGSS.</div>
                        </div>
                        <div class="mb-3">
                            <label for="carnetIrtra" class="form-label">Carnet IRTRA</label>
                            <input type="number" class="form-control" name="carnetIrtra" placeholder="Ingrese carnet del IRTRA" required>
                            <div class="invalid-feedback">Por favor, ingresa el carnet del IRTRA.</div>
                        </div>
                    </div>
                    
                    <!-- Fecha de Nacimiento y Subida de CV -->
                    <div class="mb-3">
                        <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" name="fechaNacimiento" required>
                        <div class="invalid-feedback">Por favor, ingresa la fecha de nacimiento.</div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="url_pdf" name="url_pdf" placeholder="No se ha cargado ningún archivo" required>
                        <a class="input-group-text" onclick="loginpdf()">Subir CV</a>
                    </div>

                    <div class="progress" style="height: 30px;">
                        <div id="uploadProgress" class="progress-bar" role="progressbar" style="width: 0%;" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            0%
                        </div>
                    </div>

                    <!-- Correo Electrónico y Teléfono -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="correoElectronico" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" name="correoElectronico" placeholder="Ingrese correo electrónico" required>
                            <div class="invalid-feedback">Por favor, ingresa un correo electrónico válido.</div>
                        </div>
                        <div class="mb-3">
                            <label for="numTelefono" class="form-label">Número de teléfono</label>
                            <input type="number" class="form-control" name="numTelefono" placeholder="Ingrese el número de teléfono" required>
                            <div class="invalid-feedback">Por favor, ingresa un número de teléfono.</div>
                        </div>
                    </div>

                    <!-- Selectores dinámicos -->
                    <!-- Oficina y Profesión -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="fkIdOficina" class="form-label">Oficina</label>
                            <select class="form-control" id="fkIdOficina" name="fkIdOficina" required>
                                <option value="">Seleccione una oficina</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtOficina, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_oficina"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una oficina.</div>
                        </div>

                        <div class="mb-3">
                            <label for="fkIdProfesion" class="form-label">Profesión</label>
                            <select class="form-control" id="fkIdProfesion" name="fkIdProfesion" required>
                                <option value="">Seleccione una profesión</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtProfesion, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_profesion"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una profesión.</div>
                        </div>
                    </div>

                    <!-- Departamento y Rol -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="fkIdDepartamento" class="form-label">Departamento</label>
                            <select class="form-control" id="fkIdDepartamento" name="fkIdDepartamento" required>
                                <option value="">Seleccione un departamento</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtDepartamento, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_departamento"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un departamento.</div>
                        </div>

                        <div class="mb-3">
                            <label for="fkIdRol" class="form-label">Rol</label>
                            <select class="form-control" id="fkIdRol" name="fkIdRol" required>
                                <option value="">Seleccione un rol</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtRol, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_rol"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un rol.</div>
                        </div>
                    </div>

                    <!-- Estado y Empresa -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="fkIdEstado" class="form-label">Estado</label>
                            <select class="form-control" id="fkIdEstado" name="fkIdEstado" required>
                                <option value="">Seleccione un estado</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtEstado, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_estado"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona un estado.</div>
                        </div>

                        <div class="mb-3">
                            <label for="fkIdEmpresa" class="form-label">Empresa</label>
                            <select class="form-control" id="fkIdEmpresa" name="fkIdEmpresa" required>
                                <option value="">Seleccione una empresa</option>
                                <?php
                                while ($row = sqlsrv_fetch_array($stmtEmpresa, SQLSRV_FETCH_ASSOC)) {
                                    echo '<option value="' . $row["id_empresa"] . '">' . $row["nombre"] . '</option>';
                                }
                                ?>
                            </select>
                            <div class="invalid-feedback">Por favor, selecciona una empresa.</div>
                        </div>

                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="validarFormulario(event)">Crear Empleado</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="firebase-config.js"></script>
    <script src="expediente.js"></script>
</body>

<?php
function verificarInfoEmpleado($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Capturar datos del formulario
        $nombres = $_POST["nombres"];
        $apellidos = $_POST["apellidos"];
        $tipoContrato = $_POST["tipoContrato"];
        $puesto = $_POST["puesto"];
        $dpiPasaporte = $_POST["dpiPasaporte"];
        $salario = (float)$_POST["salario"];  // Asegurarse de que es float
        $carnetIgss = $_POST["carnetIgss"];
        $carnetIrtra = $_POST["carnetIrtra"];
        $fechaNacimiento = $_POST["fechaNacimiento"];
        $expediente = $_POST["url_pdf"];
        $correoElectronico = $_POST["correoElectronico"];
        $numTelefono = $_POST["numTelefono"];
        $fkIdOficina = (int)$_POST["fkIdOficina"];  // Asegurarse de que es entero
        $fkIdProfesion = (int)$_POST["fkIdProfesion"];
        $fkIdDepartamento = (int)$_POST["fkIdDepartamento"];
        $fkIdRol = (int)$_POST["fkIdRol"];
        $fkIdEstado = (int)$_POST["fkIdEstado"];
        $fkIdEmpresa = (int)$_POST["fkIdEmpresa"];  // Asegurarse de que el valor es entero

        // Definir los parámetros del procedimiento almacenado con SQLSRV_PARAM_IN
        $sp_params = array(
            array($nombres, SQLSRV_PARAM_IN),
            array($apellidos, SQLSRV_PARAM_IN),
            array($tipoContrato, SQLSRV_PARAM_IN),
            array($puesto, SQLSRV_PARAM_IN),
            array($dpiPasaporte, SQLSRV_PARAM_IN),
            array($salario, SQLSRV_PARAM_IN),
            array($carnetIgss, SQLSRV_PARAM_IN),
            array($carnetIrtra, SQLSRV_PARAM_IN),
            array($fechaNacimiento, SQLSRV_PARAM_IN),
            array($correoElectronico, SQLSRV_PARAM_IN),
            array($numTelefono, SQLSRV_PARAM_IN),
            array($expediente, SQLSRV_PARAM_IN),
            array($fkIdOficina, SQLSRV_PARAM_IN),
            array($fkIdProfesion, SQLSRV_PARAM_IN),
            array($fkIdDepartamento, SQLSRV_PARAM_IN),
            array($fkIdRol, SQLSRV_PARAM_IN),
            array($fkIdEstado, SQLSRV_PARAM_IN),
            array($fkIdEmpresa, SQLSRV_PARAM_IN)  // Parámetro final
        );

        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_InsertarEmpleado(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)}", $sp_params);

        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            echo "Empleado insertado correctamente.";
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }
}



// Llamar a la función para verificar y procesar el formulario
verificarInfoEmpleado($conn);

// Cerrar la conexión (asumiendo que tienes esta función definida)
cerrarConexion([$stmtOficina, $stmtProfesion, $stmtDepartamento, $stmtRol, $stmtEstado, $stmtEmpresa], $conn);
?>
