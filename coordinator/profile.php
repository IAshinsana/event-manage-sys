<?php
$page_title = "My Profile";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

$user_id = $_SESSION['user_id'];

// Get user and application data
$user_sql = "SELECT u.*, ca.* FROM users u 
             LEFT JOIN coordinator_applications ca ON u.id = ca.user_id 
             WHERE u.id = $user_id";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (empty($name) || empty($email) || empty($phone)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!empty($new_password) && $new_password !== $confirm_password) {
        $error_message = "New passwords do not match.";
    } elseif (!empty($new_password) && $current_password !== $user_data['password']) {
        $error_message = "Current password is incorrect.";
    } else {
        $password_update = !empty($new_password) ? ", password = '$new_password'" : "";
        
        $update_sql = "UPDATE users SET 
                      name = '$name',
                      email = '$email',
                      phone = '$phone'
                      $password_update
                      WHERE id = $user_id";
        
        if ($conn->query($update_sql)) {
            $_SESSION['name'] = $name; // Update session
            $success_message = "Profile updated successfully!";
            // Refresh data
            $user_result = $conn->query($user_sql);
            $user_data = $user_result->fetch_assoc();
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
    }
}

// Get event statistics
$stats_sql = "SELECT 
              COUNT(*) as total_events,
              SUM(approval_status = 'approved') as approved_events,
              SUM(approval_status = 'pending') as pending_events
              FROM events WHERE created_by = $user_id";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>👤 My Profile</h1>
        <a href="dashboard.php" class="btn btn-outline">← Dashboard</a>
    </div>

    <?php if ($success_message): ?>
        <div style="background: #d4edda; border-left: 4px solid #28a745; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <div style="background: #f8d7da; border-left: 4px solid #dc3545; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
        <!-- Profile Form -->
        <div class="admin-section">
            <h3>✏️ Edit Profile</h3>
            <form method="POST">
                <div class="form-group">
                    <label class="form-label">Full Name *</label>
                    <input type="text" name="name" class="form-input" value="<?php echo $user_data['name']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Username</label>
                    <input type="text" class="form-input" value="<?php echo $user_data['username']; ?>" 
                           readonly style="background: #f8f9fa; cursor: not-allowed;">
                    <small style="color: #666;">Username cannot be changed</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-input" value="<?php echo $user_data['email']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Phone *</label>
                    <input type="tel" name="phone" class="form-input" value="<?php echo $user_data['phone']; ?>" required>
                </div>
                
                <hr style="margin: 2rem 0;">
                
                <h4>🔒 Change Password</h4>
                <div class="form-group">
                    <label class="form-label">Current Password</label>
                    <input type="password" name="current_password" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-input">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-input">
                </div>
                
                <button type="submit" class="btn btn-primary">💾 Update Profile</button>
            </form>
        </div>
        
        <!-- Profile Info -->
        <div>
            <!-- Status Card -->
            <div class="admin-section" style="margin-bottom: 1.5rem;">
                <h3>📊 Account Status</h3>
                <div style="text-align: center; padding: 1rem;">
                    <span class="status-badge status-<?php echo $user_data['coordinator_status']; ?>" style="font-size: 1rem; padding: 0.5rem 1rem;">
                        <?php echo ucfirst($user_data['coordinator_status']); ?> Coordinator
                    </span>
                    <p style="margin-top: 1rem; color: #666;">
                        Member since <?php echo date('M j, Y', strtotime($user_data['created_at'])); ?>
                    </p>
                </div>
            </div>
            
            <!-- Event Stats -->
            <div class="admin-section" style="margin-bottom: 1.5rem;">
                <h3>📈 Event Statistics</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                        <div style="font-size: 2rem; font-weight: bold; color: #007bff;"><?php echo $stats['total_events']; ?></div>
                        <div style="color: #666;">Total Events</div>
                    </div>
                    <div style="text-align: center; padding: 1rem; background: #f8f9fa; border-radius: 5px;">
                        <div style="font-size: 2rem; font-weight: bold; color: #28a745;"><?php echo $stats['approved_events']; ?></div>
                        <div style="color: #666;">Approved</div>
                    </div>
                </div>
            </div>
            
            <!-- Organization Info -->
            <?php if ($user_data['organization_name']): ?>
            <div class="admin-section">
                <h3>🏢 Organization</h3>
                <p><strong>Name:</strong> <?php echo $user_data['organization_name']; ?></p>
                <p><strong>Type:</strong> <?php echo ucfirst($user_data['organization_type']); ?></p>
                <p><strong>Experience:</strong> <?php echo $user_data['experience_years']; ?> years</p>
                <?php if ($user_data['website']): ?>
                    <p><strong>Website:</strong> <a href="<?php echo $user_data['website']; ?>" target="_blank"><?php echo $user_data['website']; ?></a></p>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
