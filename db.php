<?php
$host = "localhost";
$user = "root";
$password = "";
$database = "chaflog_db";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
  die("Veritabanına bağlanılamadı: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>
