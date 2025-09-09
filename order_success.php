<?php
$page_title = "Order Success";
include 'includes/header.php';
include 'includes/db.php';
require_login();

$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

if (!$order_id) {
    header('Location: events.php');
    exit();
}

// Get order details
$order_sql = "SELECT o.*, e.title as event_title, e.starts_at 
              FROM orders o 
              JOIN events e ON o.event_id = e.id 
              WHERE o.id = $order_id AND o.user_id = " . $_SESSION['user_id'];
$order_result = $conn->query($order_sql);

if (!$order_result || $order_result->num_rows === 0) {
    header('Location: events.php');
    exit();
}

$order = $order_result->fetch_assoc();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <div style="background: #28a745; color: white; width: 80px; height: 80px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1rem; font-size: 2rem;">
            ✓
        </div>
        <h1>Order Confirmed!</h1>
        <p style="color: #666; font-size: 1.1rem;">
            Your order has been successfully placed. Order #<?php echo $order_id; ?>
        </p>
    </div>
    
    <div style="max-width: 600px; margin: 0 auto;">
        <!-- Order Details -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3>Order Details</h3>
            
            <div style="margin-top: 1.5rem;">
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span><strong>Order Number:</strong></span>
                    <span>#<?php echo $order_id; ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span><strong>Event:</strong></span>
                    <span><?php echo $order['event_title']; ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span><strong>Event Date:</strong></span>
                    <span><?php echo date('F j, Y - g:i A', strtotime($order['starts_at'])); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span><strong>Total Amount:</strong></span>
                    <span style="font-weight: bold; color: #007bff;">LKR <?php echo number_format($order['total_cents'] / 100, 2); ?></span>
                </div>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 1rem;">
                    <span><strong>Status:</strong></span>
                    <span style="color: #dc3545; font-weight: bold;">
                        <?php echo ucfirst($order['status']); ?>
                    </span>
                </div>
                
                <div style="display: flex; justify-content: space-between;">
                    <span><strong>Order Date:</strong></span>
                    <span><?php echo date('F j, Y - g:i A', strtotime($order['created_at'])); ?></span>
                </div>
            </div>
        </div>
        
        <!-- Payment Information -->
        <div style="background: #fff3cd; border: 1px solid #ffeaa7; padding: 1.5rem; border-radius: 10px; margin-bottom: 2rem;">
            <h4 style="color: #856404; margin-bottom: 1rem;">💰 Payment Information</h4>
            <p style="color: #856404; margin: 0;">
                This order is currently <strong>pending payment</strong>. An admin will mark your order as paid 
                once payment is received. Your tickets will be available for printing after payment confirmation.
            </p>
        </div>
        
        <!-- Next Steps -->
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
            <h3>What's Next?</h3>
            
            <div style="margin-top: 1.5rem;">
                <div style="display: flex; margin-bottom: 1rem;">
                    <div style="background: #007bff; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.9rem;">1</div>
                    <div>
                        <strong>Payment Processing</strong>
                        <div style="color: #666; font-size: 0.9rem;">Wait for admin to confirm your payment</div>
                    </div>
                </div>
                
                <div style="display: flex; margin-bottom: 1rem;">
                    <div style="background: #007bff; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.9rem;">2</div>
                    <div>
                        <strong>Receive Tickets</strong>
                        <div style="color: #666; font-size: 0.9rem;">Access your tickets from "My Tickets" page</div>
                    </div>
                </div>
                
                <div style="display: flex;">
                    <div style="background: #007bff; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1rem; font-size: 0.9rem;">3</div>
                    <div>
                        <strong>Attend Event</strong>
                        <div style="color: #666; font-size: 0.9rem;">Show your tickets at the event entrance</div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <a href="orders_my.php" class="btn btn-primary" style="margin-right: 1rem;">View My Orders</a>
            <a href="events.php" class="btn btn-outline">Browse More Events</a>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
