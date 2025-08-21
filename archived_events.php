<?php
$page_title = "Archived Events";
include 'includes/header.php';
include 'includes/db.php';

// Check if user is logged in and has appropriate permissions
if (!is_logged_in()) {
    header('Location: login.php');
    exit;
}

$user_role = $_SESSION['role'];
$user_id = $_SESSION['user_id'];

// Build query based on user role
if ($user_role === 'admin') {
    // Admin can see all archived events
    $events_sql = "SELECT e.*, u.name as coordinator_name, u.email as coordinator_email,
                   COUNT(DISTINCT tt.id) as ticket_types_count,
                   COALESCE(SUM(tt.qty_total), 0) as total_tickets,
                   0 as bookings_count,
                   0 as revenue,
                   ua.name as archived_by_name
                   FROM events e
                   LEFT JOIN users u ON e.created_by = u.id
                   LEFT JOIN users ua ON e.archived_by = ua.id
                   LEFT JOIN ticket_types tt ON e.id = tt.event_id
                   WHERE e.status = 'archived' OR e.reactivation_requested = TRUE
                   GROUP BY e.id
                   ORDER BY 
                       CASE WHEN e.reactivation_requested = TRUE THEN 1 ELSE 2 END,
                       e.archived_at DESC";
} else {
    // Coordinators can only see their own archived events
    $events_sql = "SELECT e.*, u.name as coordinator_name, u.email as coordinator_email,
                   COUNT(DISTINCT tt.id) as ticket_types_count,
                   COALESCE(SUM(tt.qty_total), 0) as total_tickets,
                   0 as bookings_count,
                   0 as revenue,
                   ua.name as archived_by_name
                   FROM events e
                   LEFT JOIN users u ON e.created_by = u.id
                   LEFT JOIN users ua ON e.archived_by = ua.id
                   LEFT JOIN ticket_types tt ON e.id = tt.event_id
                   WHERE (e.status = 'archived' OR e.reactivation_requested = TRUE) 
                   AND e.created_by = ?
                   GROUP BY e.id
                   ORDER BY 
                       CASE WHEN e.reactivation_requested = TRUE THEN 1 ELSE 2 END,
                       e.archived_at DESC";
}

$stmt = $conn->prepare($events_sql);
if ($user_role !== 'admin') {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$events_result = $stmt->get_result();

// Get statistics
if ($user_role === 'admin') {
    $stats_sql = "SELECT 
        SUM(status = 'archived') AS archived_count,
        SUM(reactivation_requested = TRUE) AS reactivation_requests,
        COUNT(DISTINCT created_by) as coordinators_with_archived
        FROM events WHERE status = 'archived' OR reactivation_requested = TRUE";
} else {
    $stats_sql = "SELECT 
        SUM(status = 'archived') AS archived_count,
        SUM(reactivation_requested = TRUE) AS reactivation_requests,
        1 as coordinators_with_archived
        FROM events WHERE (status = 'archived' OR reactivation_requested = TRUE) AND created_by = ?";
}

$stmt = $conn->prepare($stats_sql);
if ($user_role !== 'admin') {
    $stmt->bind_param("i", $user_id);
}
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>🗄️ Archived Events</h1>
        <div style="display: flex; gap: 1rem;">
            <?php if ($user_role === 'admin'): ?>
                <a href="admin/index.php" class="btn btn-outline">← Admin Dashboard</a>
            <?php else: ?>
                <a href="coordinator/dashboard.php" class="btn btn-outline">← Dashboard</a>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="admin-stats" style="margin-bottom: 2rem;">
        <div class="admin-card">
            <div class="icon" style="color: #6c757d;">🗄️</div>
            <div class="number"><?php echo number_format($stats['archived_count'] ?? 0); ?></div>
            <div class="label">Archived Events</div>
        </div>
        
        <?php if ($user_role === 'admin'): ?>
            <div class="admin-card">
                <div class="icon" style="color: #17a2b8;">🔄</div>
                <div class="number"><?php echo number_format($stats['reactivation_requests'] ?? 0); ?></div>
                <div class="label">Reactivation Requests</div>
            </div>
            
            <div class="admin-card">
                <div class="icon" style="color: #28a745;">👥</div>
                <div class="number"><?php echo number_format($stats['coordinators_with_archived'] ?? 0); ?></div>
                <div class="label">Coordinators</div>
            </div>
        <?php else: ?>
            <div class="admin-card">
                <div class="icon" style="color: #17a2b8;">🔄</div>
                <div class="number"><?php echo number_format($stats['reactivation_requests'] ?? 0); ?></div>
                <div class="label">Pending Reactivation</div>
            </div>
        <?php endif; ?>
    </div>
    
    <?php if ($events_result && $events_result->num_rows > 0): ?>
        <?php while ($event = $events_result->fetch_assoc()): ?>
            <div class="admin-section" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; color: #333;"><?php echo htmlspecialchars($event['title']); ?></h3>
                        <p style="margin: 0.5rem 0; color: #666;">
                            <?php if ($user_role === 'admin'): ?>
                                <strong>Coordinator:</strong> <?php echo htmlspecialchars($event['coordinator_name']); ?> 
                                (<?php echo htmlspecialchars($event['coordinator_email']); ?>) | 
                            <?php endif; ?>
                            <strong>Archived:</strong> <?php echo $event['archived_at'] ? date('M j, Y g:i A', strtotime($event['archived_at'])) : 'N/A'; ?>
                            <?php if ($event['archived_by_name']): ?>
                                by <?php echo htmlspecialchars($event['archived_by_name']); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                        <?php if ($event['reactivation_requested']): ?>
                            <span class="status-badge status-reactivation_requested">Reactivation Requested</span>
                        <?php else: ?>
                            <span class="status-badge status-archived">Archived</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                    <!-- Event Details -->
                    <div>
                        <h4 style="color: #007bff; margin-bottom: 0.5rem;">📅 Event Details</h4>
                        <p><strong>Venue:</strong> <?php echo htmlspecialchars($event['venue'] ?: 'Not specified'); ?></p>
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($event['category'] ?: 'Not specified'); ?></p>
                        <p><strong>Start:</strong> <?php echo date('M j, Y g:i A', strtotime($event['starts_at'])); ?></p>
                        <p><strong>End:</strong> <?php echo date('M j, Y g:i A', strtotime($event['ends_at'])); ?></p>
                    </div>
                    
                    <!-- Ticket Information -->
                    <div>
                        <h4 style="color: #28a745; margin-bottom: 0.5rem;">🎫 Tickets & Bookings</h4>
                        <p><strong>Ticket Types:</strong> <?php echo $event['ticket_types_count'] ?: 0; ?></p>
                        <p><strong>Total Tickets:</strong> <?php echo number_format($event['total_tickets'] ?: 0); ?></p>
                        <p><strong>Bookings:</strong> <?php echo number_format($event['bookings_count'] ?: 0); ?></p>
                        <p><strong>Revenue:</strong> $<?php echo number_format($event['revenue'] ?: 0, 2); ?></p>
                    </div>
                    
                    <!-- Archive Information -->
                    <div>
                        <h4 style="color: #6c757d; margin-bottom: 0.5rem;">🗄️ Archive Info</h4>
                        <?php if ($event['archive_reason']): ?>
                            <p><strong>Reason:</strong> <?php echo htmlspecialchars($event['archive_reason']); ?></p>
                        <?php endif; ?>
                        <?php if ($event['reactivation_requested_at']): ?>
                            <p><strong>Reactivation Requested:</strong> 
                               <?php echo date('M j, Y g:i A', strtotime($event['reactivation_requested_at'])); ?></p>
                        <?php endif; ?>
                        <p><strong>Can Delete:</strong> 
                           <?php echo (($event['bookings_count'] ?? 0) == 0) ? 'Yes' : 'No (has bookings)'; ?></p>
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
                
                <!-- Actions -->
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #6c757d;">
                    <h4 style="margin-bottom: 1rem;">⚡ Actions</h4>
                    
                    <?php if ($event['reactivation_requested'] && $user_role === 'admin'): ?>
                        <!-- Admin handling reactivation request -->
                        <div style="background: #e7f3ff; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #17a2b8;">
                            <strong>🔄 Reactivation Request Pending</strong><br>
                            <small>Coordinator has requested to reactivate this event.</small>
                        </div>
                        <div style="display: flex; gap: 1rem;">
                            <button class="btn btn-success btn-reactivate-event" 
                                    data-event-id="<?php echo $event['id']; ?>"
                                    data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                    data-user-role="<?php echo $user_role; ?>">
                                ✅ Approve Reactivation
                            </button>
                            <button class="btn btn-danger btn-archive-event" 
                                    data-event-id="<?php echo $event['id']; ?>"
                                    data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                    data-has-tickets="<?php echo ($event['bookings_count'] > 0) ? 'true' : 'false'; ?>">
                                ❌ Deny & Keep Archived
                            </button>
                        </div>
                        
                    <?php elseif ($event['reactivation_requested']): ?>
                        <!-- Coordinator waiting for approval -->
                        <div style="background: #fff3cd; padding: 1rem; border-radius: 5px; margin-bottom: 1rem; border-left: 4px solid #ffc107;">
                            <strong>⏳ Waiting for Admin Approval</strong><br>
                            <small>Your reactivation request is pending admin review.</small>
                        </div>
                        
                    <?php else: ?>
                        <!-- Standard archived event actions -->
                        <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                            <button class="btn btn-success btn-reactivate-event" 
                                    data-event-id="<?php echo $event['id']; ?>"
                                    data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                    data-user-role="<?php echo $user_role; ?>">
                                🔄 Reactivate Event
                            </button>
                            
                            <?php if ($user_role === 'admin' && ($event['bookings_count'] ?? 0) == 0): ?>
                                <button class="btn btn-danger btn-delete-event" 
                                        data-event-id="<?php echo $event['id']; ?>"
                                        data-event-title="<?php echo htmlspecialchars($event['title']); ?>"
                                        data-has-tickets="false">
                                    🗑️ Delete Permanently
                                </button>
                            <?php endif; ?>
                            
                            <?php if (($event['bookings_count'] ?? 0) > 0): ?>
                                <a href="admin/bookings_list.php?event_id=<?php echo $event['id']; ?>" 
                                   class="btn btn-outline btn-sm">
                                    📋 View Bookings
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
        
    <?php else: ?>
        <div class="admin-section" style="text-align: center; padding: 3rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">🗄️</div>
            <h3>No Archived Events</h3>
            <p style="color: #666;">No events have been archived yet.</p>
            <?php if ($user_role === 'admin'): ?>
                <a href="admin/events_approval.php" class="btn btn-primary" style="margin-top: 1rem;">
                    📋 Review Events
                </a>
            <?php else: ?>
                <a href="coordinator/event_create.php" class="btn btn-primary" style="margin-top: 1rem;">
                    ➕ Create New Event
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script src="assets/js/event-manager.js"></script>
<?php include 'includes/footer.php'; ?>
