<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    try {
        $db = new PDO("mysql:host=localhost;dbname=aw_db;charset=utf8", "root", "");
        
        // Walidacja pliku
        $allowedTypes = ['image/jpeg', 'image/png'];
        if (!in_array($_FILES['avatar']['type'], $allowedTypes)) {
            throw new Exception("Dozwolone tylko pliki JPG i PNG");
        }

        if ($_FILES['avatar']['size'] > 2 * 1024 * 1024) { // 2MB max
            throw new Exception("Plik jest zbyt duży (max 2MB)");
        }

        $avatarData = file_get_contents($_FILES['avatar']['tmp_name']);
        
        $stmt = $db->prepare("UPDATE users SET avatar = ? WHERE id = ?");
        $stmt->execute([$avatarData, $_SESSION['user_id']]);
        
        header("Location: home.php");
        exit;
        
    } catch (Exception $e) {
        die("Błąd: " . $e->getMessage());
    }
}

?>
<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Q</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div clas="main">
        <div class='buttons-container'>
            <img src='logo.png' alt='logo missing' />
            <form action='home.php'>
                <button class='add-post-btn' type='submit'>Wróć na główną</button>
            </form>
            <form action='logout.php'>
                <button class='logout-btn' type='submit'>Wyloguj się</button>
            </form>
        </div>
        <div class="home-container">
            <form action="upload_avatar.php" method="post" enctype="multipart/form-data">
                <input type="file" name="avatar" id="avatar" accept="image/png, image/jpeg">
                <button type="submit">Zmień avatar</button>
            </form>
        </div>
    </div>
</html>