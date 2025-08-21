<?php
/**
 * Waitlist Grace Period Automation Script
 * 
 * This script should be run periodically (e.g., every hour via cron job) to:
 * 1. Expire old invitations that have passed their 24-hour deadline
 * 2. Send reminder emails to users with expiring invitations
 * 
 * Cron job example (run every hour):
 * 0 * * * * /usr/bin/php /path/to/your/project/scripts/waitlist_automation.php
 */

// Include database connection
require_once '../includes/db.php';

// Set timezone to avoid issues
date_default_timezone_set('Asia/Colombo');

echo "[" . date('Y-m-d H:i:s') . "] Starting waitlist automation...\n";

try {
    // 1. Expire old invitations
    $expire_sql = "UPDATE event_waitlist 
                   SET status = 'expired', 
                       expired_at = NOW()
                   WHERE status = 'invited' 
                   AND expires_at < NOW()
                   AND expires_at IS NOT NULL";
    
    $result = $conn->query($expire_sql);
    $expired_count = $conn->affected_rows;
    
    if ($expired_count > 0) {
        echo "✅ Expired $expired_count old invitations\n";
        
        // Log expired invitations for reference
        $log_sql = "SELECT w.*, e.title as event_title, u.name as user_name, u.email 
                    FROM event_waitlist w
                    JOIN events e ON w.event_id = e.id
                    JOIN users u ON w.user_id = u.id
                    WHERE w.status = 'expired' 
                    AND w.expired_at >= DATE_SUB(NOW(), INTERVAL 1 HOUR)";
        
        $log_result = $conn->query($log_sql);
        if ($log_result && $log_result->num_rows > 0) {
            echo "Expired invitations:\n";
            while ($row = $log_result->fetch_assoc()) {
                echo "  - {$row['user_name']} ({$row['email']}) for '{$row['event_title']}'\n";
            }
        }
    } else {
        echo "ℹ️  No invitations to expire\n";
    }
    
    // 2. Find invitations expiring in the next 4 hours (for reminder emails)
    $reminder_sql = "SELECT w.*, e.title as event_title, e.id as event_id, 
                            u.name as user_name, u.email, u.id as user_id,
                            TIMESTAMPDIFF(HOUR, NOW(), w.expires_at) as hours_remaining
                     FROM event_waitlist w
                     JOIN events e ON w.event_id = e.id
                     JOIN users u ON w.user_id = u.id
                     WHERE w.status = 'invited' 
                     AND w.expires_at > NOW()
                     AND w.expires_at <= DATE_ADD(NOW(), INTERVAL 4 HOUR)
                     AND (w.reminder_sent_at IS NULL OR w.reminder_sent_at < DATE_SUB(NOW(), INTERVAL 12 HOUR))";
    
    $reminder_result = $conn->query($reminder_sql);
    
    if ($reminder_result && $reminder_result->num_rows > 0) {
        echo "⏰ Found {$reminder_result->num_rows} invitations expiring soon:\n";
        
        while ($row = $reminder_result->fetch_assoc()) {
            $hours_remaining = $row['hours_remaining'];
            echo "  - {$row['user_name']} ({$row['email']}) for '{$row['event_title']}' - {$hours_remaining}h remaining\n";
            
            // Here you would send reminder emails
            // For now, we'll just mark that reminder should be sent
            
            // Update reminder_sent_at to prevent duplicate reminders
            $update_reminder_sql = "UPDATE event_waitlist 
                                   SET reminder_sent_at = NOW() 
                                   WHERE id = ?";
            $stmt = $conn->prepare($update_reminder_sql);
            $stmt->bind_param("i", $row['id']);
            $stmt->execute();
            
            // TODO: Implement actual email sending here
            // Example email content:
            /*
            $subject = "⏰ Your waitlist invitation expires soon - {$row['event_title']}";
            $message = "
            Hi {$row['user_name']},
            
            Your invitation to purchase tickets for '{$row['event_title']}' will expire in {$hours_remaining} hours.
            
            Don't miss out! Complete your purchase now:
            https://yourdomain.com/waitlist_join.php?event_id={$row['event_id']}&token=your_token
            
            If you don't purchase by " . date('M j, Y \a\t g:i A', strtotime($row['expires_at'])) . ", your spot will be offered to the next person on the waitlist.
            
            Best regards,
            Your Event Team
            ";
            
            // mail($row['email'], $subject, $message);
            */
        }
    } else {
        echo "ℹ️  No upcoming invitation expirations\n";
    }
    
    // 3. Generate summary statistics
    $stats_sql = "SELECT 
                    COUNT(CASE WHEN status = 'waiting' THEN 1 END) as waiting_count,
                    COUNT(CASE WHEN status = 'invited' THEN 1 END) as invited_count,
                    COUNT(CASE WHEN status = 'expired' THEN 1 END) as expired_count,
                    COUNT(CASE WHEN status = 'purchased' THEN 1 END) as purchased_count
                  FROM event_waitlist";
    
    $stats_result = $conn->query($stats_sql);
    if ($stats_result) {
        $stats = $stats_result->fetch_assoc();
        echo "\n📊 Waitlist Summary:\n";
        echo "  - Waiting: {$stats['waiting_count']}\n";
        echo "  - Invited: {$stats['invited_count']}\n";
        echo "  - Expired: {$stats['expired_count']}\n";
        echo "  - Purchased: {$stats['purchased_count']}\n";
    }
    
    echo "\n✅ Waitlist automation completed successfully\n";
    
} catch (Exception $e) {
    echo "❌ Error during waitlist automation: " . $e->getMessage() . "\n";
    error_log("Waitlist automation error: " . $e->getMessage());
}

// Close database connection
$conn->close();

echo "[" . date('Y-m-d H:i:s') . "] Automation finished\n\n";
?>
