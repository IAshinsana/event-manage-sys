<?php
include '../includes/db.php';


if ($_POST) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($name && $username && $password && $confirm_password) {
        if ($password !== $confirm_password) {
            echo 'Passwords do not match';
        } else {
            // Check if username exists
            $check_sql = "SELECT id FROM users WHERE username = '$username'";
            $check_result = $conn->query($check_sql);
            
            if ($check_result && $check_result->num_rows > 0) {
                echo 'Username already exists';
            } else {
                // Insert new user
                $sql = "INSERT INTO users (name, username, password, role) VALUES ('$name', '$username', '$password', 'ordinary')";
                if ($conn->query($sql)) {
                    echo 'ok';
                } else {
                    echo 'no';
                }
            }
        }
    } else {
        echo 'Please fill in all fields';
    }
}

?>