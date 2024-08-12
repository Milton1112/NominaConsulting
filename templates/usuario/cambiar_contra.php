<?php
// Incluye el archivo de conexión
include_once '../../includes/db_connect.php';


if ($conn) {
    if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET["id"])) {
        $userId = $_GET["id"];

        // Generar una contraseña aleatoria
        $nuevaContrasena = generarContrasenaAleatoria();

        // Obtener la dirección de correo electrónico y nombre del usuario
        $stmt = sqlsrv_query($conn, "SELECT correo, nombres + ' ' + apellidos as username FROM Usuario u INNER JOIN Empleado e ON u.fk_id_empleado = e.id_empleado WHERE id_usuario = ?", array($userId));

        if ($stmt && sqlsrv_has_rows($stmt)) {
            $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $correoUsuario = $row["correo"];
            $nombreUsuario = $row["username"];

            // Llamar al procedimiento almacenado para cambiar la contraseña
            $sp_params = array(
                array($userId, SQLSRV_PARAM_IN),
                array($nuevaContrasena, SQLSRV_PARAM_IN)
            );

            $sp_stmt = sqlsrv_query($conn, "{call sp_cambiar_contra(?, ?)}", $sp_params);

            if ($sp_stmt) {
                // Enviar la nueva contraseña por correo electrónico al usuario
                enviarCorreoContrasena($correoUsuario, $nuevaContrasena, $nombreUsuario);
                echo '<script>alert("Se ha restablecido la contraseña y enviado por correo electrónico."); window.location.href = "../../usuarios.php";</script>';
            } else {
                echo '<script>alert("Error al restablecer la contraseña."); window.location.href = "../../usuarios.php";</script>';
            }

            sqlsrv_free_stmt($sp_stmt);
        } else {
            echo '<script>alert("Usuario no encontrado."); window.location.href = "../../usuarios.php";</script>';
        }

        sqlsrv_free_stmt($stmt);
    }
} else {
    echo "No se pudo establecer conexión a la base de datos.";
}

// Función para generar una contraseña aleatoria
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

function enviarCorreoContrasena($to, $contrasena, $nombreUsuario) {
    require '../../phpmailer/src/Exception.php';
    require '../../phpmailer/src/PHPMailer.php';
    require '../../phpmailer/src/SMTP.php';


    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'nominaproyecto8@gmail.com';
    $mail->Password = 'eacnroghrhztckjf';
    $mail->SMTPSecure = 'ssl';
    $mail->Port = 465;

    $mail->setFrom('nominaproyecto8@gmail.com');
    $mail->addAddress($to);

    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = "[Nomina Solidarista] Se cambió tu contraseña";
    $mail->Body = "Hola " . $nombreUsuario . "<br><br>Su contraseña se estableció correctamente.<br><br>Nueva contraseña: " . $contrasena . "<br><br>Si no intentó iniciar sesión en su cuenta, su contraseña puede estar comprometida.<br><br>Visite: <a href='http://nominasolidarista.wuaze.com/recoverpassword.php'>http://nominasolidarista.wuaze.com/recoverpassword.php</a> para crear una contraseña nueva y segura para su cuenta de Nomina Solidarista.<br><br>Gracias,<br>El equipo de Nomina Solidarista";

    try {
        $mail->send();
        echo '<script>alert("Se ha restablecido la contraseña y enviado por correo electrónico."); window.location.href = "../../usuarios.php";</script>';
    } catch (Exception $e) {
        echo '<script>alert("Error al enviar el correo."); window.location.href = "../../usuarios.php";</script>';
    }
}

?>
