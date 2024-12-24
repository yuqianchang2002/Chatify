<?php
include('db.php');

if (isset($_GET['search'])) {
    $search = mysqli_real_escape_string($conn, $_GET['search']);
    $query = "SELECT username FROM users WHERE username LIKE '%$search%' LIMIT 10";
    $result = mysqli_query($conn, $query);

    while ($row = mysqli_fetch_assoc($result)) {
        echo '<div class="search-result-item" data-username="' . htmlspecialchars($row['username']) . '">' . htmlspecialchars($row['username']) . '</div>';
    }
}
?>