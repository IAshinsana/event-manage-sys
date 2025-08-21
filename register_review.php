<?php
$page_title = "Review & Confirm";
include 'includes/header.php';
include 'includes/db.php';
require_login();

// Function to generate unique ticket code
function generateTicketCode($conn, $is_group = false) {
    do {
        if ($is_group) {
            // Group codes: GRP + 5 random characters = 8 total
            $code = 'GRP' . substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 5);
        } else {
            // Individual codes: 8 random characters
            $code = substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        }
        
        // Check if code already exists
        $check_sql = "SELECT id FROM attendees WHERE ticket_code = '$code'";
        $check_result = $conn->query($check_sql);
    } while ($check_result && $check_result->num_rows > 0);
    
    return $code;
}

if (!isset($_SESSION['booking']) || !isset($_SESSION['booking']['attendee'])) {
    header('Location: events.php');
    exit();
}

$booking = $_SESSION['booking'];
$attendee = $_SESSION['booking']['attendee'];
$error = '';
$promo_discount = 0;
$promo_code_used = '';

// Get event details
$event_sql = "SELECT * FROM events WHERE id = " . $booking['event_id'];
$event_result = $conn->query($event_sql);
$event = $event_result->fetch_assoc();

// Calculate total
$total_cents = 0;
foreach ($booking['tickets'] as $ticket) {
    $total_cents += $ticket['price'] * $ticket['qty'];
}

// Handle promo code
if ($_POST && isset($_POST['promo_code'])) {
    $promo_code = $_POST['promo_code'];
    
    if ($promo_code) {
        $promo_sql = "SELECT * FROM promocodes 
                     WHERE event_id = " . $booking['event_id'] . " 
                     AND code = '$promo_code' 
                     AND (valid_from IS NULL OR valid_from <= NOW()) 
                     AND (valid_to IS NULL OR valid_to >= NOW()) 
                     AND (uses_limit = 0 OR uses_count < uses_limit)";
        $promo_result = $conn->query($promo_sql);
        
        if ($promo_result && $promo_result->num_rows > 0) {
            $promo = $promo_result->fetch_assoc();
            if ($promo['kind'] === 'percent') {
                $promo_discount = ($total_cents * $promo['value']) / 100;
            } else {
                $promo_discount = $promo['value'] * 100; // Convert to cents
            }
            $promo_code_used = $promo_code;
        } else {
            $error = 'Invalid or expired promo code';
        }
    }
}

$final_total = max(0, $total_cents - $promo_discount);

// Handle order creation
if ($_POST && isset($_POST['confirm_order'])) {
    if (!$error) {
        // Create order
        $order_sql = "INSERT INTO orders (user_id, event_id, total_cents, currency, status) 
                     VALUES (" . $_SESSION['user_id'] . ", " . $booking['event_id'] . ", $final_total, 'LKR', 'pending')";
        
        if ($conn->query($order_sql)) {
            $order_id = $conn->insert_id;
            
            // Create order items and attendees
            foreach ($booking['tickets'] as $ticket) {
                $item_sql = "INSERT INTO order_items (order_id, ticket_type_id, unit_price_cents, qty) 
                           VALUES ($order_id, " . $ticket['ticket_type_id'] . ", " . $ticket['price'] . ", " . $ticket['qty'] . ")";
                
                if ($conn->query($item_sql)) {
                    $item_id = $conn->insert_id;
                    
                    // Create attendees with improved ticket code generation
                    for ($i = 0; $i < $ticket['qty']; $i++) {
                        // Generate unique 8-character random uppercase ticket code
                        $ticket_code = generateTicketCode($conn, $attendee['use_group_code']);
                        
                        $attendee_sql = "INSERT INTO attendees (order_item_id, full_name, email, ticket_code) 
                                       VALUES ($item_id, '" . $attendee['full_name'] . "', '" . $attendee['email'] . "', '$ticket_code')";
                        $conn->query($attendee_sql);
                    }
                }
            }
            
            // Update promo code usage
            if ($promo_code_used) {
                $update_promo_sql = "UPDATE promocodes SET uses_count = uses_count + 1 WHERE event_id = " . $booking['event_id'] . " AND code = '$promo_code_used'";
                $conn->query($update_promo_sql);
            }
            
            // Clear booking session
            unset($_SESSION['booking']);
            
            // Redirect to success page
            header("Location: register_done.php?order_id=$order_id");
            exit();
        } else {
            $error = 'Failed to create order. Please try again.';
        }
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Review & Confirm Order</h1>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
        
        <!-- Order Details -->
        <div>
            <!-- Event Info -->
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3><?php echo $event['title']; ?></h3>
                <div style="color: #666;">
                    📅 <?php echo date('F j, Y - g:i A', strtotime($event['starts_at'])); ?>
                    <?php if ($event['venue']): ?>
                        <br>📍 <?php echo $event['venue']; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Attendee Info -->
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                <h3>Contact Information</h3>
                <div style="margin-top: 1rem; color: #666;">
                    <strong>Name:</strong> <?php echo $attendee['full_name']; ?><br>
                    <strong>Email:</strong> <?php echo $attendee['email']; ?><br>
                    <strong>Mobile:</strong> <?php echo $attendee['mobile']; ?>
                    <?php if ($booking['total_tickets'] > 1 && $attendee['use_group_code']): ?>
                        <br><strong>Ticket Type:</strong> Group Ticket (One code for all tickets)
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Promo Code -->
            <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h3>Promo Code</h3>
                <form method="POST" style="margin-top: 1rem;">
                    <div style="display: flex; gap: 1rem;">
                        <input type="text" name="promo_code" placeholder="Enter promo code" class="form-control" 
                               value="<?php echo $promo_code_used; ?>" style="flex: 1;">
                        <button type="submit" class="btn btn-primary">Apply</button>
                    </div>
                    <?php if ($promo_code_used): ?>
                        <div style="color: #28a745; margin-top: 0.5rem; font-size: 0.9rem;">
                            ✓ Promo code applied: <?php echo $promo_code_used; ?>
                        </div>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        
        <!-- Order Summary -->
        <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); height: fit-content;">
            <h3>Order Summary</h3>
            
            <div style="margin-top: 1.5rem;">
                <?php foreach ($booking['tickets'] as $ticket): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                        <span><?php echo $ticket['name']; ?> × <?php echo $ticket['qty']; ?></span>
                        <span>LKR <?php echo number_format(($ticket['price'] * $ticket['qty']) / 100, 2); ?></span>
                    </div>
                <?php endforeach; ?>
                
                <hr>
                
                <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                    <span>Subtotal</span>
                    <span>LKR <?php echo number_format($total_cents / 100, 2); ?></span>
                </div>
                
                <?php if ($promo_discount > 0): ?>
                    <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem; color: #28a745;">
                        <span>Discount (<?php echo $promo_code_used; ?>)</span>
                        <span>-LKR <?php echo number_format($promo_discount / 100, 2); ?></span>
                    </div>
                <?php endif; ?>
                
                <hr>
                
                <div style="display: flex; justify-content: space-between; font-weight: bold; font-size: 1.1rem;">
                    <span>Total</span>
                    <span>LKR <?php echo number_format($final_total / 100, 2); ?></span>
                </div>
            </div>
            
            <form method="POST" style="margin-top: 2rem;">
                <?php if ($promo_code_used): ?>
                    <input type="hidden" name="promo_code" value="<?php echo $promo_code_used; ?>">
                <?php endif; ?>
                <button type="submit" name="confirm_order" class="btn btn-success" style="width: 100%; font-size: 1.1rem;">
                    Confirm Order
                </button>
            </form>
            
            <div style="margin-top: 1rem; text-align: center;">
                <a href="register_attendees.php" class="btn btn-outline">Back to Details</a>
            </div>
            
            <div style="margin-top: 1.5rem; font-size: 0.9rem; color: #666; text-align: center;">
                <strong>Note:</strong> This is an offline payment system. After confirming your order, 
                an admin will mark it as paid once payment is received.
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
