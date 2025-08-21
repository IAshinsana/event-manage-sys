<?php
$page_title = "Home";
include 'includes/header.php';
include 'includes/db.php';
?>
<link rel="stylesheet" href="assets/css/modern_event_cards.css">
<?php

// Get upcoming events
$sql = "SELECT e.*, 
        MIN(t.price_cents) as min_price,
        MAX(t.price_cents) as max_price,
        COUNT(t.id) as ticket_count
        FROM events e 
        LEFT JOIN ticket_types t ON e.id = t.event_id 
        WHERE e.status = 'published' AND e.starts_at > NOW() 
        GROUP BY e.id 
        ORDER BY e.starts_at ASC 
        LIMIT 6";
$result = $conn->query($sql);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Your Gateway to Amazing Events</h1>
        <p>Discover and book tickets for the best events in Sri Lanka</p>
        <a href="events.php" class="btn btn-primary">Browse All Events</a>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="events-section">
    <div class="container">
        <h2 class="section-title">Upcoming Events</h2>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="events-grid">
                <?php while ($event = $result->fetch_assoc()): ?>
                    <div class="event-card <?php echo (strtotime($event['created_at']) > strtotime('-7 days')) ? 'new-event' : ''; ?>">
                        <!-- Status Indicator -->
                        <div class="event-status-indicator event-status-upcoming"></div>
                        
                        <!-- Event Image -->
                        <div class="event-image">
                            <?php if ($event['image_path'] && file_exists($event['image_path'])): ?>
                                <img src="<?php echo $BASE_URL; ?><?php echo $event['image_path']; ?>" alt="<?php echo $event['title']; ?>">
                            <?php else: ?>
                                <span>🎪 Event Image Coming Soon</span>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Event Content -->
                        <div class="event-content">
                            <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="event-date">
                                <?php echo date('F j, Y - g:i A', strtotime($event['starts_at'])); ?>
                            </div>
                            
                            <?php if ($event['venue']): ?>
                                <div class="event-venue">
                                    <?php echo htmlspecialchars($event['venue']); ?>
                                </div>
                            <?php endif; ?>
                            
                            <div class="event-price">
                                <?php if ($event['ticket_count'] > 0 && $event['min_price']): ?>
                                    <span>From</span>
                                    <span class="event-price-amount">LKR <?php echo number_format($event['min_price'] / 100, 2); ?></span>
                                <?php else: ?>
                                    <span class="event-price-waitlist">Join Waitlist</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <!-- Event Footer -->
                        <div class="event-card-footer">
                            <?php if ($event['ticket_count'] > 0): ?>
                                <a href="event_view.php?id=<?php echo $event['id']; ?>" class="btn-view-event">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M7 18c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM1 2v2h2l3.6 7.59-1.35 2.45c-.16.28-.25.61-.25.96 0 1.1.9 2 2 2h12v-2H7.42c-.14 0-.25-.11-.25-.25l.03-.12L8.1 13h7.45c.75 0 1.41-.41 1.75-1.03L21.7 4H5.21l-.94-2H1zm16 16c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                                    </svg>
                                    View Details
                                </a>
                            <?php else: ?>
                                <a href="waitlist_join.php?event_id=<?php echo $event['id']; ?>" class="btn-view-event btn-waitlist">
                                    <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm5 11h-4v4h-2v-4H7v-2h4V7h2v4h4v2z"/>
                                    </svg>
                                    Join Waitlist
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 4rem 2rem; background: white; border-radius: 16px; border: 1px solid #e2e8f0; color: #4a5568; margin: 2rem 0;">
                <div style="font-size: 4rem; margin-bottom: 1rem;">🎪</div>
                <h3 style="margin-bottom: 1rem; color: #2d3748;">No upcoming events at the moment</h3>
                <p style="font-size: 1.1rem; color: #718096;">Check back soon for exciting events!</p>
            </div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows >= 6): ?>
            <div style="text-align: center; margin-top: 3rem;">
                <a href="events.php" class="btn-view-event" style="font-size: 1.1rem; padding: 1rem 2rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                    🎫 View All Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
