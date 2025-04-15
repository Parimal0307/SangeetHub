<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = $_POST['post_id'];

// Check if the user has already liked the post
$stmt = $conn->prepare("SELECT id FROM likes WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Unlike the post
    $stmt = $conn->prepare("DELETE FROM likes WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
} else {
    // Like the post
    $stmt = $conn->prepare("INSERT INTO likes (post_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
}

// Get updated like count
$like_count_query = $conn->prepare("SELECT COUNT(*) AS like_count FROM likes WHERE post_id = ?");
$like_count_query->bind_param("i", $post_id);
$like_count_query->execute();
$like_count_result = $like_count_query->get_result()->fetch_assoc();
$like_count = $like_count_result["like_count"];

echo json_encode(["success" => true, "like_count" => $like_count]);
?>