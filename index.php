<?php
include "includes/config.php";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        die("Passwords do not match!");
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>
                alert('Registration successful! Please log in.');
                window.location.href = 'auth/login.php';
            </script>";
    } else {
        echo "<script>
                alert('Error: " . $conn->error . "');
            </script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css?v=<?php echo time();?>">
    <link rel="stylesheet" href="assets/css/register.css?v=<?php echo time();?>">
    <title>SangeetHub</title>
</head>
<body>
    <div class="register">
        <div class="left">
            <h1>SangeetHub</h1>
            <p>A platform for music lovers to discover and share their favorite music with others.</p>
        </div>
        <div class="right">
            <form action="index.php" method="POST">
                <input type="text" name="username" placeholder="Username" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <button type="submit" name="register">Register</button>
            </form>
            <hr>
            <p>Have an account? <a href="auth/login.php">Login</a></p>
        </div>
    </div>
</body>
</html>