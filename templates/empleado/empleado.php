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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
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
                            <div class="invalid-feedback">
                                Por favor, ingresa el nombre.
                            </div>
                        </div>
                        <div class="middle-div mb-3">
                            <label for="apellidos" class="form-label">Apellido</label>
                            <input type="text" class="form-control" name="apellidos" placeholder="Ingrese los apellidos" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el apellido.
                            </div>
                        </div>
                    </div>
                    
                    <!-- PUESTo y TipoContrado -->
                     <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="tipoContrato" class="form-label">Tipo de Contrato</label>
                            <input type="text" class="form-control" name="tipoContrato" placeholder="Ingrese tipo de contrato" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el tipo de contrato.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="puesto" class="form-label">Puesto</label>
                            <input type="text" class="form-control" name="puesto" placeholder="Ingrese puesto" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el puesto.
                            </div>
                        </div>
                    </div>

                    <!-- DPI y Salario -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="dpiPasaporte" class="form-label">DPI/Pasaporte</label>
                            <input type="text" class="form-control" id="dpiPasaporte" placeholder="Ingrese DPI o Pasaporte" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el DPI o Pasaporte.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="salario" class="form-label">Salario</label>
                            <input type="number" class="form-control" id="salario" placeholder="Ingrese el salario" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el salario.
                            </div>
                        </div>
                    </div>

                    <!-- IGSS y IRTRA -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="carnetIgss" class="form-label">Carnet IGSS</label>
                            <input type="number" class="form-control" id="carnetIgss" placeholder="Ingrese carnet IGSS" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el carnet IGSS.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="carnetIrtra" class="form-label">Carnet IRTRA</label>
                            <input type="number" class="form-control" id="carnetIrtra" placeholder="Ingrese carnet del IRTRA" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa el carnet del IRTRA.
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="fechaNacimiento" class="form-label">Fecha de Nacimiento</label>
                        <input type="date" class="form-control" id="fechaNacimiento" required>
                        <div class="invalid-feedback">
                            Por favor, ingresa la fecha de nacimiento.
                        </div>
                    </div>

                    <div class="input-group mb-3">
                        <input type="file" class="form-control" id="inputGroupFile02">
                        <button class="input-group-text" for="inputGroupFile02">Upload</button>
                    </div>

                    <!-- Correo y Telefono -->
                    <div class="input-group mb-3">
                        <div class="mb-3">
                            <label for="correoElectronico" class="form-label">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correoElectronico" placeholder="Ingrese correo electrónico" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa un correo electrónico válido.
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="numTelefono" class="form-label">Número de telefono</label>
                            <input type="number" class="form-control" id="numTelefono" placeholder="Ingrese el Numero de telefono" required>
                            <div class="invalid-feedback">
                                Por favor, ingresa un número de telefono.
                            </div>
                        </div>
                    </div>
                    
                    
                    <!-- Selectores dinámicos -->
                    <!-- Oficina y Profesion -->
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
                            <div class="invalid-feedback">
                                Por favor, selecciona una oficina.
                             </div>
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
                            <div class="invalid-feedback">
                                Por favor, selecciona una profesión.
                            </div>
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
                            <div class="invalid-feedback">
                                Por favor, selecciona un departamento.
                            </div>
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
                            <div class="invalid-feedback">
                                Por favor, selecciona un rol.
                            </div>
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
                            <div class="invalid-feedback">
                                Por favor, selecciona un estado.
                            </div>
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
                            <div class="invalid-feedback">
                                Por favor, selecciona una empresa.
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100" onclick="validarFormulario(event)">Crear Empleado</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>

<?php
function verificarInfoEmpleado(){
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nombres = $_POST["nombres"];
        $apellidos = $_POST["apellidos"];
        $tipoContrato = $_POST["tipoContrato"];
        $puesto = $_POST["dpiPasaporte"];
        $dpiPasaporte = $_POST["dpiPasaporte"];

        
    }
}

verificarInfoEmpleado();

// Llamada a la función para liberar los recursos y cerrar la conexión
cerrarConexion([$stmtOficina, $stmtProfesion, $stmtDepartamento, $stmtRol, $stmtEstado, $stmtEmpresa],$conn);
?>
