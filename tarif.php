<?php
session_start();
include "db.php";

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int) $_GET['id'];

    $sql = "SELECT recipes.*, categories.name AS category_name, users.name AS user_name 
            FROM recipes 
            LEFT JOIN categories ON recipes.category_id = categories.id 
            LEFT JOIN users ON recipes.user_id = users.id 
            WHERE recipes.id = $id";

    $result = mysqli_query($conn, $sql);

    if (mysqli_num_rows($result) > 0) {
        $tarif = mysqli_fetch_assoc($result);
    } else {
        echo "Tarif bulunamadı.";
        exit;
    }
} else {
    echo "Geçersiz tarif ID.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($tarif['title']) ?> | Tarif Detayı</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .tarif-detay {
            max-width: 700px;
            margin: 40px auto;
            padding: 30px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .tarif-detay h2 {
            color: #e67e22;
        }
        .tarif-detay p {
            margin: 10px 0;
        }
        .tarif-detay .malzemeler, .tarif-detay .hazirlanis {
            margin-top: 20px;
        }
    </style>
</head>
<body>
<div class="tarif-detay">
    <h2><?= htmlspecialchars($tarif['title']) ?></h2>
    <p><strong>Kategori:</strong> <?= htmlspecialchars($tarif['category_name']) ?></p>
    <p><strong>Ekleyen:</strong> <?= htmlspecialchars($tarif['user_name']) ?></p>

    <div class="malzemeler">
        <h3>Malzemeler:</h3>
        <p><?= nl2br(htmlspecialchars($tarif['ingredients'])) ?></p>
    </div>

    <div class="hazirlanis">
        <h3>Hazırlanışı:</h3>
        <p><?= nl2br(htmlspecialchars($tarif['preparation'])) ?></p>
    </div>
</div>
</body>
</html>
