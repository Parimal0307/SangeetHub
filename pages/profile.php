<?php
session_start();
include('../includes/config.php'); // Ensure the database connection is included

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user data from database
$query = "SELECT username, profile_pic FROM users WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// If user data is not found, set default values
if (!$user) {
    $user = [
        'username' => 'Guest',
        'profile_pic' => 'default.png' // Set a default profile image
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../style.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="../assets/css/profile.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="../assets/css/home.css?v=<?php echo time();?>">
    <link rel='stylesheet' href='https://cdn-uicons.flaticon.com/2.6.0/uicons-regular-straight/css/uicons-regular-straight.css'>
    <title>SangeetHub</title>
</head>
<body>
    <?php
        include "sidebar.php";
    ?>

    <div class="main-content">
        <div class="profile-container">
                <div class="profile-header">
                    <img id="profile-pic" src="../uploads/profiles/<?php echo isset($user['profile_pic']) ? $user['profile_pic'] : 'default.png'; ?>" alt="Profile Picture">
                    
                    <div class="name-cont">
                        <h2 id="display-name"><?php echo isset($user['username']) ? htmlspecialchars($user['username']) : 'Guest'; ?></h2>

                        <div class="btn-cont">
                            <input type="file" id="profile-upload" style="display: none;">
                            <button onclick="document.getElementById('profile-upload').click()">Upload image</button>
                            <a href="../auth/logout.php" onclick="return confirm('Are you sure you want to logout?')">
                                <button id="logout" onclick="">Log Out</button>
                            </a>
                        </div>
                    </div>
                </div>
            

            <div class="profile-tabs">
                <p onclick="showTab('posts')"><span><i class="fi fi-rr-menu-burger"></i></span> Posts</p>
                <p onclick="showTab('saved')"><span><i class="fi fi-rr-bookmark"></i></span> Saved</p>
            </div>

            <div id="posts" class="content">
                <!-- User Posts will be loaded here -->
                <?php
                    $sql = "SELECT posts.*, users.username, 
                            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
                            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = {$_SESSION['user_id']}) AS user_liked,
                            (SELECT COUNT(*) FROM saved_posts WHERE saved_posts.post_id = posts.id AND saved_posts.user_id = {$_SESSION['user_id']}) AS user_saved
                            FROM posts 
                            JOIN users ON posts.user_id = users.id 
                            WHERE posts.user_id = $user_id
                            ORDER BY posts.created_at DESC";

                    $result = $conn->query($sql);

                    if(!$result->num_rows > 0){
                        echo "<p id='ntd'>---Nothing to display---</p>";
                    }

                    while ($row = $result->fetch_assoc()) {
                        preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', 
                        $row['youtube_link'], $matches);
                        $video_id = $matches[1] ?? '';

                        // Determine the like button state
                        $liked_class = ($row['user_liked'] > 0) ? "liked" : "";
                        $saved_class = ($row['user_saved'] > 0) ? "saved" : "";

                        echo "<div class='post-and-comment'>";
                        echo "<div class='post' id=post-{$row['id']}>";

                        echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/$video_id' frameborder='0' allowfullscreen></iframe>";

                        echo "<div class='actions'>
                                <i class='fi fi-rr-heart like-btn $liked_class' data-post-id='{$row['id']}'></i>  
                                <i class='fi fi-rr-comment' id='comment' onclick='showComments({$row['id']})'></i>
                                <i class='fi fi-rr-bookmark save-btn $saved_class' data-post-id='{$row['id']}'></i>
                            </div>";

                        echo "<p id='likes-{$row['id']}'>{$row['like_count']} likes</p>";

                        if ($row["caption"]) {
                            echo "<p id='caption'>{$row['caption']}</p>";
                        }

                        // Fetch and display tags
                        $post_id = $row['id'];
                        $tag_query = "SELECT name FROM tags INNER JOIN post_tags ON tags.id = post_tags.tag_id WHERE post_tags.post_id = $post_id";
                        $tag_result = $conn->query($tag_query);

                        if ($tag_result->num_rows > 0) {
                            echo "<ul class='tags'>";
                            while ($tag = $tag_result->fetch_assoc()) {
                                echo "<li>{$tag['name']}</li>";
                            }
                            echo "</ul>";
                        }

                        echo "</div>";

                        // comment section
                        echo "<div class='comments-section' id='comments-section-{$row['id']}'>
                        <div class='existing-comments' id='comments-{$row['id']}'>";
                        
                        // Fetch and display comments
                        $comment_query = "SELECT comments.*, users.username, users.profile_pic FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = {$row['id']} ORDER BY comments.created_at ASC";
                        $comment_result = $conn->query($comment_query);
                        
                        while ($comment = $comment_result->fetch_assoc()) {
                            echo "<div class='comment-item'>";
                            echo "<img src='../uploads/profiles/{$comment['profile_pic']}'>";
                            echo "<p><strong>{$comment['username']}</strong> {$comment['comment']}</p>";
                            echo "</div>";
                        }

                        echo "</div>";

                        echo "<div class='comment-input-cont'><input type='text' class='comment-input' id='comment-input-{$row['id']}' placeholder='Add a comment...'>
                        <button class='comment-btn' data-post-id='{$row['id']}'>Post</button></div>";

                        echo "</div>";
                        echo "</div>";
                    }
                ?>
            </div>

            <div id="saved" class="content" style="display: none;">
                <!-- Saved Posts will be loaded here -->
                <?php
                        $newSql = "SELECT posts.*, users.username, users.profile_pic, 
                            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id) AS like_count,
                            (SELECT COUNT(*) FROM likes WHERE likes.post_id = posts.id AND likes.user_id = {$_SESSION['user_id']}) AS user_liked,
                            (SELECT COUNT(*) FROM saved_posts WHERE saved_posts.post_id = posts.id AND saved_posts.user_id = {$_SESSION['user_id']}) AS user_saved
                            FROM posts
                            JOIN users ON posts.user_id = users.id 
                            JOIN saved_posts ON posts.id = saved_posts.post_id
                            WHERE saved_posts.user_id = {$_SESSION['user_id']}
                            ORDER BY posts.created_at DESC";

                    $result = $conn->query($newSql);

                    if(!$result->num_rows > 0){
                        echo "<p id='ntd'>---Nothing to display---</p>";
                    }

                    while ($row = $result->fetch_assoc()) {
                        preg_match('/(?:youtube\.com\/(?:[^\/\n\s]+\/\S+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', 
                        $row['youtube_link'], $matches);
                        $video_id = $matches[1] ?? '';

                        // Determine the like and save button state
                        $liked_class = ($row['user_liked'] > 0) ? "liked" : "";
                        $saved_class = ($row['user_saved'] > 0) ? "saved" : "";


                        echo "<div class='post-and-comment'>";
                        echo "<div class='post' id=post-{$row['id']}>";

                        echo "<div class='dp-and-uname'>
                                <img src='../uploads/profiles/{$row['profile_pic']}'>
                                <h3>{$row['username']}</h3>
                            </div>";

                        echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/$video_id' frameborder='0' allowfullscreen></iframe>";

                        echo "<div class='actions'>
                                <i class='fi fi-rr-heart like-btn $liked_class' data-post-id='{$row['id']}'></i>  
                                <i class='fi fi-rr-comment' id='comment' onclick='showComments({$row['id']})'></i>
                                <i class='fi fi-rr-bookmark save-btn $saved_class' data-post-id='{$row['id']}'></i>
                            </div>";

                        echo "<p id='likes-{$row['id']}'>{$row['like_count']} likes</p>";

                        if ($row["caption"]) {
                            echo "<p id='caption'>{$row['caption']}</p>";
                        }

                        // Fetch and display tags
                        $post_id = $row['id'];
                        $tag_query = "SELECT name FROM tags INNER JOIN post_tags ON tags.id = post_tags.tag_id WHERE post_tags.post_id = $post_id";
                        $tag_result = $conn->query($tag_query);

                        if ($tag_result->num_rows > 0) {
                            echo "<ul class='tags'>";
                            while ($tag = $tag_result->fetch_assoc()) {
                                echo "<li>{$tag['name']}</li>";
                            }
                            echo "</ul>";
                        }

                        echo "</div>";

                        // comment section
                        echo "<div class='comments-section' id='comments-section-{$row['id']}'>
                        <div class='existing-comments' id='comments-{$row['id']}'>";
                        
                        // Fetch and display comments
                        $comment_query = "SELECT comments.*, users.username, users.profile_pic FROM comments JOIN users ON comments.user_id = users.id WHERE comments.post_id = {$row['id']} ORDER BY comments.created_at ASC";
                        $comment_result = $conn->query($comment_query);
                        
                        while ($comment = $comment_result->fetch_assoc()) {
                            echo "<div class='comment-item'>";
                            echo "<img src='../uploads/profiles/{$comment['profile_pic']}'>";
                            echo "<p><strong>{$comment['username']}</strong> {$comment['comment']}</p>";
                            echo "</div>";
                        }

                        echo "</div>";

                        echo "<div class='comment-input-cont'><input type='text' class='comment-input' id='comment-input-{$row['id']}' placeholder='Add a comment...'>
                        <button class='comment-btn' data-post-id='{$row['id']}'>Post</button></div>";

                        echo "</div>";
                        echo "</div>";
                    }
                ?>
            </div>
        </div>
    </div>

    <script src="../actions/profile.js"></script>
    <script>
        function showComments(postId){
            let commentSection = document.getElementById(`comments-section-${postId}`);
            let post = document.getElementById(`post-${postId}`);
            let postHeight = post.offsetHeight;

            // Toggle visibility
            if (commentSection.style.display === "none" || commentSection.style.display === "") {
                commentSection.style.display = "flex";
                commentSection.style.height = postHeight+"px";
            } else {
                commentSection.style.display = "none";
            }
        }
    </script>
    <script src="../actions/like.js"></script>
    <script src="../actions/comment.js"></script>
    <script src="../actions/save.js"></script>
</body>
</html>