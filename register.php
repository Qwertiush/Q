<?php
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $db = new PDO('mysql:host=localhost;dbname=aw_db;charset=utf8', 'root', '');
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Wszystkie pola są wymagane!";
    } else {
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->rowCount() > 0) {
            $error = "Użytkownik o podanym emailu już istnieje!";
        } else {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            
            $file = file_get_contents('def_avatar.png');
            $stmt = $db->prepare("INSERT INTO users (name, email, pwd) VALUES (?, ?, ?)");
            $stmt->execute([$username, $email, $password_hash]);
            
            $error = "Rejestracja zakończona pomyślnie, możesz przejść do ekranu logowania!";
            header("Location: login.php");
            exit;
        }
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
            <h2>Rejestracja</h2>
            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>
            <div class="row">
                <form method="POST">
                    <input type="text" name="username" placeholder="Login" required><br>
                    <input type="email" name="email" placeholder="Email" required><br>
                    <input type="password" name="password" placeholder="Hasło" required><br>
                    <button type="submit">Zarejestruj</button>
                </form>
                <img src="logo.png" alt="logo missing" />
            </div>
            <p>Masz już konto? <a href="login.php">Zaloguj się</a></p>
        </div>
    <div>
</body>
</html>