<?php
$page_title = "Apply as Event Coordinator";
include 'includes/header.php';
include 'includes/db.php';
?>
<link rel="stylesheet" href="assets/css/coordinator_register.css">
<?php

// Redirect if already logged in
if (is_logged_in()) {
    $user_sql = "SELECT * FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    
    if ($user['role'] === 'coordinator') {
        header('Location: coordinator/dashboard.php');
        exit;
    } elseif ($user['role'] === 'admin') {
        header('Location: admin/index.php');
        exit;
    }
}

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $organization_name = trim($_POST['organization_name'] ?? '');
    $organization_type = trim($_POST['organization_type'] ?? '');
    $experience_years = (int)($_POST['experience_years'] ?? 0);
    $previous_events = trim($_POST['previous_events'] ?? '');
    $motivation = trim($_POST['motivation'] ?? '');
    $website = trim($_POST['website'] ?? '');
    $social_media = trim($_POST['social_media'] ?? '');
    
    // Validation
    if (empty($name) || empty($username) || empty($password) || empty($email) || 
        empty($phone) || empty($organization_name) || empty($organization_type) || empty($motivation)) {
        $error_message = "Please fill in all required fields.";
    } elseif ($password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error_message = "Password must be at least 6 characters long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Check if username or email already exists
        $check_sql = "SELECT id FROM users WHERE username = ? OR email = ?";
        $stmt = $conn->prepare($check_sql);
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        
        if ($stmt->get_result()->num_rows > 0) {
            $error_message = "Username or email already exists.";
        } else {
            // Create user account (store password in raw format)
            
            $conn->begin_transaction();
            try {
                // Insert user
                $user_sql = "INSERT INTO users (name, username, password, role, email, phone, organization, coordinator_status, coordinator_applied_at) 
                            VALUES (?, ?, ?, 'coordinator', ?, ?, ?, 'pending', NOW())";
                $stmt = $conn->prepare($user_sql);
                $stmt->bind_param("ssssss", $name, $username, $password, $email, $phone, $organization_name);
                $stmt->execute();
                
                $user_id = $conn->insert_id;
                
                // Insert coordinator application
                $app_sql = "INSERT INTO coordinator_applications 
                           (user_id, organization_name, organization_type, experience_years, previous_events, 
                            motivation, contact_email, contact_phone, website, social_media) 
                           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($app_sql);
                $stmt->bind_param("ississssss", $user_id, $organization_name, $organization_type, 
                                $experience_years, $previous_events, $motivation, $email, $phone, 
                                $website, $social_media);
                $stmt->execute();
                
                $conn->commit();
                $success_message = "Application submitted successfully! Please wait for admin approval.";
                
            } catch (Exception $e) {
                $conn->rollback();
                $error_message = "Error submitting application. Please try again.";
            }
        }
    }
}
?>

<div class="container coordinator-register-container">
    <div class="coordinator-register-wrapper">
        
        <!-- Header -->
        <div class="coordinator-register-header">
            <h1 class="coordinator-register-title">🎯 Coordinator Application</h1>
            <p class="coordinator-register-subtitle">Join our platform to organize amazing events</p>
        </div>
        
        <?php if ($error_message): ?>
            <div class="coordinator-register-error">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success_message): ?>
            <div class="coordinator-register-success">
                <div class="coordinator-register-success-icon">✅</div>
                <?php echo $success_message; ?>
                <div class="coordinator-register-success-actions">
                    <a href="login.php" class="btn btn-success">Login to Check Status</a>
                </div>
            </div>
        <?php else: ?>
        
        <!-- Form Container -->
        <div class="coordinator-register-form">
            
            <form method="POST">
                
                <!-- Account Setup -->
                <div class="coordinator-register-section">
                    <h3 class="coordinator-register-section-title">
                        <span>👤</span> Account Setup
                    </h3>
                    
                    <div class="coordinator-register-form-row">
                        <div>
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" required class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                                   placeholder="Your full name">
                        </div>
                        <div>
                            <label class="form-label">Username</label>
                            <input type="text" name="username" required class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>"
                                   placeholder="Choose username">
                        </div>
                    </div>
                    
                    <div class="coordinator-register-form-row">
                        <div>
                            <label class="form-label">Password</label>
                            <input type="password" name="password" required minlength="6" class="form-input"
                                   placeholder="Minimum 6 characters">
                        </div>
                        <div>
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password" required class="form-input"
                                   placeholder="Repeat password">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" required class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                                   placeholder="your@email.com">
                        </div>
                        <div>
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" required pattern="[0-9]{10}" class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['phone'] ?? ''); ?>"
                                   placeholder="0771234567">
                        </div>
                    </div>
                </div>
                
                <hr class="coordinator-register-divider">
                
                <!-- Organization Details -->
                <div class="coordinator-register-section">
                    <h3 class="coordinator-register-section-title business">
                        <span>🏢</span> Organization Details
                    </h3>
                    
                    <div class="coordinator-register-form-row three-col">
                        <div>
                            <label class="form-label">Organization Name</label>
                            <input type="text" name="organization_name" required class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['organization_name'] ?? ''); ?>"
                                   placeholder="Company/Organization name">
                        </div>
                        <div>
                            <label class="form-label">Type</label>
                            <select name="organization_type" required class="form-input">
                                <option value="">Select Type</option>
                                <option value="company" <?php echo ($_POST['organization_type'] ?? '') === 'company' ? 'selected' : ''; ?>>Company</option>
                                <option value="ngo" <?php echo ($_POST['organization_type'] ?? '') === 'ngo' ? 'selected' : ''; ?>>NGO</option>
                                <option value="educational" <?php echo ($_POST['organization_type'] ?? '') === 'educational' ? 'selected' : ''; ?>>Educational</option>
                                <option value="government" <?php echo ($_POST['organization_type'] ?? '') === 'government' ? 'selected' : ''; ?>>Government</option>
                                <option value="freelancer" <?php echo ($_POST['organization_type'] ?? '') === 'freelancer' ? 'selected' : ''; ?>>Freelancer</option>
                                <option value="other" <?php echo ($_POST['organization_type'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>
                        <div>
                            <label class="form-label">Experience (Years)</label>
                            <input type="number" name="experience_years" min="0" max="50" class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['experience_years'] ?? '0'); ?>"
                                   placeholder="0">
                        </div>
                    </div>
                    
                    <div class="coordinator-register-form-row">
                        <div>
                            <label class="form-label">Website (Optional)</label>
                            <input type="url" name="website" class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['website'] ?? ''); ?>"
                                   placeholder="https://yourwebsite.com">
                        </div>
                        <div>
                            <label class="form-label">Social Media (Optional)</label>
                            <input type="text" name="social_media" class="form-input"
                                   value="<?php echo htmlspecialchars($_POST['social_media'] ?? ''); ?>"
                                   placeholder="@username or links">
                        </div>
                    </div>
                </div>
                
                <hr class="coordinator-register-divider">
                
                <!-- Experience & Goals -->
                <div class="coordinator-register-section">
                    <h3 class="coordinator-register-section-title experience">
                        <span>💭</span> Experience & Goals
                    </h3>
                    
                    <div class="coordinator-register-description">
                        <label class="form-label">Previous Events (Optional)</label>
                        <textarea name="previous_events" rows="3" class="form-input"
                                  placeholder="Briefly describe any events you've organized before..."><?php echo htmlspecialchars($_POST['previous_events'] ?? ''); ?></textarea>
                    </div>
                    
                    <div>
                        <label class="form-label">Why do you want to be a coordinator?</label>
                        <textarea name="motivation" rows="4" required class="form-input"
                                  placeholder="Tell us about your motivation, goals, and what you hope to achieve..."><?php echo htmlspecialchars($_POST['motivation'] ?? ''); ?></textarea>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="coordinator-register-submit">
                    <button type="submit" class="btn-submit">
                        <span>📤</span> Submit Application
                    </button>
                    <div class="coordinator-register-login-link">
                        <a href="login.php">
                            Already applied? Login here
                        </a>
                    </div>
                </div>
                
            </form>
        </div>
        
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
