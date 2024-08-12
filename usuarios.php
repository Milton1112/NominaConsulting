<?php
include_once 'includes/db_connect.php';

$rowsPerPage = 20;  // Definir el número de filas por página

function getUsers($searchTerm = null, $page = 1, $rowsPerPage = 20) {
    $conn = getConnection();
    
    $offset = ($page - 1) * $rowsPerPage;

    $sql = "{call sp_listar_usuarios(?, ?, ?)}";
    $params = array(
        array($searchTerm, SQLSRV_PARAM_IN),
        array($offset, SQLSRV_PARAM_IN),
        array($rowsPerPage, SQLSRV_PARAM_IN)
    );

    $stmt = sqlsrv_query($conn, $sql, $params);

    if ($stmt === false) {
        die(print_r(sqlsrv_errors(), true));
    }

    $users = array();
    while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $users[] = $row;
    }

    sqlsrv_free_stmt($stmt);
    sqlsrv_close($conn);

    return $users;
}

$searchTerm = isset($_GET['search']) ? sanitizeInput($_GET['search']) : null;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$users = getUsers($searchTerm, $page, $rowsPerPage);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nomina-Consulting</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>
    <header>
        <label>NOMINA-CONSULTING</label>
    </header>

    <div class="div-color container mt-5">
    <form method="get" action="usuarios.php" class="mb-4 div-search">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Buscar por username, email o ID" value="<?php echo htmlspecialchars($searchTerm); ?>">
            <button type="submit" class="btn btn-primary">Buscar</button>
            <button type="button" class="color-text-button btn btn-outline-light">Agregar usuario</button>

        </div>
    </form>

    <div class="table-responsive">
        <table class="table table-custom">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Username</th>
                    <th>ID Empresa</th>
                    <th>Nombre Empresa</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($users) > 0): ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['ID']; ?></td>
                            <td><?php echo $user['email']; ?></td>
                            <td><?php echo $user['username']; ?></td>
                            <td><?php echo $user['id_empresa']; ?></td>
                            <td><?php echo $user['nombre']; ?></td>
                            <td>
                                <div class="dropdown">
                                    <a class="bx--dots-vertical-rounded" href="#" role="button" id="dropdownMenuLink<?php echo $user['ID']; ?>" data-bs-toggle="dropdown" aria-expanded="false"></a>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuLink<?php echo $user['ID']; ?>">
                                        <li><a class="dropdown-item" href="modificar.php?id=<?php echo $user['ID']; ?>">Modificar</a></li>
                                        <li><a class="dropdown-item" href="eliminar.php?id=<?php echo $user['ID']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este usuario?');">Eliminar</a></li>
                                        <li><a class="dropdown-item" href="templates/usuario/cambiar_contra.php?id=<?php echo $user['ID']; ?>">Cambiar Contraseña</a></li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">No se encontraron usuarios.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $prevPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Anterior</a>
            </li>
            <li class="page-item <?php echo count($users) < $rowsPerPage ? 'disabled' : ''; ?>">
                <a class="page-link" href="?page=<?php echo $nextPage; ?>&search=<?php echo htmlspecialchars($searchTerm); ?>">Siguiente</a>
            </li>
        </ul>
    </nav>
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>

<?php
function sanitizeInput($data) {
    return htmlspecialchars(stripslashes(trim($data)));
}
?>
