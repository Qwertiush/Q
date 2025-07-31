<?php
require_once 'functions.php';

session_start();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    
    try {
    $db = new PDO('mysql:host=localhost;dbname=aw_db;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch(PDOException $e) {
        die("Błąd połączenia: " . $e->getMessage());
    }
    
    $user = getUserByName($db, $username);
    
    if ($user && password_verify($password, $user['pwd'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['name'];

        header("Location: home.php");
        exit;
    } else {
        $error = "Nieprawidłowy login lub hasło!";
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
    <div class="main">
        <div class="login-container">
            <h2>Logowanie</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="row">
                <form method="POST">
                    <input type="text" name="username" placeholder="Login" required><br>
                    <input type="password" name="password" placeholder="Hasło" required><br>
                    <button type="submit">Zaloguj</button>
                </form>    
                <img src="logo.png" alt="logo missing" />
            </div>
            <p>Nie masz konta? <a href="register.php">Zarejestruj się</a></p>
        </div>
    </div>  
</body>
</html>