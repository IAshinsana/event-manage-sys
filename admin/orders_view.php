<?php
$page_title = "Order Details";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$order_id) {
    header('Location: orders_list.php');
    exit();
}

// Get order details
$order_sql = "SELECT o.*, u.name as user_name, u.username, e.title as event_title, e.starts_at, e.venue 
              FROM orders o 
              JOIN users u ON o.user_id = u.id 
              JOIN events e ON o.event_id = e.id 
              WHERE o.id = $order_id";
$order_result = $conn->query($order_sql);

if (!$order_result || $order_result->num_rows === 0) {
    header('Location: orders_list.php');
    exit();
}

$order = $order_result->fetch_assoc();

// Get order items
$items_sql = "SELECT oi.*, tt.name as ticket_name, tt.price_cents as original_price 
              FROM order_items oi 
              JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
              WHERE oi.order_id = $order_id";
$items_result = $conn->query($items_sql);

// Get attendees
$attendees_sql = "SELECT a.*, oi.id as item_id, tt.name as ticket_name 
                  FROM attendees a 
                  JOIN order_items oi ON a.order_item_id = oi.id 
                  JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
                  WHERE oi.order_id = $order_id 
                  ORDER BY a.id";
$attendees_result = $conn->query($attendees_sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Order #<?php echo $order_id; ?></h1>
        <a href="orders_list.php" class="btn btn-outline">← Back to Orders</a>
    </div>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Order Details -->
        <div>
            <!-- Order Information -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3>Order Information</h3>
                
                <div style="margin-top: 1.5rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <strong>Order ID:</strong> #<?php echo $order['id']; ?><br>
                        <strong>Customer:</strong> <?php echo $order['user_name']; ?> (<?php echo $order['username']; ?>)<br>
                        <strong>Order Date:</strong> <?php echo date('F j, Y g:i A', strtotime($order['created_at'])); ?>
                    </div>
                    <div>
                        <strong>Status:</strong> 
                        <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.9rem; font-weight: bold; 
                                   background: <?php echo $order['status'] === 'paid' ? '#d4edda' : ($order['status'] === 'pending' ? '#fff3cd' : '#f8d7da'); ?>; 
                                   color: <?php echo $order['status'] === 'paid' ? '#155724' : ($order['status'] === 'pending' ? '#856404' : '#721c24'); ?>;">
                            <?php echo ucfirst($order['status']); ?>
                        </span><br>
                        <strong>Total Amount:</strong> LKR <?php echo number_format($order['total_cents'] / 100, 2); ?>
                    </div>
                </div>
            </div>
            
            <!-- Event Information -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3>Event Information</h3>
                
                <div style="margin-top: 1.5rem;">
                    <h4><?php echo $order['event_title']; ?></h4>
                    <div style="color: #666; margin-top: 0.5rem;">
                        📅 <?php echo date('F j, Y - g:i A', strtotime($order['starts_at'])); ?>
                        <?php if ($order['venue']): ?>
                            <br>📍 <?php echo $order['venue']; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Order Items -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                <h3>Order Items</h3>
                
                <?php if ($items_result && $items_result->num_rows > 0): ?>
                    <div style="margin-top: 1.5rem;">
                        <?php while ($item = $items_result->fetch_assoc()): ?>
                            <div style="border: 1px solid #eee; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                                <div style="display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <h4><?php echo $item['ticket_name']; ?></h4>
                                        <div style="color: #666;">
                                            Quantity: <?php echo $item['qty']; ?> × LKR <?php echo number_format($item['unit_price_cents'] / 100, 2); ?>
                                        </div>
                                    </div>
                                    <div style="text-align: right;">
                                        <div style="font-size: 1.1rem; font-weight: bold;">
                                            LKR <?php echo number_format(($item['unit_price_cents'] * $item['qty']) / 100, 2); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Attendees -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h3>Attendees</h3>
                
                <?php if ($attendees_result && $attendees_result->num_rows > 0): ?>
                    <div style="margin-top: 1.5rem;">
                        <?php while ($attendee = $attendees_result->fetch_assoc()): ?>
                            <div style="border: 1px solid #eee; border-radius: 8px; padding: 1.5rem; margin-bottom: 1rem;">
                                <div style="display: grid; grid-template-columns: 1fr auto; gap: 1rem; align-items: center;">
                                    <div>
                                        <h4><?php echo $attendee['full_name']; ?></h4>
                                        <div style="color: #666; margin-bottom: 0.5rem;">
                                            Email: <?php echo $attendee['email']; ?>
                                        </div>
                                        <div style="color: #666; margin-bottom: 0.5rem;">
                                            Ticket: <?php echo $attendee['ticket_name']; ?>
                                        </div>
                                        <div style="font-family: monospace; color: #007bff;">
                                            Code: <?php echo $attendee['ticket_code']; ?>
                                        </div>
                                        <?php if ($attendee['checked_in_at']): ?>
                                            <div style="color: #28a745; font-size: 0.9rem; margin-top: 0.5rem;">
                                                ✅ Checked in: <?php echo date('M j, Y g:i A', strtotime($attendee['checked_in_at'])); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <div style="text-align: center;">
                                        <?php if ($order['status'] === 'paid'): ?>
                                            <div style="background: #28a745; color: white; padding: 0.5rem; border-radius: 5px; font-size: 0.9rem;">
                                                Ticket Valid
                                            </div>
                                        <?php else: ?>
                                            <div style="background: #dc3545; color: white; padding: 0.5rem; border-radius: 5px; font-size: 0.9rem;">
                                                Payment Pending
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Actions Sidebar -->
        <div>
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); height: fit-content;">
                <h3>Actions</h3>
                
                <div style="margin-top: 1.5rem;">
                    <?php if ($order['status'] === 'pending'): ?>
                        <form method="POST" action="orders_list.php" onsubmit="return confirm('Mark this order as paid?')" style="margin-bottom: 1rem;">
                            <input type="hidden" name="order_id" value="<?php echo $order_id; ?>">
                            <button type="submit" name="mark_paid" class="btn btn-success" style="width: 100%;">
                                💰 Mark as Paid
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <a href="../event_view.php?id=<?php echo $order['event_id']; ?>" class="btn btn-outline" style="width: 100%; margin-bottom: 1rem;">
                        👁️ View Event
                    </a>
                    
                    <div style="border-top: 1px solid #eee; padding-top: 1rem; margin-top: 1rem;">
                        <h4>Order Summary</h4>
                        <div style="margin-top: 1rem;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Total Amount:</span>
                                <strong>LKR <?php echo number_format($order['total_cents'] / 100, 2); ?></strong>
                            </div>
                            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                                <span>Status:</span>
                                <span style="text-transform: capitalize;"><?php echo $order['status']; ?></span>
                            </div>
                            <div style="display: flex; justify-content: space-between;">
                                <span>Total Tickets:</span>
                                <span><?php echo $attendees_result->num_rows; ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
