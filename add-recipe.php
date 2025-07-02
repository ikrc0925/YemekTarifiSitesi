<?php 
session_start();
include "db.php";

// Başlangıçta hata mesajı boş
$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Form verilerini al
    $title = $_POST['title'] ?? '';
    $ingredients = $_POST['ingredients'] ?? '';
    $preparation = $_POST['preparation'] ?? '';
    $category_id = $_POST['category_id'] ?? '';

    // Boş alan kontrolü
    if (empty($title) || empty($ingredients) || empty($preparation) || empty($category_id)) {
        $error = "Lütfen tüm alanları doldurun.";
    } else {
        // Güvenli hale getir
        $title = mysqli_real_escape_string($conn, $title);
        $ingredients = mysqli_real_escape_string($conn, $ingredients);
        $preparation = mysqli_real_escape_string($conn, $preparation);
        $category_id = intval($category_id);
        $user_id = $_SESSION['user_id'];

        // Tarif ekleme sorgusu
        $sql = "INSERT INTO recipes (user_id, title, ingredients, preparation, category_id)
                VALUES ('$user_id', '$title', '$ingredients', '$preparation', '$category_id')";

        if (mysqli_query($conn, $sql)) {
            header("Location: tarifler.php?success=1");
            exit();
        } else {
            $error = "Tarif eklenirken hata oluştu: " . mysqli_error($conn);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tarif Ekle - Tarif Dünyası</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
  <div class="add-recipe-container">
    <h2>Tarif Ekle</h2>

    <?php if (!empty($error)) echo "<p style='color:red;'>$error</p>"; ?>

    <form action="" method="POST">
      <label for="title">Tarif Adı:</label>
      <input type="text" id="title" name="title" required />
      <br><br>

      <label for="ingredients">Malzemeler (virgül ile ayır):</label>
      <input type="text" id="ingredients" name="ingredients" required />
      <br><br>

      <label for="preparation">Yapılışı:</label>
      <textarea id="preparation" name="preparation" rows="5" required></textarea>
      <br><br>

      <label for="category">Kategori:</label>
      <select id="category" name="category_id" required>
          <option value="">Seçiniz</option>
          <option value="1">Çorba</option>
          <option value="2">Ana Yemek</option>
          <option value="3">Tatlı</option>
          <option value="4">Salata</option>
          <option value="5">Atıştırmalık</option>
      </select>

      <button type="submit">Tarifi Ekle</button>
      <br>
      <a href="tarifler.php">Ana Sayfa</a>
    </form>
  </div>
</body>
</html>
