<?php
$page_title = "Coordinator Dashboard";
include '../includes/header.php';
include '../includes/db.php';
?>
<link rel="stylesheet" href="../assets/css/coordinator_dashboard.css">
<?php

// Check if user is coordinator
if (!is_logged_in() || $_SESSION['role'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

// Get coordinator status and refresh session
$user_sql = "SELECT u.*, ca.status as app_status, ca.admin_notes 
             FROM users u 
             LEFT JOIN coordinator_applications ca ON u.id = ca.user_id 
             WHERE u.id = {$_SESSION['user_id']}";
$user_result = $conn->query($user_sql);
$coordinator = $user_result->fetch_assoc();

// Update session with current coordinator status
if ($coordinator) {
    $_SESSION['coordinator_status'] = $coordinator['coordinator_status'];
}

// Check if coordinator is approved
if ($coordinator['coordinator_status'] !== 'approved') {
    // Redirect to show current status
    if (isset($_GET['error']) && $_GET['error'] === 'not_approved') {
        // Already showing error, no redirect needed
    } else {
        header('Location: dashboard.php?error=not_approved');
        exit;
    }
}

// Get coordinator's events
$events_sql = "SELECT e.*, 
               (SELECT COUNT(DISTINCT tt.id) FROM ticket_types tt WHERE tt.event_id = e.id) AS ticket_types_count,
               (SELECT COUNT(*) FROM orders o WHERE o.event_id = e.id) AS orders_count,
               (SELECT COALESCE(SUM(o.total_cents), 0) FROM orders o WHERE o.event_id = e.id AND o.status = 'paid') AS revenue
               FROM events e 
               WHERE e.created_by = {$_SESSION['user_id']} 
               ORDER BY e.created_at DESC";
$events_result = $conn->query($events_sql);

// Get statistics
// Get statistics
$stats_sql = "SELECT 
    SUM(e.approval_status = 'pending') AS pending_events,
    SUM(e.approval_status = 'approved' AND e.status = 'published') AS published_events,
    SUM(e.approval_status = 'rejected') AS rejected_events,
    (SELECT COALESCE(SUM(o.total_cents), 0) FROM orders o WHERE o.status = 'paid' AND o.event_id IN (SELECT id FROM events WHERE created_by = {$_SESSION['user_id']})) AS total_revenue
    FROM events e 
    WHERE e.created_by = {$_SESSION['user_id']}";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<div class="container coordinator-dashboard-container">
    <?php if ($coordinator['coordinator_status'] === 'pending'): ?>
        <!-- Pending Approval -->
        <div class="coordinator-dashboard-pending-status">
            <h1>⏳ Application Under Review</h1>
            <p class="coordinator-dashboard-pending-subtitle">
                Your coordinator application is being reviewed by our admin team. 
                You'll be notified once a decision is made.
            </p>
            <div class="coordinator-dashboard-pending-details">
                <strong>Applied on:</strong> <?php echo date('M j, Y', strtotime($coordinator['coordinator_applied_at'])); ?>
            </div>
        </div>
        
    <?php elseif ($coordinator['coordinator_status'] === 'rejected'): ?>
        <!-- Rejected -->
        <div class="coordinator-dashboard-rejected-status">
            <h1>❌ Application Rejected</h1>
            <p class="coordinator-dashboard-rejected-subtitle">
                Unfortunately, your coordinator application was not approved.
            </p>
            <?php if ($coordinator['admin_notes']): ?>
                <div class="coordinator-dashboard-rejected-details">
                    <strong>Admin Notes:</strong><br>
                    <?php echo nl2br($coordinator['admin_notes']); ?>
                </div>
            <?php endif; ?>
            <div class="coordinator-dashboard-rejected-actions">
                <a href="reapply.php" class="btn coordinator-dashboard-rejected-reapply-btn">
                    Apply Again
                </a>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Approved Coordinator Dashboard -->
        <h1>🎯 Coordinator Dashboard</h1>
        <p class="coordinator-dashboard-welcome">Welcome back, <?php echo $coordinator['name']; ?>!</p>
        
        <!-- Statistics Cards -->
        <div class="admin-stats">
            <div class="admin-card">
                <div class="icon coordinator-dashboard-stats-icon">⏳</div>
                <div class="number"><?php echo number_format($stats['pending_events']); ?></div>
                <div class="label">Pending Approval</div>
            </div>
            
            <div class="admin-card">
                <div class="icon coordinator-dashboard-stats-icon approved">✅</div>
                <div class="number"><?php echo number_format($stats['published_events']); ?></div>
                <div class="label">Published Events</div>
            </div>
            
            <div class="admin-card">
                <div class="icon coordinator-dashboard-stats-icon rejected">❌</div>
                <div class="number"><?php echo number_format($stats['rejected_events']); ?></div>
                <div class="label">Rejected Events</div>
            </div>
            
            <div class="admin-card">
                <div class="icon coordinator-dashboard-stats-icon revenue">💰</div>
                <div class="number">LKR <?php echo number_format(($stats['total_revenue'] ?: 0) / 100, 2); ?></div>
                <div class="label">Total Revenue</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div class="admin-section">
            <h2>⚡ Quick Actions</h2>
            <div class="coordinator-dashboard-actions">
                <a href="event_create.php" class="coordinator-dashboard-action-card">
                    <div class="admin-card">
                        <div class="icon coordinator-dashboard-action-icon">➕</div>
                        <h4>Create New Event</h4>
                        <p class="coordinator-dashboard-action-description">Submit event for admin approval</p>
                    </div>
                </a>
                
                <a href="events_list.php" class="coordinator-dashboard-action-card events">
                    <div class="admin-card">
                        <div class="icon coordinator-dashboard-action-icon events">📋</div>
                        <h4>My Events</h4>
                        <p class="coordinator-dashboard-action-description">Manage your events</p>
                    </div>
                </a>
                
                <a href="profile.php" class="coordinator-dashboard-action-card profile">
                    <div class="admin-card">
                        <div class="icon coordinator-dashboard-action-icon profile">👤</div>
                        <h4>My Profile</h4>
                        <p class="coordinator-dashboard-action-description">Update profile settings</p>
                    </div>
                </a>
                
                <a href="../archived_events.php" class="coordinator-dashboard-action-card archived">
                    <div class="admin-card">
                        <div class="icon coordinator-dashboard-action-icon archived">🗄️</div>
                        <h4>Archived Events</h4>
                        <p class="coordinator-dashboard-action-description">View archived events</p>
                    </div>
                </a>
                
                <a href="profile.php" class="coordinator-dashboard-action-card profile-alt">
                    <div class="admin-card">
                        <div class="icon coordinator-dashboard-action-icon profile-alt">👤</div>
                        <h4>Profile Settings</h4>
                        <p class="coordinator-dashboard-action-description">Update your information</p>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Recent Events -->
        <div class="admin-section">
            <h2>📅 Recent Events</h2>
            
            <?php if ($events_result && $events_result->num_rows > 0): ?>
                <div class="coordinator-dashboard-events-table">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Event</th>
                                <th>Date</th>
                                <th>Approval Status</th>
                                <th>Publish Status</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($event = $events_result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo $event['title']; ?></strong><br>
                                        <small class="coordinator-dashboard-event-venue"><?php echo $event['venue']; ?></small>
                                    </td>
                                    <td>
                                        <?php echo date('M j, Y', strtotime($event['starts_at'])); ?><br>
                                        <small class="coordinator-dashboard-event-time"><?php echo date('g:i A', strtotime($event['starts_at'])); ?></small>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event['approval_status']; ?>">
                                            <?php echo ucfirst($event['approval_status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $event['status']; ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                        </span>
                                    </td>
                                    <td><?php echo number_format($event['orders_count']); ?></td>
                                    <td>LKR <?php echo number_format(($event['revenue'] ?: 0) / 100, 2); ?></td>
                                    <td>
                                        <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm coordinator-dashboard-event-edit-btn">Edit</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="coordinator-dashboard-no-events">
                    <div class="coordinator-dashboard-no-events-icon">📅</div>
                    <h3>No Events Yet</h3>
                    <p>Start by creating your first event!</p>
                    <a href="event_create.php" class="btn btn-primary">Create Event</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
