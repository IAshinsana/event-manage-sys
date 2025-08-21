<?php
$page_title = "Select Tickets";
include 'includes/header.php';
include 'includes/db.php';
require_login();

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if (!$event_id) {
    header('Location: events.php');
    exit();
}

// Get event details
$event_sql = "SELECT * FROM events WHERE id = $event_id AND status = 'published'";
$event_result = $conn->query($event_sql);

if (!$event_result || $event_result->num_rows === 0) {
    header('Location: events.php');
    exit();
}

$event = $event_result->fetch_assoc();

// Get available ticket types
$tickets_sql = "SELECT * FROM ticket_types WHERE event_id = $event_id ORDER BY price_cents ASC";
$tickets_result = $conn->query($tickets_sql);

$error = '';

if ($_POST) {
    $selected_tickets = [];
    $total_tickets = 0;
    
    if ($tickets_result) {
        $tickets_result->data_seek(0); // Reset result pointer
        while ($ticket = $tickets_result->fetch_assoc()) {
            $qty = isset($_POST['qty_' . $ticket['id']]) ? (int)$_POST['qty_' . $ticket['id']] : 0;
            if ($qty > 0) {
                $available = max(0, $ticket['qty_total'] - $ticket['qty_sold']);
                if ($qty > $available) {
                    $error = "Only $available tickets available for " . $ticket['name'];
                    break;
                }
                $selected_tickets[] = [
                    'ticket_type_id' => $ticket['id'],
                    'name' => $ticket['name'],
                    'price' => $ticket['price_cents'],
                    'qty' => $qty
                ];
                $total_tickets += $qty;
            }
        }
    }
    
    if (!$error) {
        if (empty($selected_tickets)) {
            $error = 'Please select at least one ticket';
        } else {
            // Store in session and redirect
            $_SESSION['booking'] = [
                'event_id' => $event_id,
                'tickets' => $selected_tickets,
                'total_tickets' => $total_tickets
            ];
            header('Location: register_attendees.php');
            exit();
        }
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Select Tickets for <?php echo $event['title']; ?></h1>
    
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <div style="color: #666;">
            📅 <?php echo date('F j, Y - g:i A', strtotime($event['starts_at'])); ?>
            <?php if ($event['venue']): ?>
                | 📍 <?php echo $event['venue']; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h2>Available Tickets</h2>
            
            <?php if ($tickets_result && $tickets_result->num_rows > 0): ?>
                <?php $tickets_result->data_seek(0); // Reset result pointer ?>
                <div style="margin-top: 1.5rem;">
                    <?php while ($ticket = $tickets_result->fetch_assoc()): ?>
                        <?php $available = max(0, $ticket['qty_total'] - $ticket['qty_sold']); ?>
                        <div style="border: 1px solid #eee; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                            <div style="display: flex; justify-content: space-between; align-items: center;">
                                <div style="flex: 1;">
                                    <h4><?php echo $ticket['name']; ?></h4>
                                    <div style="color: #666; margin: 0.5rem 0;">
                                        LKR <?php echo number_format($ticket['price_cents'] / 100, 2); ?>
                                    </div>
                                    <div style="font-size: 0.9rem; color: <?php echo $available > 0 ? '#28a745' : '#dc3545'; ?>">
                                        <?php if ($available > 0): ?>
                                            <?php echo $available; ?> tickets available
                                        <?php else: ?>
                                            Sold Out
                                        <?php endif; ?>
                                    </div>
                                </div>
                                
                                <div style="display: flex; align-items: center; gap: 1rem;">
                                    <label for="qty_<?php echo $ticket['id']; ?>" style="margin: 0;">Quantity:</label>
                                    <select name="qty_<?php echo $ticket['id']; ?>" id="qty_<?php echo $ticket['id']; ?>" class="form-control" style="width: 80px;" <?php echo $available === 0 ? 'disabled' : ''; ?>>
                                        <?php for ($i = 0; $i <= min(10, $available); $i++): ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
                
                <div style="margin-top: 2rem; text-align: center;">
                    <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem;">Continue to Details</button>
                </div>
            <?php else: ?>
                <p>No tickets available for this event.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
