<?php
session_start();
include "db.php";

// Arama ve kategori filtresi
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';

// Tarifleri çek
$sql = "SELECT recipes.*, categories.name AS category_name 
        FROM recipes 
        LEFT JOIN categories ON recipes.category_id = categories.id 
        WHERE 1=1";

if (!empty($search)) {
    $sql .= " AND recipes.title LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
}

if (!empty($category)) {
    $sql .= " AND recipes.category_id = " . intval($category);
}

$result = mysqli_query($conn, $sql);

// Kullanıcının alerjilerini çek
$user_allergies = [];
if (isset($_SESSION['user_id'])) {
    $user_id = intval($_SESSION['user_id']);
    $allergy_query = mysqli_query($conn, "SELECT allergies.name FROM user_allergies 
        JOIN allergies ON user_allergies.allergy_id = allergies.id 
        WHERE user_allergies.user_id = $user_id");
    while ($row = mysqli_fetch_assoc($allergy_query)) {
        $user_allergies[] = strtolower(trim($row['name']));
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <title>Tarifler</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>
<header>
  <h1>Tarif Dünyası</h1>
  <nav>
    <a href="index.php">Ana Sayfa</a>
    <?php if (isset($_SESSION['user_id'])): ?>
      <a href="add-recipe.php">Tarif Ekle</a>
      <a href="logout.php">Çıkış Yap</a>
    <?php else: ?>
      <a href="login.html">Giriş Yap</a>
      <a href="register.php">Kayıt Ol</a>
    <?php endif; ?>
  </nav>
</header>

<main>
  <section class="filter-section">
    <form action="tarifler.php" method="GET" style="display: flex; gap: 10px; flex-wrap: wrap;">
      <input type="text" name="search" placeholder="Yemek adı ile ara..." value="<?= htmlspecialchars($search) ?>" />
      <select name="category">
        <option value="">Tüm Kategoriler</option>
        <?php
        $cat_query = mysqli_query($conn, "SELECT * FROM categories");
        while ($cat = mysqli_fetch_assoc($cat_query)) {
            $selected = ($category == $cat['id']) ? 'selected' : '';
            echo "<option value='" . $cat['id'] . "' $selected>" . htmlspecialchars($cat['name']) . "</option>";
        }
        ?>
      </select>
      <button type="submit">Ara</button>
    </form>
  </section>

  <section class="recipe-list">
    <?php if (mysqli_num_rows($result) > 0): ?>
      <?php while ($row = mysqli_fetch_assoc($result)): ?>
        <?php
          // Alerjen kontrolü
          $ingredients = strtolower($row['ingredients']);
          $contains_allergen = false;
          $found_allergens = [];

          foreach ($user_allergies as $allergen) {
            if (strpos($ingredients, $allergen) !== false) {
              $contains_allergen = true;
              $found_allergens[] = $allergen;
            }
          }
        ?>
        <div class="recipe-card">
          <h3><?= htmlspecialchars($row['title']) ?></h3>
          <p><?= nl2br(htmlspecialchars($row['ingredients'])) ?></p>
          <p><strong>Kategori:</strong> <?= htmlspecialchars($row['category_name']) ?></p>
          <?php if ($contains_allergen): ?>
            <p style="color: red; font-weight: bold;">⚠ Bu tarif alerjiniz olan şu madde(leri) içeriyor: <?= implode(', ', $found_allergens) ?></p>
          <?php endif; ?>
          <a href="tarif.php?id=<?= $row['id'] ?>">Tarife Git</a>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Aradığınız kritere uygun tarif bulunamadı.</p>
    <?php endif; ?>
  </section>
</main>
</body>
</html>