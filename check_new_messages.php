<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    exit("You are not logged in");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $sender = $_POST['sender'];
    $receiver = $_POST['receiver'];

    // Count total messages for this conversation
    $sql = "SELECT COUNT(*) as message_count 
            FROM chat_messages 
            WHERE (sender='$sender' AND receiver='$receiver') 
               OR (sender='$receiver' AND receiver='$sender')";
    
    $result = $conn->query($sql);
    
    if ($result) {
        $row = $result->fetch_assoc();
        echo $row['message_count'];
    } else {
        echo 0;
    }
}
?>