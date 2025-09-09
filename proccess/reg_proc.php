<?php
include '../includes/db.php';

if ($_POST) {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($username) || empty($password) || empty($confirm_password)) {
        echo 'Please fill in all fields';
        exit;
    }
    
    if ($password !== $confirm_password) {
        echo 'Passwords do not match';
        exit;
    }
    
    $check_sql = "SELECT id FROM users WHERE username = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $username);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        echo 'Username already exists';
        exit;
    }
    
    $insert_sql = "INSERT INTO users (name, username, password, role) VALUES (?, ?, ?, 'ordinary')";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sss", $name, $username, $password);
    
    if ($insert_stmt->execute()) {
        echo 'ok';
    } else {
        echo 'Registration failed. Please try again.';
    }
    
} else {
    echo 'No data received';
}

?>
