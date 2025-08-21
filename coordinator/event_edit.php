<?php
$page_title = "Edit Event";
include '../includes/header.php';
include '../includes/db.php';
require_coordinator();

if (!is_approved_coordinator()) {
    header('Location: dashboard.php?error=not_approved');
    exit;
}

$event_id = (int)($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

// Get event data
$event_sql = "SELECT * FROM events WHERE id = $event_id AND created_by = $user_id";
$event_result = $conn->query($event_sql);

if (!$event_result || $event_result->num_rows === 0) {
    header('Location: events_list.php');
    exit;
}

$event = $event_result->fetch_assoc();

// Get coordinator's organization
$user_sql = "SELECT u.*, ca.organization_name FROM users u 
             LEFT JOIN coordinator_applications ca ON u.id = ca.user_id 
             WHERE u.id = $user_id";
$user_result = $conn->query($user_sql);
$user_data = $user_result->fetch_assoc();

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $venue = trim($_POST['venue']);
    $category = trim($_POST['category']);
    $starts_at = $_POST['starts_at'];
    $ends_at = $_POST['ends_at'];
    $organizer = $user_data['organization_name'] ?: $user_data['organization'];
    $booking_phone = trim($_POST['booking_phone']);
    $show_booking_phone = isset($_POST['show_booking_phone']) ? 1 : 0;
    
    if (empty($title) || empty($description) || empty($venue) || empty($starts_at) || empty($ends_at)) {
        $error_message = "Please fill in all required fields.";
    } elseif (strtotime($starts_at) >= strtotime($ends_at)) {
        $error_message = "Event end time must be after start time.";
    } else {
        // Handle image upload
        $image_update = "";
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
                    $new_filename = 'event_' . $event_id . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_path)) {
                        // Delete old image if exists
                        if ($event['image_path'] && file_exists('../' . $event['image_path'])) {
                            unlink('../' . $event['image_path']);
                        }
                        
                        $new_image_path = 'uploads/events/' . $new_filename;
                        $image_update = ", image_path = '$new_image_path'";
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
        
        if (empty($error_message)) {
            $update_sql = "UPDATE events SET 
                          title = '$title',
                          description = '$description', 
                          venue = '$venue',
                          category = '$category',
                          starts_at = '$starts_at',
                          ends_at = '$ends_at',
                          organizer = '$organizer',
                          booking_phone = '$booking_phone',
                          show_booking_phone = $show_booking_phone,
                          approval_status = 'pending'
                          $image_update
                          WHERE id = $event_id AND created_by = $user_id";
            
            if ($conn->query($update_sql)) {
                $success_message = "Event updated successfully! Changes are pending admin approval.";
                // Refresh event data
                $event_result = $conn->query($event_sql);
                $event = $event_result->fetch_assoc();
            } else {
                $error_message = "Failed to update event. Please try again.";
            }
        }
    }
}

// Categories
$categories = ['Music', 'Conference', 'Workshop', 'Sports', 'Technology', 'Art', 'Food', 'Other'];
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1>✏️ Edit Event</h1>
        <a href="events_list.php" class="btn btn-outline">← Back to Events</a>
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

    <div class="admin-section">
        <form method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Event Title *</label>
                    <input type="text" name="title" class="form-input" value="<?php echo $event['title']; ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" class="form-input">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat; ?>" <?php echo $event['category'] === $cat ? 'selected' : ''; ?>><?php echo $cat; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Description *</label>
                <textarea name="description" rows="4" class="form-input" required><?php echo $event['description']; ?></textarea>
            </div>
            
            <!-- Current Event Image -->
            <?php if ($event['image_path']): ?>
                <div class="form-group">
                    <label class="form-label">Current Event Image</label>
                    <div style="margin-top: 0.5rem;">
                        <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                             alt="Current Event Image" 
                             style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                    </div>
                </div>
            <?php endif; ?>
            
            <!-- Upload New Image -->
            <div class="form-group">
                <label class="form-label">
                    <?php echo $event['image_path'] ? 'Replace Event Image' : 'Upload Event Image'; ?>
                </label>
                <input type="file" name="event_image" id="event_image" class="form-input" 
                       accept="image/*,.webp,.svg" onchange="previewImage(this)">
                <small style="color: #666; display: block; margin-top: 0.5rem;">
                    Supported formats: JPG, JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO. Max size: <?php echo ini_get('upload_max_filesize'); ?>
                </small>
                
                <!-- Image Preview -->
                <div id="image_preview" style="margin-top: 1rem; display: none;">
                    <label class="form-label">Preview:</label>
                    <div style="border: 2px dashed #ddd; border-radius: 8px; padding: 1rem; text-align: center; background: #f9f9f9;">
                        <img id="preview_img" src="" alt="Preview" 
                             style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                        <div style="margin-top: 0.5rem;">
                            <button type="button" onclick="clearPreview()" class="remove-btn">
                                ✕ Remove
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Venue *</label>
                <input type="text" name="venue" class="form-input" value="<?php echo $event['venue']; ?>" required>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Start Date & Time *</label>
                    <input type="datetime-local" name="starts_at" class="form-input" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($event['starts_at'])); ?>" required>
                </div>
                
                <div class="form-group">
                    <label class="form-label">End Date & Time *</label>
                    <input type="datetime-local" name="ends_at" class="form-input" 
                           value="<?php echo date('Y-m-d\TH:i', strtotime($event['ends_at'])); ?>" required>
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem;">
                <div class="form-group">
                    <label class="form-label">Organizer</label>
                    <input type="text" class="form-input" value="<?php echo $event['organizer']; ?>" 
                           readonly style="background: #f8f9fa; cursor: not-allowed;">
                    <small style="color: #666;">From your profile</small>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Contact Phone</label>
                    <input type="tel" name="booking_phone" class="form-input" value="<?php echo $event['booking_phone']; ?>">
                    <div style="margin-top: 0.5rem;">
                        <label style="display: flex; align-items: center; gap: 0.5rem;">
                            <input type="checkbox" name="show_booking_phone" value="1" 
                                   <?php echo $event['show_booking_phone'] ? 'checked' : ''; ?>>
                            📞 Show phone publicly
                        </label>
                    </div>
                </div>
            </div>
            
            <div style="text-align: center; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">💾 Update Event</button>
                <a href="events_list.php" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
/* Enhanced styling for image upload */
.form-input[type="file"] {
    border: 2px dashed #ccc;
    border-radius: 8px;
    padding: 1rem;
    background: #f9f9f9;
    cursor: pointer;
    transition: all 0.3s ease;
}

.form-input[type="file"]:hover {
    border-color: #007bff;
    background: #f0f8ff;
}

.form-input[type="file"]:focus {
    outline: none;
    border-color: #007bff;
    box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
}

#image_preview {
    animation: fadeIn 0.3s ease-in;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}

.preview-container {
    position: relative;
    display: inline-block;
}

.remove-btn {
    background: #dc3545;
    color: white;
    border: none;
    padding: 0.25rem 0.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.8rem;
    transition: background 0.2s ease;
}

.remove-btn:hover {
    background: #c82333;
}
</style>

<script>
function previewImage(input) {
    const preview = document.getElementById('image_preview');
    const previewImg = document.getElementById('preview_img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(input.files[0]);
    }
}

function clearPreview() {
    const input = document.getElementById('event_image');
    const preview = document.getElementById('image_preview');
    
    input.value = '';
    preview.style.display = 'none';
}
</script>

<?php include '../includes/footer.php'; ?>
