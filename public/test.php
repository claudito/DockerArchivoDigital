<?php
$serverName = "45.55.62.182";
$connectionOptions = [
    "Database" => "BD_SIGESA_TEST",
    "Uid" => "sa",
    "PWD" => "E2x4PzUp0y3Tqw",
    "Encrypt" => 0,
    "TrustServerCertificate" => 1,
];

$conn = sqlsrv_connect($serverName, $connectionOptions);
if ($conn) {
    echo "Conexión exitosa.";
} else {
    echo "Fallo la conexión.";
    die(print_r(sqlsrv_errors(), true));
}
