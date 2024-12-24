<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    exit("You are not logged in");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize inputs
    $sender = mysqli_real_escape_string($conn, $_POST['sender']);
    $receiver = mysqli_real_escape_string($conn, $_POST['receiver']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    // Ensure database supports UTF-8 emojis
    $conn->query("SET NAMES utf8mb4");

    // Handle file upload
    $fileUrl = '';
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $fileInfo = finfo_open(FILEINFO_MIME_TYPE);
        $fileType = finfo_file($fileInfo, $_FILES['file']['tmp_name']);
        finfo_close($fileInfo);

        // Whitelist allowed file types
        $allowedTypes = array('image/jpeg', 'image/png', 'application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        if (in_array($fileType, $allowedTypes)) {
            $fileName = uniqid() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', basename($_FILES['file']['name']));
            $targetFilePath = $uploadDir . $fileName;
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetFilePath)) {
                $fileUrl = $targetFilePath;
            } else {
                error_log("Error uploading file: " . $_FILES['file']['error']);
                echo "Error uploading file. Please try again.";
                return;
            }
        } else {
            echo "Invalid file type. Please upload a valid file.";
            return;
        }
    }

    // Update message to include file information if a file was uploaded
    if ($fileUrl) {
        if (strpos($fileType, 'image/') === 0) {
            $message .= " [Image: <img src='{$fileUrl}' style='max-width:200px;'>]";
        } else {
            $message .= " [File: <a href='{$fileUrl}' target='_blank'>" . basename($fileUrl) . "</a>]";
        }
    }

    // Insert message into database - automatically uses current timestamp
    $sql = "INSERT INTO chat_messages (sender, receiver, message, file_path) VALUES ('$sender', '$receiver', '$message', '$fileUrl')";
    if ($conn->query($sql)) {
        echo "Message sent successfully.";
    } else {
        error_log("Error inserting message: " . $conn->error);
        echo "Error sending message. Please try again.";
    }

    $conn->close();
}
?>