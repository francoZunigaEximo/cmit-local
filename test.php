<?php
$servername = "localhost";
$username = "root";
$password = "Hiperion$3773";
$database = "db_cmit_test";

try {
    $conn = new PDO("mysql:host=$servername;dbname=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "La conexión correcta";
} catch(PDOException $e) {
    echo "No funciona. Error:  " . $e->getMessage();
}

echo "<hr />";

$rewrite = function_exists('apache_get_modules') && in_array('mod_rewrite', apache_get_modules());

if ($rewrite) {
    echo "El módulo habilitado.";
} else {
    echo "(mod_rewrite) no está habilitado.";
}
