<?php
include '../includes/db.php';
include '../includes/base_url.php';

// Get month parameter
$month = $_GET['month'] ?? 'current';

// Set date range based on month
if ($month === 'next') {
    // Next month events
    $start_date = date('Y-m-01', strtotime('+1 month'));
    $end_date = date('Y-m-t', strtotime('+1 month'));
} else {
    // Current month events (default)
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
}

// Get events for the specified month
$sql = "SELECT e.*, 
        MIN(t.price_cents) as min_price,
        MAX(t.price_cents) as max_price,
        COUNT(t.id) as ticket_count
        FROM events e 
        LEFT JOIN ticket_types t ON e.id = t.event_id 
        WHERE e.status = 'published' 
        AND e.starts_at >= ? 
        AND e.starts_at <= ? 
        GROUP BY e.id 
        ORDER BY e.starts_at ASC 
        LIMIT 8";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$result = $stmt->get_result();

// Generate HTML for events
if ($result && $result->num_rows > 0) {
    while ($event = $result->fetch_assoc()) {
?>
        <div class="minimal-event-card card" onclick="window.location.href='event_view.php?id=<?php echo $event['id']; ?>'">
            <!-- Event Image -->
            <div class="minimal-event-image">
                <?php if ($event['image_path']): ?>
                    <img src="<?php echo $event['image_path']; ?>" alt="<?php echo $event['title']; ?>">
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
    <?php
    }
} else {
    // No events found
    $monthName = ($month === 'next') ? 'next month' : 'this month';
    ?>
    <div class="text-center p-4" style="background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius); color: var(--gray-600); margin: 2rem 0; grid-column: 1 / -1;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">🎪</div>
        <h3 style="margin-bottom: 1rem; color: var(--primary);">No events found for <?php echo $monthName; ?></h3>
        <p style="color: var(--gray-600);">Check back soon for exciting events!</p>
    </div>
<?php
}

$stmt->close();
$conn->close();
?>