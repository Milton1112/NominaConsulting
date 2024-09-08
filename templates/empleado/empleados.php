<?php
include_once '../../includes/db_connect.php';
include_once '../../includes/functions.php';

$conn = getConnection();

// Verificar si la conexión es válida
if (!$conn) {
    die("Error al conectar a la base de datos.");
}

// Consulta para obtener los empleados
$sql = "SELECT E.id_empleado, E.nombres, E.apellidos, E.fecha_contratacion, 
        E.puesto, E.dpi_pasaporte, E.numero_telefono, E.correo_electronico, P.nombre AS profesion, D.nombre AS departamento
        FROM Empleado E
        INNER JOIN Profesion P ON E.fk_id_profesion = P.id_profesion
        INNER JOIN Departamento D ON E.fk_id_departamento = D.id_departamento";

$stmt = sqlsrv_query($conn, $sql);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>

<!doctype html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Lista de Empleados</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../assets/css/global.css">
    <style>
        body {
            background-color: #F4F7FC;
        }

        .table thead {
            background-color: #2F2C59;
            color: #fff;
        }

        .table-hover tbody tr:hover {
            background-color: #DDE2FF;
        }

        .action-btns {
            display: flex;
            justify-content: space-around;
        }

        .btn-edit {
            color: #007BFF;
            background-color: transparent;
            border: none;
        }

        .btn-delete {
            color: #FF6347;
            background-color: transparent;
            border: none;
        }

        .btn-edit:hover {
            color: #0056b3;
        }

        .btn-delete:hover {
            color: #d9534f;
        }

        .header-title {
            text-align: center;
            color: #2F2C59;
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <header class="bg-primary text-white py-3 shadow-sm">
        <div class="container d-flex justify-content-between align-items-center">
            <!-- Botón de Regresar con icono -->
            <a href="../../index.php" class="btn btn-outline-light d-flex align-items-center">
                <i class="bi bi-arrow-left-circle me-2"></i> Regresar
            </a>
            <!-- Título centrado -->
            <div class="text-center flex-grow-1">
                <h1 class="fs-3 mb-0 fw-bold">Lista de Empleados</h1>
            </div>
        </div>
    </header>

    <div class="container-fluid mt-5 mb-5">
        <div class="mx-auto p-4 bg-white rounded">
            <div class="table-responsive">
                <table class="table table-hover table-borderless align-middle">
                    <thead class="bg-gradient bg-primary text-white rounded">
                        <tr class="text-center">
                            <th>#</th>
                            <th>Nombres</th>
                            <th>Apellidos</th>
                            <th>Fecha Contratación</th>
                            <th>Puesto</th>
                            <th>DPI/Pasaporte</th>
                            <th>Teléfono</th>
                            <th>Email</th>
                            <th>Profesión</th>
                            <th>Departamento</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (sqlsrv_has_rows($stmt)) : ?>
                            <?php while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) : ?>
                                <tr class="shadow-sm rounded bg-light mb-2">
                                    <td class="text-center fw-bold"><?php echo $row['id_empleado']; ?></td>
                                    <td class="fw-bold text-primary"><?php echo $row['nombres']; ?></td>
                                    <td><?php echo $row['apellidos']; ?></td>
                                    <td><?php echo $row['fecha_contratacion']->format('Y-m-d'); ?></td>
                                    <td><?php echo $row['puesto']; ?></td>
                                    <td><?php echo $row['dpi_pasaporte']; ?></td>
                                    <td><?php echo $row['numero_telefono']; ?></td>
                                    <td><?php echo $row['correo_electronico']; ?></td>
                                    <td><?php echo $row['profesion']; ?></td>
                                    <td><?php echo $row['departamento']; ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-info btn-sm rounded-pill px-3 me-2" onclick="window.location.href='editar_empleado.php?id=<?php echo $row['id_empleado']; ?>'">
                                            <i class="fas fa-pencil-alt"></i> Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm rounded-pill px-3" onclick="window.location.href='eliminar_empleado.php?id=<?php echo $row['id_empleado']; ?>'">
                                            <i class="fas fa-trash-alt"></i> Eliminar
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr>
                                <td colspan="11" class="text-center text-muted py-3">No se encontraron empleados</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

<?php
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>