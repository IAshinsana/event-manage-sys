<?php
include '../includes/db.php';
include '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['fullName'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['cPassword'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $organization_name = trim($_POST['organization_name'] ?? '');
    $organization_type = trim($_POST['organization_type'] ?? '');
    $experience_years = (int)($_POST['experience_years'] ?? 0);
    $previous_events = trim($_POST['previous_events'] ?? '');
    $motivation = trim($_POST['motivation'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $social_media = trim($_POST['social_media'] ?? '');

    if (empty($name) || empty($username) || empty($password) || empty($email) || 
        empty($phone) || empty($organization_name) || empty($organization_type) || empty($motivation)) {
        echo "Please fill in all required fields.";
        exit;
    }
    
    if ($password !== $confirm_password) {
        echo "Passwords do not match.";
        exit;
    }
    
    if (strlen($password) < 6) {
        echo "Password must be at least 6 characters long.";
        exit;
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
        exit;
    }
    
    $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $username, $email);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        echo "Username or email already exists.";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        $user_sql = "INSERT INTO users (name, username, password, role, email, phone, organization, coordinator_status, coordinator_applied_at) 
                    VALUES (?, ?, ?, 'coordinator', ?, ?, ?, 'pending', NOW())";
        $user_stmt = $conn->prepare($user_sql);
        $user_stmt->bind_param("ssssss", $name, $username, $password, $email, $phone, $organization_name);
        $user_stmt->execute();
        
        $user_id = $conn->insert_id;
        
        $app_sql = "INSERT INTO coordinator_applications 
                   (user_id, organization_name, organization_type, experience_years, previous_events, 
                    motivation, contact_email, contact_phone, website, social_media) 
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $app_stmt = $conn->prepare($app_sql);
        $app_stmt->bind_param(
            "ississssss",
            $user_id, $organization_name, $organization_type, $experience_years, 
            $previous_events, $motivation, $email, $phone, $website, $social_media
        );
        $app_stmt->execute();
        
        $conn->commit();
        echo "ok";
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error submitting application. Please try again.";
    }
    
} else {
    echo "Invalid request method";
}

?>
