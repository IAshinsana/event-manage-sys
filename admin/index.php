<?php
$page_title = "Admin Dashboard";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

// Get statistics
$stats_sql = "SELECT 
    (SELECT COUNT(*) FROM users WHERE role != 'admin') as total_users,
    (SELECT COUNT(*) FROM events WHERE status = 'published') as published_events,
    (SELECT COUNT(*) FROM orders WHERE status = 'pending') as pending_orders,
    (SELECT COUNT(*) FROM orders WHERE status = 'paid') as paid_orders,
    (SELECT SUM(total_cents) FROM orders WHERE status = 'paid') as total_revenue";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🏠 Admin Dashboard</h1>
    
    <!-- Statistics Cards -->
    <div class="admin-stats">
        
        <div class="admin-card">
            <div class="icon" style="color: #007bff;">👥</div>
            <div class="number">
                <?php echo number_format($stats['total_users']); ?>
            </div>
            <div class="label">Total Users</div>
        </div>
        
        <div class="admin-card">
            <div class="icon" style="color: #28a745;">🎉</div>
            <div class="number">
                <?php echo number_format($stats['published_events']); ?>
            </div>
            <div class="label">Published Events</div>
        </div>
        
        <div class="admin-card">
            <div class="icon" style="color: #ffc107;">⏳</div>
            <div class="number">
                <?php echo number_format($stats['pending_orders']); ?>
            </div>
            <div class="label">Pending Orders</div>
        </div>
        
        <div class="admin-card">
            <div class="icon" style="color: #dc3545;">💰</div>
            <div class="number">
                LKR <?php echo number_format(($stats['total_revenue'] ?: 0) / 100, 2); ?>
            </div>
            <div class="label">Total Revenue</div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="admin-section">
        <h2>⚡ Quick Actions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            
            <a href="event_edit.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">➕</div>
                    <h4>Create New Event</h4>
                    <p style="color: #666; font-size: 0.9rem;">Add a new event with tickets</p>
                </div>
            </a>
            
            <a href="orders_list.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">📋</div>
                    <h4>Manage Orders</h4>
                    <p style="color: #666; font-size: 0.9rem;">View and update order status</p>
                </div>
            </a>
            
            <a href="events_list.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">🎫</div>
                    <h4>Manage Events</h4>
                    <p style="color: #666; font-size: 0.9rem;">Edit events and ticket types</p>
                </div>
            </a>
            
            <a href="users_list.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">👤</div>
                    <h4>Manage Users</h4>
                    <p style="color: #666; font-size: 0.9rem;">View and edit user accounts</p>
                </div>
            </a>
            
            <a href="coordinator_applications.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">🎯</div>
                    <h4>Coordinator Applications</h4>
                    <p style="color: #666; font-size: 0.9rem;">Review coordinator applications</p>
                </div>
            </a>
            
            <a href="../archived_events.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">🗄️</div>
                    <h4>Archived Events</h4>
                    <p style="color: #666; font-size: 0.9rem;">View and manage archived events</p>
                </div>
            </a>
            
            <a href="events_approval.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">✅</div>
                    <h4>Event Approvals</h4>
                    <p style="color: #666; font-size: 0.9rem;">Approve coordinator events</p>
                </div>
            </a>
                </div>
            </a>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="admin-section">
        <h2>📈 Recent Orders</h2>
        
        <?php
        $recent_orders_sql = "SELECT o.*, u.name as user_name, e.title as event_title 
                             FROM orders o 
                             JOIN users u ON o.user_id = u.id 
                             JOIN events e ON o.event_id = e.id 
                             ORDER BY o.created_at DESC 
                             LIMIT 5";
        $recent_orders_result = $conn->query($recent_orders_sql);
        ?>
        
        <?php if ($recent_orders_result && $recent_orders_result->num_rows > 0): ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $recent_orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo $order['user_name']; ?></td>
                                <td><?php echo $order['event_title']; ?></td>
                                <td>LKR <?php echo number_format($order['total_cents'] / 100, 2); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['status']; ?>">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($order['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div style="text-align: center; margin-top: 1rem;">
                <a href="orders_list.php" class="btn btn-primary">View All Orders</a>
            </div>
        <?php else: ?>
            <p style="text-align: center; color: #666;">No recent orders</p>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
