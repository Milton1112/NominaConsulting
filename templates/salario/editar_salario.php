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

// Verificar si se proporcionó el ID del empleado
if (isset($_GET['id'])) {
    $id_salario = $_GET['id'];
    // Consulta para obtener los datos del empleado
    $sql = "SELECT * FROM Salario WHERE id_salario = ?";
    $params = array($id_salario);
    $stmt = sqlsrv_query($conn, $sql, $params);

    // Verificar si se obtuvo un resultado
    if ($stmt && sqlsrv_has_rows($stmt)) {
        $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

        // Almacenar los datos del empleado en variables
        $salario_base = $row['salario_base'];
        $salario_anterior = $row['salario_anterior'];
        
    }else {
        echo "No se encontró el empleado.";
        exit;
    }

} else {
    die("No se proporcionó el ID del empleado.");
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
        if ($stmt !== false && $stmt !== null) {
            sqlsrv_free_stmt($stmt); // Asegurarse de que $stmt no sea null
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
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-auth.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-analytics.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.0/firebase-storage.js"></script>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <a href="../../salario.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Actualizar Salario</h1>
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
                    <input type="hidden" name="id_Salario" value="<?php echo $id_salario; ?>">
                    
                <!-- Salrio nuevo y salario anterior -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="salrio_base" class="form-label">Salario Base</label>
                        <input type="text" class="form-control" name="salario_base" value="<?php echo $salario_base; ?>" readonly>
                    </div>
                    <div class="col-md-6">
                        <label for="salario_anterior" class="form-label">Salario Anterior</label>
                        <input type="text" class="form-control" name="salario_anterior" value="<?php echo $salario_anterior; ?>"  readonly>
                    </div>
                </div>

                <div>
                     <div>
                        <label for="salario_nuevo" class="form-label">Nuevo salario</label>
                        <input type="number" class="form-control" name="salario_nuevo" required>
                    </div>
                
                </div>

                    <button type="submit" style="margin-top: 20px;" class="btn btn-primary w-100" onclick="validarFormulario(event)">Actualizar Expediente</button>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="firebase-config.js"></script>
    <script src="expediente.js"></script>
</body>

</html>

<?php

function verificarActulaizarSalario($conn) {
    
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $id = $_POST["id_Salario"];
        $salario_base = $_POST["salario_base"];
        $salario_nuevo = $_POST["salario_nuevo"];

        // Crear parámetros para el procedimiento almacenado
        $sp_params = array(
            array($id, SQLSRV_PARAM_IN),
            array($salario_base, SQLSRV_PARAM_IN),
            array($salario_nuevo, SQLSRV_PARAM_IN)
        );

         // Llamar al procedimiento almacenado
         $sp_stmt = sqlsrv_query($conn, "{CALL sp_actualizar_salario(?,?,?)}", $sp_params);

         // Verificar si la ejecución fue exitosa
        if ($sp_stmt) {
            // Consulta para obtener los datos del empleado
            $sqlEmpleado = "SELECT 
                                e.nombres + ' ' + e.apellidos AS nombre,
                                s.salario_anterior,
                                s.salario_base,
                                e.correo_electronico
                            FROM 
                                Salario s
                            INNER JOIN  
                                Empleado e ON s.fk_id_empleado = e.id_empleado
                            WHERE 
                                id_salario = ?";
            $paramsEmpleado = array($id);
            $stmtEmpleado = sqlsrv_query($conn, $sqlEmpleado, $paramsEmpleado);

            // Verificar si se obtuvo un resultado
            if ($stmtEmpleado && sqlsrv_has_rows($stmtEmpleado)) {
                $row = sqlsrv_fetch_array($stmtEmpleado, SQLSRV_FETCH_ASSOC);
                // Almacenar los datos del empleado en variables
                $nombre = $row['nombre'];
                $salario_anterior = $row['salario_anterior'];
                $salario_base = $row['salario_base'];
                $correo = $row['correo_electronico'];
                
                enviarEmailVerificacion($nombre, $salario_anterior, $salario_base, $correo);
            
            } else {
                echo "No se encontró el empleado.";
                exit;
            }

        } else {
            echo "Error al ejecutar el procedimiento almacenado:<br>";
            die(print_r(sqlsrv_errors(), true));  // Mostrar errores de ejecución
        }

        // Liberar recursos
        sqlsrv_free_stmt($sp_stmt);

    }

}


// Función para enviar la nueva contraseña por correo electrónico
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarEmailVerificacion($nombre, $salario_anterior, $salario_base,  $correo){
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
    $mail->Subject = "[Nomina-Consulting], Aumento de salario";
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
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #dddddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
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
            <h1>¡Felicidades! por su aumento</h1>
        </div>
        <div class='content'>
            <p>Hola <strong>" . $nombre . "</strong>,</p>
            <p>En la siguiente tabla puede ver toda su información actualizada:</p>
            <table>
                <tr>
                    <th>Salario Anterior</th>
                    <th>Salario Nuevo</th>
                </tr>
                <tr>
                    <td>" . $salario_anterior . "</td>
                    <td>" . $salario_base . "</td>
                </tr>
               
            </table>
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
        echo '<script>alert("Se realizo el aumento de salario y se envio el correo al empleado."); window.location.href = "../../salario.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al enviar el correo."); window.location.href = "../../salario.php";</script>'; 
    }

}

verificarActulaizarSalario($conn);

?>