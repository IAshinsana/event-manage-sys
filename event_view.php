<?php
include 'includes/header.php';
include 'includes/db.php';

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$event_id) {
    header('Location: events.php');
    exit();
}

// Get event details
$sql = "SELECT * FROM events WHERE id = $event_id AND status = 'published'";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    header('Location: events.php');
    exit();
}

$event = $result->fetch_assoc();
$page_title = $event['title'];

// Check if event has ended
$event_end_time = strtotime($event['ends_at']);
$current_time = time();
$event_has_ended = $event_end_time < $current_time;

// Get ticket types
$tickets_sql = "SELECT * FROM ticket_types WHERE event_id = $event_id ORDER BY price_cents ASC";
$tickets_result = $conn->query($tickets_sql);
?>

<link rel="stylesheet" href="assets/css/event_view.css">
<script src="assets/js/countdown.js"></script>
?>

<!-- Event Hero Section -->
<section class="event-hero" style="background-image: url('<?php echo $event['image_path'] && file_exists($event['image_path']) ? $BASE_URL . $event['image_path'] : $BASE_URL . 'assets/img/header-bg.svg'; ?>');">
    <div class="container">
        <div class="event-hero-content">
            <h1 class="event-title-main"><?php echo htmlspecialchars($event['title']); ?></h1>
            
            <?php if ($event['category']): ?>
                <div class="event-category-badge">
                    <?php echo htmlspecialchars($event['category']); ?>
                </div>
            <?php endif; ?>
            
            <div class="event-date-time">
                📅 <?php echo date('F j, Y', strtotime($event['starts_at'])); ?> at <?php echo date('g:i A', strtotime($event['starts_at'])); ?>
            </div>
            
            <?php if ($event['venue']): ?>
                <div class="event-venue">
                    📍 <?php echo htmlspecialchars($event['venue']); ?>
                </div>
            <?php endif; ?>
            
            <!-- Countdown Timer Container -->
            <div class="countdown-container">
                <div id="countdown"></div>
            </div>
            
            <!-- Book Tickets Button -->
            <?php if ($tickets_result && $tickets_result->num_rows > 0 && !$event_has_ended): ?>
                <a href="#tickets" class="btn-book-tickets" id="mainBookButton">
                    🎫 Book Tickets Now
                </a>
            <?php elseif ($event_has_ended): ?>
                <button class="btn-book-tickets" disabled style="background: #6c757d; cursor: not-allowed;">
                    ⏰ Event Has Ended
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container">
    <div class="event-content-grid">
        
        <!-- Event Details -->
        <div class="event-details-card">
            <h2 class="event-details-title">About This Event</h2>
            <?php if ($event['description']): ?>
                <div class="event-description">
                    <?php echo nl2br(htmlspecialchars($event['description'])); ?>
                </div>
            <?php else: ?>
                <div class="event-description" style="color: #999; font-style: italic;">
                    Event details will be updated soon. Stay tuned for more information!
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Event Info Sidebar -->
        <div class="event-sidebar-card">
            <h3 style="color: #333; margin-bottom: 1.5rem; font-size: 1.4rem; font-weight: 600;">Event Information</h3>
            
            <div class="event-info-item">
                <div class="event-info-label">
                    📅 Date & Time
                </div>
                <div class="event-info-value">
                    <?php echo date('l, F j, Y', strtotime($event['starts_at'])); ?><br>
                    <?php echo date('g:i A', strtotime($event['starts_at'])); ?> - <?php echo date('g:i A', strtotime($event['ends_at'])); ?>
                </div>
            </div>
            
            <?php if ($event['venue']): ?>
                <div class="event-info-item">
                    <div class="event-info-label">
                        📍 Venue
                    </div>
                    <div class="event-info-value">
                        <?php echo htmlspecialchars($event['venue']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($event['show_organizer'] && $event['organizer']): ?>
                <div class="event-info-item">
                    <div class="event-info-label">
                        👥 Organizer
                    </div>
                    <div class="event-info-value">
                        <?php echo htmlspecialchars($event['organizer']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($event['show_booking_phone'] && $event['booking_phone']): ?>
                <div class="event-info-item">
                    <div class="event-info-label">
                        📞 Contact
                    </div>
                    <div class="event-info-value">
                        <?php echo htmlspecialchars($event['booking_phone']); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if ($event['category']): ?>
                <div class="event-info-item">
                    <div class="event-info-label">
                        🏷️ Category
                    </div>
                    <div class="event-info-value">
                        <?php echo htmlspecialchars($event['category']); ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- Tickets Section -->
    <?php if ($tickets_result && $tickets_result->num_rows > 0): ?>
        <div id="tickets" class="tickets-section">
            <h2 class="tickets-title">Available Tickets</h2>
            
            <?php if ($event_has_ended): ?>
                <div style="background: linear-gradient(135deg, #f8d7da, #f5c6cb); border: 1px solid #f5c6cb; color: #721c24; padding: 1rem 1.5rem; border-radius: 10px; margin-bottom: 1.5rem; text-align: center; font-weight: 600;">
                    ⚠️ Ticket sales have ended as this event has already concluded.
                </div>
            <?php endif; ?>
            
            <div style="display: grid; gap: 1rem; margin-top: 1.5rem;">
                <?php while ($ticket = $tickets_result->fetch_assoc()): ?>
                    <?php $available = max(0, $ticket['qty_total'] - $ticket['qty_sold']); ?>
                    <div class="ticket-card">
                        <div class="ticket-info">
                            <h4><?php echo htmlspecialchars($ticket['name']); ?></h4>
                            <div class="ticket-price">
                                LKR <?php echo number_format($ticket['price_cents'] / 100, 2); ?>
                            </div>
                            <div class="ticket-availability">
                                <?php if ($event_has_ended): ?>
                                    <span style="color: #dc3545;">Event Ended</span>
                                <?php elseif ($available > 0): ?>
                                    <span class="ticket-available"><?php echo $available; ?> tickets available</span>
                                <?php else: ?>
                                    <span class="ticket-sold-out">Sold Out</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="ticket-actions">
                            <?php if ($event_has_ended): ?>
                                <button class="btn-sold-out">Event Ended</button>
                            <?php elseif ($available > 0): ?>
                                <?php if (is_logged_in()): ?>
                                    <a href="register_start.php?event_id=<?php echo $event_id; ?>" class="btn-book-now">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; fill: currentColor;">
                                            <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                        </svg>
                                        Book Now
                                    </a>
                                <?php else: ?>
                                    <a href="login.php" class="btn-login">
                                        <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg" style="width: 16px; height: 16px; fill: currentColor;">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                        </svg>
                                        Login to Book
                                    </a>
                                <?php endif; ?>
                            <?php else: ?>
                                <button class="btn-sold-out">Sold Out</button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    <?php else: ?>
        <div class="no-tickets-card">
            <div class="no-tickets-icon">🎫</div>
            <h3 class="no-tickets-title">Tickets Coming Soon</h3>
            <p class="no-tickets-subtitle">Ticket information will be available soon.</p>
        </div>
    <?php endif; ?>
</div>

<script>
// Start countdown timer with event end time checking
startCountdown('<?php echo $event['starts_at']; ?>', 'countdown');

// Check if event has ended on page load
<?php if ($event_has_ended): ?>
document.addEventListener('DOMContentLoaded', function() {
    // Disable all booking buttons immediately for ended events
    const bookButtons = document.querySelectorAll('.btn-book-now, .btn-login, .btn-book-tickets');
    bookButtons.forEach(button => {
        button.style.background = '#6c757d';
        button.style.cursor = 'not-allowed';
        button.onclick = function(e) {
            e.preventDefault();
            alert('Sorry, this event has already ended.');
            return false;
        };
        if (button.tagName === 'A') {
            button.href = 'javascript:void(0)';
        }
    });
});
<?php endif; ?>
</script>

<?php include 'includes/footer.php'; ?>
