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

    if (!isset($_GET['id'])) {
        header("Location: home.php");
        exit;
    }

    try {
        $db = new PDO("mysql:host=localhost;dbname=aw_db;charset=utf8", "root", "");
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment'])) {
            $content = trim($_POST['comment']);
            $user_id = $_SESSION['user_id'];
            $post_id = $_GET['id'];

            if (empty($content)) {
                die("Treść komentarza jest wymagana!");
            }

            try {
                $query = "INSERT INTO comments (content, user_id, post_id) VALUES (:content, :user_id, :post_id)";
                $stmt = $db->prepare($query);
                $stmt->execute([
                    ':content' => $content,
                    ':user_id' => $user_id,
                    ':post_id' => $post_id
                ]);
                
                header("Location: post.php?id=" . $post_id);
                exit();
            } catch(PDOException $e) {
                die("Błąd zapytania: " . $e->getMessage());
            }
        } 

        $post = getPost($db, $_GET['id']);

        echo "<div class='main'>
                <div class='home-container'>
                    <div class='buttons-container'>
                        <img src='logo.png' alt='logo missing' />
                        <form action='home.php' method='GET'>
                            <button class='add-post-btn' type='submit'>Wróc na główną</button>
                        </form>
                        <form action='logout.php' method='GET'>
                            <button class='logout-btn' type='submit'>Wyloguj się</button>
                        </form>
                    </div>";


        echo "<br/>";
            $nr_of_comments = getNrOfComments($db, $_GET['id']);

            $q_names = "SELECT name FROM users WHERE id = $post[user_id]";
            $stmt = $db->query($q_names);
            $name = $stmt->fetchColumn();

            $counts = getReactions($db, $post['id'], 'post');

            $avatarData = base64_encode(getAvatar($db, $post['user_id']));
            $avatarMime = 'image/png';

            echo "
            <div class='posts-container'>
            <div class='static-post-container'>";
            
            if($_SESSION['user_id'] == $post['user_id']){
                echo "  <div class='delete-post-btn-container'>
                            <form action='delete_post.php' method='POST'>
                                <input type='hidden' name='post_id' value='{$post['id']}'>
                                <button class='logout-btn' type='submit'>Usuń post</button>
                            </form>
                        </div>";
            }

            echo "
                <h3>" . htmlspecialchars($post['title']) . "</h3>
                <p>" . htmlspecialchars($post['content']) . "</p>
                <div class='post-meta'>
                    <img src='data:$avatarMime;base64,$avatarData' alt='avatar is missing' class='avatar' />
                    <h6>" . htmlspecialchars($name) . "</h6>
                    <h6>" . htmlspecialchars($post['created_at']) . "</h6>
                        <div class='likes-dislikes'>
                            <form action='reaction.php' method='POST'>
                                <input type='hidden' name='target_id' value='{$post['id']}'>
                                <input type='hidden' name='target_type' value='post'>
                                <input type='hidden' name='type' value='like'>
                                <button class='like-btn' type='submit'>+ (" . $counts['like'] . ")</button>
                            </form>
                            <form action='reaction.php' method='POST'>
                                <input type='hidden' name='target_id' value='{$post['id']}'>
                                <input type='hidden' name='target_type' value='post'>
                                <input type='hidden' name='type' value='dislike'>
                                <button class='like-btn' type='submit'>- (" . $counts['dislike'] . ")</button>
                            </form>
                        </div>
                    <h6>Komentarze: " . $nr_of_comments . "</h6>
                </div>
            </div>";

        echo "
        <br/>
        <div class='static-post-container'>
            <form method='POST'>
            <input type='hidden' name='post_id' value='" . $_GET['id'] . "'>
                <textarea class='textarea-comment' name='comment' placeholder='Napisz komentarz...' required></textarea>
                <button class='add-post-btn' type='submit'>Komentuj</button>
            </form>
        </div>
        ";

        $stmt_comments = $db->prepare("
            SELECT c.*, u.name as author_name 
            FROM comments c
            JOIN users u ON c.user_id = u.id
            WHERE c.post_id = ?
            ORDER BY c.created_at DESC
        ");
        $stmt_comments->execute([$_GET['id']]);
        $comments = $stmt_comments->fetchAll(PDO::FETCH_ASSOC);


        if (empty($comments)) {
            echo "<p>Brak komentarzy.</p>";
        } else {
            echo "<p>Liczba komentarzy: " . count($comments) . "</p>";
        }
        echo "<br/>";
        foreach ($comments as $comment) {

            $counts = getReactions($db, $comment['id'], 'comment');

            $avatarData = base64_encode(getAvatar($db, $comment['user_id']));
            $avatarMime = 'image/png';

            echo "
            <div class='static-post-container'>
                <p>" . htmlspecialchars($comment['content']) . "</p>
                <div class='post-meta'>
                    <img src='data:$avatarMime;base64,$avatarData' alt='avatar is missing' class='avatar' />
                    <h6>Autor: " . htmlspecialchars($comment['author_name']) . "</h6>
                    <h6>" . htmlspecialchars($comment['created_at']) . "</h6>
                        <div class='likes-dislikes'>
                            <form action='reaction.php' method='POST'>
                                <input type='hidden' name='target_id' value='{$comment['id']}'>
                                <input type='hidden' name='post_id' value='{$post['id']}'>
                                <input type='hidden' name='target_type' value='comment'>
                                <input type='hidden' name='type' value='like'>
                                <button class='like-btn' type='submit'>+ (" . $counts['like'] . ")</button>
                            </form>
                            <form action='reaction.php' method='POST'>
                                <input type='hidden' name='target_id' value='{$comment['id']}'>
                                <input type='hidden' name='post_id' value='{$post['id']}'>
                                <input type='hidden' name='target_type' value='comment'>
                                <input type='hidden' name='type' value='dislike'>
                                <button class='like-btn' type='submit'>- (" . $counts['dislike'] . ")</button>
                            </form>
                        </div>
                </div>
            </div>";
        }
        echo "</div>";
        echo "</div>";
        echo "</div>";

    } catch (PDOException $e) {
        echo "Błąd połączenia z bazą danych: " . $e->getMessage();
    }
?>

</body>
</html>