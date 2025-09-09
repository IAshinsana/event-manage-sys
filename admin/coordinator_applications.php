<?php
$page_title = "Coordinator Applications";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$success_message = '';
$error_message = '';

// Handle application approval/rejection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $application_id = (int)$_POST['application_id'];
    $action = $_POST['action'];
    $admin_notes = trim($_POST['admin_notes'] ?? '');
    
    if ($action === 'approve' || $action === 'reject') {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        
        $conn->autocommit(false);
        try {
            // Update coordinator application
            $app_sql = "UPDATE coordinator_applications 
                       SET status = '$status', admin_notes = '$admin_notes', reviewed_at = NOW(), reviewed_by = {$_SESSION['user_id']} 
                       WHERE id = $application_id";
            $conn->query($app_sql);
            
            // Get user_id from application
            $user_sql = "SELECT user_id FROM coordinator_applications WHERE id = $application_id";
            $user_result = $conn->query($user_sql);
            $user_id = $user_result->fetch_assoc()['user_id'];
            
            // Update user coordinator status
            $approved_at = $status === 'approved' ? 'NOW()' : 'NULL';
            $update_user_sql = "UPDATE users 
                               SET coordinator_status = '$status', 
                                   coordinator_approved_at = $approved_at,
                                   coordinator_approved_by = {$_SESSION['user_id']} 
                               WHERE id = $user_id";
            $conn->query($update_user_sql);
            
            $conn->commit();
            $success_message = "Application " . ($status === 'approved' ? 'approved' : 'rejected') . " successfully!";
            
        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "Error updating application. Please try again.";
        }
        $conn->autocommit(true);
    }
}

// Get all coordinator applications
$applications_sql = "SELECT ca.*, u.name, u.username, u.coordinator_status,
                     admin.name as reviewed_by_name
                     FROM coordinator_applications ca
                     JOIN users u ON ca.user_id = u.id
                     LEFT JOIN users admin ON ca.reviewed_by = admin.id
                     ORDER BY ca.created_at DESC";
$applications_result = $conn->query($applications_sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>📋 Coordinator Applications</h1>
        <a href="index.php" class="btn btn-outline">← Dashboard</a>
    </div>
    
    <?php if ($success_message): ?>
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($error_message): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($applications_result && $applications_result->num_rows > 0): ?>
        <?php while ($app = $applications_result->fetch_assoc()): ?>
            <div class="admin-section" style="margin-bottom: 2rem;">
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <h3 style="margin: 0; color: #333;">
                            <?php echo $app['name']; ?>
                            <?php if ($app['status'] === 'reapplied'): ?>
                                <span style="font-size: 0.8rem; color: #0056b3; margin-left: 0.5rem;">🔄 Reapplied</span>
                            <?php endif; ?>
                        </h3>
                        <p style="margin: 0.5rem 0; color: #666;">
                            <strong>Username:</strong> <?php echo $app['username']; ?> | 
                            <strong>Applied:</strong> <?php echo date('M j, Y g:i A', strtotime($app['created_at'])); ?>
                            <?php if ($app['status'] === 'reapplied'): ?>
                                <br><strong style="color: #0056b3;">📅 Reapplied:</strong> <?php echo date('M j, Y g:i A', strtotime($app['applied_at'])); ?>
                            <?php endif; ?>
                        </p>
                    </div>
                    <span class="status-badge status-<?php echo $app['status']; ?>">
                        <?php echo ucfirst($app['status']); ?>
                    </span>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 1.5rem;">
                    <!-- Contact Information -->
                    <div>
                        <h4 style="color: #007bff; margin-bottom: 0.5rem;">📞 Contact Information</h4>
                        <p><strong>Email:</strong> <?php echo $app['contact_email']; ?></p>
                        <p><strong>Phone:</strong> <?php echo $app['contact_phone']; ?></p>
                        <?php if ($app['website']): ?>
                            <p><strong>Website:</strong> <a href="<?php echo $app['website']; ?>" target="_blank"><?php echo $app['website']; ?></a></p>
                        <?php endif; ?>
                        <?php if ($app['social_media']): ?>
                            <p><strong>Social Media:</strong> <?php echo $app['social_media']; ?></p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Organization Information -->
                    <div>
                        <h4 style="color: #28a745; margin-bottom: 0.5rem;">🏢 Organization</h4>
                        <p><strong>Name:</strong> <?php echo $app['organization_name']; ?></p>
                        <p><strong>Type:</strong> <?php echo ucfirst($app['organization_type']); ?></p>
                        <p><strong>Experience:</strong> <?php echo $app['experience_years']; ?> years</p>
                    </div>
                </div>
                
                <!-- Previous Events -->
                <?php if ($app['previous_events']): ?>
                    <div style="margin-bottom: 1.5rem;">
                        <h4 style="color: #ffc107; margin-bottom: 0.5rem;">🎭 Previous Events</h4>
                        <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                            <?php echo nl2br($app['previous_events']); ?>
                        </div>
                    </div>
                <?php endif; ?>
                
                <!-- Motivation -->
                <div style="margin-bottom: 1.5rem;">
                    <h4 style="color: #dc3545; margin-bottom: 0.5rem;">💭 Motivation</h4>
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 5px;">
                        <?php echo nl2br($app['motivation']); ?>
                    </div>
                </div>
                
                <?php if ($app['status'] === 'pending' || $app['status'] === 'reapplied'): ?>
                    <!-- Approval/Rejection Form -->
                    <div style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #007bff;">
                        <h4 style="margin-bottom: 1rem;">🎯 Review Application</h4>
                        <form method="POST" style="display: grid; gap: 1rem;">
                            <input type="hidden" name="application_id" value="<?php echo $app['id']; ?>">
                            
                            <div>
                                <label style="display: block; margin-bottom: 0.5rem; font-weight: bold;">Admin Notes</label>
                                <textarea name="admin_notes" rows="3" 
                                          style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;"
                                          placeholder="Add notes about your decision..."></textarea>
                            </div>
                            
                            <div style="display: flex; gap: 1rem;">
                                <button type="submit" name="action" value="approve" 
                                        class="btn btn-success" 
                                        onclick="return confirm('Are you sure you want to approve this coordinator application?')">
                                    ✅ Approve
                                </button>
                                <button type="submit" name="action" value="reject" 
                                        class="btn" style="background: #dc3545; color: white;"
                                        onclick="return confirm('Are you sure you want to reject this coordinator application?')">
                                    ❌ Reject
                                </button>
                                <button type="button" 
                                        class="btn" style="background: #6c757d; color: white;"
                                        onclick="deleteApplication(<?php echo $app['id']; ?>)"
                                        title="Permanently delete this application">
                                    🗑️ Delete
                                </button>
                            </div>
                        </form>
                    </div>
                    
                <?php else: ?>
                    <!-- Review Information -->
                    <div style="background: <?php echo $app['status'] === 'approved' ? '#d4edda' : '#f8d7da'; ?>; 
                               padding: 1.5rem; border-radius: 8px;">
                        <h4 style="margin-bottom: 1rem;">
                            <?php echo $app['status'] === 'approved' ? '✅ Approved' : '❌ Rejected'; ?>
                        </h4>
                        
                        <?php if ($app['reviewed_by_name']): ?>
                            <p><strong>Reviewed by:</strong> <?php echo $app['reviewed_by_name']; ?></p>
                        <?php endif; ?>
                        
                        <?php if ($app['reviewed_at']): ?>
                            <p><strong>Reviewed on:</strong> <?php echo date('M j, Y g:i A', strtotime($app['reviewed_at'])); ?></p>
                        <?php endif; ?>
                        
                        <?php if ($app['admin_notes']): ?>
                            <div style="margin-top: 1rem;">
                                <strong>Admin Notes:</strong><br>
                                <div style="background: rgba(255,255,255,0.7); padding: 1rem; border-radius: 5px; margin-top: 0.5rem;">
                                    <?php echo nl2br($app['admin_notes']); ?>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($app['status'] !== 'pending'): ?>
                            <div style="margin-top: 1rem;">
                                <button type="button" 
                                        class="btn" style="background: #dc3545; color: white;"
                                        onclick="deleteApplication(<?php echo $app['id']; ?>)"
                                        title="Permanently delete this reviewed application">
                                    🗑️ Delete Application
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        
    <?php else: ?>
        <div class="admin-section" style="text-align: center; padding: 3rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">📋</div>
            <h3>No Applications Found</h3>
            <p style="color: #666;">No coordinator applications have been submitted yet.</p>
        </div>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
