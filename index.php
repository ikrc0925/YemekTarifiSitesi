<?php
session_start(); // Oturum kontrolü için en üstte olmalı
include 'db.php';

/* Kategorileri çek */
$kategoriler = [];
$katSorgu = mysqli_query($conn, "SELECT * FROM categories");
while ($row = mysqli_fetch_assoc($katSorgu)) {
    $kategoriler[] = $row;
}

/* Son 10 tarifi çek */
$tarifSorgu = mysqli_query(
    $conn,
    "SELECT r.id, r.title, r.ingredients, c.name AS category_name
     FROM recipes r
     LEFT JOIN categories c ON r.category_id = c.id
     ORDER BY r.id DESC
     LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="tr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Tarif Dünyası</title>
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
  <!-- Filtre formu -->
  <section class="filter-section">
    <form action="tarifler.php" method="GET">
      <input type="text" name="search" placeholder="Yemek adı ile ara..." />
      <select name="category">
        <option value="">Tüm Kategoriler</option>
        <?php foreach ($kategoriler as $kat): ?>
          <option value="<?= $kat['id'] ?>">
            <?= htmlspecialchars($kat['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <button type="submit">Ara</button>
    </form>
  </section>

  <!-- Tarif kartları -->
  <section class="recipe-list">
    <?php if (mysqli_num_rows($tarifSorgu) > 0): ?>
      <?php while($tarif = mysqli_fetch_assoc($tarifSorgu)): ?>
        <div class="recipe-card">
          <h3>
            <a href="tarif.php?id=<?= htmlspecialchars($tarif['id']) ?>">
              <?= htmlspecialchars($tarif['title']) ?>
            </a>
          </h3>
          <p><strong>Kategori:</strong> <?= htmlspecialchars($tarif['category_name']) ?></p>
          <?php if (!empty($tarif['id'])): ?>
            <a href="tarif.php?id=<?= intval($tarif['id']) ?>">Tarife Git</a>
          <?php else: ?>
            <span>Tarif ID eksik</span>
          <?php endif; ?>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>Henüz tarif yok.</p>
    <?php endif; ?>
  </section>
</main>
</body>
</html>
