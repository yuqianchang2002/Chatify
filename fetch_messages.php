<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    exit("You are not logged in");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];

    $sql = "SELECT * FROM chat_messages WHERE (sender='$sender' AND receiver='$receiver') OR (sender='$receiver' AND receiver='$sender') ORDER BY created_at";
    $result = $conn->query($sql);



    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Escape the message to prevent XSS attacks
            $message = htmlspecialchars($row['message'], ENT_QUOTES, 'UTF-8');
            
            // Convert URLs into clickable links
            $message = preg_replace(
                '/(https?:\/\/[^\s]+)/',
                '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>',
                $message
            );

            // Format timestamp
            $timestamp = strtotime($row['created_at']);
            $formattedTime = date('h:i A', $timestamp);
            $formattedDate = date('F j, Y', $timestamp);

            // Determine if the message is sent or received
            $messageClass = ($row['sender'] == $sender) ? 'message-sent' : 'message-received';

			$message = renderFormatting($message);

            echo '<div class="message ' . $messageClass . '">';
            echo $message; // Display the message with clickable links
            echo '<div class="message-timestamp" title="' . $formattedDate . '">' . $formattedTime . '</div>';
            echo '</div>';
        }
    }
}
// Add formatting rendering functions
function renderFormatting($message) {
    // Bold: **text**
    $message = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $message);
    
    // Italic: *text*
    $message = preg_replace('/\*(.*?)\*/s', '<em>$1</em>', $message);
    
    // Underline: <u>text</u>
    $message = preg_replace('/&lt;u&gt;(.*?)&lt;\/u&gt;/s', '<u>$1</u>', $message);
    
    // Strikethrough: ~~text~~
    $message = preg_replace('/~~(.*?)~~/s', '<del>$1</del>', $message);    
    return $message;
}
?>