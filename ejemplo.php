<?php
// Configuración de la conexión
$serverName = "Nomina-Consultingg.mssql.somee.com"; 
$connectionOptions = array(
    "Database" => "Nomina-Consultingg",  // Asumo que este es el nombre de la base de datos, ajusta si es necesario.
    "Uid" => "eacunap2024_SQLLogin_1",
    "PWD" => "Holaque@#2"
);

// Conectar a la base de datos
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Verificar si la conexión fue exitosa
if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Consulta SQL
$sql = "SELECT id_empleado, nombres, apellidos, dpi_pasaporte FROM Empleado";

// Ejecutar la consulta
$stmt = sqlsrv_query($conn, $sql);

// Verificar si la consulta fue exitosa
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

// Mostrar los resultados en una tabla HTML
echo "<table border='1'>
        <tr>
            <th>ID Empleado</th>
            <th>Nombres</th>
            <th>Apellidos</th>
            <th>DPI/Pasaporte</th>
        </tr>";

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    echo "<tr>
            <td>{$row['id_empleado']}</td>
            <td>{$row['nombres']}</td>
            <td>{$row['apellidos']}</td>
            <td>{$row['dpi_pasaporte']}</td>
          </tr>";
}

echo "</table>";

// Cerrar la conexión
sqlsrv_free_stmt($stmt);
sqlsrv_close($conn);
?>
