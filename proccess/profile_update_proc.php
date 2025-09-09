<?php
include '../includes/db.php';
include '../includes/auth.php';

if (!is_logged_in()) {
    echo "Please log in first";
    exit;
}

if ($_POST) {
    $user_id = $_SESSION['user_id'];
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($name) || empty($email) || empty($phone)) {
        echo "Please fill in all required fields.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit;
    }
    
    $password_update = false;
    if (!empty($new_password)) {
        $password_update = true;
        
        if (empty($current_password)) {
            echo "Please enter your current password to change it.";
            exit;
        }
        
        if ($new_password !== $confirm_password) {
            echo "New passwords do not match.";
            exit;
        }
        
        if (strlen($new_password) < 6) {
            echo "New password must be at least 6 characters long.";
            exit;
        }
        
        $check_sql = "SELECT password FROM users WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result && $check_result->num_rows > 0) {
            $user_data = $check_result->fetch_assoc();
            if ($user_data['password'] !== $current_password) {
                echo "Current password is incorrect.";
                exit;
            }
        } else {
            echo "User not found.";
            exit;
        }
    }
    
    $check_email_sql = "SELECT id FROM users WHERE email = ? AND id != ?";
    $check_email_stmt = $conn->prepare($check_email_sql);
    $check_email_stmt->bind_param("si", $email, $user_id);
    $check_email_stmt->execute();
    $email_result = $check_email_stmt->get_result();
    
    if ($email_result && $email_result->num_rows > 0) {
        echo "Email already exists for another user.";
        exit;
    }
    
    if ($password_update) {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $name, $email, $phone, $new_password, $user_id);
    } else {
        $sql = "UPDATE users SET name = ?, email = ?, phone = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $name, $email, $phone, $user_id);
    }
    
    if ($stmt->execute()) {
        $_SESSION['name'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['phone'] = $phone;
        
        echo "ok";
    } else {
        echo "Failed to update profile. Please try again.";
    }
    
} else {
    echo "No data received";
}

?>
