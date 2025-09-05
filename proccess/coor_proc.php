<?php
include '../includes/db.php';
include '../includes/auth.php';


echo '';
$success_message = '';

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
    
    // Validation
    if (empty($name) || empty($username) || empty($password) || empty($email) || 
        empty($phone) || empty($organization_name) || empty($organization_type) || empty($motivation)) {
        echo "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        echo "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        echo "Password must be at least 6 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Please enter a valid email address.";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            echo "Username or email already exists.";
        } else {
            // Create user account (store password in raw format)
            
            $conn->begin_transaction();
            try {
                // Insert user
                $user_sql = "INSERT INTO users (name, username, password, role, email, phone, organization, coordinator_status, coordinator_applied_at) 
                            VALUES (?, ?, ?, 'coordinator', ?, ?, ?, 'pending', NOW())";
                $stmt = $conn->prepare($user_sql);
                $stmt->bind_param("ssssss", $name, $username, $password, $email, $phone, $organization_name);
                $stmt->execute();
                
                $user_id = $conn->insert_id;
                
                // Insert coordinator application
                $app_sql = "INSERT INTO coordinator_applications 
                           (user_id, organization_name, organization_type, experience_years, previous_events, 
                            motivation, contact_email, contact_phone, website, social_media) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($app_sql);
                $stmt->bind_param("ississssss", $user_id, $organization_name, $organization_type, 
                                $experience_years, $previous_events, $motivation, $email, $phone, 
                                $website, $social_media);
                $stmt->execute();
                
                $conn->commit();
                echo "ok";
                
            } catch (Exception $e) {
                $conn->rollback();
                echo "Error submitting application. Please try again.";
            }
        }
    }
}
?>
