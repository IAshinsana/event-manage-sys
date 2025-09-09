<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $application_id = (int)$_POST['application_id'];
    $action = $_POST['action'] ?? '';
    $admin_notes = trim($_POST['admin_notes'] ?? '');
    
    if (!$application_id || !in_array($action, ['approve', 'reject', 'delete'])) {
        echo "Invalid parameters";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        if ($action === 'delete') {
            $sql = "DELETE FROM coordinator_applications WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $application_id);
            
            if ($stmt->execute()) {
                echo "ok";
            } else {
                throw new Exception("Failed to delete application");
            }
            
        } else {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $admin_id = $_SESSION['user_id'];
            $current_time = date('Y-m-d H:i:s');
            
            $app_sql = "UPDATE coordinator_applications 
                       SET status = ?, admin_notes = ?, reviewed_at = ?, reviewed_by = ?
                       WHERE id = ?";
            $app_stmt = $conn->prepare($app_sql);
            $app_stmt->bind_param("sssii", $status, $admin_notes, $current_time, $admin_id, $application_id);
            
            if (!$app_stmt->execute()) {
                throw new Exception("Failed to update application");
            }
            
            $user_sql = "SELECT user_id FROM coordinator_applications WHERE id = ?";
            $user_stmt = $conn->prepare($user_sql);
            $user_stmt->bind_param("i", $application_id);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            
            if ($user_result && $user_result->num_rows > 0) {
                $user_data = $user_result->fetch_assoc();
                $user_id = $user_data['user_id'];
                
                $coordinator_status = ($action === 'approve') ? 'approved' : 'rejected';
                $update_user_sql = "UPDATE users SET coordinator_status = ? WHERE id = ?";
                $update_user_stmt = $conn->prepare($update_user_sql);
                $update_user_stmt->bind_param("si", $coordinator_status, $user_id);
                
                if (!$update_user_stmt->execute()) {
                    throw new Exception("Failed to update user status");
                }
            }
            
            echo "ok";
        }
        
        $conn->commit();
        
    } catch (Exception $e) {
        $conn->rollback();
        echo "Error: " . $e->getMessage();
    }
    
} else {
    echo "No data received";
}

?>

