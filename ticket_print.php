<?php
include 'includes/db.php';
include 'includes/auth.php';
require_login();

$ticket_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$order_id = isset($_GET['order_id']) ? (int)$_GET['order_id'] : 0;
$type = isset($_GET['type']) ? $_GET['type'] : 'individual';

if ($type === 'group' && $order_id) {
    // Group ticket print
    $sql = "SELECT a.*, oi.*, tt.name as ticket_name, o.id as order_id, 
            e.title as event_title, e.starts_at, e.venue, e.organizer 
            FROM attendees a 
            JOIN order_items oi ON a.order_item_id = oi.id 
            JOIN orders o ON oi.order_id = o.id 
            JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
            JOIN events e ON o.event_id = e.id 
            WHERE o.id = ? AND o.user_id = ? AND o.status = 'paid' 
            ORDER BY a.id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $order_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        die('<div style="text-align:center; padding:2rem; font-family: Arial;">
            <h3>Ticket not found or access denied</h3>
            <p><a href="tickets_my.php">← Back to My Tickets</a></p>
            </div>');
    }
    
    $tickets = [];
    while ($ticket = $result->fetch_assoc()) {
        $tickets[] = $ticket;
    }
    $main_ticket = $tickets[0];
    
} else if ($ticket_id) {
    // Individual ticket print - Use exact same query as tickets_my.php
    $sql = "SELECT a.*, oi.*, tt.name as ticket_name, o.id as order_id, 
            e.title as event_title, e.starts_at, e.venue, e.organizer 
            FROM attendees a 
            JOIN order_items oi ON a.order_item_id = oi.id 
            JOIN orders o ON oi.order_id = o.id 
            JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
            JOIN events e ON o.event_id = e.id 
            WHERE a.id = ? AND o.user_id = ? AND o.status = 'paid'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $ticket_id, $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if (!$result || $result->num_rows === 0) {
        die('<div style="text-align:center; padding:2rem; font-family: Arial;">
            <h3>Ticket not found or access denied</h3>
            <p>Please make sure you own this ticket and the order is paid.</p>
            <p><a href="tickets_my.php">← Back to My Tickets</a></p>
            </div>');
    }
    
    $main_ticket = $result->fetch_assoc();
    
} else {
    die('<div style="text-align:center; padding:2rem; font-family: Arial;">
        <h3>Invalid request</h3>
        <p><a href="tickets_my.php">← Back to My Tickets</a></p>
        </div>');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket - <?php echo htmlspecialchars($main_ticket['event_title']); ?></title>
</head>
<body style="font-family: Arial, sans-serif; margin: 0; padding: 20px; background-color: #f5f5f5;">
    
    <!-- Print buttons (hidden when printing) -->
    <div style="text-align: center; margin-bottom: 20px;" class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2c3e50; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 10px; font-size: 14px;">
            🖨️ Print Ticket
        </button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #666; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            ← Close
        </button>
    </div>

    <!-- Ticket Container -->
    <div style="max-width: 600px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); border: 2px solid #e0e0e0;">
        
        <!-- Header -->
        <div style="background: #2c3e50; color: white; padding: 30px 20px; text-align: center;">
            <h1 style="margin: 0 0 10px 0; font-size: 24px; font-weight: bold;">
                <?php echo htmlspecialchars($main_ticket['event_title']); ?>
            </h1>
            <div style="font-size: 16px; opacity: 0.9;">
                <?php echo date('l, F j, Y', strtotime($main_ticket['starts_at'])); ?><br>
                <?php echo date('g:i A', strtotime($main_ticket['starts_at'])); ?>
            </div>
        </div>
        
        <!-- Body -->
        <div style="padding: 30px;">
            
            <!-- Event Info -->
            <table style="width: 100%; margin-bottom: 25px; border-collapse: collapse;">
                <tr>
                    <td style="padding: 8px 0; width: 50%; vertical-align: top;">
                        <strong style="color: #2c3e50; font-size: 14px;">📍 Venue:</strong><br>
                        <span style="color: #666; font-size: 14px;">
                            <?php echo htmlspecialchars($main_ticket['venue'] ?: 'TBA'); ?>
                        </span>
                    </td>
                    <td style="padding: 8px 0; width: 50%; vertical-align: top;">
                        <strong style="color: #2c3e50; font-size: 14px;">📋 Order ID:</strong><br>
                        <span style="color: #666; font-size: 14px;">#<?php echo $main_ticket['order_id']; ?></span>
                    </td>
                </tr>
                <tr>
                    <td style="padding: 8px 0; vertical-align: top;">
                        <strong style="color: #2c3e50; font-size: 14px;">🎫 Ticket Type:</strong><br>
                        <span style="color: #666; font-size: 14px;">
                            <?php echo htmlspecialchars($main_ticket['ticket_name']); ?>
                        </span>
                    </td>
                    <?php if ($main_ticket['organizer']): ?>
                    <td style="padding: 8px 0; vertical-align: top;">
                        <strong style="color: #2c3e50; font-size: 14px;">👥 Organizer:</strong><br>
                        <span style="color: #666; font-size: 14px;">
                            <?php echo htmlspecialchars($main_ticket['organizer']); ?>
                        </span>
                    </td>
                    <?php endif; ?>
                </tr>
            </table>
            
            <!-- Ticket Code -->
            <div style="text-align: center; padding: 20px; background: #f8f9fa; border-radius: 8px; border: 1px solid #e0e0e0; margin-bottom: 25px;">
                <div style="font-family: 'Courier New', monospace; font-size: 28px; font-weight: bold; color: #2c3e50; letter-spacing: 3px; margin-bottom: 8px;">
                    <?php echo htmlspecialchars($main_ticket['ticket_code']); ?>
                </div>
                <div style="color: #666; font-size: 12px;">
                    <?php if ($type === 'group'): ?>
                        Group Ticket Code (Valid for <?php echo count($tickets); ?> attendees)
                    <?php else: ?>
                        Ticket Verification Code
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Attendee Information -->
            <?php if ($type === 'group'): ?>
                <div style="margin-bottom: 25px;">
                    <strong style="color: #2c3e50; font-size: 14px; display: block; margin-bottom: 15px;">
                        👥 Attendees (<?php echo count($tickets); ?> people):
                    </strong>
                    <?php foreach ($tickets as $index => $ticket): ?>
                        <div style="padding: 10px 0; border-bottom: 1px solid #f0f0f0; <?php echo $index == count($tickets)-1 ? 'border-bottom: none;' : ''; ?>">
                            <strong style="color: #333; font-size: 14px;">
                                <?php echo ($index + 1) . '. ' . htmlspecialchars($ticket['full_name']); ?>
                            </strong>
                            <div style="font-size: 12px; color: #666; margin-top: 3px;">
                                <?php echo htmlspecialchars($ticket['ticket_name']); ?> • <?php echo htmlspecialchars($ticket['email']); ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div style="margin-bottom: 25px;">
                    <strong style="color: #2c3e50; font-size: 14px; display: block; margin-bottom: 10px;">
                        👤 Attendee Information:
                    </strong>
                    <div style="font-size: 14px; color: #333; margin-bottom: 5px;">
                        <strong><?php echo htmlspecialchars($main_ticket['full_name']); ?></strong>
                    </div>
                    <div style="font-size: 14px; color: #666;">
                        <?php echo htmlspecialchars($main_ticket['email']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Footer -->
            <div style="text-align: center; padding-top: 20px; border-top: 1px solid #e0e0e0; color: #666; font-size: 12px;">
                <div style="margin-bottom: 8px;">
                    Present this ticket at the event entrance for admission.
                </div>
                <div>
                    <strong>EventGate</strong> • Generated on <?php echo date('F j, Y g:i A'); ?>
                </div>
            </div>
            
        </div>
    </div>
    
    <!-- Print Styles -->
    <style>
        @media print {
            body {
                background-color: white !important;
                padding: 0 !important;
                margin: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
        
        .no-print {
            display: block;
        }
        
        @media screen and (max-width: 600px) {
            body {
                padding: 10px !important;
            }
        }
    </style>
    
</body>
</html>
