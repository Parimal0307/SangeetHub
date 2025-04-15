<?php
include "../includes/config.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];
$comment = trim($_POST['comment']);

if (empty($comment)) {
    echo json_encode(["success" => false, "error" => "Comment cannot be empty"]);
    exit;
}

$sql = "INSERT INTO comments (post_id, user_id, comment) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iis", $post_id, $user_id, $comment);

if ($stmt->execute()) {
    $comment_id = $stmt->insert_id;
    $username_query = "SELECT username, profile_pic FROM users WHERE id = ?";
    $stmt = $conn->prepare($username_query);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    // $stmt->bind_result($username, $profile_pic);
    // $stmt->fetch();

    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    
    echo json_encode([
        "success" => true,
        "comment_id" => $comment_id,
        "username" => $user['username'],
        "profile_pic" => $user['profile_pic'],
        "comment" => $comment
    ]);
} else {
    echo json_encode(["success" => false, "error" => "Database error"]);
}
?>