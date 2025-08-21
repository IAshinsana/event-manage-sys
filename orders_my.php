<?php
$page_title = "My Orders";
include 'includes/header.php';
include 'includes/db.php';
require_login();

// Get user's orders
$orders_sql = "SELECT o.*, e.title as event_title, e.starts_at, e.venue 
               FROM orders o 
               JOIN events e ON o.event_id = e.id 
               WHERE o.user_id = " . $_SESSION['user_id'] . " 
               ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_sql);
?>

<link rel="stylesheet" href="assets/css/orders_my.css">

<div class="container orders-container">
    <div class="orders-header">
        <h1 class="orders-title">📋 My Orders</h1>
        <p class="orders-subtitle">Track your event bookings and payments</p>
    </div>
    
    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
        <div style="margin-top: 1.5rem;">
            <?php while ($order = $orders_result->fetch_assoc()): ?>
                <div style="background: white; padding: 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    
                    <!-- Event Header -->
                    <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
                        <h3 style="margin-bottom: 0.5rem; color: #333; font-size: 1.8rem;">
                            <a href="event_view.php?id=<?php echo $order['event_id']; ?>" style="text-decoration: none; color: #333; transition: color 0.3s ease;">
                                <?php echo $order['event_title']; ?>
                            </a>
                        </h3>
                        <div style="color: #666; font-size: 1rem; margin-bottom: 0.5rem;">
                            📅 <?php echo date('F j, Y - g:i A', strtotime($order['starts_at'])); ?>
                            <?php if ($order['venue']): ?>
                                <br>📍 <?php echo $order['venue']; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <!-- Order Details -->
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; margin-bottom: 1.5rem;">
                        <div style="text-align: center; padding: 1.5rem; background: #f8f9fa; border-radius: 10px;">
                            <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Amount</div>
                            <div style="font-size: 2rem; font-weight: bold; color: #333;">
                                LKR <?php echo number_format($order['total_cents'] / 100, 2); ?>
                            </div>
                        </div>
                        
                        <div style="text-align: center; padding: 1.5rem; border-radius: 10px;
                                  background: <?php echo $order['status'] === 'paid' ? '#d4edda' : ($order['status'] === 'pending' ? '#fff3cd' : '#f8d7da'); ?>;">
                            <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 0.5px;">Order Status</div>
                            <div style="font-size: 1.5rem; font-weight: bold; 
                                      color: <?php echo $order['status'] === 'paid' ? '#155724' : ($order['status'] === 'pending' ? '#856404' : '#721c24'); ?>;">
                                <?php 
                                $status_icons = [
                                    'paid' => '✅ Paid',
                                    'pending' => '⏳ Pending',
                                    'cancelled' => '❌ Cancelled'
                                ];
                                echo $status_icons[$order['status']] ?? ucfirst($order['status']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <!-- Order Actions -->
                    <div style="border-top: 2px solid #f0f0f0; padding-top: 1.5rem; text-align: center;">
                        <div style="margin-bottom: 1.5rem; padding: 1rem; background: #f8f9fa; border-radius: 8px; display: inline-block;">
                            <div style="font-size: 0.9rem; color: #666; margin-bottom: 0.5rem;">Order Information</div>
                            <div style="font-weight: bold; color: #333;">
                                Order #<?php echo $order['id']; ?> • Placed on <?php echo date('M j, Y', strtotime($order['created_at'])); ?>
                            </div>
                        </div>
                        
                        <div>
                            <?php if ($order['status'] === 'paid'): ?>
                                <a href="tickets_my.php?order_id=<?php echo $order['id']; ?>" 
                                   class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1.1rem;">
                                    🎫 View My Tickets
                                </a>
                            <?php elseif ($order['status'] === 'pending'): ?>
                                <div style="margin-bottom: 1rem;">
                                    <div style="color: #856404; font-weight: bold; margin-bottom: 0.5rem;">⏳ Payment Pending</div>
                                    <div style="color: #666; font-size: 0.9rem;">Your tickets will be available once payment is confirmed</div>
                                </div>
                                <a href="event_view.php?id=<?php echo $order['event_id']; ?>" 
                                   class="btn btn-outline" style="padding: 0.8rem 2rem; font-size: 1rem;">
                                    📄 View Event Details
                                </a>
                            <?php else: ?>
                                <div style="color: #721c24; font-weight: bold; margin-bottom: 1rem;">❌ Order Cancelled</div>
                                <a href="events.php" 
                                   class="btn btn-outline" style="padding: 0.8rem 2rem; font-size: 1rem;">
                                    🎉 Browse Other Events
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 4rem 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; max-width: 500px; margin: 2rem auto;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">📋</div>
            <h3 style="color: #333; margin-bottom: 1rem; font-size: 1.8rem;">No Orders Yet</h3>
            <p style="color: #666; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.6;">
                You haven't placed any orders yet.<br>
                Start by browsing our amazing events!
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="events.php" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-size: 1rem;">
                    🎉 Browse Events
                </a>
                <a href="tickets_my.php" class="btn btn-outline" style="padding: 0.8rem 1.5rem; font-size: 1rem;">
                    🎫 View My Tickets
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
