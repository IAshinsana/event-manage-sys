<?php
$page_title = "My Events";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

if (!is_approved_coordinator()) {
    header('Location: dashboard.php?error=not_approved');
    exit;
}

// Get coordinator's events
$user_id = $_SESSION['user_id'];
$events_sql = "SELECT e.*, COUNT(tt.id) as ticket_types, 
               COUNT(DISTINCT CASE WHEN tt.id IS NOT NULL THEN tt.id END) as unique_ticket_types
               FROM events e 
               LEFT JOIN ticket_types tt ON e.id = tt.event_id 
               WHERE e.created_by = $user_id 
               GROUP BY e.id 
               ORDER BY e.created_at DESC";
$events_result = $conn->query($events_sql);
?>

<style>
.events-container {
    min-height: 100vh;
    background: #f8f9fa;
    padding: 2rem 0;
}

.events-header {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.event-card {
    background: white;
    border-radius: 15px;
    padding: 2rem;
    margin-bottom: 1.5rem;
    box-shadow: 0 4px 15px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
}

.event-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    border-color: #007bff;
}

.event-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.event-meta {
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 1rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
}

.status-badge-new {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-approved {
    background: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.status-pending {
    background: #fff3cd;
    color: #856404;
    border: 1px solid #ffeaa7;
}

.status-rejected {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 1rem;
    margin: 1.5rem 0;
}

.stat-card {
    background: #f8f9fa;
    color: #333;
    padding: 1.5rem;
    border-radius: 12px;
    text-align: center;
    border: 2px solid #e9ecef;
    transition: all 0.3s ease;
}

.stat-card:hover {
    border-color: #007bff;
    transform: translateY(-2px);
}

.stat-card.primary {
    border-color: #007bff;
    background: linear-gradient(135deg, #e3f2fd, #f0f8ff);
}

.stat-card.success {
    border-color: #28a745;
    background: linear-gradient(135deg, #e8f5e8, #f0fff0);
}

.stat-card.warning {
    border-color: #ffc107;
    background: linear-gradient(135deg, #fff8e1, #fffef7);
}

.stat-card.info {
    border-color: #17a2b8;
    background: linear-gradient(135deg, #e0f2f1, #f0fdff);
}

.stat-number {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
    color: #333;
}

.stat-label {
    font-size: 0.8rem;
    color: #666;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    font-weight: 500;
}

.action-buttons {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
    margin-top: 1.5rem;
}

.btn-modern {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    background: white;
}

.btn-primary-modern {
    border-color: #007bff;
    color: #007bff;
}

.btn-primary-modern:hover {
    background: #007bff;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.3);
}

.btn-secondary-modern {
    border-color: #6c757d;
    color: #6c757d;
}

.btn-secondary-modern:hover {
    background: #6c757d;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
}

.btn-success-modern {
    border-color: #28a745;
    color: #28a745;
}

.btn-success-modern:hover {
    background: #28a745;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-info-modern {
    border-color: #17a2b8;
    color: #17a2b8;
}

.btn-info-modern:hover {
    background: #17a2b8;
    color: white;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(23, 162, 184, 0.3);
}

.btn-warning-modern {
    border-color: #ffc107;
    color: #856404;
}

.btn-warning-modern:hover {
    background: #ffc107;
    color: #212529;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 193, 7, 0.3);
}

.event-image {
    width: 100px;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 2px solid #e9ecef;
}

.no-events {
    text-align: center;
    padding: 4rem 2rem;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border: 1px solid #e9ecef;
}

.no-events-icon {
    font-size: 4rem;
    margin-bottom: 1rem;
    opacity: 0.5;
}

.category-tag {
    background: #e9ecef;
    color: #495057;
    padding: 0.25rem 0.75rem;
    border-radius: 12px;
    font-size: 0.8rem;
    border: 1px solid #dee2e6;
}
</style>

<div class="events-container">
    <div class="container">
        <div class="events-header">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="margin: 0; color: #333; font-size: 2.2rem; font-weight: 700;">
                        📋 My Events Dashboard
                    </h1>
                    <p style="color: #666; margin: 0.5rem 0 0 0; font-size: 1rem;">
                        Manage your events, tickets, and track performance
                    </p>
                </div>
                <div style="display: flex; gap: 1rem;">
                    <a href="event_create.php" class="btn-modern btn-primary-modern">
                        ➕ Create New Event
                    </a>
                    <a href="dashboard.php" class="btn-modern btn-secondary-modern">
                        ← Dashboard
                    </a>
                </div>
            </div>
        </div>

        <?php if ($events_result && $events_result->num_rows > 0): ?>
            <?php while ($event = $events_result->fetch_assoc()): ?>
                <div class="event-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                        <div style="flex: 1;">
                            <div class="event-title">
                                <span><?php echo htmlspecialchars($event['title']); ?></span>
                                <span class="status-badge-new status-<?php echo $event['approval_status']; ?>">
                                    <?php echo ucfirst($event['approval_status']); ?>
                                </span>
                            </div>
                            
                            <div class="event-meta">
                                <span>📍 <?php echo htmlspecialchars($event['venue']); ?></span>
                                <span>📅 <?php echo date('M j, Y', strtotime($event['starts_at'])); ?></span>
                                <span>⏰ <?php echo date('g:i A', strtotime($event['starts_at'])); ?></span>
                                <?php if ($event['category']): ?>
                                    <span class="category-tag">
                                        <?php echo htmlspecialchars($event['category']); ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <?php if ($event['image_path']): ?>
                            <div style="margin-left: 1rem;">
                                <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                                     alt="Event Image" 
                                     class="event-image">
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="stats-grid">
                        <div class="stat-card primary">
                            <div class="stat-number"><?php echo $event['unique_ticket_types']; ?></div>
                            <div class="stat-label">Ticket Types</div>
                        </div>
                        <div class="stat-card success">
                            <div class="stat-number">0</div>
                            <div class="stat-label">Tickets Sold</div>
                        </div>
                        <div class="stat-card warning">
                            <div class="stat-number">LKR 0</div>
                            <div class="stat-label">Revenue</div>
                        </div>
                        <div class="stat-card info">
                            <div class="stat-number"><?php echo max(0, strtotime($event['starts_at']) - time()) > 0 ? ceil((strtotime($event['starts_at']) - time()) / 86400) : 0; ?></div>
                            <div class="stat-label">Days to Event</div>
                        </div>
                    </div>
                    
                    <div class="action-buttons">
                        <a href="event_edit.php?id=<?php echo $event['id']; ?>" class="btn-modern btn-secondary-modern">
                            ✏️ Edit Event
                        </a>
                        <a href="../event_view.php?id=<?php echo $event['id']; ?>" class="btn-modern btn-secondary-modern" target="_blank">
                            👁️ Preview
                        </a>
                        <?php if ($event['approval_status'] === 'approved'): ?>
                            <a href="tickets_manage.php?event_id=<?php echo $event['id']; ?>" class="btn-modern btn-success-modern">
                                🎫 Manage Tickets
                            </a>
                            <a href="waitlist_manage.php?event_id=<?php echo $event['id']; ?>" class="btn-modern btn-warning-modern">
                                📋 Manage Waitlist
                            </a>
                            <a href="../checkin.php?event_id=<?php echo $event['id']; ?>" class="btn-modern btn-info-modern">
                                ✅ Check-in Portal
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="no-events">
                <div class="no-events-icon">📋</div>
                <h2 style="color: #333; margin-bottom: 1rem;">No Events Created Yet</h2>
                <p style="color: #666; margin-bottom: 2rem; font-size: 1rem;">
                    Start your event management journey by creating your first event
                </p>
                <a href="event_create.php" class="btn-modern btn-primary-modern" style="font-size: 1rem; padding: 1rem 2rem;">
                    ➕ Create Your First Event
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
