<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $event_id = (int)$_POST['event_id'];
    $action = $_POST['action'] ?? '';
    
    if (!$event_id || !in_array($action, ['soft_delete', 'hard_delete'])) {
        echo "Invalid parameters";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        if ($action === 'soft_delete') {
            $sql = "UPDATE events SET status = 'archived', archived_at = NOW() WHERE id = $event_id";
            if ($conn->query($sql)) {
                echo "ok";
            } else {
                throw new Exception("Failed to archive event");
            }
            
        } elseif ($action === 'hard_delete') {
            $image_sql = "SELECT image_path FROM events WHERE id = $event_id";
            $image_result = $conn->query($image_sql);
            $image_path = null;
            if ($image_result && $image_result->num_rows > 0) {
                $image_data = $image_result->fetch_assoc();
                $image_path = $image_data['image_path'];
            }
            
            // Clean up all related event data
            $conn->query("DELETE FROM attendees WHERE order_item_id IN (SELECT id FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE event_id = $event_id))");
            $conn->query("DELETE FROM order_items WHERE order_id IN (SELECT id FROM orders WHERE event_id = $event_id)");
            $conn->query("DELETE FROM orders WHERE event_id = $event_id");
            $conn->query("DELETE FROM ticket_types WHERE event_id = $event_id");
            $conn->query("DELETE FROM events WHERE id = $event_id");
            
            if ($image_path && file_exists('../' . $image_path)) {
                unlink('../' . $image_path);
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

