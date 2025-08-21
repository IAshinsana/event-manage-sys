<?php
$page_title = "All Events";
include 'includes/header.php';
include 'includes/db.php';
?>
<link rel="stylesheet" href="assets/css/modern_event_cards.css">
<?php

// Get categories for filter
$categories_sql = "SELECT DISTINCT category FROM events WHERE category IS NOT NULL AND category != '' ORDER BY category";
$categories_result = $conn->query($categories_sql);

// Get search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$category = isset($_GET['category']) ? $_GET['category'] : '';

// Build SQL query
$sql = "SELECT e.*, 
        MIN(t.price_cents) as min_price,
        MAX(t.price_cents) as max_price,
        COUNT(t.id) as ticket_count
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
    <h1 class="section-title">All Events</h1>
    
    <!-- Search and Filter -->
    <div style="background: white; padding: 1.5rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <form method="GET" class="d-flex" style="gap: 1rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="search" placeholder="Search events..." class="form-control" value="<?php echo $search; ?>">
            </div>
            
            <div style="min-width: 150px;">
                <select name="category" class="form-control">
                    <option value="">All Categories</option>
                    <?php if ($categories_result && $categories_result->num_rows > 0): ?>
                        <?php while ($cat = $categories_result->fetch_assoc()): ?>
                            <option value="<?php echo $cat['category']; ?>" <?php echo $category === $cat['category'] ? 'selected' : ''; ?>>
                                <?php echo $cat['category']; ?>
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
    
    <!-- Events Grid -->
    <?php if ($result && $result->num_rows > 0): ?>
        <div class="events-grid">
            <?php while ($event = $result->fetch_assoc()): ?>
                <div class="event-card <?php echo (strtotime($event['created_at']) > strtotime('-7 days')) ? 'new-event' : ''; ?>">
                    <!-- Status Indicator -->
                    <div class="event-status-indicator event-status-upcoming"></div>
                    
                    <!-- Event Image -->
                    <div class="event-image">
                        <?php if ($event['image_path'] && file_exists($event['image_path'])): ?>
                            <img src="<?php echo $BASE_URL; ?><?php echo $event['image_path']; ?>" alt="<?php echo htmlspecialchars($event['title']); ?>">
                        <?php else: ?>
                            <span>🎪 Event Image Coming Soon</span>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Event Content -->
                    <div class="event-content">
                        <h3 class="event-title"><?php echo htmlspecialchars($event['title']); ?></h3>
                        
                        <?php if ($event['category']): ?>
                            <div class="event-category">
                                <?php echo htmlspecialchars($event['category']); ?>
                            </div>
                        <?php endif; ?>
                        
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
        <div style="background: white; padding: 4rem 2rem; border-radius: 12px; box-shadow: 0 8px 25px rgba(0,0,0,0.1); text-align: center; max-width: 600px; margin: 0 auto;">
            <div style="font-size: 4rem; margin-bottom: 1.5rem;">🎪</div>
            <h3 style="color: #333; margin-bottom: 1rem; font-size: 1.5rem;">No Events Found</h3>
            <?php if ($search || $category): ?>
                <p style="color: #666; margin-bottom: 2rem; font-size: 1.1rem;">
                    We couldn't find any events matching your search criteria.<br>
                    Try adjusting your filters or search terms.
                </p>
                <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap;">
                    <a href="events.php" class="btn btn-primary" style="padding: 0.75rem 2rem;">
                        🔍 View All Events
                    </a>
                    <button onclick="history.back()" class="btn btn-outline" style="padding: 0.75rem 2rem;">
                        ← Go Back
                    </button>
                </div>
            <?php else: ?>
                <p style="color: #666; margin-bottom: 2rem; font-size: 1.1rem;">
                    No events are currently available.<br>
                    Check back soon for exciting upcoming events!
                </p>
                <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #007bff; margin-top: 2rem;">
                    <strong style="color: #007bff;">💡 Tip:</strong> New events are added regularly. 
                    <a href="mailto:info@events.com" style="color: #007bff; text-decoration: none;">Contact us</a> if you'd like to organize an event!
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
