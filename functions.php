<?php
// funkcja zwraca avatar użytkownika
function getAvatar(PDO $db, int $user_id): ?string {
    $stmt = $db->prepare("SELECT avatar FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $avatar = $stmt->fetchColumn();
    
    if (empty($avatar)) {
        $defaultAvatarPath = __DIR__ . '/def_avatar.png';
        return file_exists($defaultAvatarPath) ? file_get_contents($defaultAvatarPath) : null;
    }
    
    return $avatar;
}
// funkcja zwraca liczbę komentarzy do posta
function getNrOfComments(PDO $db, int $post_id): int {
    $q_comments = "SELECT COUNT(*) FROM comments Where post_id = $post_id";
    $stmt_comments = $db->query($q_comments);
    return $stmt_comments->fetchColumn();
}
//funkcja zwraca wszystkie posty
function getPosts(PDO $db): array {
    $stmt = $db->query("SELECT * FROM posts ORDER BY created_at DESC");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
//funkcja zwraca nazwe użytkownika
function getUserName(PDO $db, int $user_id): string {
    $stmt = $db->prepare("SELECT name FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchColumn() ?: 'Nieznany użytkownik';
}
//funkcja zwraca post o danym id
function getPost(PDO $db, int $post_id): array {
    $stmt = $db->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$post_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
//funkcja zwraca reakcje do posta lub komentarza (type: post lub comment)
function getReactions(PDO $db, int $id, string $type ): array {

    if ($type === 'comment') {
        $q_reactions = "SELECT type, COUNT(*) as count 
            FROM reactions 
            WHERE comment_id = ? 
            GROUP BY type";
    } else {
        $q_reactions = "SELECT type, COUNT(*) as count 
            FROM reactions 
            WHERE post_id = ? 
            GROUP BY type";
    }
    $stmt = $db->prepare($q_reactions);
    $stmt->execute([$id]);
    $reactions = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $counts = ['like' => 0, 'dislike' => 0];
    foreach ($reactions as $row) {
        $counts[$row['type']] = $row['count'];
    }

    return $counts;
}
//funkcja zwraca użytkonika po nazwie
function getUserByName(PDO $db, string $user_name): array {
    $stmt = $db->prepare("SELECT * FROM users WHERE name = ?");
    $stmt->execute([$user_name]);
	$result = $stmt->fetch(PDO::FETCH_ASSOC);
	
    return $result;
}

?>