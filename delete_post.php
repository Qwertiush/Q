<?php
session_start();
require_once 'functions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$post_id = intval($_POST['post_id'] ?? 0);

$error = $post_id;

echo $post_id;

    try {
        $db = new PDO("mysql:host=localhost;dbname=aw_db;charset=utf8", "root", "");
        
        $query = "DELETE FROM posts WHERE id = ?";
        $stmt = $db->prepare($query);
        $stmt->execute([$post_id]);

        header("Location: home.php");
        exit();
        
    } catch (Exception $e) {
        $error = "Błąd: " . $e->getMessage();
        die();
    }

?>