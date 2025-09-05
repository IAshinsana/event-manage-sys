<?php
$page_title = "Coordinator Dashboard";
include '../includes/header.php';
include '../includes/db.php';
?>
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

<div class="container" style="max-width: 1200px; margin: 2rem auto; padding: 0 1rem;">
    <?php if ($coordinator['coordinator_status'] === 'pending'): ?>
        <!-- Pending Approval -->
        <div class="card" style="text-align: center; padding: 2.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--warning);">⏳</div>
            <h1 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.5rem;">Application Under Review</h1>
            <p style="color: var(--gray-600); font-size: 1rem; margin-bottom: 1.5rem;">
                Your coordinator application is being reviewed by our admin team. 
                You'll be notified once a decision is made.
            </p>
            <div style="background: var(--gray-50); border-radius: 8px; padding: 1rem; color: var(--gray-700);">
                <strong>Applied on:</strong> <?php echo date('M j, Y', strtotime($coordinator['coordinator_applied_at'])); ?>
            </div>
        </div>
        
    <?php elseif ($coordinator['coordinator_status'] === 'rejected'): ?>
        <!-- Rejected -->
        <div class="card" style="text-align: center; padding: 2.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--danger);">❌</div>
            <h1 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.5rem;">Application Rejected</h1>
            <p style="color: var(--gray-600); font-size: 1rem; margin-bottom: 1.5rem;">
                Unfortunately, your coordinator application was not approved.
            </p>
            <?php if ($coordinator['admin_notes']): ?>
                <div style="background: var(--gray-50); border-radius: 8px; padding: 1rem; margin-bottom: 1.5rem; text-align: left;">
                    <strong style="color: var(--primary);">Admin Notes:</strong><br>
                    <span style="color: var(--gray-700);"><?php echo nl2br($coordinator['admin_notes']); ?></span>
                </div>
            <?php endif; ?>
            <a href="reapply.php" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                Apply Again
            </a>
        </div>
        
    <?php else: ?>
        <!-- Approved Coordinator Dashboard -->
        <div style="margin-bottom: 2rem;">
            <h1 style="color: var(--primary); font-size: 1.75rem; margin-bottom: 0.5rem;">Coordinator Dashboard</h1>
            <p style="color: var(--gray-600); font-size: 1rem;">Welcome back, <?php echo $coordinator['name']; ?>!</p>
        </div>
        
        <!-- Statistics Cards -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2.5rem;">
            <div class="card" style="text-align: center; padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--warning);">⏳</div>
                <div style="font-size: 1.75rem; font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;"><?php echo number_format($stats['pending_events']); ?></div>
                <div style="color: var(--gray-600); font-size: 0.9rem;">Pending Approval</div>
            </div>
            
            <div class="card" style="text-align: center; padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--success);">✅</div>
                <div style="font-size: 1.75rem; font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;"><?php echo number_format($stats['published_events']); ?></div>
                <div style="color: var(--gray-600); font-size: 0.9rem;">Published Events</div>
            </div>
            
            <div class="card" style="text-align: center; padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--danger);">❌</div>
                <div style="font-size: 1.75rem; font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;"><?php echo number_format($stats['rejected_events']); ?></div>
                <div style="color: var(--gray-600); font-size: 0.9rem;">Rejected Events</div>
            </div>
            
            <div class="card" style="text-align: center; padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                <div style="font-size: 2rem; margin-bottom: 0.5rem; color: var(--accent);">💰</div>
                <div style="font-size: 1.75rem; font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;">LKR <?php echo number_format(($stats['total_revenue'] ?: 0) / 100, 2); ?></div>
                <div style="color: var(--gray-600); font-size: 0.9rem;">Total Revenue</div>
            </div>
        </div>
        
        <!-- Quick Actions -->
        <div style="margin-bottom: 2.5rem;">
            <h2 style="color: var(--primary); font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 600;">Quick Actions</h2>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.5rem;">
                <a href="event_create.php" style="text-decoration: none; color: inherit;">
                    <div class="card" style="padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid var(--success); transition: transform 0.2s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; color: var(--success);">➕</div>
                            <div>
                                <h4 style="color: var(--primary); margin: 0 0 0.25rem 0; font-size: 1rem;">Create New Event</h4>
                                <p style="color: var(--gray-600); margin: 0; font-size: 0.875rem;">Submit event for admin approval</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="events_list.php" style="text-decoration: none; color: inherit;">
                    <div class="card" style="padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid var(--accent); transition: transform 0.2s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; color: var(--accent);">📋</div>
                            <div>
                                <h4 style="color: var(--primary); margin: 0 0 0.25rem 0; font-size: 1rem;">My Events</h4>
                                <p style="color: var(--gray-600); margin: 0; font-size: 0.875rem;">Manage your events</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="profile.php" style="text-decoration: none; color: inherit;">
                    <div class="card" style="padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid var(--primary); transition: transform 0.2s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; color: var(--primary);">👤</div>
                            <div>
                                <h4 style="color: var(--primary); margin: 0 0 0.25rem 0; font-size: 1rem;">My Profile</h4>
                                <p style="color: var(--gray-600); margin: 0; font-size: 0.875rem;">Update profile settings</p>
                            </div>
                        </div>
                    </div>
                </a>
                
                <a href="../archived_events.php" style="text-decoration: none; color: inherit;">
                    <div class="card" style="padding: 1.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border-left: 4px solid var(--gray-500); transition: transform 0.2s ease; cursor: pointer;" onmouseover="this.style.transform='translateY(-2px)'" onmouseout="this.style.transform='translateY(0)'">
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            <div style="font-size: 2rem; color: var(--gray-500);">🗄️</div>
                            <div>
                                <h4 style="color: var(--primary); margin: 0 0 0.25rem 0; font-size: 1rem;">Archived Events</h4>
                                <p style="color: var(--gray-600); margin: 0; font-size: 0.875rem;">View archived events</p>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        </div>
        
        <!-- Recent Events -->
        <div>
            <h2 style="color: var(--primary); font-size: 1.25rem; margin-bottom: 1.5rem; font-weight: 600;">Recent Events</h2>
            
            <?php if ($events_result && $events_result->num_rows > 0): ?>
                <div class="card" style="background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden;">
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr style="background: var(--gray-50);">
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Event</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Date</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Approval</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Status</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Orders</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Revenue</th>
                                    <th style="padding: 1rem; text-align: left; font-weight: 600; color: var(--primary); border-bottom: 1px solid var(--gray-200);">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($event = $events_result->fetch_assoc()): ?>
                                    <tr style="border-bottom: 1px solid var(--gray-100);">
                                        <td style="padding: 1rem;">
                                            <div style="font-weight: 600; color: var(--primary); margin-bottom: 0.25rem;"><?php echo $event['title']; ?></div>
                                            <div style="font-size: 0.875rem; color: var(--gray-600);"><?php echo $event['venue']; ?></div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <div style="color: var(--primary); font-weight: 500;"><?php echo date('M j, Y', strtotime($event['starts_at'])); ?></div>
                                            <div style="font-size: 0.875rem; color: var(--gray-600);"><?php echo date('g:i A', strtotime($event['starts_at'])); ?></div>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php
                                            $approval_color = $event['approval_status'] === 'approved' ? 'var(--success)' : 
                                                            ($event['approval_status'] === 'rejected' ? 'var(--danger)' : 'var(--warning)');
                                            ?>
                                            <span style="background: <?php echo $approval_color; ?>; color: var(--white); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                                <?php echo ucfirst($event['approval_status']); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem;">
                                            <?php
                                            $status_color = $event['status'] === 'published' ? 'var(--accent)' : 'var(--gray-500)';
                                            ?>
                                            <span style="background: <?php echo $status_color; ?>; color: var(--white); padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.75rem; font-weight: 600;">
                                                <?php echo ucfirst(str_replace('_', ' ', $event['status'])); ?>
                                            </span>
                                        </td>
                                        <td style="padding: 1rem; color: var(--primary); font-weight: 600;"><?php echo number_format($event['orders_count']); ?></td>
                                        <td style="padding: 1rem; color: var(--success); font-weight: 600;">LKR <?php echo number_format(($event['revenue'] ?: 0) / 100, 2); ?></td>
                                        <td style="padding: 1rem;">
                                            <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-sm btn-outline" style="font-size: 0.75rem; padding: 0.25rem 0.75rem;">Edit</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php else: ?>
                <div class="card" style="text-align: center; padding: 2.5rem; background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                    <div style="font-size: 3rem; margin-bottom: 1rem; color: var(--gray-400);">📅</div>
                    <h3 style="color: var(--primary); margin-bottom: 1rem;">No Events Yet</h3>
                    <p style="color: var(--gray-600); margin-bottom: 1.5rem;">Start by creating your first event!</p>
                    <a href="event_create.php" class="btn btn-primary">Create Event</a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
