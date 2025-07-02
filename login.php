<?php
session_start();
include "db.php"; // Veritabanı bağlantısı

// Formdan gelen verileri al
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Güvenlik için girişler boş mu diye kontrol et
if (empty($email) || empty($password)) {
  echo "Lütfen e-posta ve şifrenizi giriniz.";
  exit;
}

// Kullanıcıyı veritabanında ara
$sql = "SELECT * FROM users WHERE email = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "s", $email);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($user = mysqli_fetch_assoc($result)) {
  // Şifreyi doğrula
  if (password_verify($password, $user['password'])) {
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['allergies'] = $user['allergies'];
    header("Location: tarifler.php");
    exit;
  } else {
    echo "Hatalı şifre girdiniz.";
  }
} else {
  echo "Bu e-posta ile kayıtlı bir kullanıcı bulunamadı.";
}
?>
