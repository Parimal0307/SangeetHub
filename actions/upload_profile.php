<?php
session_start();
include '../includes/config.php'; // Database connection

$user_id = $_SESSION['user_id'];

if(isset($_FILES['profile_pic'])){
    $target_dir = "../uploads/profiles/";
    $file_name = $user_id . "_" . basename($_FILES["profile_pic"]["name"]);
    $target_file = $target_dir . $file_name;

    if(move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)){
        $sql = "UPDATE users SET profile_pic='$file_name' WHERE id='$user_id'";
        mysqli_query($conn, $sql);
        echo "Profile updated!";
    } else {
        echo "Error uploading!";
    }
}
?>