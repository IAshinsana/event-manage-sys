<?php
$page_title = "Home";
include 'includes/header.php';
include 'includes/db.php';
?>
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
        LIMIT 8";
$result = $conn->query($sql);
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <h1>Discover Amazing Events</h1>
        <p>Find and book tickets for the best events in Sri Lanka</p>
        <a href="events.php" class="btn btn-accent">Browse Events</a>
    </div>
</section>

<!-- Upcoming Events Section -->
<section class="events-section">
    <div class="container">
        <div class="d-flex justify-between items-center mb-4">
            <h2 class="section-title mb-0">What's happening in <?php echo date('F'); ?></h2>
            <div class="d-flex gap-2">
                <button class="btn btn-sm btn-primary month-filter" data-month="current">This Month</button>
                <button class="btn btn-sm btn-outline month-filter" data-month="next">Next Month</button>
                <a href="events.php" class="btn btn-sm btn-outline">View more →</a>
            </div>
        </div>
        
        <?php if ($result && $result->num_rows > 0): ?>
            <div class="minimal-events-grid">
                <?php while ($event = $result->fetch_assoc()): ?>
                    <div class="minimal-event-card card" onclick="window.location.href='event_view.php?id=<?php echo $event['id']; ?>'">
                        <!-- Event Image -->
                        <div class="minimal-event-image">
                            <?php if ($event['image_path'] && file_exists($event['image_path'])): ?>
                                <img src="<?php echo $BASE_URL; ?><?php echo $event['image_path']; ?>" alt="<?php echo $event['title']; ?>">
                            <?php else: ?>
                                <div class="minimal-image-placeholder">
                                    <span>🎪</span>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Event Content -->
                        <div class="card-body">
                            <h3 class="minimal-event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                            
                            <div class="minimal-event-meta">
                                <div class="minimal-event-date">
                                    <?php echo date('M d, Y • H:i', strtotime($event['starts_at'])); ?>
                                </div>
                                
                                <?php if ($event['venue']): ?>
                                    <div class="minimal-event-venue">
                                        <?php echo htmlspecialchars($event['venue']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="minimal-event-footer">
                                <div class="minimal-event-price">
                                    <?php if ($event['ticket_count'] > 0 && $event['min_price']): ?>
                                        <span class="price-label">From</span>
                                        <span class="price-amount"><?php echo number_format($event['min_price'] / 100, 0); ?> LKR</span>
                                        <span class="price-note">onwards</span>
                                    <?php else: ?>
                                        <span class="sold-out-text">Sold Out</span>
                                    <?php endif; ?>
                                </div>
                                
                                <?php if ($event['ticket_count'] > 0): ?>
                                    <button class="btn btn-primary btn-sm minimal-buy-btn">
                                        Buy Tickets
                                    </button>
                                <?php else: ?>
                                    <button class="btn btn-sm minimal-buy-btn sold-out-btn" disabled>
                                        Sold Out
                                    </button>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="text-center p-4" style="background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); color: var(--gray-600); margin: 2rem 0;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">🎪</div>
                <h3 style="margin-bottom: 1rem; color: var(--primary);">No upcoming events</h3>
                <p style="color: var(--gray-600);">Check back soon for exciting events!</p>
            </div>
        <?php endif; ?>
        
        <?php if ($result && $result->num_rows >= 8): ?>
            <div class="text-center mt-4">
                <a href="events.php" class="btn btn-outline">
                    View All Events
                </a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
