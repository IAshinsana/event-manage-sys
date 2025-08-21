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
            WHERE o.id = $order_id AND o.user_id = " . $_SESSION['user_id'] . " AND o.status = 'paid' 
            ORDER BY a.id ASC";
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        die('Ticket not found or access denied');
    }
    
    $tickets = [];
    while ($ticket = $result->fetch_assoc()) {
        $tickets[] = $ticket;
    }
    $main_ticket = $tickets[0];
    
} else if ($ticket_id) {
    // Individual ticket print
    $sql = "SELECT a.*, oi.*, tt.name as ticket_name, o.id as order_id, 
            e.title as event_title, e.starts_at, e.venue, e.organizer 
            FROM attendees a 
            JOIN order_items oi ON a.order_item_id = oi.id 
            JOIN orders o ON oi.order_id = o.id 
            JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
            JOIN events e ON o.event_id = e.id 
            WHERE a.id = $ticket_id AND o.user_id = " . $_SESSION['user_id'] . " AND o.status = 'paid'";
    $result = $conn->query($sql);
    
    if (!$result || $result->num_rows === 0) {
        die('Ticket not found or access denied');
    }
    
    $main_ticket = $result->fetch_assoc();
    
} else {
    die('Invalid request');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Event Ticket - <?php echo $main_ticket['event_title']; ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f5f5;
            padding: 20px;
        }
        
        .ticket {
            background: white;
            max-width: 600px;
            margin: 0 auto;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            border: 2px dashed #ddd;
        }
        
        .ticket-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .ticket-body {
            padding: 2rem;
        }
        
        .event-title {
            font-size: 1.8rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .event-date {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        
        .ticket-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }
        
        .info-section h4 {
            color: #333;
            margin-bottom: 0.5rem;
            font-size: 1rem;
        }
        
        .info-section p {
            color: #666;
            margin-bottom: 0.25rem;
        }
        
        .ticket-code-section {
            text-align: center;
            padding: 1.5rem;
            background: #f8f9fa;
            border-radius: 10px;
            margin-bottom: 1.5rem;
        }
        
        .ticket-code {
            font-size: 2rem;
            font-weight: bold;
            color: #333;
            letter-spacing: 3px;
            margin-bottom: 0.5rem;
        }
        
        .code-label {
            color: #666;
            font-size: 0.9rem;
        }
        
        .attendees-list {
            margin-top: 1rem;
        }
        
        .attendee-item {
            padding: 0.5rem 0;
            border-bottom: 1px solid #eee;
        }
        
        .attendee-item:last-child {
            border-bottom: none;
        }
        
        .footer-note {
            text-align: center;
            color: #666;
            font-size: 0.9rem;
            border-top: 1px solid #eee;
            padding-top: 1rem;
        }
        
        @media print {
            body {
                background: white;
                padding: 0;
            }
            
            .ticket {
                box-shadow: none;
                border: 2px solid #ddd;
            }
        }
        
        .no-print {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" style="padding: 0.5rem 1rem; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer;">
            🖨️ Print Ticket
        </button>
        <button onclick="window.close()" style="padding: 0.5rem 1rem; background: #6c757d; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 0.5rem;">
            Close
        </button>
    </div>

    <div class="ticket">
        <div class="ticket-header">
            <div class="event-title"><?php echo $main_ticket['event_title']; ?></div>
            <div class="event-date">
                <?php echo date('l, F j, Y', strtotime($main_ticket['starts_at'])); ?><br>
                <?php echo date('g:i A', strtotime($main_ticket['starts_at'])); ?>
            </div>
        </div>
        
        <div class="ticket-body">
            <div class="ticket-info">
                <div class="info-section">
                    <h4>📍 Venue</h4>
                    <p><?php echo $main_ticket['venue'] ?: 'TBA'; ?></p>
                    
                    <?php if ($main_ticket['organizer']): ?>
                        <h4 style="margin-top: 1rem;">👥 Organizer</h4>
                        <p><?php echo $main_ticket['organizer']; ?></p>
                    <?php endif; ?>
                </div>
                
                <div class="info-section">
                    <h4>🎫 Ticket Type</h4>
                    <p><?php echo $main_ticket['ticket_name']; ?></p>
                    
                    <h4 style="margin-top: 1rem;">📋 Order</h4>
                    <p>#<?php echo $main_ticket['order_id']; ?></p>
                </div>
            </div>
            
            <div class="ticket-code-section">
                <div class="ticket-code"><?php echo $main_ticket['ticket_code']; ?></div>
                <div class="code-label">
                    <?php if ($type === 'group'): ?>
                        Group Ticket Code (Valid for <?php echo count($tickets); ?> attendees)
                    <?php else: ?>
                        Ticket Code
                    <?php endif; ?>
                </div>
            </div>
            
            <?php if ($type === 'group'): ?>
                <div class="info-section">
                    <h4>👥 Attendees (<?php echo count($tickets); ?> people)</h4>
                    <div class="attendees-list">
                        <?php foreach ($tickets as $index => $ticket): ?>
                            <div class="attendee-item">
                                <strong><?php echo ($index + 1) . '. ' . $ticket['full_name']; ?></strong>
                                <div style="font-size: 0.9rem; color: #666;">
                                    <?php echo $ticket['ticket_name']; ?> • <?php echo $ticket['email']; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="info-section">
                    <h4>👤 Attendee</h4>
                    <p><strong><?php echo $main_ticket['full_name']; ?></strong></p>
                    <p><?php echo $main_ticket['email']; ?></p>
                </div>
            <?php endif; ?>
            
            <div class="footer-note">
                <p>Present this ticket at the event entrance for admission.</p>
                <p>EventTickets • Generated on <?php echo date('F j, Y g:i A'); ?></p>
            </div>
        </div>
    </div>
</body>
</html>
