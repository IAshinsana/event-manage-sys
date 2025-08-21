<?php
$page_title = "Manage Events";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

// Get events
$events_sql = "SELECT e.*, 
               (SELECT COUNT(*) FROM ticket_types tt WHERE tt.event_id = e.id) AS ticket_types_count,
               (SELECT COALESCE(SUM(tt.qty_total), 0) FROM ticket_types tt WHERE tt.event_id = e.id) AS total_tickets,
               (SELECT COALESCE(SUM(tt.qty_sold), 0) FROM ticket_types tt WHERE tt.event_id = e.id) AS sold_tickets
               FROM events e 
               ORDER BY e.starts_at DESC";
$events_result = $conn->query($events_sql);
?>

<link rel="stylesheet" href="../assets/css/admin_events_list.css">

<div class="container admin-events-container">
    <div class="admin-events-header">
        <h1>🎉 Manage Events</h1>
        <div>
            <a href="event_edit.php" class="btn btn-success">+ Create New Event</a>
            <a href="index.php" class="btn btn-outline">← Dashboard</a>
        </div>
    </div>
    
    <?php if ($events_result && $events_result->num_rows > 0): ?>
        <div class="admin-section">
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Event</th>
                            <th>Image</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Tickets</th>
                            <th>Sales</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($event = $events_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <div>
                                        <strong><?php echo $event['title']; ?></strong>
                                        <?php if ($event['category']): ?>
                                            <div style="font-size: 0.8rem; color: #666;"><?php echo $event['category']; ?></div>
                                        <?php endif; ?>
                                        <?php if ($event['venue']): ?>
                                            <div style="font-size: 0.8rem; color: #666;">📍 <?php echo $event['venue']; ?></div>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td>
                                    <?php if ($event['image_path']): ?>
                                        <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                                             alt="Event Image" 
                                             style="width: 60px; height: 40px; object-fit: cover; border-radius: 4px;">
                                    <?php else: ?>
                                        <span style="color: #999; font-size: 0.8rem;">No image</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($event['starts_at'])); ?><br>
                                    <small style="color: #666;"><?php echo date('g:i A', strtotime($event['starts_at'])); ?></small>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $event['status']; ?>">
                                        <?php echo ucfirst($event['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($event['ticket_types_count'] > 0): ?>
                                        <?php echo $event['ticket_types_count']; ?> type(s)<br>
                                        <small style="color: #666;"><?php echo number_format($event['total_tickets'] ?: 0); ?> total</small>
                                    <?php else: ?>
                                        <span style="color: #dc3545;">No tickets</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($event['total_tickets'] > 0): ?>
                                        <?php echo number_format($event['sold_tickets'] ?: 0); ?> / <?php echo number_format($event['total_tickets']); ?><br>
                                        <small style="color: #666;">
                                            <?php echo $event['total_tickets'] > 0 ? round(($event['sold_tickets'] ?: 0) / $event['total_tickets'] * 100, 1) : 0; ?>% sold
                                        </small>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="admin-events-actions">
                                        <a href="../event_view.php?id=<?php echo $event['id']; ?>" class="btn btn-outline admin-events-action-btn">
                                            View
                                        </a>
                                        <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn btn-primary admin-events-action-btn">
                                            Edit
                                        </a>
                                        <a href="tickets_list.php?event_id=<?php echo $event['id']; ?>" class="btn btn-success admin-events-action-btn">
                                            Tickets
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">
            <h3>No Events Found</h3>
            <p style="color: #666; margin-bottom: 2rem;">Get started by creating your first event.</p>
            <a href="event_edit.php" class="btn btn-success">+ Create New Event</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
