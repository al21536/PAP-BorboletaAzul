<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "borboleta_azul";

// Criar ligação
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar ligação
if ($conn->connect_error) {
    die("Falha na ligação: " . $conn->connect_error);
}
?>
