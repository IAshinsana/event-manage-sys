<?php
include '../includes/db.php';
include '../includes/auth.php';

if (!is_logged_in()) {
    echo "Please log in first";
    exit;
}

if ($_POST) {
    $action = $_POST['action'] ?? '';
    $event_id = (int)$_POST['event_id'];
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if ($role !== 'admin') {
        $check_sql = "SELECT id FROM events WHERE id = $event_id AND created_by = $user_id";
        $check_result = $conn->query($check_sql);
        if (!$check_result || $check_result->num_rows === 0) {
            echo "You don't have permission to manage tickets for this event";
            exit;
        }
    }
    
    if ($action === 'add_ticket') {
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $price_cents = (int)($price * 100);
        
        if (empty($name) || $price < 0 || $quantity <= 0) {
            echo "Please fill in all required fields with valid values.";
            exit;
        }
        
        $sql = "INSERT INTO ticket_types (event_id, name, price_cents, qty_total) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("isii", $event_id, $name, $price_cents, $quantity);
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "Failed to add ticket type. Please try again.";
        }
        
    } elseif ($action === 'update_ticket') {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $price = (float)($_POST['price'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 0);
        $price_cents = (int)($price * 100);
        
        if (!$ticket_id || empty($name) || $price < 0 || $quantity <= 0) {
            echo "Please fill in all required fields with valid values.";
            exit;
        }
        
        $sql = "UPDATE ticket_types SET 
                name = ?, price_cents = ?, qty_total = ?
                WHERE id = ? AND event_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("siiii", $name, $price_cents, $quantity, $ticket_id, $event_id);
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "Failed to update ticket type. Please try again.";
        }
        
    } elseif ($action === 'delete_ticket') {
        $ticket_id = (int)($_POST['ticket_id'] ?? 0);
        
        if (!$ticket_id) {
            echo "Invalid ticket ID";
            exit;
        }
        
        $check_sql = "SELECT COUNT(*) as order_count FROM order_items oi 
                     JOIN orders o ON oi.order_id = o.id 
                     WHERE oi.ticket_type_id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $ticket_id);
        $check_stmt->execute();
        $order_result = $check_stmt->get_result();
        $order_data = $order_result->fetch_assoc();
        
        if ($order_data['order_count'] > 0) {
            echo "Cannot delete ticket type that has existing orders";
            exit;
        }
        
        $sql = "DELETE FROM ticket_types WHERE id = ? AND event_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $ticket_id, $event_id);
        
        if ($stmt->execute()) {
            echo "ok";
        } else {
            echo "Failed to delete ticket type. Please try again.";
        }
        
    } else {
        echo "Invalid action";
    }
    
} else {
    echo "No data received";
}

?>
