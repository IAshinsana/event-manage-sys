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

<div class="container" style="max-width: 700px; margin: 2rem auto; padding: 0 1rem;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <h1 style="font-size: 1.75rem; color: var(--primary); margin-bottom: 0.5rem; font-weight: 600;">Reapply as Coordinator</h1>
        <p style="color: var(--gray-600); font-size: 0.95rem;">Update your application and resubmit for review</p>
    </div>
    
    <!-- Previous Rejection Notice -->
    <div class="alert alert-warning" style="margin-bottom: 2rem;">
        <strong>📋 Previous Application Status:</strong> Rejected<br>
        <?php if ($user_data['admin_notes']): ?>
            <strong>Admin Notes:</strong> <?php echo $user_data['admin_notes']; ?>
        <?php endif; ?>
    </div>
    
    <!-- Messages -->
    <?php if ($error_message): ?>
        <div class="alert alert-danger mb-3">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if ($success_message): ?>
        <div class="card" style="text-align: center; padding: 2.5rem; margin-bottom: 2rem;">
            <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
            <h3 style="color: var(--success); margin-bottom: 1rem;">Application Resubmitted!</h3>
            <p style="color: var(--gray-600); margin-bottom: 1.5rem;"><?php echo $success_message; ?></p>
            <a href="dashboard.php" class="btn btn-success">Return to Dashboard</a>
        </div>
    <?php else: ?>
    
    <!-- Form -->
    <div style="background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 2.5rem;">
        <form method="POST">
            <!-- Account Information -->
            <div style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>👤</span> Account Information
                </h3>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Full Name *</label>
                        <input type="text" name="name" required class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['name']); ?>" 
                               placeholder="Your full name" style="margin-bottom: 0.5rem;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Username *</label>
                        <input type="text" name="username" required class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['username']); ?>" 
                               placeholder="Choose username" style="margin-bottom: 0.5rem;">
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Email Address *</label>
                        <input type="email" name="email" required class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['email']); ?>" 
                               placeholder="your@email.com" style="margin-bottom: 0.5rem;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Phone Number *</label>
                        <input type="tel" name="phone" required class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['phone']); ?>" 
                               placeholder="0771234567" style="margin-bottom: 0.5rem;">
                    </div>
                </div>
            </div>
            
            <hr style="border: none; height: 1px; background: var(--gray-200); margin: 2.5rem 0;">
            
            <!-- Organization Details -->
            <div style="margin-bottom: 2.5rem;">
                <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>🏢</span> Organization Details
                </h3>
                
                <div class="row mb-4">
                    <div class="col-md-6">
                        <label class="form-label">Organization Name *</label>
                        <input type="text" name="organization_name" required class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['organization_name']); ?>" 
                               placeholder="Company/Organization name" style="margin-bottom: 0.5rem;">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Organization Type *</label>
                        <select name="organization_type" required class="form-control" style="margin-bottom: 0.5rem;">
                            <option value="">Select Type</option>
                            <option value="company" <?php echo $user_data['organization_type'] === 'company' ? 'selected' : ''; ?>>Company</option>
                            <option value="ngo" <?php echo $user_data['organization_type'] === 'ngo' ? 'selected' : ''; ?>>NGO</option>
                            <option value="educational" <?php echo $user_data['organization_type'] === 'educational' ? 'selected' : ''; ?>>Educational</option>
                            <option value="government" <?php echo $user_data['organization_type'] === 'government' ? 'selected' : ''; ?>>Government</option>
                            <option value="freelancer" <?php echo $user_data['organization_type'] === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                            <option value="other" <?php echo $user_data['organization_type'] === 'other' ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label">Experience (Years)</label>
                        <input type="number" name="experience_years" min="0" max="50" class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['experience_years']); ?>" 
                               placeholder="0" style="margin-bottom: 0.5rem;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Website (Optional)</label>
                        <input type="url" name="website" class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['website']); ?>" 
                               placeholder="https://yourwebsite.com" style="margin-bottom: 0.5rem;">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Social Media (Optional)</label>
                        <input type="text" name="social_media" class="form-control" 
                               value="<?php echo htmlspecialchars($user_data['social_media']); ?>" 
                               placeholder="@username" style="margin-bottom: 0.5rem;">
                    </div>
                </div>
            </div>
            
            <hr style="border: none; height: 1px; background: var(--gray-200); margin: 2.5rem 0;">
            
            <!-- Experience & Goals -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                    <span>💭</span> Experience & Goals
                </h3>
                
                <div class="mb-4">
                    <label class="form-label">Previous Events (Optional)</label>
                    <textarea name="previous_events" rows="3" class="form-control" 
                              placeholder="Briefly describe any events you've organized before..." 
                              style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($user_data['previous_events']); ?></textarea>
                </div>
                
                <div class="mb-3">
                    <label class="form-label">Why do you want to be a coordinator? *</label>
                    <textarea name="motivation" rows="4" required class="form-control" 
                              placeholder="Tell us about your motivation, goals, and what you hope to achieve..." 
                              style="margin-bottom: 0.5rem;"><?php echo htmlspecialchars($user_data['motivation']); ?></textarea>
                </div>
            </div>
            
            <!-- Submit Button -->
            <div style="text-align: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
                <button type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-weight: 600; font-size: 1rem;">
                    Resubmit Application
                </button>
                <div style="margin-top: 1.5rem;">
                    <a href="dashboard.php" style="color: var(--gray-600); text-decoration: none; font-size: 0.9rem;">
                        ← Return to Dashboard
                    </a>
                </div>
            </div>
        </form>
    </div>
    
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
