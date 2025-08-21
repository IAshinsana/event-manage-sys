<?php
$page_title = "Create Event";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

// Check if coordinator is approved
if (!is_approved_coordinator()) {
    header('Location: dashboard.php?error=not_approved');
    exit;
}

// Get coordinator's organization details
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT u.*, ca.organization_name 
             FROM users u 
             LEFT JOIN coordinator_applications ca ON u.id = ca.user_id 
             WHERE u.id = $user_id";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

$success_message = '';
$error_message = '';
$form_data = [
    'title' => '',
    'description' => '',
    'venue' => '',
    'category' => '',
    'starts_at' => '',
    'ends_at' => '',
    'organizer' => $user_data['organization_name'] ?: $user_data['organization'], // Use from application or user table
    'booking_phone' => $user_data['phone'],
    'show_booking_phone' => 1
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $venue = trim($_POST['venue']);
    $category = trim($_POST['category']);
    $starts_at = $_POST['starts_at'];
    $ends_at = $_POST['ends_at'];
    $organizer = $user_data['organization_name'] ?: $user_data['organization']; // Always use from profile
    $booking_phone = trim($_POST['booking_phone']);
    $show_booking_phone = isset($_POST['show_booking_phone']) ? 1 : 0;
    
    // Store form data for redisplay on error
    $form_data = $_POST;
    $form_data['organizer'] = $organizer; // Keep organization from profile
    
    // Validate required fields
    if (empty($title) || empty($description) || empty($venue) || empty($starts_at) || empty($ends_at)) {
        $error_message = "Please fill in all required fields.";
    } elseif (strtotime($starts_at) >= strtotime($ends_at)) {
        $error_message = "Event end time must be after start time.";
    } elseif (strtotime($starts_at) <= time()) {
        $error_message = "Event start time must be in the future.";
    } else {
        // Handle image upload
        $image_path = null;
        if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = '../uploads/events/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }
            
            $file_extension = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
            $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'svg', 'bmp', 'tiff', 'tif', 'ico'];
            
            // MIME type validation for security (fallback if GD not available)
            $allowed_mime_types = [
                'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
                'image/webp', 'image/svg+xml', 'image/bmp', 
                'image/tiff', 'image/x-icon', 'image/vnd.microsoft.icon'
            ];
            
            $is_valid_image = false;
            
            // Use getimagesize if available (most reliable)
            if (function_exists('getimagesize')) {
                $file_info = getimagesize($_FILES['event_image']['tmp_name']);
                $is_valid_image = $file_info !== false && in_array($file_info['mime'], $allowed_mime_types);
            } else {
                // Fallback to finfo if available
                if (function_exists('finfo_open')) {
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mime_type = finfo_file($finfo, $_FILES['event_image']['tmp_name']);
                    finfo_close($finfo);
                    $is_valid_image = in_array($mime_type, $allowed_mime_types);
                } else {
                    // Basic extension check only (least secure)
                    $is_valid_image = in_array($file_extension, $allowed_extensions);
                }
            }
            
            if ($is_valid_image && in_array($file_extension, $allowed_extensions)) {
                // Check file size (max 10MB or server limit, whichever is smaller)
                $max_size = min(10 * 1024 * 1024, (int)ini_get('upload_max_filesize') * 1024 * 1024);
                if ($_FILES['event_image']['size'] <= $max_size) {
                    $new_filename = 'event_' . time() . '_' . uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_path)) {
                        $image_path = 'uploads/events/' . $new_filename;
                    } else {
                        $error_message = "Failed to upload image. Please try again.";
                    }
                } else {
                    $error_message = "Image file size must be less than " . round($max_size / (1024 * 1024)) . "MB.";
                }
            } else {
                $error_message = "Please upload a valid image file. Supported formats: JPG, JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO.";
            }
        }
        
        try {
            $sql = "INSERT INTO events (title, description, venue, category, starts_at, ends_at, 
                    organizer, booking_phone, show_booking_phone, image_path, created_by, status, approval_status, created_at) 
                    VALUES ('$title', '$description', '$venue', '$category', '$starts_at', '$ends_at', 
                            '$organizer', '$booking_phone', $show_booking_phone, '$image_path', {$_SESSION['user_id']}, 'draft', 'pending', NOW())";
            
            if ($conn->query($sql)) {
                $event_id = $conn->insert_id;
                $success_message = "Event created successfully! It has been submitted for admin approval.";
                
                // Reset form data after successful submission
                $form_data = [
                    'title' => '',
                    'description' => '',
                    'venue' => '',
                    'category' => '',
                    'starts_at' => '',
                    'ends_at' => '',
                    'organizer' => $user_data['organization_name'] ?: $user_data['organization'],
                    'booking_phone' => $user_data['phone'],
                    'show_booking_phone' => 1
                ];
            } else {
                $error_message = "Failed to create event. Please try again.";
            }
        } catch (Exception $e) {
            $error_message = "Database error. Please try again.";
        }
    }
}
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>➕ Create New Event</h1>
        <a href="dashboard.php" class="btn btn-outline">← Back to Dashboard</a>
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
    
    <div class="admin-section">
        <h2>Event Information</h2>
        <form method="POST" enctype="multipart/form-data" style="display: grid; gap: 1.5rem;">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Event Title *</label>
                    <input type="text" name="title" class="form-input" 
                           value="<?php echo $form_data['title']; ?>" 
                           placeholder="Enter event title" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-input">
                        <option value="">Select category</option>
                        <option value="Conference" <?php echo $form_data['category'] === 'Conference' ? 'selected' : ''; ?>>Conference</option>
                        <option value="Workshop" <?php echo $form_data['category'] === 'Workshop' ? 'selected' : ''; ?>>Workshop</option>
                        <option value="Seminar" <?php echo $form_data['category'] === 'Seminar' ? 'selected' : ''; ?>>Seminar</option>
                        <option value="Concert" <?php echo $form_data['category'] === 'Concert' ? 'selected' : ''; ?>>Concert</option>
                        <option value="Sports" <?php echo $form_data['category'] === 'Sports' ? 'selected' : ''; ?>>Sports</option>
                        <option value="Exhibition" <?php echo $form_data['category'] === 'Exhibition' ? 'selected' : ''; ?>>Exhibition</option>
                        <option value="Social" <?php echo $form_data['category'] === 'Social' ? 'selected' : ''; ?>>Social</option>
                        <option value="Other" <?php echo $form_data['category'] === 'Other' ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Event Description *</label>
                <textarea name="description" rows="4" class="form-input" 
                          placeholder="Describe your event..." required><?php echo $form_data['description']; ?></textarea>
            </div>
            
            <div class="form-group">
                <label class="form-label">Venue *</label>
                <input type="text" name="venue" class="form-input" 
                       value="<?php echo $form_data['venue']; ?>" 
                       placeholder="Event venue/location" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Start Date & Time *</label>
                    <input type="datetime-local" name="starts_at" class="form-input" 
                           value="<?php echo $form_data['starts_at']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">End Date & Time *</label>
                    <input type="datetime-local" name="ends_at" class="form-input" 
                           value="<?php echo $form_data['ends_at']; ?>" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Organizer</label>
                    <input type="text" name="organizer" class="form-input" 
                           value="<?php echo $form_data['organizer']; ?>" 
                           readonly style="background: #f8f9fa; cursor: not-allowed;"
                           title="Organization name from your profile">
                    <small style="color: #666; font-size: 0.8em;">📝 From your profile (cannot be changed here)</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contact Phone</label>
                    <input type="tel" name="booking_phone" class="form-input" 
                           value="<?php echo $form_data['booking_phone']; ?>" 
                           placeholder="Contact phone number">
                    <div style="margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem; font-size: 0.9em;">
                            <input type="checkbox" name="show_booking_phone" value="1" 
                                   <?php echo isset($form_data['show_booking_phone']) && $form_data['show_booking_phone'] ? 'checked' : ''; ?>>
                            📞 Show phone number publicly on event page
                        </label>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Event Image</label>
                <input type="file" name="event_image" class="form-input" 
                       accept="image/*,.webp,.svg">
                <small style="color: #666; font-size: 0.85rem;">
                    Supported formats: JPG, JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO. Max size: <?php echo ini_get('upload_max_filesize'); ?>
                </small>
            </div>
            
            <div style="background: #e7f3ff; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #007bff;">
                <h4 style="margin-bottom: 0.5rem;">📋 What happens next?</h4>
                <ol style="margin: 0; padding-left: 1.2rem; color: #666;">
                    <li>Your event will be submitted for admin review</li>
                    <li>You'll receive notification once approved/rejected</li>
                    <li>After approval, you can create ticket types and pricing</li>
                    <li>Event will be published and open for bookings</li>
                </ol>
            </div>
            
            <div style="display: flex; gap: 1rem; justify-content: flex-end;">
                <a href="dashboard.php" class="btn btn-outline">Cancel</a>
                <button type="submit" class="btn-submit">
                    📝 Submit for Approval
                </button>
            </div>
        </form>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
