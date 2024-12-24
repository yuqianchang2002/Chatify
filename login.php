<?php 
session_start();
include('db.php');

// Generate a unique session ID for each login attempt
$session_id = session_id();

if (isset($_SESSION['username'])) {
    // Optional: Instead of redirecting, you could allow multiple logins
    // Remove the redirect if you want to allow multiple simultaneous logins
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Use prepared statements to prevent SQL injection
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        // Create a unique session tracking
        $_SESSION['username'] = $username;
        $_SESSION['login_time'] = time();
        $_SESSION['session_id'] = $session_id;

        // Optional: Store login session in database
        $insert_session = $conn->prepare("INSERT INTO user_sessions (username, session_id, login_time) VALUES (?, ?, NOW())");
        $insert_session->bind_param("ss", $username, $session_id);
        $insert_session->execute();

        header("Location: chat.php");
        exit();
    } else {
        $error = "Invalid username or password";
    }
}
$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="style_main.css" rel="stylesheet">
</head>
<body>
    <div class="container">
	<h1>Chatify</h1>
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required><br>
            <button type="submit">Login</button>
        </form>
        <p>New account? <a href="register.php">Register here</a>.</p>
    </div>
</body>
</html>