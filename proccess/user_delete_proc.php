<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $user_id = (int)$_POST['user_id'];
    $action = $_POST['action'] ?? '';
    $current_admin_id = $_SESSION['user_id'];
    
    if (!$user_id || !in_array($action, ['delete', 'activate', 'deactivate'])) {
        echo "Invalid parameters";
        exit;
    }
    
    if ($user_id == $current_admin_id && in_array($action, ['delete', 'deactivate'])) {
        echo "Cannot delete or deactivate your own account";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        if ($action === 'delete') {
            $orders_check_sql = "SELECT COUNT(*) as order_count FROM orders WHERE user_id = $user_id";
            $orders_result = $conn->query($orders_check_sql);
            $orders_data = $orders_result->fetch_assoc();
            
            if ($orders_data['order_count'] > 0) {
                echo "Cannot delete user with existing orders. Please cancel orders first.";
                exit;
            }
            
            $events_check_sql = "SELECT COUNT(*) as event_count FROM events WHERE created_by = $user_id";
            $events_result = $conn->query($events_check_sql);
            $events_data = $events_result->fetch_assoc();
            
            if ($events_data['event_count'] > 0) {
                echo "Cannot delete coordinator with existing events. Please reassign or delete events first.";
                exit;
            }
            
            $conn->query("DELETE FROM coordinator_applications WHERE user_id = $user_id");
            $conn->query("DELETE FROM users WHERE id = $user_id");
            
            echo "ok";
            
        } elseif ($action === 'activate' || $action === 'deactivate') {
            $active_status = ($action === 'activate') ? 1 : 0;
            $sql = "UPDATE users SET active = $active_status WHERE id = $user_id";
            
            if ($conn->query($sql)) {
                echo "ok";
            } else {
                throw new Exception("Failed to update user status");
            }
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
