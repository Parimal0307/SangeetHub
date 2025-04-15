<?php
session_start();
include "../includes/config.php"; // Database connection file

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"]; // Get logged-in user's ID
    $youtube_link = trim($_POST["youtube_link"]);
    $caption = trim($_POST["caption"]);
    $tags = $_POST["tags"];

    // Insert post into the database
    $sql = "INSERT INTO posts (user_id, youtube_link, caption) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $youtube_link, $caption);
    $stmt->execute();
    $post_id = $stmt->insert_id;  // Get the inserted post ID
    $stmt->close();

    // Process Tags
    $tagList = array_map('trim', explode(',', $tags));

    foreach ($tagList as $tag) {
        if (!empty($tag)) {
            $tag = strtolower($tag);
            
            // Insert tag if not exists
            $stmt = $conn->prepare("INSERT IGNORE INTO tags (name) VALUES (?)");
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            $stmt->close();

            // Get Tag ID
            $stmt = $conn->prepare("SELECT id FROM tags WHERE name = ?");
            $stmt->bind_param("s", $tag);
            $stmt->execute();
            $stmt->bind_result($tag_id);
            $stmt->fetch();
            $stmt->close();

            // Link Tag to Post
            $stmt = $conn->prepare("INSERT INTO post_tags (post_id, tag_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $post_id, $tag_id);
            $stmt->execute();
            $stmt->close();
        }
    }

    header("Location: home.php");
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="../assets/css/upload.css?v=<?php echo time();?>">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-rounded/css/uicons-regular-rounded.css'>
    <title>SangeetHub</title>
</head>
<body>
    <?php
        include 'sidebar.php';
    ?>

    <div class="main-content">
        <h1>Share Your Favorite Tracks!!</h1>

    
        <iframe class="previewArea " id="videoPreview" frameborder="0" allowfullscreen></iframe>
        
        <div class="previewArea" id="preview">
            <div class="preview-content">
                <i class="fi fi-rr-film"></i>
                <p>Your video will appear here after you add a valid YouTube link.</p>
            </div>
        </div>
        <form action="upload.php" method="POST">
            <input type="text" id="youtubeLink" name="youtube_link" placeholder="Enter YouTube Video URL" required oninput="showPreview()">
            <hr>
            <input type="text" name="tags" placeholder="Enter tags here (comma seperated)">
            <hr>
            <textarea name="caption" placeholder="Add a caption"></textarea>
            <button type="submit" name="upload">Post</button>
        </form>
    </div>
    

    <script>
        function showPreview() {
            let url = document.getElementById("youtubeLink").value;
            let videoId = extractVideoID(url);
            let iframe = document.getElementById("videoPreview");
            let preview = document.getElementById("preview");

            if (videoId) {
                iframe.src = `https://www.youtube.com/embed/${videoId}`;
                iframe.style.display = "block";  // Show the video preview
                preview.style.display = "none";
            } else {
                iframe.style.display = "none";   // Hide preview if no valid link
                preview.style.display = "flex";
            }
        }

        function extractVideoID(url) {
            let regex = /(?:https?:\/\/)?(?:www\.)?(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/;
            let match = url.match(regex);
            return match ? match[1] : null;
        }
    </script>
</body>
</html>