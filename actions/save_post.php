<?php
session_start();
include "../includes/config.php";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["success" => false, "error" => "User not logged in."]);
    exit;
}

if (!isset($_POST['post_id'])) {
    echo json_encode(["success" => false, "error" => "Post ID missing."]);
    exit;
}

$user_id = $_SESSION['user_id'];
$post_id = intval($_POST['post_id']); // Ensure it's an integer

// Check if the post is already saved
$stmt = $conn->prepare("SELECT id FROM saved_posts WHERE post_id = ? AND user_id = ?");
$stmt->bind_param("ii", $post_id, $user_id);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    // Unsave the post
    $stmt = $conn->prepare("DELETE FROM saved_posts WHERE post_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $action = "unsaved";
} else {
    // Save the post
    $stmt = $conn->prepare("INSERT INTO saved_posts (post_id, user_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $post_id, $user_id);
    $stmt->execute();
    $action = "saved";
}

// Return JSON response
echo json_encode(["success" => true, "action" => $action]);
?>