<?php
session_start();
include('db.php');

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username = $_SESSION['username'];
$selectedUser = '';

if (isset($_GET['user'])) {
    $selectedUser = $_GET['user'];
    $selectedUser = mysqli_real_escape_string($conn, $selectedUser);
    $showChatBox = true;
} else {
    $showChatBox = false;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chatify</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="container">
    <div class="header">
        <h1>Chatify</h1>
        <div class="header-buttons">
            <a href="edit_profile.php" class="logout">Edit Profile</a>
            <a href="logout.php" class="logout">Logout</a>
        </div>
    </div>
    <div class="account-info">
        <div class="welcome">
            <h2>Welcome, <?php echo ucfirst($username); ?>!</h2>
        </div>
        <div class="user-list">
            <h2>Select a User to Chat With:</h2>
		<div class="search-bar">
            <input type="text" id="user-search" placeholder="Search users to chat with..." autocomplete="off">
            <div class="search-results" id="search-results"></div>
        </div>	
            <ul>
                <?php 
                // Fetch all users except the current user
                $sql = "SELECT username, role FROM users WHERE username != '$username'";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $user = $row['username'];
                        $role = $row['role'];
                        $user = ucfirst($user);
                        $roleText = "<span style='color:grey; font-size:80%;'>(".$role.")</span>";
                        echo "<li><a href='chat.php?user=$user'>$user $roleText</a></li>";
                    }
                }
                ?>
            </ul>
        </div>
    </div>

<?php if ($showChatBox): ?>
<div class="chat-box" id="chat-box">
    <div class="chat-box-header">
        <h2><?php echo ucfirst($selectedUser); ?></h2>
        <button class="close-btn" onclick="closeChat()">✖</button>
    </div>
    <div class="chat-box-body" id="chat-box-body">
	            <!-- Chat messages will be loaded here -->
        </div>
		
       <form class="chat-form" id="chat-form" enctype="multipart/form-data">
        <input type="hidden" id="sender" value="<?php echo $username; ?>">
        <input type="hidden" id="receiver" value="<?php echo $selectedUser; ?>">
        <input type="text" id="message" placeholder="Type your message...">
        <input type="file" id="file" name="file" accept="image/*,.pdf,.docx,.zip">        
		
		<button type="button" onclick="applyFormatting('bold')" title="Bold">B</button>
   		<button type="button" onclick="applyFormatting('italic')" title="Italic">I</button>
    	<button type="button" onclick="applyFormatting('underline')" title="Underline">U</button>
		<button type="button" onclick="applyFormatting('strikethrough')" title="Strikethrough">S</button>
    	<button type="button" id="emoji-toggle">😀</button>
        <div id="emoji-picker" style="display:none;">
		   		<span onclick="addEmoji('😀')">😀</span>
    			<span onclick="addEmoji('😍')">😍</span>
				<span onclick="addEmoji('😎')">😎</span>
    			<span onclick="addEmoji('🤔')">🤔</span>
   				<span onclick="addEmoji('😂')">😂</span>
				<span onclick="addEmoji('🤩')">🤩</span>
				<span onclick="addEmoji('🤯')">🤯</span>
				<span onclick="addEmoji('😢')">😢</span>
				<span onclick="addEmoji('😩')">😩</span>
				<span onclick="addEmoji('😏')">😏</span>
				<span onclick="addEmoji('☹️')">☹️</span>
    			<span onclick="addEmoji('🥳')">🥳</span>
				<span onclick="addEmoji('😃')">😃</span>
				<span onclick="addEmoji('😆')">😆</span>
				<span onclick="addEmoji('😅')">😅</span>
				<span onclick="addEmoji('😇')">😇</span>
				<span onclick="addEmoji('🥲')">🥲</span>
				<span onclick="addEmoji('🤤')">🤤</span>
				<span onclick="addEmoji('🤢')">🤢</span>
				<span onclick="addEmoji('🤮')">🤮</span>
				<span onclick="addEmoji('🤧')">🤧</span>
				<span onclick="addEmoji('🤕')">🤕</span>
				<span onclick="addEmoji('🐶')">🐶</span>
				<span onclick="addEmoji('🐱')">🐱</span>
				<span onclick="addEmoji('🐭')">🐭</span>
				<span onclick="addEmoji('🐹')">🐹</span>
				<span onclick="addEmoji('🐰')">🐰</span>
				<span onclick="addEmoji('🦊')">🦊</span>
				<span onclick="addEmoji('🐻')">🐻</span>
				<span onclick="addEmoji('🐼')">🐼</span>
				<span onclick="addEmoji('🐨')">🐨</span>
				<span onclick="addEmoji('🐯')">🐯</span>
				<span onclick="addEmoji('🍎')">🍎</span>
				<span onclick="addEmoji('🍌')">🍌</span>
				<span onclick="addEmoji('🍒')">🍒</span>
				<span onclick="addEmoji('🍇')">🍇</span>
				<span onclick="addEmoji('🍓')">🍓</span>
				<span onclick="addEmoji('🍍')">🍍</span>
				<span onclick="addEmoji('🥭')">🥭</span>
				<span onclick="addEmoji('🍔')">🍔</span>
				<span onclick="addEmoji('🍕')">🍕</span>
				<span onclick="addEmoji('🍩')">🍩</span>
				<span onclick="addEmoji('✈️')">✈️</span>
				<span onclick="addEmoji('🚗')">🚗</span>
				<span onclick="addEmoji('🚀')">🚀</span>
				<span onclick="addEmoji('🗼')">🗼</span>
				<span onclick="addEmoji('🗽')">🗽</span>
				<span onclick="addEmoji('🌍')">🌍</span>
				<span onclick="addEmoji('🌋')">🌋</span>
				<span onclick="addEmoji('🏕️')">🏕️</span>
				<span onclick="addEmoji('🏖️')">🏖️</span>
				<span onclick="addEmoji('🏰')">🏰</span>				
				<span onclick="addEmoji('👍')">👍</span>
				<span onclick="addEmoji('👎')">👎</span>
    			<span onclick="addEmoji('🙌')">🙌</span>
    			<span onclick="addEmoji('👏')">👏</span>
    			<span onclick="addEmoji('💪')">💪</span>
    			<span onclick="addEmoji('🙏')">🙏</span>
    			<span onclick="addEmoji('🔥')">🔥</span>
				<span onclick="addEmoji('🌸')">🌸</span>
				<span onclick="addEmoji('🌺')">🌺</span>
				<span onclick="addEmoji('🌳')">🌳</span>
				<span onclick="addEmoji('🌴')">🌴</span>
				<span onclick="addEmoji('🌊')">🌊</span>
				<span onclick="addEmoji('☀️')">☀️</span>
				<span onclick="addEmoji('🌙')">🌙</span>
				<span onclick="addEmoji('🌈')">🌈</span>
				<span onclick="addEmoji('⛄')">⛄</span>
				<span onclick="addEmoji('📱')">📱</span>
				<span onclick="addEmoji('💻')">💻</span>
				<span onclick="addEmoji('🎧')">🎧</span>
				<span onclick="addEmoji('🎮')">🎮</span>
				<span onclick="addEmoji('🎵')">🎵</span>
				<span onclick="addEmoji('📚')">📚</span>
				<span onclick="addEmoji('✏️')">✏️</span>
				<span onclick="addEmoji('📷')">📷</span>
				<span onclick="addEmoji('🔒')">🔒</span>
				<span onclick="addEmoji('💡')">💡</span>
   				 <span onclick="addEmoji('🎉')">🎉</span>
    			<span onclick="addEmoji('❤️')">❤️</span>
    			<span onclick="addEmoji('🌟')">🌟</span>
			</div>
		
        <button type="submit">Send</button>
    </form>
</div>
<?php endif; ?>



<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>

    function closeChat() {
        document.getElementById("chat-box").style.display = "none";
    }


    // Function to toggle chat box visibility
    function toggleChatBox() {
    var chatBox = document.getElementById("chat-box");
    if (chatBox.style.display === "none") {
        chatBox.style.display = "block"; // Show the chat box
    } else {
        chatBox.style.display = "none"; // Hide the chat box
    }
}


function fetchMessages() {
            var sender = $('#sender').val();
            var receiver = $('#receiver').val();
            
            $.ajax({
                url: 'fetch_messages.php',
                type: 'POST',
                data: {sender: sender, receiver: receiver},
                success: function(data) {
                    $('#chat-box-body').html(data);
                    scrollChatToBottom();
                }
            });
        }


        // Function to scroll the chat box to the bottom
        function scrollChatToBottom() {
            var chatBox = $('#chat-box-body');
            chatBox.scrollTop(chatBox.prop("scrollHeight"));
        }

 
        
        $(document).ready(function() {
            // Fetch messages every 3 seconds
            
            fetchMessages();
            setInterval(fetchMessages, 3000);
			$('#emoji-toggle').click(function() {
        	$('#emoji-picker').toggle();
    });
        });

			
            // Submit the chat message
$('#chat-form').submit(function(e) {
    e.preventDefault();
    console.log('Form submitted');

    var sender = $('#sender').val();
    var receiver = $('#receiver').val();
    var message = $('#message').val();
    var file = $('#file')[0].files[0];

    console.log('Sender:', sender);
    console.log('Receiver:', receiver);
    console.log('Message:', message);
    console.log('File:', file);

    var formData = new FormData();
    formData.append('sender', sender);
    formData.append('receiver', receiver);
    formData.append('message', message);
    formData.append('file', file);

    $.ajax({
        url: 'submit_message.php',
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response) {
            $('#message').val('');
            $('#file').val('');
            fetchMessages(); // Fetch messages after submitting
        },
        error: function(xhr, status, error) {
            console.error('Error submitting message:', error);
        }
    });
});

let lastMessageCount = 0;

function checkNewMessages() {
    var sender = $('#sender').val();
    var receiver = $('#receiver').val();
    
    $.ajax({
        url: 'check_new_messages.php',
        type: 'POST',
        data: {sender: sender, receiver: receiver},
        success: function(response) {
            var newMessageCount = parseInt(response);
            
            // Check if there are new messages
            if (newMessageCount > lastMessageCount) {
                // Create a browser notification
                if (Notification.permission === "granted") {
                    var notification = new Notification('New Message', {
                        body: `You have ${newMessageCount - lastMessageCount} new message(s) from ${receiver}`,
                        icon: 'path/to/your/icon.png' // Optional: Add an application icon
                    });
                    
                    // Optional: Play a sound
                    var audio = new Audio('notification-sound.mp3');
                    audio.play();
                }
                
                // Update last message count
                lastMessageCount = newMessageCount;
            }
        }
    });
}

$(document).ready(function() {
    // Request notification permission
    if (Notification.permission !== "granted") {
        Notification.requestPermission();
    }

    // Add notification check to existing interval
    setInterval(checkNewMessages, 3000);
});
		//Add Emoji Function
			function addEmoji(emoji) {
    			var currentMessage = $('#message').val();
    			$('#message').val(currentMessage + emoji);
    			$('#emoji-picker').hide();
    			$('#message').focus();
			}
			//show timestamp function
function formatRelativeTime(timestamp) {
    const now = new Date();
    const messageDate = new Date(timestamp);
    const diffInSeconds = Math.floor((now - messageDate) / 1000);
    
    if (diffInSeconds < 60) return 'just now';
    if (diffInSeconds < 3600) return Math.floor(diffInSeconds / 60) + ' min ago';
    if (diffInSeconds < 86400) return Math.floor(diffInSeconds / 3600) + ' hr ago';
    return messageDate.toLocaleDateString();
}

// Text Formatting Functions
function applyFormatting(formatType) {
    var messageInput = $('#message');
    var currentValue = messageInput.val();
    
    let formattedText;
    switch(formatType) {
        case 'bold':
            formattedText = `**${currentValue}**`;
            break;
        case 'italic':
            formattedText = `*${currentValue}*`;
            break;
        case 'underline':
            formattedText = `<u>${currentValue}</u>`;
            break;
        case 'strikethrough':
            formattedText = `~~${currentValue}~~`;
            break;
    }
    
    // Replace the entire message with formatted text
    messageInput.val(formattedText);
    messageInput.focus();
}

//search users function
$(document).ready(function() {
        $('#user-search').on('input', function() {
            let query = $(this).val();
            if (query.length > 2) {
                $.ajax({
                    url: 'search_users.php',
                    method: 'GET',
                    data: { search: query },
                    success: function(data) {
                        $('#search-results').html(data);
                    }
                });
            } else {
                $('#search-results').empty();
            }
        });

        $(document).on('click', '.search-result-item', function() {
            const username = $(this).data('username');
            window.location.href = `chat.php?user=${username}`;
        });
    });
	
	$(document).ready(function() {
        $('#message-form').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);

            $.ajax({
                url: 'submit_message.php',
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    $('#messages').append(response);
                    $('#message-input').val('');
                    $('#file-input').val('');
                }
            });
        });
    });
</script>
    
</body>
</html>