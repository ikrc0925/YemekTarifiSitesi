<?php 
include "db.php";
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!$name || !$email || !$password) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        // Email benzersizliği kontrolü
        $check = mysqli_query($conn, "SELECT id FROM users WHERE email = '" . mysqli_real_escape_string($conn, $email) . "'");
        if (mysqli_num_rows($check) > 0) {
            $error = "Bu email zaten kayıtlı.";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO users (name, email, password) VALUES (?, ?, ?)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sss", $name, $email, $hashed_password);
            if (mysqli_stmt_execute($stmt)) {
                // Yeni kullanıcı ID'si
                $user_id = mysqli_insert_id($conn);

                // Alerji verilerini al
                $allergies = $_POST['allergy'] ?? [];

                // Alerjileri veritabanına ekle
                foreach ($allergies as $allergy_id) {
                    $allergy_id = (int)$allergy_id;

                    // Alerji ID'si gerçekten allergies tablosunda var mı diye kontrol et
                    $check_allergy = mysqli_query($conn, "SELECT id FROM allergies WHERE id = $allergy_id");
                    if (mysqli_num_rows($check_allergy) > 0) {
                        $sql_allergy = "INSERT INTO user_allergies (user_id, allergy_id) VALUES ($user_id, $allergy_id)";
                        mysqli_query($conn, $sql_allergy);
                    }
                }

                $_SESSION['user_id'] = $user_id;
                header("Location: tarifler.php");
                exit;
            } else {
                $error = "Kayıt sırasında hata oluştu.";
            }
        }
    }
}
?>

<!DOCTYPE html> 
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Kayıt Ol - Chaflog</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="register-container">
    <h2>Kayıt Ol</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="POST">
      <label for="name">Ad Soyad:</label>
      <input type="text" id="name" name="name" required />
      <br>

      <label for="email">E-posta:</label>
      <input type="email" id="email" name="email" required />
      <br>

      <label for="password">Şifre:</label>
      <input type="password" id="password" name="password" required />
      <br>

      <label>Alerjileriniz:</label>
      <div class="allergy-options">
        <label><input type="checkbox" name="allergy[]" value="1" /> Süt</label>
        <label><input type="checkbox" name="allergy[]" value="2" /> Gluten</label>
        <label><input type="checkbox" name="allergy[]" value="3" /> Fıstık</label>
        <label><input type="checkbox" name="allergy[]" value="4" /> Yumurta</label>
        <label><input type="checkbox" name="allergy[]" value="5" /> Deniz Ürünleri</label>
      </div>

      <button type="submit">Kayıt Ol</button>
    </form>
    <p>Zaten hesabınız var mı? <a href="login.html">Giriş Yap</a></p>
    <a href="index.php">Ana Sayfa</a>
  </div>
</body>
</html>
