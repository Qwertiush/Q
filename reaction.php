<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$target_id = intval($_POST['target_id'] ?? 0);
$post_id = intval($_POST['post_id'] ?? 0);
$target_type = ($_POST['target_type'] === 'post' ? 'post' : 'comment');
$type = ($_POST['type'] === 'like') ? 'like' : 'dislike';

$db = new PDO('mysql:host=localhost;dbname=aw_db;charset=utf8', 'root', '');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

if($target_type === 'post'){
    $stmt = $db->prepare("SELECT id FROM reactions WHERE user_id = ? AND post_id = ?");
    $stmt->execute([$_SESSION['user_id'], $target_id]);

    if ($stmt->fetch()) {
        $stmt = $db->prepare("UPDATE reactions SET type = ? WHERE user_id = ? AND post_id = ?");
        $stmt->execute([$type, $_SESSION['user_id'], $target_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO reactions (user_id, post_id, type) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $target_id, $type]);
    }

    header("Location: post.php?id=$target_id");
    exit;
}
else if($target_type === 'comment'){
    $stmt = $db->prepare("SELECT id FROM reactions WHERE user_id = ? AND comment_id = ?");
    $stmt->execute([$_SESSION['user_id'], $target_id]);

    if ($stmt->fetch()) {
        $stmt = $db->prepare("UPDATE reactions SET type = ? WHERE user_id = ? AND comment_id = ?");
        $stmt->execute([$type, $_SESSION['user_id'], $target_id]);
    } else {
        $stmt = $db->prepare("INSERT INTO reactions (user_id, comment_id, type) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['user_id'], $target_id, $type]);
    }
    
    header("Location: post.php?id=$post_id");
    exit;
}
else{
    exit("Źle sie stało!");
}
?>