<?php
$page_title = "Join Waitlist";
include 'includes/header.php';
include 'includes/db.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php?redirect=' . urlencode('waitlist_join.php?event_id=' . ($_GET['event_id'] ?? '')));
    exit;
}

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if (!$event_id) {
    header('Location: events.php');
    exit;
}

// Get event details
$event_sql = "SELECT * FROM events WHERE id = ? AND status = 'published'";
$stmt = $conn->prepare($event_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$event_result = $stmt->get_result();

if (!$event_result || $event_result->num_rows === 0) {
    header('Location: events.php');
    exit;
}

$event = $event_result->fetch_assoc();

// Check if user is already on waitlist
$waitlist_check_sql = "SELECT * FROM event_waitlist WHERE event_id = ? AND user_id = ?";
$stmt = $conn->prepare($waitlist_check_sql);
$stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
$stmt->execute();
$existing_waitlist = $stmt->get_result()->fetch_assoc();

// Check if tickets are available
$tickets_sql = "SELECT * FROM ticket_types WHERE event_id = ? ORDER BY price_cents ASC";
$stmt = $conn->prepare($tickets_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$tickets_result = $stmt->get_result();

$has_tickets = $tickets_result && $tickets_result->num_rows > 0;

// If tickets are available, redirect to event view
if ($has_tickets) {
    header('Location: event_view.php?id=' . $event_id);
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['join_waitlist'])) {
    if (!$existing_waitlist) {
        $user_sql = "SELECT name, email FROM users WHERE id = ?";
        $stmt = $conn->prepare($user_sql);
        $stmt->bind_param("i", $_SESSION['user_id']);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();
        
        // Check if user data was found and has required fields
        if (!$user) {
            $error_message = "User not found. Please log in again.";
        } else {
            // Ensure email and name are not null
            $user_email = $user['email'] ?: 'no-email@placeholder.com';
            $user_name = $user['name'] ?: 'Unknown User';
            
            $insert_sql = "INSERT INTO event_waitlist (event_id, user_id, email, name) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($insert_sql);
            $stmt->bind_param("iiss", $event_id, $_SESSION['user_id'], $user_email, $user_name);
            
            if ($stmt->execute()) {
                $success_message = "Successfully joined the waitlist! We'll notify you when tickets become available.";
                $existing_waitlist = [
                    'id' => $stmt->insert_id,
                    'status' => 'waiting',
                    'joined_at' => date('Y-m-d H:i:s')
                ];
            } else {
                $error_message = "Error joining waitlist: " . $conn->error;
            }
        }
    }
}

// Get waitlist position
$position_sql = "SELECT COUNT(*) + 1 as position FROM event_waitlist WHERE event_id = ? AND joined_at < COALESCE(?, NOW()) AND status = 'waiting'";
$stmt = $conn->prepare($position_sql);
$join_time = $existing_waitlist['joined_at'] ?? date('Y-m-d H:i:s');
$stmt->bind_param("is", $event_id, $join_time);
$stmt->execute();
$position_result = $stmt->get_result();
$waitlist_position = $position_result->fetch_assoc()['position'] ?? 1;

// Get total waitlist count
$total_sql = "SELECT COUNT(*) as total FROM event_waitlist WHERE event_id = ? AND status = 'waiting'";
$stmt = $conn->prepare($total_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$total_waitlist = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
?>

<style>
.waitlist-container {
    max-width: 800px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.waitlist-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.waitlist-header {
    background: #f7fafc;
    padding: 2rem;
    text-align: center;
    border-bottom: 1px solid #e2e8f0;
}

.waitlist-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.waitlist-subtitle {
    color: #718096;
    font-size: 1.1rem;
}

.event-info {
    background: #fff;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.event-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
}

.event-details {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-top: 1rem;
}

.event-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4a5568;
    font-size: 0.95rem;
}

.waitlist-content {
    padding: 2rem;
}

.terms-section {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
}

.terms-title {
    font-size: 1.2rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.terms-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.terms-list li {
    padding: 0.5rem 0;
    color: #4a5568;
    display: flex;
    align-items: flex-start;
    gap: 0.5rem;
}

.terms-list li:before {
    content: "✓";
    color: #48bb78;
    font-weight: bold;
    flex-shrink: 0;
}

.status-card {
    background: #e6fffa;
    border: 1px solid #81e6d9;
    border-radius: 12px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    text-align: center;
}

.status-card.waiting {
    background: #fffaf0;
    border-color: #fed7aa;
}

.status-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.status-position {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ed8936;
    margin-bottom: 0.5rem;
}

.btn-join-waitlist {
    background: #ed8936;
    color: white;
    padding: 1rem 2rem;
    border-radius: 8px;
    border: none;
    font-size: 1.1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    width: 100%;
    margin-top: 1rem;
}

.btn-join-waitlist:hover {
    background: #dd7724;
    transform: translateY(-1px);
}

.btn-secondary {
    background: #718096;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    font-weight: 600;
    text-decoration: none;
    display: inline-block;
    transition: all 0.3s ease;
    text-align: center;
}

.btn-secondary:hover {
    background: #4a5568;
    color: white;
    text-decoration: none;
}

.success-message {
    background: #d4edda;
    color: #155724;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: 1px solid #c3e6cb;
    margin-bottom: 1.5rem;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    padding: 1rem 1.5rem;
    border-radius: 8px;
    border: 1px solid #f5c6cb;
    margin-bottom: 1.5rem;
}
</style>

<div class="waitlist-container">
    <div class="waitlist-card">
        <div class="waitlist-header">
            <h1 class="waitlist-title">🎫 Join Event Waitlist</h1>
            <p class="waitlist-subtitle">Get notified when tickets become available</p>
        </div>
        
        <div class="event-info">
            <h2 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h2>
            
            <div class="event-details">
                <div class="event-detail">
                    <span>📅</span>
                    <span><?php echo date('F j, Y - g:i A', strtotime($event['starts_at'])); ?></span>
                </div>
                
                <?php if ($event['venue']): ?>
                <div class="event-detail">
                    <span>📍</span>
                    <span><?php echo htmlspecialchars($event['venue']); ?></span>
                </div>
                <?php endif; ?>
                
                <?php if ($event['category']): ?>
                <div class="event-detail">
                    <span>🏷️</span>
                    <span><?php echo htmlspecialchars($event['category']); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="waitlist-content">
            <?php if (isset($success_message)): ?>
                <div class="success-message"><?php echo $success_message; ?></div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="error-message"><?php echo $error_message; ?></div>
            <?php endif; ?>
            
            <?php if ($existing_waitlist): ?>
                <div class="status-card waiting">
                    <h3 class="status-title">✅ You're on the Waitlist!</h3>
                    <div class="status-position">Position #<?php echo $waitlist_position; ?></div>
                    <p>out of <?php echo $total_waitlist; ?> people waiting</p>
                    <p style="margin-top: 1rem; color: #4a5568;">
                        Joined on <?php echo date('F j, Y \a\t g:i A', strtotime($existing_waitlist['joined_at'])); ?>
                    </p>
                </div>
                
                <div style="text-align: center;">
                    <a href="my_waitlist.php" class="btn-secondary">View My Waitlist</a>
                    <a href="events.php" class="btn-secondary" style="margin-left: 1rem;">Browse Events</a>
                </div>
            <?php else: ?>
                <div class="terms-section">
                    <h3 class="terms-title">📋 Waitlist Terms & Conditions</h3>
                    <ul class="terms-list">
                        <li>You'll be notified via email when tickets become available</li>
                        <li>You'll have <strong>24 hours</strong> to purchase your reserved tickets</li>
                        <li>Your position in the waitlist determines priority access</li>
                        <li>If you don't purchase within 24 hours, your reservation expires</li>
                        <li>Joining the waitlist is free and doesn't guarantee ticket availability</li>
                        <li>You can leave the waitlist anytime from your account</li>
                    </ul>
                </div>
                
                <form method="POST" style="text-align: center;">
                    <button type="submit" name="join_waitlist" class="btn-join-waitlist" 
                            onclick="return confirm('Ready to join the waitlist for <?php echo htmlspecialchars($event['title']); ?>? You\'ll be notified when tickets become available!')">
                        🚀 Let's Join the Waitlist!
                    </button>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <a href="event_view.php?id=<?php echo $event_id; ?>" class="btn-secondary">← Back to Event</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
