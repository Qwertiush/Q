<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Q</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$error = '';

try {
    $db = new PDO('mysql:host=localhost;dbname=aw_db;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Błąd połączenia: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['title'], $_POST['content'])) {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $user_id = $_SESSION['user_id'] ?? null;

    if (empty($title) || empty($content)) {
        $error = "Tytuł i treść są wymagane!";
        die();
    }

    try {
        $query = "INSERT INTO posts (title, content, user_id) VALUES (:title, :content, :user_id)";
        $stmt = $db->prepare($query);
        $stmt->execute([
            ':title' => $title,
            ':content' => $content,
            ':user_id' => $user_id
        ]);

        echo "Post został dodany!";
        header("Location: home.php");
        exit();
    } catch(PDOException $e) {
        $error = "Błąd zapytania: " . $e->getMessage();
        die();
    }
}

echo "
<div class='main'>
    <div class='home-container'>
        <div class='buttons-container'>
            <img src='logo.png' alt='logo missing' />
            <form action='home.php' method='GET'>
                <button class='add-post-btn' type='submit'>Wróc na główną</button>
            </form>
            <form action='logout.php' method='GET'>
                <button class='logout-btn' type='submit'>Wyloguj się</button>
            </form>
        </div>
    <h2>Dodaj post</h2>
    <?php if ($error): ?>
        <p style='color: red;'><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
    <div class='posts-container'>
        <div class='post-container'>
            <form action='add-post.php' method='POST'>
                <input type='text' name='title' placeholder='Tytuł' required>
                <textarea name='content' placeholder='Treść' required></textarea>
                <button type='submit'>Publikuj</button>
            </form>
        </div>
    </div>
    </div>
</div>
    ";
?>
</body>
</html>