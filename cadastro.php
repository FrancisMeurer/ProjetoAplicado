<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gestao_de_pessoas";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

$sql = "SELECT MAX(id) as maxId FROM usuarios";
$result = $conn->query($sql);

$nextId = 1;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $nextId = $row['maxId'] + 1;
}

echo $nextId;

$conn->close();
?>
