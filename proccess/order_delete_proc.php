<?php
include '../includes/db.php';
include '../includes/auth.php';
require_admin();

if ($_POST) {
    $order_id = (int)$_POST['order_id'];
    $action = $_POST['action'] ?? '';
    
    if (!$order_id || !in_array($action, ['cancel', 'delete'])) {
        echo "Invalid parameters";
        exit;
    }
    
    $conn->begin_transaction();
    
    try {
        if ($action === 'cancel') {
            $sql = "UPDATE orders SET status = 'cancelled' WHERE id = $order_id";
            if (!$conn->query($sql)) {
                throw new Exception("Failed to cancel order");
            }
            
            $update_tickets_sql = "UPDATE ticket_types tt 
                                  INNER JOIN order_items oi ON tt.id = oi.ticket_type_id 
                                  SET tt.qty_sold = tt.qty_sold - oi.qty,
                                      tt.quantity_available = tt.quantity_available + oi.qty 
                                  WHERE oi.order_id = $order_id";
            $conn->query($update_tickets_sql);
            
            echo "ok";
            
        } elseif ($action === 'delete') {
            $order_check_sql = "SELECT status FROM orders WHERE id = $order_id";
            $order_result = $conn->query($order_check_sql);
            $order = $order_result->fetch_assoc();
            
            if ($order['status'] === 'paid') {
                echo "Cannot delete paid orders. Cancel instead.";
                exit;
            }
            
            $conn->query("DELETE FROM attendees WHERE order_item_id IN (SELECT id FROM order_items WHERE order_id = $order_id)");
            $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
            
            $delete_order_sql = "DELETE FROM orders WHERE id = $order_id";
            if ($conn->query($delete_order_sql)) {
                echo "ok";
            } else {
                throw new Exception("Failed to delete order");
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