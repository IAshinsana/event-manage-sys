<?php
include 'includes/db.php';
include 'includes/auth.php';

// Ensure user is logged in and has appropriate permissions
if (!is_logged_in()) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$action = $_POST['action'] ?? '';
$event_id = (int)($_POST['event_id'] ?? 0);
$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['role'];

if (!$event_id || !$action) {
    http_response_code(400);
    echo "Invalid request";
    exit;
}


// Verify user has permission to manage this event
$event_check_sql = "SELECT e.*, 
                    COALESCE(
                        (SELECT SUM(oi.qty) 
                         FROM orders o 
                         INNER JOIN order_items oi ON o.id = oi.order_id
                         INNER JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
                         WHERE tt.event_id = e.id AND o.status = 'paid'), 0
                    ) as tickets_sold 
                    FROM events e WHERE e.id = ?";
$stmt = $conn->prepare($event_check_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event = $stmt->get_result()->fetch_assoc();

if (!$event) {
    http_response_code(404);
    echo "Event not found";
    exit;
}

// Check permissions
$can_manage = false;
if ($user_role === 'admin') {
    $can_manage = true;
} elseif ($user_role === 'coordinator' && $event['created_by'] == $user_id) {
    $can_manage = true;
}

if (!$can_manage) {
    http_response_code(403);
    echo "Access denied";
    exit;
}

try {
    $conn->begin_transaction();
    
    switch ($action) {
        case 'archive':
            $reason = trim($_POST['reason'] ?? '');
            
            // Archive the event
            $archive_sql = "UPDATE events 
                           SET status = 'archived', 
                               archived_at = NOW(), 
                               archived_by = ?,
                               archive_reason = ?
                           WHERE id = ?";
            $stmt = $conn->prepare($archive_sql);
            $stmt->bind_param("isi", $user_id, $reason, $event_id);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception("Failed to archive event");
            }
            
            break;
            
        case 'reactivate':
            $needs_approval = $_POST['needs_approval'] === '1';
            
            if ($needs_approval && $user_role === 'coordinator') {
                // Coordinator reactivation - send for approval
                $reactivate_sql = "UPDATE events 
                                  SET reactivation_requested = TRUE,
                                      reactivation_requested_at = NOW(),
                                      archive_reason = NULL
                                  WHERE id = ?";
                $stmt = $conn->prepare($reactivate_sql);
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                
                if ($stmt->affected_rows === 0) {
                    throw new Exception("Failed to request reactivation");
                }
            } else {
                // Admin reactivation - immediate
                $reactivate_sql = "UPDATE events 
                                  SET status = 'published',
                                      archived_at = NULL,
                                      archived_by = NULL,
                                      archive_reason = NULL,
                                      reactivation_requested = FALSE,
                                      reactivation_requested_at = NULL
                                  WHERE id = ?";
                $stmt = $conn->prepare($reactivate_sql);
                $stmt->bind_param("i", $event_id);
                $stmt->execute();
                
                if ($stmt->affected_rows === 0) {
                    throw new Exception("Failed to reactivate event");
                }
            }
            
            break;
            
        case 'delete':
            // Only allow deletion if no tickets sold and user is admin
            if ($user_role !== 'admin') {
                throw new Exception("Only admins can delete events");
            }
            
            if ($event['tickets_sold'] > 0) {
                throw new Exception("Cannot delete event with sold tickets");
            }
            
            // Delete related records first
            $delete_tickets_sql = "DELETE FROM ticket_types WHERE event_id = ?";
            $stmt = $conn->prepare($delete_tickets_sql);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            
          
            // Delete the event
            $delete_event_sql = "DELETE FROM events WHERE id = ?";
            $stmt = $conn->prepare($delete_event_sql);
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            
            if ($stmt->affected_rows === 0) {
                throw new Exception("Failed to delete event");
            }
            
            // Delete event image if exists
            if ($event['image_path'] && file_exists($event['image_path'])) {
                unlink($event['image_path']);
            }
            
            break;
            
        default:
            throw new Exception("Invalid action");
    }
    
    $conn->commit();
    http_response_code(200);
    echo "Success";
    
} catch (Exception $e) {
    $conn->rollback();
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>
