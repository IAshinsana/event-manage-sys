<?php
$page_title = "Reapply as Event Coordinator";
include '../includes/header.php';
include '../includes/db.php';

// Check if user is coordinator with rejected status
if (!is_logged_in() || $_SESSION['role'] !== 'coordinator') {
    header('Location: ../login.php');
    exit;
}

// Get current application data
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT u.*, ca.* FROM users u 
             LEFT JOIN coordinator_applications ca ON u.id = ca.user_id 
             WHERE u.id = $user_id";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

// Check if user can reapply (should be rejected)
if ($user_data['status'] !== 'rejected') {
    header('Location: dashboard.php');
    exit;
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $organization_name = trim($_POST['organization_name']);
    $organization_type = trim($_POST['organization_type']);
    $experience_years = (int)$_POST['experience_years'];
    $previous_events = trim($_POST['previous_events']);
    $motivation = trim($_POST['motivation']);
    $website = trim($_POST['website']);
    $social_media = trim($_POST['social_media']);
    
    // Basic validation
    if (empty($name) || empty($username) || empty($email) || 
        empty($phone) || empty($organization_name) || empty($organization_type) || empty($motivation)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Check if username or email exists for other users
        $check_sql = "SELECT id FROM users WHERE (username = '$username' OR email = '$email') AND id != $user_id";
        $check_result = $conn->query($check_sql);
        
        if ($check_result->num_rows > 0) {
            $error_message = "Username or email already exists for another user.";
        } else {
            // Update user and application data
            $conn->autocommit(false);
            
            try {
                // Update user table
                $update_user_sql = "UPDATE users SET 
                                   name = '$name', 
                                   username = '$username', 
                                   email = '$email', 
                                   phone = '$phone', 
                                   organization = '$organization_name',
                                   coordinator_status = 'pending'
                                   WHERE id = $user_id";
                $conn->query($update_user_sql);
                
                // Update coordinator application
                $update_app_sql = "UPDATE coordinator_applications SET 
                                  organization_name = '$organization_name',
                                  organization_type = '$organization_type',
                                  experience_years = $experience_years,
                                  previous_events = '$previous_events',
                                  motivation = '$motivation',
                                  contact_email = '$email',
                                  contact_phone = '$phone',
                                  website = '$website',
                                  social_media = '$social_media',
                                  status = 'reapplied',
                                  applied_at = NOW(),
                                  admin_notes = NULL,
                                  reviewed_at = NULL,
                                  reviewed_by = NULL
                                  WHERE user_id = $user_id";
                $conn->query($update_app_sql);
                
                $conn->commit();
                $success_message = "Application resubmitted successfully! Please wait for admin review.";
                
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error updating application. Please try again.";
            }
            
            $conn->autocommit(true);
        }
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <div style="max-width: 700px; margin: 0 auto;">
        
        <!-- Header -->
        <div style="text-align: center; margin-bottom: 2rem;">
            <h1 style="font-size: 2.2rem; color: #333; margin-bottom: 0.5rem;">🔄 Reapply as Coordinator</h1>
            <p style="color: #666; font-size: 1rem;">Update your application and resubmit for review</p>
        </div>
        
        <!-- Previous Rejection Notice -->
        <div style="background: #fff3cd; border-left: 4px solid #ffc107; color: #856404; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
            <strong>📋 Previous Application Status:</strong> Rejected<br>
            <?php if ($user_data['admin_notes']): ?>
                <strong>Admin Notes:</strong> <?php echo $user_data['admin_notes']; ?>
            <?php endif; ?>
        </div>
        
        <?php if ($error_message): ?>
            <div style="background: #fee; border-left: 4px solid #e74c3c; color: #c0392b; padding: 1rem; border-radius: 5px; margin-bottom: 1.5rem;">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div style="background: #eafaf1; border-left: 4px solid #27ae60; color: #1e8449; padding: 1.5rem; border-radius: 5px; margin-bottom: 2rem; text-align: center;">
                <div style="font-size: 2rem; margin-bottom: 0.5rem;">✅</div>
                <?php echo $success_message; ?>
                <div style="margin-top: 1rem;">
                    <a href="dashboard.php" class="btn btn-success">Return to Dashboard</a>
                </div>
            </div>
        <?php else: ?>
        
        <!-- Form Container -->
        <div style="background: white; border-radius: 15px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); overflow: hidden;">
            
            <form method="POST" style="padding: 2rem;">
                
                <!-- Account Setup -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #3498db; margin-bottom: 1.5rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>👤</span> Account Information
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" required class="form-input"
                                   value="<?php echo $user_data['name']; ?>"
                                   placeholder="Your full name">
                        </div>
                        <div>
                            <label class="form-label">Username</label>
                            <input type="text" name="username" required class="form-input"
                                   value="<?php echo $user_data['username']; ?>"
                                   placeholder="Choose username">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" required class="form-input"
                                   value="<?php echo $user_data['email']; ?>"
                                   placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" required class="form-input"
                                   value="<?php echo $user_data['phone']; ?>"
                                   placeholder="0771234567">
                        </div>
                    </div>
                </div>
                
                <hr style="border: none; height: 1px; background: #eee; margin: 2rem 0;">
                
                <!-- Organization Details -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #e67e22; margin-bottom: 1.5rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>🏢</span> Organization Details
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div>
                            <label class="form-label">Organization Name</label>
                            <input type="text" name="organization_name" required class="form-input"
                                   value="<?php echo $user_data['organization_name']; ?>"
                                   placeholder="Company/Organization name">
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <select name="organization_type" required class="form-input">
                                <option value="">Select Type</option>
                                <option value="company" <?php echo $user_data['organization_type'] === 'company' ? 'selected' : ''; ?>>Company</option>
                                <option value="ngo" <?php echo $user_data['organization_type'] === 'ngo' ? 'selected' : ''; ?>>NGO</option>
                                <option value="educational" <?php echo $user_data['organization_type'] === 'educational' ? 'selected' : ''; ?>>Educational</option>
                                <option value="government" <?php echo $user_data['organization_type'] === 'government' ? 'selected' : ''; ?>>Government</option>
                                <option value="freelancer" <?php echo $user_data['organization_type'] === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                                <option value="other" <?php echo $user_data['organization_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" name="experience_years" min="0" max="50" class="form-input"
                                   value="<?php echo $user_data['experience_years']; ?>"
                                   placeholder="0">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label">Website (Optional)</label>
                            <input type="url" name="website" class="form-input"
                                   value="<?php echo $user_data['website']; ?>"
                                   placeholder="https://yourwebsite.com">
                        </div>
                        <div>
                            <label class="form-label">Social Media (Optional)</label>
                            <input type="text" name="social_media" class="form-input"
                                   value="<?php echo $user_data['social_media']; ?>"
                                   placeholder="@username or links">
                        </div>
                    </div>
                </div>
                
                <hr style="border: none; height: 1px; background: #eee; margin: 2rem 0;">
                
                <!-- Experience & Goals -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #9b59b6; margin-bottom: 1.5rem; font-size: 1.3rem; display: flex; align-items: center; gap: 0.5rem;">
                        <span>💭</span> Experience & Goals
                    </h3>
                    
                    <div style="margin-bottom: 1rem;">
                        <label class="form-label">Previous Events (Optional)</label>
                        <textarea name="previous_events" rows="3" class="form-input"
                                  placeholder="Briefly describe any events you've organized before..."><?php echo $user_data['previous_events']; ?></textarea>
                    </div>
                    
                    <div>
                        <label class="form-label">Why do you want to be a coordinator?</label>
                        <textarea name="motivation" rows="4" required class="form-input"
                                  placeholder="Tell us about your motivation, goals, and what you hope to achieve..."><?php echo $user_data['motivation']; ?></textarea>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div style="text-align: center; padding-top: 1rem;">
                    <button type="submit" class="btn-submit">
                        <span>🔄</span> Resubmit Application
                    </button>
                    <div style="margin-top: 1rem;">
                        <a href="dashboard.php" style="color: #666; text-decoration: none; font-size: 0.9rem;">
                            ← Return to Dashboard
                        </a>
                    </div>
                </div>
                
            </form>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
