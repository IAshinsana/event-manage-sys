<?php
$page_title = "Manage Tickets";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if (!$event_id) {
    header('Location: events_list.php');
    exit();
}

// Get event details
$event_sql = "SELECT * FROM events WHERE id = $event_id";
$event_result = $conn->query($event_sql);
$event = $event_result->fetch_assoc();

// Get ticket types
$tickets_sql = "SELECT * FROM ticket_types WHERE event_id = $event_id ORDER BY price_cents ASC";
$tickets_result = $conn->query($tickets_sql);

// Handle delete ticket type
if ($_POST && isset($_POST['delete_ticket'])) {
    $ticket_id = (int)$_POST['ticket_id'];
    $delete_sql = "DELETE FROM ticket_types WHERE id = $ticket_id";
    if ($conn->query($delete_sql)) {
        $success = 'Ticket type deleted successfully!';
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Manage Tickets - <?php echo $event['title']; ?></h1>
        <div>
            <a href="ticket_edit.php?event_id=<?php echo $event_id; ?>" class="btn btn-success">+ Add Ticket Type</a>
            <a href="event_edit.php?id=<?php echo $event_id; ?>" class="btn btn-outline">← Back to Event</a>
        </div>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <!-- Event Info -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center;">
            <div>
                <h3><?php echo $event['title']; ?></h3>
                <div style="color: #666;">
                    📅 <?php echo date('F j, Y - g:i A', strtotime($event['starts_at'])); ?>
                    <?php if ($event['venue']): ?>
                        | 📍 <?php echo $event['venue']; ?>
                    <?php endif; ?>
                </div>
            </div>
            <div style="text-align: right;">
                <div style="font-size: 0.9rem; color: #666;">Status</div>
                <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.9rem; font-weight: bold; 
                           background: <?php echo $event['status'] === 'published' ? '#d4edda' : ($event['status'] === 'draft' ? '#fff3cd' : '#f8d7da'); ?>; 
                           color: <?php echo $event['status'] === 'published' ? '#155724' : ($event['status'] === 'draft' ? '#856404' : '#721c24'); ?>;">
                    <?php echo ucfirst($event['status']); ?>
                </span>
            </div>
        </div>
    </div>
    
    <!-- Ticket Types -->
    <?php if ($tickets_result && $tickets_result->num_rows > 0): ?>
        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Ticket Type</th>
                            <th>Price</th>
                            <th>Availability</th>
                            <th>Sales</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $tickets_result->fetch_assoc()): ?>
                            <?php $available = max(0, $ticket['qty_total'] - $ticket['qty_sold']); ?>
                            <tr>
                                <td>
                                    <strong><?php echo $ticket['name']; ?></strong>
                                </td>
                                <td>
                                    <strong><?php echo $ticket['currency']; ?> <?php echo number_format($ticket['price_cents'] / 100, 2); ?></strong>
                                </td>
                                <td>
                                    <div><?php echo number_format($available); ?> available</div>
                                    <small style="color: #666;">of <?php echo number_format($ticket['qty_total']); ?> total</small>
                                </td>
                                <td>
                                    <div style="font-weight: bold;"><?php echo number_format($ticket['qty_sold']); ?> sold</div>
                                    <small style="color: #666;">
                                        <?php echo $ticket['qty_total'] > 0 ? round($ticket['qty_sold'] / $ticket['qty_total'] * 100, 1) : 0; ?>%
                                    </small>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="ticket_edit.php?id=<?php echo $ticket['id']; ?>" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                            Edit
                                        </a>
                                        
                                        <?php if ($ticket['qty_sold'] == 0): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Delete this ticket type?')">
                                                <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                                <button type="submit" name="delete_ticket" class="btn" style="background: #dc3545; color: white; font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                                    Delete
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button class="btn" style="background: #ccc; color: #666; font-size: 0.85rem; padding: 0.4rem 0.8rem;" disabled>
                                                Can't Delete
                                            </button>
                                        <?php endif; ?>
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
            <h3>No Ticket Types</h3>
            <p style="color: #666; margin-bottom: 2rem;">Create ticket types to allow people to book this event.</p>
            <a href="ticket_edit.php?event_id=<?php echo $event_id; ?>" class="btn btn-success">+ Add First Ticket Type</a>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
