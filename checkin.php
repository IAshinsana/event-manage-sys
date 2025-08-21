<?php
$page_title = "Check-in Tickets";
include 'includes/header.php';
include 'includes/db.php';
require_checker();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$results = [];
$error = '';

if ($search && strlen($search) >= 8) { // Only search for 8+ character codes
    // Search by exact ticket code or email (exact match for ticket codes)
    $search_sql = "SELECT a.*, oi.*, tt.name as ticket_name, o.id as order_id, o.status as order_status,
                   e.title as event_title, e.starts_at, e.venue, u.name as user_name 
                   FROM attendees a 
                   JOIN order_items oi ON a.order_item_id = oi.id 
                   JOIN orders o ON oi.order_id = o.id 
                   JOIN ticket_types tt ON oi.ticket_type_id = tt.id 
                   JOIN events e ON o.event_id = e.id 
                   JOIN users u ON o.user_id = u.id 
                   WHERE (a.ticket_code = '$search' OR a.email LIKE '%$search%') 
                   AND o.status = 'paid' 
                   ORDER BY e.starts_at DESC, a.id";
    $search_result = $conn->query($search_sql);
    
    if ($search_result && $search_result->num_rows > 0) {
        while ($row = $search_result->fetch_assoc()) {
            $results[] = $row;
        }
    } else {
        $error = 'No tickets found for: ' . $search;
    }
} elseif ($search && strlen($search) < 8) {
    if (strpos($search, 'GRP') === 0) {
        $error = 'Group codes must be exactly 8 characters (GRP + 5 characters). Example: GRP12345';
    } else {
        $error = 'Please enter a complete 8-character ticket code or email address';
    }
}

// Handle check-in
if ($_POST && isset($_POST['checkin_attendee'])) {
    $attendee_id = (int)$_POST['attendee_id'];
    $checkin_sql = "UPDATE attendees SET checked_in_at = NOW(), checked_in_by = " . $_SESSION['user_id'] . " WHERE id = $attendee_id";
    
    if ($conn->query($checkin_sql)) {
        $success = 'Attendee checked in successfully!';
        // Refresh results
        if ($search) {
            header("Location: checkin.php?search=" . urlencode($search));
            exit();
        }
    } else {
        $error = 'Failed to check in attendee';
    }
}

// Handle check-in all for group tickets
if ($_POST && isset($_POST['checkin_all'])) {
    $ticket_code = $_POST['ticket_code'];
    $checkin_all_sql = "UPDATE attendees a 
                       JOIN order_items oi ON a.order_item_id = oi.id 
                       JOIN orders o ON oi.order_id = o.id 
                       SET a.checked_in_at = NOW(), a.checked_in_by = " . $_SESSION['user_id'] . " 
                       WHERE a.ticket_code = '$ticket_code' AND o.status = 'paid' AND a.checked_in_at IS NULL";
    
    if ($conn->query($checkin_all_sql)) {
        $success = 'All attendees for this group ticket checked in successfully!';
        if ($search) {
            header("Location: checkin.php?search=" . urlencode($search));
            exit();
        }
    } else {
        $error = 'Failed to check in group';
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <h1>🎫 Check-in Tickets</h1>
    
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <!-- Search Form -->
    <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 2rem;">
        <h3>Search Tickets</h3>
        <form method="GET" id="searchForm" style="margin-top: 1rem;">
            <div style="display: flex; gap: 1rem;">
                <input type="text" name="search" id="searchInput" placeholder="Enter 8-character ticket code or email address..." 
                       class="form-control" value="<?php echo $search; ?>" style="flex: 1;" 
                       pattern="[A-Z0-9]{8}|.*@.*" title="Enter 8-character code (letters/numbers) or email">
                <button type="submit" class="btn btn-primary">Search</button>
            </div>
        </form>
        
        <div style="margin-top: 1rem; font-size: 0.9rem; color: #666;">
            <strong>Examples:</strong> ABCD1234, GRP5E7F2, user@example.com
            <br><strong>Note:</strong> Ticket codes must be exactly 8 characters
        </div>
        
        <div id="searchStatus" style="margin-top: 0.5rem; font-size: 0.9rem; display: none;"></div>
    </div>

    <script>
    document.getElementById('searchInput').addEventListener('input', function() {
        const input = this.value.trim();
        const status = document.getElementById('searchStatus');
        
        if (input.length === 0) {
            status.style.display = 'none';
            return;
        }
        
        status.style.display = 'block';
        
        if (input.includes('@')) {
            // Email validation
            if (input.includes('@') && input.includes('.')) {
                status.innerHTML = '<span style="color: #28a745;">✅ Email format detected - ready to search</span>';
                this.style.borderColor = '#28a745';
            } else {
                status.innerHTML = '<span style="color: #ffc107;">⚠️ Complete email address required</span>';
                this.style.borderColor = '#ffc107';
            }
        } else {
            // Ticket code validation
            if (input.length < 8) {
                status.innerHTML = '<span style="color: #dc3545;">⏳ Enter ' + (8 - input.length) + ' more characters (' + input.length + '/8)</span>';
                this.style.borderColor = '#dc3545';
            } else if (input.length === 8) {
                if (/^[A-Z0-9]{8}$/.test(input)) {
                    status.innerHTML = '<span style="color: #28a745;">✅ Valid ticket code format - ready to search</span>';
                    this.style.borderColor = '#28a745';
                    
                    // Auto-submit for exact 8-character codes
                    setTimeout(() => {
                        document.getElementById('searchForm').submit();
                    }, 500);
                } else {
                    status.innerHTML = '<span style="color: #dc3545;">❌ Ticket codes must be uppercase letters and numbers only</span>';
                    this.style.borderColor = '#dc3545';
                }
            } else {
                status.innerHTML = '<span style="color: #dc3545;">❌ Ticket code must be exactly 8 characters</span>';
                this.style.borderColor = '#dc3545';
            }
        }
    });
    
    // Convert input to uppercase for ticket codes
    document.getElementById('searchInput').addEventListener('input', function() {
        if (!this.value.includes('@')) {
            this.value = this.value.toUpperCase();
        }
    });
    </script>
    
    <!-- Search Results -->
    <?php if (!empty($results)): ?>
        <?php
        // Group results by ticket code
        $grouped_results = [];
        foreach ($results as $result) {
            $grouped_results[$result['ticket_code']][] = $result;
        }
        ?>
        
        <div style="margin-bottom: 2rem;">
            <h3>Search Results</h3>
            
            <?php foreach ($grouped_results as $ticket_code => $attendees): ?>
                <?php $first_attendee = $attendees[0]; ?>
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                    
                    <!-- Event Header -->
                    <div style="border-bottom: 1px solid #eee; padding-bottom: 1rem; margin-bottom: 1.5rem;">
                        <h4><?php echo $first_attendee['event_title']; ?></h4>
                        <div style="color: #666;">
                            📅 <?php echo date('F j, Y - g:i A', strtotime($first_attendee['starts_at'])); ?>
                            <?php if ($first_attendee['venue']): ?>
                                | 📍 <?php echo $first_attendee['venue']; ?>
                            <?php endif; ?>
                        </div>
                        <div style="margin-top: 0.5rem;">
                            <strong>Ticket Code:</strong> <span style="font-family: monospace; color: #007bff;"><?php echo $ticket_code; ?></span>
                            | <strong>Order:</strong> #<?php echo $first_attendee['order_id']; ?>
                        </div>
                    </div>
                    
                    <!-- Check-in Summary -->
                    <?php 
                    $total_attendees = count($attendees);
                    $checked_in = 0;
                    foreach ($attendees as $att) {
                        if ($att['checked_in_at']) $checked_in++;
                    }
                    $remaining = $total_attendees - $checked_in;
                    ?>
                    
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(120px, 1fr)); gap: 1rem; margin-bottom: 1.5rem;">
                        <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 8px;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #333;"><?php echo $total_attendees; ?></div>
                            <div style="font-size: 0.9rem; color: #666;">Total Seats</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: #d4edda; border-radius: 8px;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #155724;"><?php echo $checked_in; ?></div>
                            <div style="font-size: 0.9rem; color: #155724;">Checked In</div>
                        </div>
                        <div style="text-align: center; padding: 1rem; background: #fff3cd; border-radius: 8px;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #856404;"><?php echo $remaining; ?></div>
                            <div style="font-size: 0.9rem; color: #856404;">Remaining</div>
                        </div>
                    </div>
                    
                    <!-- Group Actions -->
                    <?php if ($remaining > 0): ?>
                        <div style="text-align: center; margin-bottom: 1.5rem;">
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Check in all remaining attendees for this ticket?')">
                                <input type="hidden" name="ticket_code" value="<?php echo $ticket_code; ?>">
                                <button type="submit" name="checkin_all" class="btn btn-success">
                                    ✅ Check In All Remaining (<?php echo $remaining; ?>)
                                </button>
                            </form>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Individual Attendees -->
                    <div>
                        <h5>Attendees:</h5>
                        <div style="margin-top: 1rem;">
                            <?php foreach ($attendees as $attendee): ?>
                                <div style="border: 1px solid #eee; border-radius: 8px; padding: 1rem; margin-bottom: 1rem; 
                                          background: <?php echo $attendee['checked_in_at'] ? '#f8f9fa' : 'white'; ?>;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <div>
                                            <strong><?php echo $attendee['full_name']; ?></strong>
                                            <div style="color: #666; font-size: 0.9rem;">
                                                <?php echo $attendee['email']; ?> • <?php echo $attendee['ticket_name']; ?>
                                            </div>
                                            <?php if ($attendee['checked_in_at']): ?>
                                                <div style="color: #28a745; font-size: 0.9rem; margin-top: 0.25rem;">
                                                    ✅ Checked in: <?php echo date('M j, Y g:i A', strtotime($attendee['checked_in_at'])); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <div>
                                            <?php if ($attendee['checked_in_at']): ?>
                                                <span style="background: #28a745; color: white; padding: 0.5rem 1rem; border-radius: 20px; font-size: 0.9rem;">
                                                    ✅ Checked In
                                                </span>
                                            <?php else: ?>
                                                <form method="POST" style="display: inline;">
                                                    <input type="hidden" name="attendee_id" value="<?php echo $attendee['id']; ?>">
                                                    <button type="submit" name="checkin_attendee" class="btn btn-success">
                                                        Check In
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($search): ?>
        <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">
            <h3>No Results Found</h3>
            <p style="color: #666;">No paid tickets found for "<?php echo $search; ?>"</p>
            <p style="color: #666; font-size: 0.9rem;">Make sure the ticket code or email is correct and the order is marked as paid.</p>
        </div>
    <?php else: ?>
        <div style="background: white; padding: 3rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); text-align: center;">
            <h3>🔍 Search for Tickets</h3>
            <p style="color: #666;">Enter a ticket code or email address to find tickets for check-in.</p>
        </div>
    <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
