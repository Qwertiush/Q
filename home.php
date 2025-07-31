<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8" />
    <title>Q</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php
    require_once 'functions.php';

    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    try {
    
        $db = new PDO("mysql:host=localhost;dbname=aw_db;charset=utf8", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $posts = getPosts($db);

        $name = getUserName($db, $_SESSION['user_id']);

        $avatarData = base64_encode(getAvatar($db, $_SESSION['user_id']));
        $avatarMime = 'image/png';

        echo "
            <div class='main'>
                <div class='home-container'>
                    <div class='buttons-container'>
                        <img src='logo.png' alt='logo missing' />";
        echo "          <a href='upload_avatar.php'><img src='data:$avatarMime;base64,$avatarData' alt='avatar is missing' class='avatar' style='cursor: pointer'/></a>
                        <h1>Witaj " . $_SESSION['username'] . "</h1>
                        <form action='add-post.php'>
                            <button class='add-post-btn' type='submit'>+ Dodaj nowy post</button>
                        </form>
                        <form action='logout.php'>
                            <button class='logout-btn' type='submit'>Wyloguj się</button>
                        </form>
                    </div>
            ";


        echo "<br/>";
        foreach ($posts as $post) {
            $nr_of_comments = getNrOfComments($db, $post['id']);

            $name = getUserName($db, $post['user_id']);

            $counts = getReactions($db, $post['id'], 'post');

            $avatarData = base64_encode(getAvatar($db, $post['user_id']));
            $avatarMime = 'image/png';
            if (empty($avatarData)) {
                $avatarData = base64_encode(file_get_contents('def_avatar.png'));
            }

            echo "<div class='posts-container'>";
            echo "
                        <div class='post-container'>
                            <a href='post.php?id=" . $post['id'] . "' class='post-link' style='text-decoration: none;'>
                                <h3>" . htmlspecialchars($post['title']) . "</h3>
                                <p>" . htmlspecialchars($post['content']) . "</p>
                            
                                <div class='post-meta'>
                                    <img src='data:$avatarMime;base64,$avatarData' alt='avatar is missing' class='avatar' />
                                    <h6>" . htmlspecialchars($name) . "</h6>
                                    <h6>" . htmlspecialchars($post['created_at']) . "</h6>
                                    <div class='likes-dislikes'>
                                        <h6>+ (" . $counts['like'] . ")</h6>
                                        <h6>- (" . $counts['dislike'] . ")</h6>
                                    </div>
                                    <h6>Komentarze: " . $nr_of_comments . "</h6>
                                </div>
                            </a>
                        </div>
                    ";
            echo "</div>";
        }
        echo "</div>";
        echo "</div>";
    } catch (PDOException $e) {
        echo "Błąd połączenia z bazą danych: " . $e->getMessage();
    }
?>
</body>
</html>