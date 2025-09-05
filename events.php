<?php
$page_title = "All Events";
include 'includes/header.php';
include 'includes/db.php';
?>
<?php

// Get categories for filter
$categories_sql = "SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category != '' ORDER BY category";
$categories_result = $conn->query($categories_sql);

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL query - Include both upcoming and past events
$sql = "SELECT e.*, 
        MIN(t.price_cents) as min_price,
        MAX(t.price_cents) as max_price,
        COUNT(t.id) as ticket_count,
        CASE 
            WHEN e.starts_at > NOW() THEN 'upcoming'
            WHEN e.starts_at <= NOW() AND e.ends_at > NOW() THEN 'ongoing' 
            ELSE 'ended'
        END as event_status
        FROM events e 
        LEFT JOIN ticket_types t ON e.id = t.event_id 
        WHERE e.status = 'published'";

if ($search) {
    $sql .= " AND (e.title LIKE '%$search%' OR e.description LIKE '%$search%')";
}

if ($category) {
    $sql .= " AND e.category = '$category'";
}

$sql .= " GROUP BY e.id ORDER BY e.starts_at ASC";

$result = $conn->query($sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div class="d-flex justify-between items-center mb-4">
        <h1 class="section-title mb-0">All Events</h1>
    </div>
    
    <!-- Search and Filter -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="d-flex gap-3" style="flex-wrap: wrap;">
                <div style="flex: 1; min-width: 200px;">
                    <input type="text" name="search" placeholder="Search events..." class="form-control" value="<?php echo htmlspecialchars($search); ?>">
                </div>
                
                <div style="min-width: 150px;">
                    <select name="category" class="form-control">
                        <option value="">All Categories</option>
                        <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                            <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($cat['category']); ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($cat['category']); ?>
                                </option>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <button type="submit" class="btn btn-primary">Search</button>
                <?php if ($search || $category): ?>
                    <a href="events.php" class="btn btn-outline">Clear</a>
                <?php endif; ?>
            </form>
        </div>
    </div>
    
    <!-- Events Grid -->
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="minimal-events-grid">
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="minimal-event-card card <?php echo $event['event_status'] === 'ended' ? 'event-ended' : ''; ?>" onclick="window.location.href='event_view.php?id=<?php echo $event['id']; ?>'">
                    <!-- Event Image -->
                    <div class="minimal-event-image">
                        <?php if ($event['image_path'] && file_exists($event['image_path'])): ?>
                            <img src="<?php echo $BASE_URL; ?><?php echo $event['image_path']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <?php else: ?>
                            <div class="minimal-image-placeholder">
                                <span>🎪</span>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Event Status Badge -->
                        <?php if ($event['event_status'] === 'ended'): ?>
                            <div class="event-status-badge ended">Event Ended</div>
                        <?php elseif ($event['event_status'] === 'ongoing'): ?>
                            <div class="event-status-badge ongoing">Live Now</div>
                        <?php elseif ($event['category']): ?>
                            <div class="event-category-badge"><?php echo htmlspecialchars($event['category']); ?></div>
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
                                <?php if ($event['event_status'] === 'ended'): ?>
                                    <span class="event-ended-text">Event Ended</span>
                                <?php elseif ($event['ticket_count'] > 0 && $event['min_price']): ?>
                                    <span class="price-label">From</span>
                                    <span class="price-amount"><?php echo number_format($event['min_price'] / 100, 0); ?> LKR</span>
                                    <span class="price-note">onwards</span>
                                <?php else: ?>
                                    <span class="sold-out-text">Sold Out</span>
                                <?php endif; ?>
                            </div>
                            
                            <?php if ($event['event_status'] === 'ended'): ?>
                                <button class="btn btn-sm minimal-buy-btn" disabled style="background: var(--gray-400); border-color: var(--gray-400); cursor: not-allowed;">
                                    Ended
                                </button>
                            <?php elseif ($event['ticket_count'] > 0): ?>
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
        <div class="card text-center p-4" style="max-width: 600px; margin: 2rem auto;">
            <div class="card-body">
                <div style="font-size: 3rem; margin-bottom: 1.5rem;">🎪</div>
                <h3 style="color: var(--primary); margin-bottom: 1rem; font-size: 1.5rem;">No Events Found</h3>
                <?php if ($search || $category): ?>
                    <p style="color: var(--gray-600); margin-bottom: 2rem;">
                        We couldn't find any events matching your search criteria.<br>
                        Try adjusting your filters or search terms.
                    </p>
                    <div class="d-flex gap-3 justify-center" style="flex-wrap: wrap;">
                        <a href="events.php" class="btn btn-primary">
                            View All Events
                        </a>
                        <button onclick="history.back()" class="btn btn-outline">
                            Go Back
                        </button>
                    </div>
                <?php else: ?>
                    <p style="color: var(--gray-600); margin-bottom: 2rem;">
                        No events are currently available.<br>
                        Check back soon for exciting upcoming events!
                    </p>
                    <div class="alert alert-info" style="text-align: left; margin-top: 2rem;">
                        <strong>💡 Tip:</strong> New events are added regularly. 
                        <a href="mailto:info@eventtickets.com" style="color: var(--accent); text-decoration: none;">Contact us</a> if you'd like to organize an event!
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
