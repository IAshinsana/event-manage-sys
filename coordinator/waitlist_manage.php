<?php
$page_title = "Manage Event Waitlist";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

if (!is_approved_coordinator()) {
    header('Location: dashboard.php?error=not_approved');
    exit;
}

$event_id = isset($_GET['event_id']) ? (int)$_GET['event_id'] : 0;

if (!$event_id) {
    header('Location: events_list.php');
    exit;
}

// Check if event belongs to this coordinator
$event_sql = "SELECT * FROM events WHERE id = ? AND created_by = ?";
$stmt = $conn->prepare($event_sql);
$stmt->bind_param("ii", $event_id, $_SESSION['user_id']);
$stmt->execute();
$event_result = $stmt->get_result();

if (!$event_result || $event_result->num_rows === 0) {
    header('Location: events_list.php');
    exit;
}

$event = $event_result->fetch_assoc();

// Handle bulk invite action
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invite_users'])) {
    $invite_count = (int)$_POST['invite_count'];
    $grace_hours = 24; // 24 hour grace period
    
    if ($invite_count > 0) {
        // Get users to invite (oldest first)
        $invite_sql = "SELECT * FROM event_waitlist 
                       WHERE event_id = ? AND status = 'waiting' 
                       ORDER BY joined_at ASC 
                       LIMIT ?";
        $stmt = $conn->prepare($invite_sql);
        $stmt->bind_param("ii", $event_id, $invite_count);
        $stmt->execute();
        $users_to_invite = $stmt->get_result();
        
        $invited_count = 0;
        $expires_at = date('Y-m-d H:i:s', strtotime("+$grace_hours hours"));
        
        while ($user = $users_to_invite->fetch_assoc()) {
            $update_sql = "UPDATE event_waitlist 
                          SET status = 'invited', invited_at = NOW(), expires_at = ? 
                          WHERE id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $expires_at, $user['id']);
            
            if ($stmt->execute()) {
                $invited_count++;
                // Here you could send email notifications
            }
        }
        
        $success_message = "Successfully invited $invited_count users from the waitlist!";
    }
}

// Handle expire old invitations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expire_old'])) {
    $expire_sql = "UPDATE event_waitlist 
                   SET status = 'expired' 
                   WHERE event_id = ? AND status = 'invited' AND expires_at < NOW()";
    $stmt = $conn->prepare($expire_sql);
    $stmt->bind_param("i", $event_id);
    $expired_count = $stmt->execute() ? $stmt->affected_rows : 0;
    
    $success_message = "Expired $expired_count old invitations.";
}

// Get waitlist statistics
$stats_sql = "SELECT 
                COUNT(CASE WHEN status = 'waiting' THEN 1 END) as waiting_count,
                COUNT(CASE WHEN status = 'invited' THEN 1 END) as invited_count,
                COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_count,
                COUNT(CASE WHEN status = 'purchased' THEN 1 END) as purchased_count,
                COUNT(*) as total_count
              FROM event_waitlist 
              WHERE event_id = ?";

$stmt = $conn->prepare($stats_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$stats = $stmt->get_result()->fetch_assoc();

// Get current waitlist users
$waitlist_sql = "SELECT w.*, u.name, u.email, u.phone
                 FROM event_waitlist w
                 JOIN users u ON w.user_id = u.id
                 WHERE w.event_id = ?
                 ORDER BY 
                   CASE w.status 
                     WHEN 'invited' THEN 1 
                     WHEN 'waiting' THEN 2 
                     WHEN 'expired' THEN 3 
                     WHEN 'purchased' THEN 4 
                   END,
                   w.joined_at ASC";

$stmt = $conn->prepare($waitlist_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$waitlist_result = $stmt->get_result();

// Check if event has tickets
$tickets_sql = "SELECT COUNT(*) as ticket_count FROM ticket_types WHERE event_id = ?";
$stmt = $conn->prepare($tickets_sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$has_tickets = $stmt->get_result()->fetch_assoc()['ticket_count'] > 0;
?>

<style>
.waitlist-container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.page-header {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2rem;
}

.page-title {
    font-size: 2rem;
    font-weight: 700;
    color: #2d3748;
    margin-bottom: 0.5rem;
}

.event-title {
    font-size: 1.3rem;
    color: #4a5568;
    margin-bottom: 1rem;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    padding: 1.5rem;
    text-align: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.stat-label {
    font-size: 0.9rem;
    color: #718096;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.stat-waiting .stat-number { color: #ed8936; }
.stat-invited .stat-number { color: #3182ce; }
.stat-expired .stat-number { color: #e53e3e; }
.stat-purchased .stat-number { color: #48bb78; }

.actions-section {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    padding: 2rem;
    margin-bottom: 2rem;
}

.actions-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 1.5rem;
}

.action-form {
    display: flex;
    gap: 1rem;
    align-items: end;
    flex-wrap: wrap;
    margin-bottom: 1rem;
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.form-group label {
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
}

.form-group input {
    padding: 0.6rem;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    font-size: 0.95rem;
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
    font-size: 0.95rem;
}

.btn-primary {
    background: #3182ce;
    color: white;
}

.btn-primary:hover {
    background: #2c5282;
}

.btn-warning {
    background: #ed8936;
    color: white;
}

.btn-warning:hover {
    background: #dd7724;
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

.waitlist-table {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.table-header {
    background: #f7fafc;
    padding: 1.5rem 2rem;
    border-bottom: 1px solid #e2e8f0;
}

.table-title {
    font-size: 1.5rem;
    font-weight: 600;
    color: #2d3748;
}

.table-container {
    overflow-x: auto;
}

table {
    width: 100%;
    border-collapse: collapse;
}

th, td {
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid #f0f0f0;
}

th {
    background: #f7fafc;
    font-weight: 600;
    color: #4a5568;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-badge {
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

.status-purchased {
    background: #c6f6d5;
    color: #276749;
}

.position-badge {
    background: #e2e8f0;
    color: #4a5568;
    padding: 0.3rem 0.6rem;
    border-radius: 12px;
    font-size: 0.8rem;
    font-weight: 600;
}

.success-message, .error-message, .info-message {
    padding: 1rem 1.5rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
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

.info-message {
    background: #d4edda;
    color: #155724;
    border: 1px solid #bee5eb;
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: #718096;
}

.empty-icon {
    font-size: 3rem;
    margin-bottom: 1rem;
    opacity: 0.6;
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .action-form {
        flex-direction: column;
        align-items: stretch;
    }
    
    .btn {
        width: 100%;
    }
    
    th, td {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
}
</style>

<div class="waitlist-container">
    <div class="page-header">
        <h1 class="page-title">📋 Waitlist Management</h1>
        <div class="event-title"><?php echo htmlspecialchars($event['title']); ?></div>
        <div style="margin-top: 1rem;">
            <a href="events_list.php" class="btn btn-secondary">← Back to Events</a>
            <a href="event_edit.php?id=<?php echo $event_id; ?>" class="btn btn-secondary" style="margin-left: 0.5rem;">Edit Event</a>
        </div>
    </div>
    
    <?php if (isset($success_message)): ?>
        <div class="success-message"><?php echo $success_message; ?></div>
    <?php endif; ?>
    
    <?php if (!$has_tickets): ?>
        <div class="info-message">
            💡 <strong>No tickets listed yet.</strong> Users can join the waitlist and will be notified when you add tickets to this event.
        </div>
    <?php endif; ?>
    
    <!-- Statistics -->
    <div class="stats-grid">
        <div class="stat-card stat-waiting">
            <div class="stat-number"><?php echo $stats['waiting_count']; ?></div>
            <div class="stat-label">Waiting</div>
        </div>
        <div class="stat-card stat-invited">
            <div class="stat-number"><?php echo $stats['invited_count']; ?></div>
            <div class="stat-label">Invited</div>
        </div>
        <div class="stat-card stat-expired">
            <div class="stat-number"><?php echo $stats['expired_count']; ?></div>
            <div class="stat-label">Expired</div>
        </div>
        <div class="stat-card stat-purchased">
            <div class="stat-number"><?php echo $stats['purchased_count']; ?></div>
            <div class="stat-label">Purchased</div>
        </div>
    </div>
    
    <!-- Actions -->
    <?php if ($stats['waiting_count'] > 0 || $stats['invited_count'] > 0): ?>
    <div class="actions-section">
        <h2 class="actions-title">⚡ Quick Actions</h2>
        
        <?php if ($stats['waiting_count'] > 0): ?>
        <form method="POST" class="action-form">
            <div class="form-group">
                <label for="invite_count">Invite Users from Waitlist</label>
                <input type="number" id="invite_count" name="invite_count" min="1" max="<?php echo $stats['waiting_count']; ?>" value="<?php echo min(5, $stats['waiting_count']); ?>" required>
            </div>
            <button type="submit" name="invite_users" class="btn btn-primary" onclick="return confirm('This will invite users and give them 24 hours to purchase tickets. Continue?')">
                🚀 Invite Users
            </button>
        </form>
        <p style="color: #718096; font-size: 0.9rem; margin-top: 0.5rem;">
            Invited users will have 24 hours to purchase tickets before their invitation expires.
        </p>
        <?php endif; ?>
        
        <?php if ($stats['invited_count'] > 0): ?>
        <form method="POST" class="action-form" style="margin-top: 1.5rem;">
            <button type="submit" name="expire_old" class="btn btn-warning" onclick="return confirm('This will expire all invitations that have passed their 24-hour deadline. Continue?')">
                ⏰ Expire Old Invitations
            </button>
        </form>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    
    <!-- Waitlist Table -->
    <div class="waitlist-table">
        <div class="table-header">
            <h2 class="table-title">Waitlist Users (<?php echo $stats['total_count']; ?>)</h2>
        </div>
        
        <?php if ($waitlist_result && $waitlist_result->num_rows > 0): ?>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Position</th>
                        <th>User</th>
                        <th>Contact</th>
                        <th>Joined</th>
                        <th>Status</th>
                        <th>Expires</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $position = 1;
                    while ($user = $waitlist_result->fetch_assoc()): 
                        $show_position = $user['status'] === 'waiting';
                    ?>
                    <tr>
                        <td>
                            <?php if ($show_position): ?>
                                <span class="position-badge">#<?php echo $position++; ?></span>
                            <?php else: ?>
                                <span style="color: #a0aec0;">—</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                        </td>
                        <td>
                            <div><?php echo htmlspecialchars($user['email']); ?></div>
                            <?php if ($user['phone']): ?>
                                <div style="font-size: 0.85rem; color: #718096;"><?php echo htmlspecialchars($user['phone']); ?></div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo date('M j, Y \a\t g:i A', strtotime($user['joined_at'])); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo $user['status']; ?>">
                                <?php 
                                switch($user['status']) {
                                    case 'waiting': echo '⏳ Waiting'; break;
                                    case 'invited': echo '✉️ Invited'; break;
                                    case 'expired': echo '⏰ Expired'; break;
                                    case 'purchased': echo '✅ Purchased'; break;
                                }
                                ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($user['expires_at']): ?>
                                <?php 
                                $expires = strtotime($user['expires_at']);
                                $now = time();
                                $is_expired = $expires < $now;
                                ?>
                                <span style="color: <?php echo $is_expired ? '#e53e3e' : '#4a5568'; ?>;">
                                    <?php echo date('M j \a\t g:i A', $expires); ?>
                                    <?php if ($is_expired): ?>
                                        <br><small style="color: #e53e3e;">Expired</small>
                                    <?php endif; ?>
                                </span>
                            <?php else: ?>
                                <span style="color: #a0aec0;">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="empty-state">
            <div class="empty-icon">📋</div>
            <p>No users on the waitlist yet.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
