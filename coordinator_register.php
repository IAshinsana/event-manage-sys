<?php
$page_title = "Apply as Event Coordinator";
include 'includes/header.php';

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

?>



<div class="container" style="max-width: 700px; margin: 2rem auto; padding: 0 1rem;">
    <!-- Header -->
    <div style="text-align: center; margin-bottom: 2.5rem;">
        <h1 style="font-size: 1.75rem; color: var(--primary); margin-bottom: 0.5rem; font-weight: 600;">Apply as Event Coordinator</h1>
        <p style="color: var(--gray-600); font-size: 0.95rem;">Join our platform to organize amazing events</p>
    </div>
    
    <!-- Messages -->
    <div id="errId" class="mb-3"></div>
    
    <div id="succ" class="d-none" style="background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 2.5rem; text-align: center; margin-bottom: 2rem;">
        <div style="font-size: 3rem; margin-bottom: 1rem;">✅</div>
        <h3 style="color: var(--success); margin-bottom: 1rem;">Application Submitted!</h3>
        <p style="color: var(--gray-600); margin-bottom: 1.5rem;">We'll review your application and notify you via email.</p>
        <a href="login.php?msg=reg_succuss" class="btn btn-success">Login to Check Status</a>
    </div>
    
    <!-- Form -->
    <div style="background: var(--white); border-radius: 12px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); padding: 2.5rem;">
        <!-- Account Setup -->
        <div style="margin-bottom: 2.5rem;">
            <h3 style="color: var(--primary); margin-bottom: 1.5rem; font-size: 1.1rem; font-weight: 600; display: flex; align-items: center; gap: 0.5rem;">
                <span>👤</span> Account Setup
            </h3>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input id="fullName" type="text" required class="form-control" placeholder="Your full name" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Username *</label>
                    <input type="text" id="username" required class="form-control" placeholder="Choose username" style="margin-bottom: 0.5rem;">
                </div>
            </div>
            
            <div class="row mb-4">
                <div class="col-md-6">
                    <label class="form-label">Password *</label>
                    <input type="password" id="password" required minlength="6" class="form-control" placeholder="Minimum 6 characters" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Confirm Password *</label>
                    <input type="password" id="cPassword" required class="form-control" placeholder="Repeat password" style="margin-bottom: 0.5rem;">
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Email Address *</label>
                    <input type="email" id="email" required class="form-control" placeholder="your@email.com" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Phone Number *</label>
                    <input type="tel" id="phone" required pattern="[0-9]{10}" class="form-control" placeholder="0771234567" style="margin-bottom: 0.5rem;">
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
                    <input type="text" id="organization_name" required class="form-control" placeholder="Company/Organization name" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Organization Type *</label>
                    <select id="organization_type" required class="form-control" style="margin-bottom: 0.5rem;">
                        <option value="">Select Type</option>
                        <option value="company">Company</option>
                        <option value="ngo">NGO</option>
                        <option value="educational">Educational</option>
                        <option value="government">Government</option>
                        <option value="freelancer">Freelancer</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
            
            <div class="row mb-3">
                <div class="col-md-4">
                    <label class="form-label">Experience (Years)</label>
                    <input type="number" id="experience_years" min="0" max="50" class="form-control" placeholder="0" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Website (Optional)</label>
                    <input type="url" id="website" class="form-control" placeholder="https://yourwebsite.com" style="margin-bottom: 0.5rem;">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Social Media (Optional)</label>
                    <input type="text" id="social_media" class="form-control" placeholder="@username" style="margin-bottom: 0.5rem;">
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
                <textarea id="previous_events" rows="3" class="form-control" placeholder="Briefly describe any events you've organized before..." style="margin-bottom: 0.5rem;"></textarea>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Why do you want to be a coordinator? *</label>
                <textarea id="motivation" rows="4" required class="form-control" placeholder="Tell us about your motivation, goals, and what you hope to achieve..." style="margin-bottom: 0.5rem;"></textarea>
            </div>
        </div>
        
        <!-- Submit Button -->
        <div style="text-align: center; padding-top: 1.5rem; border-top: 1px solid var(--gray-200);">
            <button onclick="regCoor()" type="submit" class="btn btn-primary" style="padding: 0.75rem 2.5rem; font-weight: 600; font-size: 1rem;">
                Submit Application
            </button>
            <div style="margin-top: 1.5rem;">
                <a href="login.php" style="color: var(--gray-600); text-decoration: none; font-size: 0.9rem;">
                    Already applied? Login here
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
