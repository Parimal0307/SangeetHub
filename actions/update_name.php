<?php
session_start();
include '../includes/config.php';

$user_id = $_SESSION['user_id'];
$new_name = mysqli_real_escape_string($conn, $_POST['display_name']);

$sql = "UPDATE users SET username='$new_name' WHERE id='$user_id'";
mysqli_query($conn, $sql);

echo "Name updated!";
?>