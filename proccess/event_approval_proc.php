<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $event_id = (int)$_POST['event_id'];
    $action = $_POST['action'] ?? '';
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');
    
    if (!$event_id || !in_array($action, ['approve', 'reject'])) {
        echo "Invalid parameters";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        if ($action === 'approve') {
            $approval_status = 'approved';
            $event_status = 'published';
        } else {
            $approval_status = 'rejected';
            $event_status = 'rejected';
        }
        
        $admin_id = $_SESSION['user_id'];
        $current_time = date('Y-m-d H:i:s');
        
        $sql = "UPDATE events 
                SET approval_status = ?, 
                    status = ?, 
                    approved_by = ?,
                    approved_at = ?,
                    rejection_reason = ?
                WHERE id = ?";
                
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssissi",
            $approval_status, $event_status, $admin_id, 
            $current_time, $rejection_reason, $event_id
        );
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            throw new Exception("Failed to update event status");
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