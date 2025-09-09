<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $action = $_POST['action'] ?? '';
    
    if ($action === 'update_role') {
        $user_id = (int)$_POST['user_id'];
        $role = $_POST['role'] ?? '';
        
        if (!$user_id || !in_array($role, ['ordinary', 'admin', 'checker', 'coordinator'])) {
            echo "Invalid parameters";
            exit;
        }
        
        $sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $role, $user_id);
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "Failed to update user role. Please try again.";
        }
        
    } elseif ($action === 'toggle_status') {
        $user_id = (int)$_POST['user_id'];
        
        if (!$user_id) {
            echo "Invalid user ID";
            exit;
        }
        
        $get_status_sql = "SELECT active FROM users WHERE id = ?";
        $stmt = $conn->prepare($get_status_sql);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $current_status = $user['active'];
            $new_status = $current_status ? 0 : 1;
            
            $update_sql = "UPDATE users SET active = ? WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("ii", $new_status, $user_id);
            
            if ($stmt->execute()) {
                echo "ok";
            } else {
                echo "Failed to update user status. Please try again.";
            }
            
        } else {
            echo "User not found";
        }
        
    } else {
        echo "Invalid action";
    }
    
} else {
    echo "No data received";
}

?>