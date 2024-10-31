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

$user = $_SESSION['correo_usuario'];

// Obtener la conexión
$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Función para liberar recursos y cerrar la conexión
function cerrarConexion($stmts, $conn)
{
    foreach ($stmts as $stmt) {
        if ($stmt !== false) {
            sqlsrv_free_stmt($stmt);
        }
    }
    sqlsrv_close($conn);
}

// Obtener el fk_id_empresa desde la sesión
$fk_id_empresa = $_SESSION['fk_id_empresa']; // Asegúrate de que 'fk_id_empresa' esté en la sesión

// Consulta para obtener las empresas
$sqlEmpresa = "SELECT id_empresa, nombre FROM Empresa;";
$stmtEmpresa = sqlsrv_query($conn, $sqlEmpresa);


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
        // Función para validar el formulario
        function validarFormulario(event) {
            const form = document.querySelector('form');
            if (!form.checkValidity()) {
                event.preventDefault(); // Evitar el envío del formulario
                event.stopPropagation();

                // Mostrar mensaje de error
                const alertError = document.getElementById('alertError');
                const textAlert = document.getElementById('textAlert');
                textAlert.textContent = 'Por favor, completa todos los campos.';
                alertError.style.display = 'block'; // Mostrar el mensaje de error
            } else {
                // ocultar la alerta
                const alertError = document.getElementById('alertError');
                alertError.style.display = 'none';
            }
            form.classList.add('was-validated'); // Agrega clase para mostrar los estilos de validación
        }
    </script>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../usuarios.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Agregar usuario</h1>
            </div>
        </div>
    </header>

    <div class="container mt-5 mb-5">
        <div class="card mx-auto rounded" style="max-width: 600px;">
            <div class="card-header text-center bg-primary text-white rounded-top">
                Formulario de Empleado
            </div>
            <div class="alert alert-danger p-2 mt-2" role="alert" id="alertError" style="display: none;">
                <p id="textAlert" class="text-center"></p>
            </div>
            <div class="card-body">
                <form action="" method="POST" novalidate>

                    <div class="input-group mb-3">
                        <input type="email" class="form-control" name="email" placeholder="nomina-consulting@example.com" required>
                    </div>

                    <div class="col-md-6">
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
                        <div class="col-md-6">
                            <label for="dpiPasaporte" class="form-label">DPI/PASAPORTE</label>
                            <input type="number" class="form-control" name="dpiPasaporte" placeholder="Ingrese el DPI/PASAPORTE" required>
                            <div class="invalid-feedback">Por favor, ingresa el DPI/PASAPORTE del empleado.</div>
                        
                       </div>


                    <button type="submit" class="btn btn-primary w-100">Crear empleado</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

function verificarInfoUsuario($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $correo = $_POST["email"];
        $fkIdEmpresa = $_POST["fkIdEmpresa"];
        $dpiPasaporte = $_POST["dpiPasaporte"];

        // Consulta SQL
        $sqlVerificarEmpleado = 'SELECT id_empleado, dpi_pasaporte, fk_id_empresa, nombres, apellidos
                                 FROM Empleado
                                 WHERE dpi_pasaporte = ? AND fk_id_empresa = ?';
        
        // Parámetros de la consulta
        $params = array($dpiPasaporte, $fkIdEmpresa);
        
        // Preparar la declaración
        $stmt = sqlsrv_prepare($conn, $sqlVerificarEmpleado, $params);
        
        if ($stmt) {
            // Ejecutar la consulta
            $result = sqlsrv_execute($stmt);
            
            if ($result) {
                // Verificar si se encontró un empleado que coincida
                if (sqlsrv_has_rows($stmt)) {
                    // Obtener el resultado de la consulta
                    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
                    
                    // Extraer los datos necesarios del resultado
                    $fkIdEmpleado = $row['id_empleado'];
                    $nombre = $row['nombres'];
                    $apellido = $row['apellidos'];
                    $nombreUsuario = $nombre . ' ' . $apellido;
                    
                    // Llamar a la función agregarUsuario con los datos necesarios
                    agregarUsuario($conn, $correo, $fkIdEmpresa, $fkIdEmpleado, $nombreUsuario);
                } else {
                    // Mostrar el mensaje de error si no hay coincidencia
                    echo "<div class='alert alert-warning' role='alert'>El empleado no coincide con la empresa que seleccionó.</div>";
                }
            } else {
                // Manejo de errores de ejecución
                echo "Error al ejecutar la consulta: " . print_r(sqlsrv_errors(), true);
            }
            
            // Cerrar la declaración
            sqlsrv_free_stmt($stmt);
        } else {
            // Manejo de errores al preparar la declaración
            echo "Error al preparar la consulta: " . print_r(sqlsrv_errors(), true);
        }
    }
}

function agregarUsuario($conn, $correo, $fkIdEmpresa, $fkIdEmpleado, $nombreUsuario) {
    // Generar una contraseña para el usuario
    $nuevaContrasena = generarContrasenaAleatoria();

    $sp_params = array(
        array($correo, SQLSRV_PARAM_IN),
        array($nuevaContrasena, SQLSRV_PARAM_IN),
        array($fkIdEmpleado, SQLSRV_PARAM_IN),
        array($fkIdEmpresa, SQLSRV_PARAM_IN)
    );

    $sp_stmt = sqlsrv_query($conn, "{call sp_registrar_usuario(?, ?, ?, ?)}", $sp_params);

    if ($sp_stmt) {
        // Enviar la nueva contraseña por correo electrónico al usuario
        enviarCorreoContrasena($correo, $nuevaContrasena, $nombreUsuario);
    } else {
        echo '<script>alert("Error al crear el usuario."); window.location.href = "../../usuarios.php";</script>';
    }
    sqlsrv_free_stmt($sp_stmt);
}

function generarContrasenaAleatoria($length = 8) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $contrasena = '';

    for ($i = 0; $i < $length; $i++) {
        $contrasena .= $characters[rand(0, $charactersLength - 1)];
    }

    return $contrasena;
}

// Función para enviar la nueva contraseña por correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoContrasena($correo, $nuevaContrasena, $nombreUsuario){
    require '../../phpmailer/src/Exception.php';
    require '../../phpmailer/src/PHPMailer.php';
    require '../../phpmailer/src/SMTP.php';


    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply.nomina.consulting@gmail.com';
    $mail->Password = 'vfntiwpxbxnhvapu';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('noreply.nomina.consulting@gmail.com');
    $mail->addAddress($correo);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "[Nomina-Consulting] Bienvenido al equipo";
    $mail->Body = "
<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #113069;
            padding: 10px;
            border-radius: 8px 8px 0 0;
            text-align: center;
            color: #ffffff;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .logo {
            max-width: 150px;
            margin-bottom: 20px;
            width:80%;
        }
        .content {
            padding: 20px;
        }
        .content p {
            margin: 0 0 15px;
        }
        .button {
            display: inline-block;
            padding: 10px 20px;
            margin: 20px 0;
            color: #ffffff;
            background-color: #113069;
            text-decoration: none;
            border-radius: 5px;
        }
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #cccccc;
            text-align: center;
            font-size: 14px;
            color: #999999;
        }
    </style>
</head>
<body>
    <div class='container'>
        <div class='header'>
            <img src='https://firebasestorage.googleapis.com/v0/b/eroma-quiker.appspot.com/o/logo.png?alt=media&token=c7ec3188-c9aa-41a8-b614-c88fc5809b1a' alt='Logo de la empresa' class='logo'>
            <h1>¡Bienvenido a Nomina-Consulting!</h1>
        </div>
        <div class='content'>
            <p>Hola <strong>" . $nombreUsuario . "</strong>,</p>
            <p>Esta es tu correo y contraseña para iniciar sesión.</p>
            <p><strong>Nueva usuario:</strong> ". $correo. ' '. "contraseña:".  $nuevaContrasena . "</p>
            <p>Por favor, visite el siguiente enlace para crear una contraseña nueva y segura para su cuenta de Nomina-Consulting:</p>
            <p><a href='https://nominaconsulting-enb5eqc4adc7cab3.canadacentral-01.azurewebsites.net/modules/restablecer_contrasena.php' class='button'>Restablecer Contraseña</a></p>
        </div>
        <div class='footer'>
            <p>Gracias,<br>El equipo de Nomina-Consulting</p>
        </div>
    </div>
</body>
</html>
";
try {
    $mail->send();
    echo '<script>alert("Se ha creado el usuario y enviado por correo electrónico."); window.location.href = "../../usuarios.php";</script>';
} catch (Exception $e) {
    echo '<script>alert("Error al enviar el correo."); window.location.href = "../../usuarios.php";</script>';
}
}

// Llamar a la función para verificar y procesar el formulario
verificarInfoUsuario($conn);

// Cerrar la conexión (asumiendo que tienes esta función definida)
cerrarConexion([$stmtEmpresa], $conn);

?>