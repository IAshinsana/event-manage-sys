<?php
$page_title = "Manage Tickets";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

if (!is_approved_coordinator()) {
    header('Location: dashboard.php?error=not_approved');
    exit;
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

// Verify this event belongs to the coordinator
$event_sql = "SELECT * FROM events WHERE id = $event_id AND created_by = $user_id";
$event_result = $conn->query($event_sql);

if (!$event_result || $event_result->num_rows === 0) {
    header('Location: events_list.php?error=event_not_found');
    exit;
}

$event = $event_result->fetch_assoc();

// Handle form submissions
$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add_ticket') {
            $name = trim($_POST['name']);
            $description = isset($_POST['description']) ? trim($_POST['description']) : '';
            $price_cents = intval($_POST['price'] * 100);
            $quantity = intval($_POST['quantity']);
            $max_per_order = isset($_POST['max_per_order']) ? intval($_POST['max_per_order']) : 10;
            
            if (empty($name) || $price_cents < 0 || $quantity <= 0) {
                $error_message = "Please fill in all required fields with valid values.";
            } else {
                // Use your actual database columns
                $insert_sql = "INSERT INTO ticket_types (event_id, name, price_cents, qty_total) 
                              VALUES ($event_id, '$name', $price_cents, $quantity)";
                
                if ($conn->query($insert_sql)) {
                    $success_message = "Ticket type added successfully!";
                } else {
                    $error_message = "Failed to add ticket type: " . $conn->error;
                }
            }
        } elseif ($_POST['action'] === 'update_ticket') {
            $ticket_id = intval($_POST['ticket_id']);
            $name = trim($_POST['name']);
            $price_cents = intval($_POST['price'] * 100);
            $quantity = intval($_POST['quantity']);
            
            // Update with actual database columns
            $update_sql = "UPDATE ticket_types SET 
                          name = '$name',
                          price_cents = $price_cents,
                          qty_total = $quantity
                          WHERE id = $ticket_id AND event_id = $event_id";
            
            if ($conn->query($update_sql)) {
                $success_message = "Ticket type updated successfully!";
            } else {
                $error_message = "Failed to update ticket type.";
            }
        } elseif ($_POST['action'] === 'delete_ticket') {
            $ticket_id = intval($_POST['ticket_id']);
            
            // For now, allow deletion (we can add order checking later when order system is properly set up)
            $delete_sql = "DELETE FROM ticket_types WHERE id = $ticket_id AND event_id = $event_id";
            if ($conn->query($delete_sql)) {
                $success_message = "Ticket type deleted successfully!";
            } else {
                $error_message = "Failed to delete ticket type.";
            }
        }
    }
}

// Get ticket types for this event with correct column names
$tickets_sql = "
    SELECT 
        tt.*,
        COALESCE(s.sold_qty, 0) AS sold,
        COALESCE(s.revenue_cents, 0) AS revenue
    FROM ticket_types tt
    LEFT JOIN (
        SELECT 
            oi.ticket_type_id,
            SUM(oi.qty) AS sold_qty,
            SUM(oi.qty * oi.unit_price_cents) AS revenue_cents
        FROM order_items oi
        JOIN orders o 
            ON o.id = oi.order_id 
           AND o.status = 'paid'
        GROUP BY oi.ticket_type_id
    ) s ON s.ticket_type_id = tt.id
    WHERE tt.event_id = $event_id
    ORDER BY tt.id ASC
";
$tickets_result = $conn->query($tickets_sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <div>
            <h1>🎫 Manage Tickets</h1>
            <p style="color: #666; margin: 0.5rem 0;"><?php echo $event['title']; ?></p>
        </div>
        <div>
            <a href="events_list.php" class="btn btn-outline">← Back to Events</a>
        </div>
    </div>

    <?php if ($success_message): ?>
        <div class="alert alert-success">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div class="alert alert-error">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <!-- Event Status Check -->
    <?php if ($event['approval_status'] !== 'approved'): ?>
        <div class="alert alert-warning">
            <strong>⚠️ Event Not Approved</strong><br>
            Your event must be approved by an administrator before ticket sales can begin.
            Current Status: <strong><?php echo ucfirst($event['approval_status']); ?></strong>
        </div>
    <?php endif; ?>

    <!-- Add New Ticket Type -->
    <div class="admin-section" style="margin-bottom: 2rem;">
        <h2>➕ Add New Ticket Type</h2>
        <form method="POST">
            <input type="hidden" name="action" value="add_ticket">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Ticket Name *</label>
                    <input type="text" name="name" class="form-input" required placeholder="e.g., General Admission">
                </div>
                <div class="form-group">
                    <label class="form-label">Price (LKR) *</label>
                    <input type="number" name="price" class="form-input" step="0.01" min="0" required placeholder="0.00">
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Available Quantity *</label>
                    <input type="number" name="quantity" class="form-input" min="1" required placeholder="100">
                </div>
            </div>
            <button type="submit" class="btn btn-primary">💾 Add Ticket Type</button>
        </form>
    </div>

    <!-- Existing Ticket Types -->
    <div class="admin-section">
        <h2>🎫 Current Ticket Types</h2>
        <?php if ($tickets_result && $tickets_result->num_rows > 0): ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Ticket Type</th>
                            <th>Price</th>
                            <th>Available</th>
                            <th>Sold</th>
                            <th>Revenue</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($ticket = $tickets_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong><?php echo $ticket['name']; ?></strong>
                                </td>
                                <td>
                                    <strong>LKR <?php echo number_format($ticket['price_cents'] / 100, 2); ?></strong>
                                </td>
                                <td>
                                    <span style="color: #007bff;"><?php echo $ticket['qty_total']; ?></span>
                                </td>
                                <td>
                                    <span style="color: #28a745;"><?php echo $ticket['sold']; ?></span>
                                </td>
                                <td>
                                    <strong style="color: #17a2b8;">LKR <?php echo number_format($ticket['revenue'] / 100, 2); ?></strong>
                                </td>
                                <td>
                                    <button onclick="editTicket(<?php echo htmlspecialchars(json_encode($ticket)); ?>)" 
                                            class="btn btn-outline" style="margin-right: 0.5rem;">✏️ Edit</button>
                                    <?php if ($ticket['sold'] == 0): ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this ticket type?')">
                                            <input type="hidden" name="action" value="delete_ticket">
                                            <input type="hidden" name="ticket_id" value="<?php echo $ticket['id']; ?>">
                                            <button type="submit" class="btn btn-outline" style="color: #dc3545; border-color: #dc3545;">🗑️ Delete</button>
                                        </form>
                                    <?php else: ?>
                                        <span style="color: #666; font-size: 0.8rem;">Cannot delete - sold tickets</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎫</div>
                <h3>No Ticket Types Yet</h3>
                <p>Create your first ticket type above to start selling tickets for this event.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Edit Ticket Modal -->
<div id="editModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="background: white; margin: 5% auto; padding: 2rem; width: 80%; max-width: 600px; border-radius: 8px;">
        <h3>✏️ Edit Ticket Type</h3>
        <form method="POST" id="editForm">
            <input type="hidden" name="action" value="update_ticket">
            <input type="hidden" name="ticket_id" id="edit_ticket_id">
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Ticket Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-input" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Price (LKR) *</label>
                    <input type="number" name="price" id="edit_price" class="form-input" step="0.01" min="0" required>
                </div>
            </div>
            <div style="display: grid; grid-template-columns: 1fr; gap: 1rem; margin-bottom: 1rem;">
                <div class="form-group">
                    <label class="form-label">Available Quantity *</label>
                    <input type="number" name="quantity" id="edit_quantity" class="form-input" min="1" required>
                </div>
            </div>
            <div style="text-align: right;">
                <button type="button" onclick="closeEditModal()" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary">💾 Update Ticket</button>
            </div>
        </form>
    </div>
</div>

<script>
function editTicket(ticket) {
    document.getElementById('edit_ticket_id').value = ticket.id;
    document.getElementById('edit_name').value = ticket.name;
    document.getElementById('edit_price').value = (ticket.price_cents / 100).toFixed(2);
    document.getElementById('edit_quantity').value = ticket.qty_total;
    
    document.getElementById('editModal').style.display = 'block';
}

function closeEditModal() {
    document.getElementById('editModal').style.display = 'none';
}

// Close modal when clicking outside
document.getElementById('editModal').onclick = function(e) {
    if (e.target === this) {
        closeEditModal();
    }
}
</script>

<?php include '../includes/footer.php'; ?>
