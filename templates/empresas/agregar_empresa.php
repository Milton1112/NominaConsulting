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
            <a href="../../empresa.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Agregar Empresa</h1>
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

                <div>
                        <label for="name" class="form-label">Nombre de la empresa</label>
                        <input type="text" class="form-control" name="name" required>
                </div>

                <div>
                        <label for="telefono" class="form-label">Numero de telefono</label>
                        <input type="number" class="form-control" name="telefono" required>
                </div>

                <div>
                        <label for="direccion" class="form-label">Direccion de la empresa</label>
                        <input type="text" class="form-control" name="direccion" required>
                </div>

                <div>
                        <label for="email" class="form-label">Correo electronico</label>
                        <input type="email" class="form-control" name="email" required>
                </div>

                <button style="margin-top:30px;" type="submit" class="btn btn-primary w-100">Crear Empresa</button>

                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php

function VerificarInfoEmpresa($conn){
    
    if ($_SERVER["REQUEST_METHOD"] == "POST"){

        $name = $_POST["name"];
        $telefono = $_POST["telefono"];
        $direccion = $_POST["direccion"];
        $email = $_POST["email"];

        // Definir los parámetros del procedimiento almacenado con SQLSRV_PARAM_IN
        $sp_params = array(
            array($name, SQLSRV_PARAM_IN),
            array($telefono, SQLSRV_PARAM_IN),
            array($direccion, SQLSRV_PARAM_IN),
            array($email, SQLSRV_PARAM_IN)
        );

        
        // Llamar al procedimiento almacenado
        $sp_stmt = sqlsrv_query($conn, "{CALL sp_insertar_empresa(?, ?, ?, ?)}", $sp_params);


        // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            enviarCorreoEmpresa($name, $email);
        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);
    }

}

//Funcion para evnviar correo a un nuevo empleado de bienvenida
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreoEmpresa($name, $email){
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
    $mail->addAddress($email);

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
            <p>Estimados representantes de <strong><?php echo $name; ?></strong>,</p>
            <p>Queremos expresar nuestro más sincero agradecimiento por confiar en Nomina-Consulting para gestionar la nómina de su empresa. Nos sentimos honrados de poder colaborar con ustedes y ayudarles a optimizar los procesos de administración de su personal.</p>
            <p>Nuestra plataforma está diseñada para ofrecerles una experiencia eficiente y confiable, permitiéndoles dedicar más tiempo a lo que realmente importa: el crecimiento de su empresa. Estamos comprometidos en brindarles un servicio de calidad y asistencia continua para asegurar que aprovechen al máximo todas las funcionalidades que ofrecemos.</p>
            <p>Les recordamos que, al hacer uso de nuestros servicios, aceptan nuestros <a href='URL_TERMINOS_Y_CONDICIONES'>Términos y Condiciones</a>. Les recomendamos revisar este documento para estar completamente informados de nuestras políticas y condiciones de uso.</p>
        </div>
        <div class='footer'>
            <p>Una vez más, les agradecemos por elegirnos como su socio estratégico en la gestión de nómina.</p>
            <p>Atentamente,<br>El equipo de Nomina-Consulting</p>
        </div>
    </div>
    

</body>
</html>
";

    try {
        $mail->send();
        echo '<script>alert("Se ha creado la empresa y enviado por correo electrónico."); window.location.href = "../../empresa.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al enviar el correo."); window.location.href = "../../empresa.php";</script>';
    }
}

VerificarInfoEmpresa($conn);

?>