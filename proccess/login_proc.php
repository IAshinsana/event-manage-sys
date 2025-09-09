<?php
include '../includes/db.php';
session_start();

if ($_POST) {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    
    if (empty($username) || empty($password)) {
        echo 'Please enter both username and password';
        exit;
    }
    
    $sql = "SELECT * FROM users WHERE username = ? AND active = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 0) {
        echo 'Invalid username or password';
        exit;
    }
    
    $user = $result->fetch_assoc();
    
    if ($user['password'] !== $password) {
        echo 'Invalid username or password';
        exit;
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name'] = $user['name'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['phone'] = $user['phone'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['coordinator_status'] = $user['coordinator_status'];
    
    if ($user['role'] === 'admin') {
        echo "admin";
    } elseif ($user['role'] === 'coordinator') {
        echo "coordinator";
    } else {
        echo "user";
    }
    
} else {
    echo 'No data received';
}

?>
