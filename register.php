<?php 

session_start();
include('db.php');

if (isset($_SESSION['username'])) {
    header("Location: chat.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role']; // New role field

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $error = "Username already exists";
    } else {
        if (!isStrongPassword($password)) {
            $error = "Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.";
        } else {
            // Insert new user into the database
            $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
            $stmt->bind_param("ss", $username, $password);
            if ($stmt->execute()) {
                $_SESSION['username'] = $username;
                header("Location: chat.php");
                exit();
            } else {
                $error = "Registration failed";
            }
        }
    }

    $stmt->close();
}

function isStrongPassword($password) {
    // At least 8 characters, including one uppercase letter, one lowercase letter, one number, and one special character
    return preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password);
}

$conn->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link href="style_main.css" rel="stylesheet">
</head>
<body>

    <div class="container">
        <h1>Chatify</h1>
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <form method="post">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required><br>
			            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="">Select Role</option>
                <option value="student">Student</option>
                <option value="lecturer">Lecturer</option>
                <option value="staff">Staff</option>
            </select><br><br>
            <div style="position: relative;">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required style="padding-right: 40px;">
                <button type="button" id="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); border: none; background: none; cursor: pointer;">
                    <br><img id="toggle-icon" src="view.png" alt="Show Password" style="width: 20px; height: 20px;">
                </button>
            </div>
            <p style="color:grey; font-size:80%;">Password must be at least 8 characters long and include uppercase, lowercase, numbers, and special characters.</p><br>
            <button type="button" onclick="suggestPassword()">Suggest Strong Password</button>
            <div>
                <label for="password_strength"><br>Password Strength:</label>
                <span id="password_strength"></span>
            </div>  

            <br><button type="submit">Register</button>
        </form>
        <p>Already have an account? <a href="login.php">Login here</a>.</p>
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
            for (let i = 4; i < 12; i++) {
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

            const strongPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;

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
                toggleIcon.src = 'hide.png'; // Replace with the "eye-slash" icon image
                toggleIcon.alt = 'Hide';
            } else {
                passwordField.type = 'password';
                toggleIcon.src = 'view.png'; // Replace with the "eye" icon image
                toggleIcon.alt = 'Show';
            }
        });
    </script>

</body>
</html>
