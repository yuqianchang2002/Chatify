<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$error = '';
$success = '';

// Fetch current user details
$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $newUsername = mysqli_real_escape_string($conn, $_POST['username']);
    $newPassword = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);

    // Validate input
    if (empty($newUsername) || empty($newPassword)) {
        $error = "Username and password cannot be empty";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match";
    } elseif (!isStrongPassword($newPassword)) {
        $error = "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.";
    } else {
        // Update user profile - Note: Role is NOT updated
        $updateSql = "UPDATE users SET username='$newUsername', password='$newPassword' WHERE username='$username'";

        if ($conn->query($updateSql)) {
            // Update session with new username
            $_SESSION['username'] = $newUsername;
            $success = "Profile updated successfully";
            $username = $newUsername; // Update local username variable
            
            // Refresh user details
            $sql = "SELECT * FROM users WHERE username='$username'";
            $result = $conn->query($sql);
            $user = $result->fetch_assoc();
        } else {
            $error = "Error updating profile: " . $conn->error;
        }
    }
}

function isStrongPassword($password) {
    // At least 8 characters, including one uppercase letter, one lowercase letter, one number, and one special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - Chatify</title>
    <link href="style_main.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h1>Chatify</h1>
        <h2>Edit Profile</h2>
        
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>
        
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($username); ?>" required>
            
            <label for="role">Role:</label>
            <input type="text" id="role" name="role" value="<?php echo htmlspecialchars(ucfirst($user['role'])); ?>" readonly>
            
            <div style="position: relative;">
    		<label for="password">Password:</label>
			    <input type="password" id="password" name="password" required style="padding-right: 40px;">
			    <button type="button" id="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
        		<br><img id="toggle-icon" src="view.png" alt="Show Password" style="width: 20px; height: 20px;">
    				</button>
			
			</div>

				<p style = "color:grey;font-size:80%">Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.</p>		
			<button type="button" onclick="suggestPassword()">Suggest Strong Password</button>
			<div>
    		<label for="password_strength"><br>Password Strength:</label>
    		<span id="password_strength"></span>
			</div>
            
            <div style="position: relative;">
    <label for="confirm-password"><br>Confirm New Password:</label>
    <input type="password" id="confirm-password" name="confirm-password" required style="padding-right: 40px;">
    <button type="button" id="toggle-confirm-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
        <br><br><img id="toggle-confirm-icon" src="view.png" alt="Show Password" style="width: 20px; height: 20px;">
    </button>
</div>
            
            <button type="submit">Update Profile</button>
        </form>
        
        <p><a href="chat.php">Back to Chat</a></p>
    </div>

	<script>
  function suggestPassword() {
            const uppercase = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
            const lowercase = "abcdefghijklmnopqrstuvwxyz";
            const numbers = "0123456789";
            const specialChars = "!@#$%^&*()_+";
            let password = "";

            // Ensure at least one character from each group
            password += uppercase.charAt(Math.floor(Math.random() * uppercase.length));
            password += lowercase.charAt(Math.floor(Math.random() * lowercase.length));
            password += numbers.charAt(Math.floor(Math.random() * numbers.length));
            password += specialChars.charAt(Math.floor(Math.random() * specialChars.length));

            // Fill the rest of the password with random characters from all groups
            const allChars = uppercase + lowercase + numbers + specialChars;
            for (let i = 4; i < 8; i++) {
                password += allChars.charAt(Math.floor(Math.random() * allChars.length));
            }

            // Shuffle the password to randomize
            password = password.split('').sort(() => 0.5 - Math.random()).join('');
            document.getElementById('password').value = password;
            checkPasswordStrength(password);
        }

        document.getElementById('password').addEventListener('input', function () {
            checkPasswordStrength(this.value);
        });

        function checkPasswordStrength(password) {
            const strengthIndicator = document.getElementById('password_strength');

            const strongPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{5,}$/;

            if (strongPattern.test(password)) {
                strengthIndicator.textContent = "Strong";
                strengthIndicator.style.color = "green";
            } else {
                strengthIndicator.textContent = "Weak";
                strengthIndicator.style.color = "red";
            }
        }
	document.getElementById('toggle-password').addEventListener('click', function () {
    const passwordField = document.getElementById('password');
    const toggleIcon = document.getElementById('toggle-icon');

    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.src = 'hide.png'; 
        toggleIcon.alt = 'Hide';
    } else {
        passwordField.type = 'password';
        toggleIcon.src = 'view.png'; 
        toggleIcon.alt = 'Show';
    }
});

    const toggleButton = document.getElementById("toggle-confirm-password");
    const passwordField = document.getElementById("confirm-password");
    const icon = document.getElementById("toggle-confirm-icon");

    toggleButton.addEventListener("click", () => {
        // Toggle password visibility
        if (passwordField.type === "password") {
            passwordField.type = "text";
            icon.src = "hide.png"; // Change to a "hide" icon when showing the password
            icon.alt = "Hide Password";
        } else {
            passwordField.type = "password";
            icon.src = "view.png"; // Change back to "view" icon when hiding the password
            icon.alt = "Show Password";
        }
    });
</script>
</body>
</html>