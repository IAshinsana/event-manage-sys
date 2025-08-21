<?php
$page_title = "Ticket Editor";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;
$ticket = null;
$event = null;
$error = '';
$success = '';

// Get ticket details if editing
if ($ticket_id) {
    $ticket_sql = "SELECT tt.*, e.title as event_title FROM ticket_types tt JOIN events e ON tt.event_id = e.id WHERE tt.id = $ticket_id";
    $ticket_result = $conn->query($ticket_sql);
    if ($ticket_result && $ticket_result->num_rows > 0) {
        $ticket = $ticket_result->fetch_assoc();
        $event_id = $ticket['event_id'];
        $page_title = "Edit Ticket Type";
    } else {
        header('Location: events_list.php');
        exit();
    }
}

// Get event details
if ($event_id) {
    $event_sql = "SELECT * FROM events WHERE id = $event_id";
    $event_result = $conn->query($event_sql);
    if ($event_result && $event_result->num_rows > 0) {
        $event = $event_result->fetch_assoc();
    } else {
        header('Location: events_list.php');
        exit();
    }
} else {
    header('Location: events_list.php');
    exit();
}

if (!$ticket_id) {
    $page_title = "Add Ticket Type";
}

// Handle form submission
if ($_POST) {
    $name = $_POST['name'];
    $price = (float)$_POST['price'];
    $price_cents = (int)($price * 100);
    $currency = $_POST['currency'];
    $qty_total = (int)$_POST['qty_total'];
    
    if ($name && $price >= 0 && $qty_total >= 0) {
        if ($ticket_id) {
            // Update existing ticket type
            $sql = "UPDATE ticket_types SET 
                    name = '$name',
                    price_cents = $price_cents,
                    currency = '$currency',
                    qty_total = $qty_total
                    WHERE id = $ticket_id";
        } else {
            // Create new ticket type
            $sql = "INSERT INTO ticket_types (event_id, name, price_cents, currency, qty_total) 
                   VALUES ($event_id, '$name', $price_cents, '$currency', $qty_total)";
        }
        
        if ($conn->query($sql)) {
            if (!$ticket_id) {
                header("Location: tickets_list.php?event_id=$event_id");
                exit();
            } else {
                $success = 'Ticket type updated successfully!';
                // Refresh ticket data
                $ticket_result = $conn->query("SELECT * FROM ticket_types WHERE id = $ticket_id");
                $ticket = $ticket_result->fetch_assoc();
            }
        } else {
            $error = 'Failed to save ticket type';
        }
    } else {
        $error = 'Please fill in all required fields';
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1><?php echo $ticket_id ? 'Edit Ticket Type' : 'Add Ticket Type'; ?></h1>
        <a href="tickets_list.php?event_id=<?php echo $event_id; ?>" class="btn btn-outline">← Back to Tickets</a>
    </div>
    
    <!-- Event Info -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h3><?php echo $event['title']; ?></h3>
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
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div style="max-width: 600px;">
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h3>Ticket Details</h3>
                
                <div class="form-group">
                    <label for="name">Ticket Name *</label>
                    <input type="text" id="name" name="name" class="form-control" 
                           value="<?php echo $ticket ? $ticket['name'] : ''; ?>" 
                           placeholder="e.g. General Admission, VIP, Early Bird" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="price">Price *</label>
                        <input type="number" id="price" name="price" class="form-control" 
                               step="0.01" min="0"
                               value="<?php echo $ticket ? number_format($ticket['price_cents'] / 100, 2, '.', '') : ''; ?>" 
                               placeholder="0.00" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="currency">Currency</label>
                        <select id="currency" name="currency" class="form-control">
                            <option value="LKR" <?php echo (!$ticket || $ticket['currency'] === 'LKR') ? 'selected' : ''; ?>>LKR</option>
                            <option value="USD" <?php echo ($ticket && $ticket['currency'] === 'USD') ? 'selected' : ''; ?>>USD</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="qty_total">Total Quantity *</label>
                    <input type="number" id="qty_total" name="qty_total" class="form-control" 
                           min="0"
                           value="<?php echo $ticket ? $ticket['qty_total'] : ''; ?>" 
                           placeholder="Number of tickets available" required>
                    <small style="color: #666;">Total number of tickets available for this type</small>
                </div>
                
                <?php if ($ticket && $ticket['qty_sold'] > 0): ?>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
                        <strong>Sales Information:</strong><br>
                        <?php echo number_format($ticket['qty_sold']); ?> tickets already sold<br>
                        <small style="color: #666;">You can increase the total quantity, but cannot reduce it below the sold amount.</small>
                    </div>
                <?php endif; ?>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <button type="submit" class="btn btn-success" style="padding: 0.75rem 2rem;">
                        <?php echo $ticket_id ? 'Update Ticket Type' : 'Create Ticket Type'; ?>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
// Prevent reducing quantity below sold amount
<?php if ($ticket && $ticket['qty_sold'] > 0): ?>
document.getElementById('qty_total').addEventListener('change', function() {
    const soldTickets = <?php echo $ticket['qty_sold']; ?>;
    if (parseInt(this.value) < soldTickets) {
        alert('Cannot reduce quantity below ' + soldTickets + ' (already sold tickets)');
        this.value = soldTickets;
    }
});
<?php endif; ?>
</script>

<?php include '../includes/footer.php'; ?>
