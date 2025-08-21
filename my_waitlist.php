<?php
$page_title = "My Waitlist";
include 'includes/header.php';
include 'includes/db.php';

// Check if user is logged in
if (!is_logged_in()) {
    header('Location: login.php?redirect=my_waitlist.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle leave waitlist action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leave_waitlist'])) {
    $waitlist_id = (int)$_POST['waitlist_id'];
    
    $delete_sql = "DELETE FROM event_waitlist WHERE id = ? AND user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("ii", $waitlist_id, $user_id);
    
    if ($stmt->execute()) {
        $success_message = "Successfully left the waitlist.";
    } else {
        $error_message = "Error leaving waitlist. Please try again.";
    }
}

// Get user's waitlist entries with event details
$waitlist_sql = "SELECT w.*, e.title, e.starts_at, e.ends_at, e.venue, e.category, e.image_path,
                        (SELECT COUNT(*) FROM event_waitlist w2 WHERE w2.event_id = w.event_id AND w2.joined_at < w.joined_at AND w2.status = 'waiting') + 1 as position,
                        (SELECT COUNT(*) FROM event_waitlist w3 WHERE w3.event_id = w.event_id AND w3.status = 'waiting') as total_waiting,
                        (SELECT COUNT(*) FROM ticket_types tt WHERE tt.event_id = w.event_id) as has_tickets
                 FROM event_waitlist w 
                 JOIN events e ON w.event_id = e.id 
                 WHERE w.user_id = ? 
                 ORDER BY w.joined_at DESC";

$stmt = $conn->prepare($waitlist_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$waitlist_result = $stmt->get_result();
?>

<style>
.waitlist-container {
    max-width: 1000px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    text-align: center;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2.5rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    color: #718096;
    font-size: 1.1rem;
}

.waitlist-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(400px, 1fr));
    gap: 1.5rem;
}

.waitlist-card {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.waitlist-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 25px rgba(0,0,0,0.12);
}

.card-image {
    height: 200px;
    background: #f7fafc;
    overflow: hidden;
    position: relative;
}

.card-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.card-image .placeholder {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 100%;
    color: #718096;
    font-size: 1.1rem;
}

.status-badge {
    position: absolute;
    top: 1rem;
    right: 1rem;
    padding: 0.4rem 0.8rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-waiting {
    background: #fed7aa;
    color: #c05621;
}

.status-invited {
    background: #bee3f8;
    color: #2c5282;
}

.status-expired {
    background: #fed7d7;
    color: #c53030;
}

.card-content {
    padding: 1.5rem;
}

.event-title {
    font-size: 1.3rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1rem;
    line-height: 1.3;
}

.event-details {
    display: grid;
    gap: 0.5rem;
    margin-bottom: 1.5rem;
}

.event-detail {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    color: #4a5568;
    font-size: 0.9rem;
}

.position-info {
    background: #f7fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.position-number {
    font-size: 1.5rem;
    font-weight: 700;
    color: #ed8936;
    margin-bottom: 0.25rem;
}

.position-text {
    color: #4a5568;
    font-size: 0.9rem;
}

.card-actions {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.btn {
    padding: 0.6rem 1.2rem;
    border-radius: 8px;
    font-weight: 600;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    font-size: 0.9rem;
    flex: 1;
    min-width: 120px;
}

.btn-primary {
    background: #3182ce;
    color: white;
}

.btn-primary:hover {
    background: #2c5282;
    color: white;
    text-decoration: none;
}

.btn-success {
    background: #28a745;
    color: white;
}

.btn-success:hover {
    background: #218838;
    color: white;
    text-decoration: none;
}

.btn-danger {
    background: #e53e3e;
    color: white;
}

.btn-danger:hover {
    background: #c53030;
}

.btn-secondary {
    background: #718096;
    color: white;
}

.btn-secondary:hover {
    background: #4a5568;
    color: white;
    text-decoration: none;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
}

.empty-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

.empty-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.empty-subtitle {
    color: #718096;
    margin-bottom: 2rem;
}

.success-message, .error-message {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
    text-align: center;
}

.success-message {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.error-message {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

@media (max-width: 768px) {
    .waitlist-grid {
        grid-template-columns: 1fr;
    }
    
    .card-actions {
        flex-direction: column;
    }
    
    .btn {
        flex: none;
    }
}
</style>

<div class="waitlist-container">
    <div class="page-header">
        <h1 class="page-title">📋 My Waitlist</h1>
        <p class="page-subtitle">Track your waitlisted events and their status</p>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div class="error-message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    
    <?php if ($waitlist_result && $waitlist_result->num_rows > 0): ?>
        <div class="waitlist-grid">
            <?php while ($item = $waitlist_result->fetch_assoc()): ?>
                <div class="waitlist-card">
                    <div class="card-image">
                        <?php if ($item['image_path'] && file_exists($item['image_path'])): ?>
                            <img src="<?php echo $BASE_URL . $item['image_path']; ?>" alt="<?php echo htmlspecialchars($item['title']); ?>">
                        <?php else: ?>
                            <div class="placeholder">🎪 Event Image Coming Soon</div>
                        <?php endif; ?>
                        
                        <div class="status-badge status-<?php echo $item['status']; ?>">
                            <?php 
                            switch($item['status']) {
                                case 'waiting': echo '⏳ Waiting'; break;
                                case 'invited': echo '✉️ Invited'; break;
                                case 'expired': echo '⏰ Expired'; break;
                                case 'purchased': echo '✅ Purchased'; break;
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="card-content">
                        <h3 class="event-title"><?php echo htmlspecialchars($item['title']); ?></h3>
                        
                        <div class="event-details">
                            <div class="event-detail">
                                <span>📅</span>
                                <span><?php echo date('F j, Y - g:i A', strtotime($item['starts_at'])); ?></span>
                            </div>
                            
                            <?php if ($item['venue']): ?>
                            <div class="event-detail">
                                <span>📍</span>
                                <span><?php echo htmlspecialchars($item['venue']); ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="event-detail">
                                <span>🕒</span>
                                <span>Joined <?php echo date('M j, Y', strtotime($item['joined_at'])); ?></span>
                            </div>
                        </div>
                        
                        <?php if ($item['status'] === 'waiting'): ?>
                            <div class="position-info">
                                <div class="position-number">#<?php echo $item['position']; ?></div>
                                <div class="position-text">in line (<?php echo $item['total_waiting']; ?> total waiting)</div>
                            </div>
                        <?php elseif ($item['status'] === 'invited'): ?>
                            <div class="position-info" style="background: #bee3f8; border-color: #90cdf4;">
                                <div class="position-number" style="color: #2c5282;">🎫 Invited!</div>
                                <div class="position-text" style="color: #2c5282;">
                                    Expires: <?php echo date('M j, Y \a\t g:i A', strtotime($item['expires_at'])); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <div class="card-actions">
                            <?php if ($item['has_tickets'] > 0 && $item['status'] === 'waiting'): ?>
                                <a href="event_view.php?id=<?php echo $item['event_id']; ?>" class="btn btn-success">
                                    🎫 Tickets Available!
                                </a>
                            <?php elseif ($item['status'] === 'invited'): ?>
                                <a href="event_view.php?id=<?php echo $item['event_id']; ?>" class="btn btn-success">
                                    🚀 Purchase Now
                                </a>
                            <?php else: ?>
                                <a href="event_view.php?id=<?php echo $item['event_id']; ?>" class="btn btn-primary">
                                    View Event
                                </a>
                            <?php endif; ?>
                            
                            <?php if ($item['status'] === 'waiting'): ?>
                                <form method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to leave this waitlist?')">
                                    <input type="hidden" name="waitlist_id" value="<?php echo $item['id']; ?>">
                                    <button type="submit" name="leave_waitlist" class="btn btn-danger" style="width: 100%;">
                                        Leave Waitlist
                                    </button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <h2 class="empty-title">No Waitlisted Events</h2>
            <p class="empty-subtitle">You haven't joined any event waitlists yet.</p>
            <a href="events.php" class="btn btn-primary">Browse Events</a>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
