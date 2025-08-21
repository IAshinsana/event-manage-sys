<?php
$page_title = "Event Approvals";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$success_message = '';
$error_message = '';

// Handle event approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $event_id = (int)$_POST['event_id'];
    $action = $_POST['action'];
    $rejection_reason = trim($_POST['rejection_reason'] ?? '');
    
    if ($action === 'approve' || $action === 'reject') {
        $approval_status = $action === 'approve' ? 'approved' : 'rejected';
        $event_status = $action === 'approve' ? 'published' : 'rejected';
        
        $conn->begin_transaction();
        try {
            // Update event approval status
            $event_sql = "UPDATE events 
                         SET approval_status = ?, status = ?, approved_by = ?, 
                             approved_at = NOW(), rejection_reason = ?
                         WHERE id = ?";
            $stmt = $conn->prepare($event_sql);
            $stmt->bind_param("ssisi", $approval_status, $event_status, $_SESSION['user_id'], 
                            $rejection_reason, $event_id);
            $stmt->execute();
            
            $conn->commit();
            $success_message = "Event " . ($action === 'approve' ? 'approved' : 'rejected') . " successfully!";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error updating event status. Please try again.";
        }
    }
}

// Get events pending approval
$events_sql = "SELECT e.*, u.name as coordinator_name, u.email as coordinator_email,
               COUNT(tt.id) as ticket_types_count,
               SUM(tt.qty_total) as total_tickets
               FROM events e
               JOIN users u ON e.created_by = u.id
               LEFT JOIN ticket_types tt ON e.id = tt.event_id
               WHERE e.approval_status IN ('pending', 'approved', 'rejected')
               GROUP BY e.id
               ORDER BY 
                   CASE e.approval_status 
                       WHEN 'pending' THEN 1 
                       WHEN 'approved' THEN 2 
                       WHEN 'rejected' THEN 3 
                   END,
                   e.created_at DESC";
$events_result = $conn->query($events_sql);

// Get statistics
$stats_sql = "SELECT 
    SUM(approval_status = 'pending') AS pending_count,
    SUM(approval_status = 'approved') AS approved_count,
    SUM(approval_status = 'rejected') AS rejected_count
    FROM events WHERE created_by IS NOT NULL";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>✅ Event Approvals</h1>
        <a href="index.php" class="btn btn-outline">← Dashboard</a>
    </div>
    
    <!-- Statistics Cards -->
    <div class="admin-stats" style="margin-bottom: 2rem;">
        <div class="admin-card">
            <div class="icon" style="color: #ffc107;">⏳</div>
            <div class="number"><?php echo number_format($stats['pending_count']); ?></div>
            <div class="label">Pending Approval</div>
        </div>
        
        <div class="admin-card">
            <div class="icon" style="color: #28a745;">✅</div>
            <div class="number"><?php echo number_format($stats['approved_count']); ?></div>
            <div class="label">Approved Events</div>
        </div>
        
        <div class="admin-card">
            <div class="icon" style="color: #dc3545;">❌</div>
            <div class="number"><?php echo number_format($stats['rejected_count']); ?></div>
            <div class="label">Rejected Events</div>
        </div>
    </div>
    
    <?php if ($success_message): ?>
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($events_result && $events_result->num_rows > 0): ?>
        <?php while ($event = $events_result->fetch_assoc()): ?>
            <div class="admin-section" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; color: #333;"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p style="margin: 0.5rem 0; color: #666;">
                            <strong>Coordinator:</strong> <?php echo htmlspecialchars($event['coordinator_name']); ?> 
                            (<?php echo htmlspecialchars($event['coordinator_email']); ?>) | 
                            <strong>Submitted:</strong> <?php echo date('M j, Y g:i A', strtotime($event['created_at'])); ?>
                        </p>
                    </div>
                    <span class="status-badge status-<?php echo $event['approval_status']; ?>">
                        <?php echo ucfirst($event['approval_status']); ?>
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                    <!-- Event Details -->
                    <div>
                        <h4 style="color: #007bff; margin-bottom: 0.5rem;">📅 Event Details</h4>
                        <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue'] ?: 'Not specified'); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($event['category'] ?: 'Not specified'); ?></p>
                        <p><strong>Start Date:</strong> <?php echo date('M j, Y g:i A', strtotime($event['starts_at'])); ?></p>
                        <p><strong>End Date:</strong> <?php echo date('M j, Y g:i A', strtotime($event['ends_at'])); ?></p>
                        <?php if ($event['organizer']): ?>
                            <p><strong>Organizer:</strong> <?php echo htmlspecialchars($event['organizer']); ?></p>
                        <?php endif; ?>
                        <?php if ($event['booking_phone']): ?>
                            <p><strong>Contact:</strong> <?php echo htmlspecialchars($event['booking_phone']); ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Ticket Information -->
                    <div>
                        <h4 style="color: #28a745; margin-bottom: 0.5rem;">🎫 Ticket Information</h4>
                        <?php if ($event['ticket_types_count'] > 0): ?>
                            <p><strong>Ticket Types:</strong> <?php echo $event['ticket_types_count']; ?></p>
                            <p><strong>Total Tickets:</strong> <?php echo number_format($event['total_tickets'] ?: 0); ?></p>
                            <a href="../admin/tickets_list.php?event_id=<?php echo $event['id']; ?>" 
                               style="color: #007bff; text-decoration: none; font-size: 0.9rem;">
                                View Ticket Details →
                            </a>
                        <?php else: ?>
                            <p style="color: #666; font-style: italic;">No ticket types configured yet</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Event Description -->
                <?php if ($event['description']): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #6610f2; margin-bottom: 0.5rem;">📝 Description</h4>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; border-left: 4px solid #6610f2;">
                            <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Event Image -->
                <?php if ($event['image_path']): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #e67e22; margin-bottom: 0.5rem;">🖼️ Event Image</h4>
                        <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                             alt="Event Image" 
                             style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    </div>
                <?php endif; ?>
                
                <?php if ($event['approval_status'] === 'pending'): ?>
                    <!-- Approval/Rejection Form -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ffc107;">
                        <h4 style="margin-bottom: 1rem;">🎯 Review Event</h4>
                        <form method="POST" style="display: grid; gap: 1rem;">
                            <input type="hidden" name="event_id" value="<?php echo $event['id']; ?>">
                            
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Rejection Reason (Optional)</label>
                                <textarea name="rejection_reason" rows="3" 
                                          style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                          placeholder="If rejecting, please provide a reason for the coordinator..."></textarea>
                                <small style="color: #666;">Note: This field is only used if you reject the event.</small>
                            </div>
                            
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" name="action" value="approve" 
                                        class="btn btn-success" 
                                        onclick="return confirm('Are you sure you want to approve this event? It will be published immediately.')">
                                    ✅ Approve & Publish
                                </button>
                                <button type="submit" name="action" value="reject" 
                                        class="btn" style="background: #dc3545; color: white;"
                                        onclick="return confirm('Are you sure you want to reject this event?')">
                                    ❌ Reject Event
                                </button>
                            </div>
                        </form>
                    </div>
                    
                <?php else: ?>
                    <!-- Review Information -->
                    <div style="background: <?php echo $event['approval_status'] === 'approved' ? '#d4edda' : '#f8d7da'; ?>; 
                               padding: 1.5rem; border-radius: 8px;">
                        <h4 style="margin-bottom: 1rem;">
                            <?php echo $event['approval_status'] === 'approved' ? '✅ Event Approved' : '❌ Event Rejected'; ?>
                        </h4>
                        
                        <?php if ($event['approved_at']): ?>
                            <p><strong>Reviewed on:</strong> <?php echo date('M j, Y g:i A', strtotime($event['approved_at'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($event['approval_status'] === 'approved'): ?>
                            <p><strong>Status:</strong> Event is now published and accepting bookings</p>
                            <div style="display: flex; gap: 1rem; margin-top: 1rem; flex-wrap: wrap;">
                                <a href="../events_view.php?id=<?php echo $event['id']; ?>" 
                                   class="btn btn-primary btn-sm">
                                    👁️ View Public Page
                                </a>
                                <button class="btn btn-warning btn-sm btn-archive-event" 
                                        data-event-id="<?php echo $event['id']; ?>"
                                        data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                        data-has-tickets="<?php echo ($event['total_tickets'] > 0) ? 'true' : 'false'; ?>">
                                    🗄️ Archive Event
                                </button>
                                <?php 
                                // For now, assume no bookings since table may not exist
                                $has_bookings = false;
                                ?>
                                <?php if (!$has_bookings): ?>
                                    <button class="btn btn-danger btn-sm btn-delete-event" 
                                            data-event-id="<?php echo $event['id']; ?>"
                                            data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                            data-has-tickets="false">
                                        🗑️ Delete Event
                                    </button>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($event['rejection_reason']): ?>
                            <div style="margin-top: 1rem;">
                                <strong>Rejection Reason:</strong><br>
                                <div style="background: rgba(255,255,255,0.7); padding: 1rem; border-radius: 5px; margin-top: 0.5rem;">
                                    <?php echo nl2br(htmlspecialchars($event['rejection_reason'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        
    <?php else: ?>
        <div class="admin-section" style="text-align: center; padding: 3rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📅</div>
            <h3>No Events Found</h3>
            <p style="color: #666;">No coordinator events have been submitted for approval yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
