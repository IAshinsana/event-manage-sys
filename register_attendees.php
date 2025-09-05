<?php
$page_title = "Attendee Details";
include 'includes/header.php';
include 'includes/db.php';
require_login();

if (!isset($_SESSION['booking'])) {
    header('Location: events.php');
    exit();
}

$booking = $_SESSION['booking'];
$error = '';

if ($_POST) {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $use_group_code = isset($_POST['use_group_code']) ? 1 : 0;
    
    if ($full_name && $email && $mobile) {
        // Basic email validation
        if (!preg_match('/^[^\s@]+@[^\s@]+\.[^\s@]+$/', $email)) {
            $error = 'Please enter a valid email address';
        }
        // Sri Lanka mobile validation
        elseif (!preg_match('/^(070|071|072|074|075|076|077|078)\d{7}$/', $mobile)) {
            $error = 'Please enter a valid Sri Lankan mobile number (070xxxxxxx)';
        }
        else {
            // Store attendee details in session
            $_SESSION['booking']['attendee'] = [
                'full_name' => $full_name,
                'email' => $email,
                'mobile' => $mobile,
                'use_group_code' => $use_group_code
            ];
            header('Location: register_review.php');
            exit();
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <h1>Attendee Details</h1>
    
    <!-- Booking Summary -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h3>Selected Tickets</h3>
        <?php foreach ($booking['tickets'] as $ticket): ?>
            <div style="display: flex; justify-content: space-between; margin-bottom: 0.5rem;">
                <span><?php echo $ticket['name']; ?> × <?php echo $ticket['qty']; ?></span>
                <span>LKR <?php echo number_format(($ticket['price'] * $ticket['qty']) / 100, 2); ?></span>
            </div>
        <?php endforeach; ?>
        <hr>
        <div style="display: flex; justify-content: space-between; font-weight: bold;">
            <span>Total Tickets: <?php echo $booking['total_tickets']; ?></span>
            <span>Total: LKR <?php 
                $total = 0;
                foreach ($booking['tickets'] as $ticket) {
                    $total += $ticket['price'] * $ticket['qty'];
                }
                echo number_format($total / 100, 2);
            ?></span>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <form method="POST">
        <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <h2>Contact Information</h2>
            <p style="color: #666; margin-bottom: 1.5rem;">
                This information will be used for all tickets in this order.
            </p>
            
            <div class="form-group">
                <label for="full_name">Full Name *</label>
                <input type="text" id="full_name" name="full_name" class="form-control" 
                       value="<?php echo $_SESSION['name']; ?>" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address *</label>
                <input type="email" id="email" name="email" class="form-control" 
                       value="<?php echo $_SESSION['email']; ?>" required>
                <small style="color: #666;">Tickets will be sent to this email address</small>
            </div>
            
            <div class="form-group">
                <label for="mobile">Mobile Number *</label>
                <input type="tel" id="mobile" name="mobile" class="form-control" 
                       value="<?php echo $_SESSION['phone']?>" 
                       placeholder="070xxxxxxx" required>
                <small style="color: #666;">Enter Sri lanka mobile number</small>
            </div>
            
            <?php if ($booking['total_tickets'] > 1): ?>
                <div class="form-group">
                    <label>
                        <input type="checkbox" name="use_group_code" value="1" 
                               <?php echo isset($_POST['use_group_code']) ? 'checked' : ''; ?>>
                        Use one ticket code for all tickets (Group Ticket)
                    </label>
                    <small style="display: block; color: #666; margin-top: 0.25rem;">
                        If checked, all tickets will share the same code. Otherwise, each ticket gets a unique code.
                    </small>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 2rem; text-align: center;">
                <a href="register_start.php?event_id=<?php echo $booking['event_id']; ?>" class="btn btn-outline" style="margin-right: 1rem;">Back</a>
                <button type="submit" class="btn btn-success">Continue to Review</button>
            </div>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
