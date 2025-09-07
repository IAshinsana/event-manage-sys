<?php
$page_title = "My Tickets";
include 'includes/header.php';
include 'includes/db.php';
require_login();


$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;

// Get user's paid tickets
$where_clause = "o.user_id = " . $_SESSION['user_id'] . " AND o.status = 'paid'";
if ($order_id) {
    $where_clause .= " AND o.id = $order_id";
}

$tickets_sql = "SELECT a.*, oi.*, tt.name as ticket_name, o.id as order_id, 
                e.title as event_title, e.starts_at, e.venue, e.organizer 
                FROM attendees a 
                JOIN order_items oi ON a.order_item_id = oi.id 
                JOIN orders o ON oi.order_id = o.id 
                JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
                JOIN events e ON o.event_id = e.id 
                WHERE $where_clause 
                ORDER BY o.created_at DESC, a.id ASC";
$tickets_result = $conn->query($tickets_sql);

// Group tickets by order for group ticket detection
$orders_tickets = [];
if ($tickets_result) {
    while ($ticket = $tickets_result->fetch_assoc()) {
        $orders_tickets[$ticket['order_id']][] = $ticket;
    }
}
?>

<div class="container" style="margin-top: 2rem; max-width: 900px; margin-left: auto; margin-right: auto;">
    <div style="text-align: center; margin-bottom: 2rem;">
        <h1 style="color: #333; font-size: 2.5rem; margin-bottom: 0.5rem;">🎫 My Tickets</h1>
        <p style="color: #666; font-size: 1.1rem;">View and manage your event tickets</p>
    </div>
    
    <?php if (!empty($orders_tickets)): ?>
        <div style="margin-top: 1.5rem;">
            <?php foreach ($orders_tickets as $order_id => $tickets): ?>
                <?php $first_ticket = $tickets[0]; ?>
                <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
                    
                    <!-- Event Header -->
                    <div style="border-bottom: 2px solid #f0f0f0; padding-bottom: 1.5rem; margin-bottom: 1.5rem; text-align: center;">
                        <h3 style="margin-bottom: 0.5rem; color: #333; font-size: 1.8rem;"><?php echo $first_ticket['event_title']; ?></h3>
                        <div style="color: #666; font-size: 1rem; margin-bottom: 0.5rem;">
                            📅 <?php echo date('F j, Y - g:i A', strtotime($first_ticket['starts_at'])); ?>
                            <?php if ($first_ticket['venue']): ?>
                                <br>📍 <?php echo $first_ticket['venue']; ?>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 1rem; font-size: 0.9rem; color: #888; padding: 0.5rem; background: #f8f9fa; border-radius: 5px; display: inline-block;">
                            Order #<?php echo $order_id; ?> • <?php echo count($tickets); ?> ticket(s)
                        </div>
                    </div>
                    
                    <!-- Check if all tickets have same code (group ticket) -->
                    <?php 
                    $is_group_ticket = true;
                    $first_code = $tickets[0]['ticket_code'];
                    foreach ($tickets as $ticket) {
                        if ($ticket['ticket_code'] !== $first_code) {
                            $is_group_ticket = false;
                            break;
                        }
                    }
                    ?>
                    
                    <?php if ($is_group_ticket && count($tickets) > 1): ?>
                        <!-- Group Ticket Display -->
                        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 2rem; border-radius: 12px; margin-bottom: 1.5rem; text-align: center;">
                            <h4 style="margin-bottom: 1.5rem; font-size: 1.5rem;">🎫 Group Ticket</h4>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center; text-align: left;">
                                <div>
                                    <div style="margin-bottom: 0.8rem;"><strong>Attendee:</strong> <?php echo $first_ticket['full_name']; ?></div>
                                    <div style="margin-bottom: 0.8rem;"><strong>Email:</strong> <?php echo $first_ticket['email']; ?></div>
                                    <div><strong>Total Tickets:</strong> <?php echo count($tickets); ?> tickets</div>
                                </div>
                                <div style="text-align: center;">
                                    <div style="font-size: 2rem; font-weight: bold; margin-bottom: 0.5rem; font-family: monospace; background: rgba(255,255,255,0.2); padding: 1rem; border-radius: 8px;">
                                        <?php echo $first_ticket['ticket_code']; ?>
                                    </div>
                                    <div style="font-size: 0.9rem; opacity: 0.9;">
                                        Valid for all <?php echo count($tickets); ?> attendees
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div style="text-align: center; margin-bottom: 2rem;">
                            <a href="ticket_print.php?order_id=<?php echo $order_id; ?>&type=group" target="_blank" 
                               class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1.1rem;">
                                🖨️ Print Group Ticket
                            </a>
                        </div>
                        
                        <!-- Individual ticket list for reference -->
                        <details style="margin-top: 1.5rem;">
                            <summary style="cursor: pointer; color: #007bff; font-weight: 500; text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px; margin-bottom: 1rem;">
                                📋 View Individual Ticket Details
                            </summary>
                            <div style="margin-top: 1rem; padding: 1.5rem; background: #f8f9fa; border-radius: 8px;">
                                <?php foreach ($tickets as $index => $ticket): ?>
                                    <div style="padding: 1rem; <?php echo $index < count($tickets) - 1 ? 'border-bottom: 1px solid #dee2e6;' : ''; ?> text-align: center;">
                                        <div style="font-weight: bold; margin-bottom: 0.5rem; color: #333;"><?php echo $ticket['ticket_name']; ?> - <?php echo $ticket['full_name']; ?></div>
                                        <div style="font-size: 0.9rem; color: #666; font-family: monospace;">
                                            Code: <strong><?php echo $ticket['ticket_code']; ?></strong>
                                            <?php if ($ticket['checked_in_at']): ?>
                                                <br><span style="color: #28a745;">✅ Checked in: <?php echo date('M j, Y g:i A', strtotime($ticket['checked_in_at'])); ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </details>
                        
                    <?php else: ?>
                        <!-- Individual Tickets Display -->
                        <div style="display: grid; gap: 1.5rem;">
                            <?php foreach ($tickets as $ticket): ?>
                                <div style="border: 2px solid #eee; border-radius: 12px; padding: 2rem; position: relative; text-align: center; background: #fafafa;">
                                    <?php if ($ticket['checked_in_at']): ?>
                                        <div style="position: absolute; top: 1rem; right: 1rem; background: #28a745; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem; font-weight: bold;">
                                            ✅ Checked In
                                        </div>
                                    <?php endif; ?>
                                    
                                    <div style="margin-bottom: 1.5rem;">
                                        <h4 style="margin-bottom: 1rem; color: #333; font-size: 1.4rem;"><?php echo $ticket['ticket_name']; ?></h4>
                                        
                                        <div style="background: white; padding: 1.5rem; border-radius: 8px; margin-bottom: 1rem; text-align: left; max-width: 400px; margin-left: auto; margin-right: auto;">
                                            <div style="margin-bottom: 0.8rem;">
                                                <strong style="color: #555;">Attendee:</strong> <span style="color: #333;"><?php echo $ticket['full_name']; ?></span>
                                            </div>
                                            <div style="margin-bottom: 0.8rem;">
                                                <strong style="color: #555;">Email:</strong> <span style="color: #333;"><?php echo $ticket['email']; ?></span>
                                            </div>
                                            <div style="text-align: center; margin-top: 1rem; padding: 1rem; background: #f8f9fa; border-radius: 6px;">
                                                <strong style="color: #555;">Ticket Code:</strong><br>
                                                <span style="font-family: monospace; font-size: 1.2rem; color: #007bff; font-weight: bold;"><?php echo $ticket['ticket_code']; ?></span>
                                            </div>
                                        </div>
                                        
                                        <?php if ($ticket['checked_in_at']): ?>
                                            <div style="color: #28a745; font-size: 0.9rem; margin-bottom: 1rem; padding: 0.5rem; background: #d4edda; border-radius: 5px;">
                                                <strong>✅ Checked in:</strong> <?php echo date('M j, Y g:i A', strtotime($ticket['checked_in_at'])); ?>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <div style="text-align: center;">
                                            <a href="ticket_print.php?id=<?php echo $ticket['id']; ?>" target="_blank" 
                                               class="btn btn-primary" style="padding: 0.8rem 2rem; font-size: 1.1rem;">
                                                🖨️ Print Ticket
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 4rem 2rem; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; max-width: 500px; margin: 2rem auto;">
            <div style="font-size: 4rem; margin-bottom: 1rem;">🎫</div>
            <h3 style="color: #333; margin-bottom: 1rem; font-size: 1.8rem;">No Tickets Available</h3>
            <p style="color: #666; margin-bottom: 2rem; font-size: 1.1rem; line-height: 1.6;">
                You don't have any paid tickets yet.<br>
                Tickets will appear here once your orders are marked as paid.
            </p>
            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                <a href="orders_my.php" class="btn btn-primary" style="padding: 0.8rem 1.5rem; font-size: 1rem;">
                    📋 View My Orders
                </a>
                <a href="events.php" class="btn btn-outline" style="padding: 0.8rem 1.5rem; font-size: 1rem;">
                    🎉 Browse Events
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
