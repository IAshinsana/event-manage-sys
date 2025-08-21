<?php
$page_title = "Manage Orders";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$filter_status = isset($_GET['status']) ? $_GET['status'] : '';

// Handle mark as paid
if ($_POST && isset($_POST['mark_paid'])) {
    $order_id = (int)$_POST['order_id'];
    
    // Mark order as paid
    $update_sql = "UPDATE orders SET status = 'paid' WHERE id = $order_id";
    if ($conn->query($update_sql)) {
        // Update qty_sold for ticket types
        $items_sql = "SELECT oi.ticket_type_id, oi.qty 
                     FROM order_items oi 
                     WHERE oi.order_id = $order_id";
        $items_result = $conn->query($items_sql);
        
        while ($item = $items_result->fetch_assoc()) {
            $update_qty_sql = "UPDATE ticket_types 
                              SET qty_sold = qty_sold + " . $item['qty'] . " 
                              WHERE id = " . $item['ticket_type_id'];
            $conn->query($update_qty_sql);
        }
        
        $success = "Order #$order_id marked as paid successfully!";
    } else {
        $error = "Failed to update order status";
    }
}

// Get orders
$where_clause = "1=1";
if ($filter_status) {
    $where_clause .= " AND o.status = '$filter_status'";
}

$orders_sql = "SELECT o.*, u.name as user_name, e.title as event_title 
               FROM orders o 
               JOIN users u ON o.user_id = u.id 
               JOIN events e ON o.event_id = e.id 
               WHERE $where_clause 
               ORDER BY o.created_at DESC";
$orders_result = $conn->query($orders_sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>Manage Orders</h1>
        <a href="index.php" class="btn btn-outline">← Back to Dashboard</a>
    </div>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error)): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Filter -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <form method="GET" style="display: flex; gap: 1rem; align-items: center;">
            <label for="status">Filter by Status:</label>
            <select name="status" id="status" class="form-control" style="width: 200px;">
                <option value="">All Orders</option>
                <option value="pending" <?php echo $filter_status === 'pending' ? 'selected' : ''; ?>>Pending</option>
                <option value="paid" <?php echo $filter_status === 'paid' ? 'selected' : ''; ?>>Paid</option>
                <option value="cancelled" <?php echo $filter_status === 'cancelled' ? 'selected' : ''; ?>>Cancelled</option>
            </select>
            <button type="submit" class="btn btn-primary">Filter</button>
            <?php if ($filter_status): ?>
                <a href="orders_list.php" class="btn btn-outline">Clear</a>
            <?php endif; ?>
        </form>
    </div>
    
    <!-- Orders Table -->
    <?php if ($orders_result && $orders_result->num_rows > 0): ?>
        <div style="background: white; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); overflow: hidden;">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Event</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($order = $orders_result->fetch_assoc()): ?>
                            <tr>
                                <td>
                                    <strong>#<?php echo $order['id']; ?></strong>
                                </td>
                                <td><?php echo $order['user_name']; ?></td>
                                <td><?php echo $order['event_title']; ?></td>
                                <td>
                                    <strong>LKR <?php echo number_format($order['total_cents'] / 100, 2); ?></strong>
                                </td>
                                <td>
                                    <span style="padding: 0.25rem 0.75rem; border-radius: 15px; font-size: 0.9rem; font-weight: bold; 
                                               background: <?php echo $order['status'] === 'paid' ? '#d4edda' : ($order['status'] === 'pending' ? '#fff3cd' : '#f8d7da'); ?>; 
                                               color: <?php echo $order['status'] === 'paid' ? '#155724' : ($order['status'] === 'pending' ? '#856404' : '#721c24'); ?>;">
                                        <?php echo ucfirst($order['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo date('M j, Y', strtotime($order['created_at'])); ?><br>
                                    <small style="color: #666;"><?php echo date('g:i A', strtotime($order['created_at'])); ?></small>
                                </td>
                                <td>
                                    <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                                        <a href="orders_view.php?id=<?php echo $order['id']; ?>" class="btn btn-outline" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                            View
                                        </a>
                                        
                                        <?php if ($order['status'] === 'pending'): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Mark this order as paid?')">
                                                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                                <button type="submit" name="mark_paid" class="btn btn-success" style="font-size: 0.85rem; padding: 0.4rem 0.8rem;">
                                                    Mark Paid
                                                </button>
                                            </form>
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
            <h3>No Orders Found</h3>
            <p style="color: #666;">
                <?php if ($filter_status): ?>
                    No <?php echo $filter_status; ?> orders found.
                <?php else: ?>
                    No orders have been placed yet.
                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
