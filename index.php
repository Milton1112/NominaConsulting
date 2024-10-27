<?php
include_once 'includes/functions.php';
include 'includes/db_connect.php';

$conn = getConnection();

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_logueado']) || $_SESSION['usuario_logueado'] !== true) {
    SignIn();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['signout'])) {
    SignOut();
}

// Consultas SQL para extraer los datos

// Total de productos vendidos
$totalProductosVendidos = 0;
$queryProductosVendidos = "SELECT SUM(cantidad) as total_vendidos FROM VentaTienda";
$result = sqlsrv_query($conn, $queryProductosVendidos);
if ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $totalProductosVendidos = $row['total_vendidos'];
}

// Cantidad de empleados por estado (activo, vacaciones, despedido, jubilado, retirado)
$empleadosPorEstado = [];
$queryEmpleadosEstado = "
    SELECT e.nombre AS estado, COUNT(emp.id_empleado) AS cantidad
    FROM Empleado emp
    INNER JOIN Estado e ON emp.fk_id_estado = e.id_estado
    GROUP BY e.nombre";
$result = sqlsrv_query($conn, $queryEmpleadosEstado);
while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $empleadosPorEstado[$row['estado']] = $row['cantidad'];
}

// Cantidad de usuarios registrados
$totalUsuarios = 0;
$queryUsuarios = "SELECT COUNT(id_usuario) as total_usuarios FROM Usuario";
$result = sqlsrv_query($conn, $queryUsuarios);
if ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $totalUsuarios = $row['total_usuarios'];
}

// Salario más alto y más bajo
$salarioAlto = 0;
$salarioBajo = 0;
$querySalario = "SELECT MAX(salario_base) AS salario_alto, MIN(salario_base) AS salario_bajo FROM Salario";
$result = sqlsrv_query($conn, $querySalario);
if ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
    $salarioAlto = $row['salario_alto'];
    $salarioBajo = $row['salario_bajo'];
}

sqlsrv_close($conn);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Nomina Consulting</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</head>
<body>
    <!-- Barra de navegación -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="fas fa-building"></i> Consulting S.A</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownRRHH" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-tie"></i> RRHH
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownRRHH">
                            <a class="dropdown-item" href="empleados.php"><i class="fas fa-users"></i> Empleados</a>
                            <a class="dropdown-item" href="expediente.php"><i class="fas fa-folder"></i> Expediente</a>
                            <a class="dropdown-item" href="bono14.php"><i class="fas fa-gift"></i> Bono 14</a>
                            <a class="dropdown-item" href="aguinaldo.php"><i class="fas fa-gift"></i> Aguinaldo</a>
                            <a class="dropdown-item" href="liquidacion.php"><i class="fas fa-money-check-alt"></i> Liquidación</a>
                            <a class="dropdown-item" href="salario.php"><i class="fas fa-money-bill"></i> Salario</a>
                            <a class="dropdown-item" href="anticipo.php"><i class="fas fa-hand-holding-usd"></i> Anticipo</a>
                            <a class="dropdown-item" href="ausencia.php"><i class="fas fa-calendar-times"></i> Ausencia</a>
                            <a class="dropdown-item" href="hora_extra.php"><i class="fas fa-clock"></i> Horas Extras</a>
                            <a class="dropdown-item" href="profesion.php"><i class="fas fa-user-graduate"></i> Profesiones</a>
                            <a class="dropdown-item" href="departamento.php"><i class="fas fa-building"></i> Departamentos</a>
                            <a class="dropdown-item" href="rol.php"><i class="fas fa-user-tag"></i> Roles</a>
                            <a class="dropdown-item" href="estado.php"><i class="fas fa-info-circle"></i> Estados</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownPlanilla" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-user-tie"></i> Planilla
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownPlanilla">
                            <a class="dropdown-item" href="planilla_salario.php"><i class="fas fa-info-circle"></i> Planilla Salario</a>
                            <a class="dropdown-item" href="quincena1.php"><i class="fas fa-info-circle"></i> Quincena1</a>
                            <a class="dropdown-item" href="quincena2.php"><i class="fas fa-info-circle"></i> Quincena2</a>
                        </div>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownTienda" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fas fa-store"></i> Tienda
                        </a>
                        <div class="dropdown-menu" aria-labelledby="navbarDropdownTienda">
                            <a class="dropdown-item" href="producto.php"><i class="fas fa-box"></i> Productos</a>
                            <a class="dropdown-item" href="ventas.php"><i class="fas fa-shopping-cart"></i> Ventas</a>
                            <a class="dropdown-item" href="marca.php"><i class="fas fa-tags"></i> Marcas</a>
                            <a class="dropdown-item" href="categoria.php"><i class="fas fa-th-list"></i> Categorías</a>
                        </div>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="empresa.php"><i class="fas fa-building"></i> Empresa</a></li>
                    <li class="nav-item"><a class="nav-link" href="oficina.php"><i class="fas fa-map-marker-alt"></i> Oficinas</a></li>
                    <li class="nav-item"><a class="nav-link" href="usuarios.php"><i class="fas fa-user"></i> Usuarios</a></li>
                </ul>
            </div>
            <form class="form-inline" method="post">
                <button class="btn btn-light my-2 my-sm-0" type="submit" name="signout"><i class="fas fa-sign-out-alt"></i> Salir</button>
            </form>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Dashboard de Nomina Consulting</h2>
        
        <!-- Total de Productos Vendidos -->
        <div class="mb-5">
            <h5 class="text-center">Total de Productos Vendidos</h5>
            <canvas id="productosVendidosChart"></canvas>
        </div>

        <!-- Cantidad de Empleados por Estado -->
        <div class="mb-5">
            <h5 class="text-center">Cantidad de Empleados por Estado</h5>
            <canvas id="empleadosEstadoChart"></canvas>
        </div>

        <!-- Cantidad de Usuarios -->
        <div class="mb-5">
            <h5 class="text-center">Cantidad de Usuarios Registrados</h5>
            <canvas id="usuariosChart"></canvas>
        </div>

        <!-- Salario Máximo y Mínimo -->
        <div class="mb-5">
            <h5 class="text-center">Salario Máximo y Mínimo</h5>
            <canvas id="salarioChart"></canvas>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Gráfica de Productos Vendidos
        const productosVendidosCtx = document.getElementById('productosVendidosChart').getContext('2d');
        new Chart(productosVendidosCtx, {
            type: 'bar',
            data: {
                labels: ['Total de Productos Vendidos'],
                datasets: [{
                    label: 'Productos Vendidos',
                    data: [<?php echo $totalProductosVendidos; ?>],
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                }]
            }
        });

        // Gráfica de Empleados por Estado
        const empleadosEstadoCtx = document.getElementById('empleadosEstadoChart').getContext('2d');
        new Chart(empleadosEstadoCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode(array_keys($empleadosPorEstado)); ?>,
                datasets: [{
                    label: 'Empleados por Estado',
                    data: <?php echo json_encode(array_values($empleadosPorEstado)); ?>,
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 206, 86, 0.6)',
                        'rgba(153, 102, 255, 0.6)',
                        'rgba(255, 159, 64, 0.6)'
                    ]
                }]
            }
        });

        // Gráfica de Usuarios
        const usuariosCtx = document.getElementById('usuariosChart').getContext('2d');
        new Chart(usuariosCtx, {
            type: 'doughnut',
            data: {
                labels: ['Total de Usuarios'],
                datasets: [{
                    label: 'Usuarios Registrados',
                    data: [<?php echo $totalUsuarios; ?>],
                    backgroundColor: 'rgba(153, 102, 255, 0.6)',
                }]
            }
        });

        // Gráfica de Salario Máximo y Mínimo
        const salarioCtx = document.getElementById('salarioChart').getContext('2d');
        new Chart(salarioCtx, {
            type: 'bar',
            data: {
                labels: ['Salario Máximo', 'Salario Mínimo'],
                datasets: [{
                    label: 'Salarios',
                    data: [<?php echo $salarioAlto; ?>, <?php echo $salarioBajo; ?>],
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(54, 162, 235, 0.6)'
                    ]
                }]
            }
        });
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
