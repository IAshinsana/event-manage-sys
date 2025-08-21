<?php
$page_title = "Event Editor";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

$event_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$event = null;
$error = '';
$success = '';

// Get event details if editing
if ($event_id) {
    $event_sql = "SELECT * FROM events WHERE id = $event_id";
    $event_result = $conn->query($event_sql);
    if ($event_result && $event_result->num_rows > 0) {
        $event = $event_result->fetch_assoc();
        $page_title = "Edit Event";
    } else {
        header('Location: events_list.php');
        exit();
    }
} else {
    $page_title = "Create New Event";
}

// Handle form submission
if ($_POST) {
    $title = $_POST['title'];
    $category = $_POST['category'];
    $venue = $_POST['venue'];
    $organizer = $_POST['organizer'];
    $booking_phone = $_POST['booking_phone'];
    $starts_at = $_POST['starts_at'];
    $ends_at = $_POST['ends_at'];
    $status = $_POST['status'];
    $description = $_POST['description'];
    $show_organizer = isset($_POST['show_organizer']) ? 1 : 0;
    $show_booking_phone = isset($_POST['show_booking_phone']) ? 1 : 0;
    
    if ($title && $starts_at && $ends_at) {
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
                    $new_filename = 'event_' . ($event_id ?: 'new') . '_' . time() . '.' . $file_extension;
                    $upload_path = $upload_dir . $new_filename;
                    
                    if (move_uploaded_file($_FILES['event_image']['tmp_name'], $upload_path)) {
                        // Delete old image if exists and we're updating
                        if ($event_id && $event['image_path'] && file_exists('../' . $event['image_path'])) {
                            unlink('../' . $event['image_path']);
                        }
                        
                        $new_image_path = 'uploads/events/' . $new_filename;
                        $image_update = $event_id ? ", image_path = '$new_image_path'" : "'$new_image_path'";
                    } else {
                        $error = "Failed to upload image. Please try again.";
                    }
                } else {
                    $error = "Image file size must be less than " . round($max_size / (1024 * 1024)) . "MB.";
                }
            } else {
                $error = "Please upload a valid image file. Supported formats: JPG, JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO.";
            }
        }
        
        if (empty($error)) {
            if ($event_id) {
                // Update existing event
                $sql = "UPDATE events SET 
                        title = '$title',
                        category = '$category',
                        venue = '$venue',
                        organizer = '$organizer',
                        booking_phone = '$booking_phone',
                        show_organizer = $show_organizer,
                        show_booking_phone = $show_booking_phone,
                        starts_at = '$starts_at',
                        ends_at = '$ends_at',
                        status = '$status',
                        description = '$description'
                        $image_update
                        WHERE id = $event_id";
            } else {
                // Create new event
                $image_field = !empty($image_update) ? ", image_path" : "";
                $image_value = !empty($image_update) ? ", $image_update" : "";
                $sql = "INSERT INTO events (title, category, venue, organizer, booking_phone, show_organizer, show_booking_phone, starts_at, ends_at, status, description$image_field) 
                       VALUES ('$title', '$category', '$venue', '$organizer', '$booking_phone', $show_organizer, $show_booking_phone, '$starts_at', '$ends_at', '$status', '$description'$image_value)";
            }
        }
        
        if ($conn->query($sql)) {
            if (!$event_id) {
                $event_id = $conn->insert_id;
                header("Location: event_edit.php?id=$event_id");
                exit();
            } else {
                $success = 'Event updated successfully!';
                // Refresh event data
                $event_result = $conn->query("SELECT * FROM events WHERE id = $event_id");
                $event = $event_result->fetch_assoc();
            }
        } else {
            $error = 'Failed to save event';
        }
    } else {
        $error = 'Please fill in all required fields';
    }
}

// Get categories
$categories_sql = "SELECT name FROM categories ORDER BY name";
$categories_result = $conn->query($categories_sql);
?>

<div class="container" style="margin-top: 2rem;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1><?php echo $event_id ? 'Edit Event' : 'Create New Event'; ?></h1>
        <div>
            <?php if ($event_id): ?>
                <a href="tickets_list.php?event_id=<?php echo $event_id; ?>" class="btn btn-success">Manage Tickets</a>
            <?php endif; ?>
            <a href="events_list.php" class="btn btn-outline">← Back to Events</a>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    
    <form method="POST" enctype="multipart/form-data">
        <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 2rem;">
            
            <!-- Main Details -->
            <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                <h3>Event Details</h3>
                
                <div class="form-group">
                    <label for="title">Event Title *</label>
                    <input type="text" id="title" name="title" class="form-control" 
                           value="<?php echo $event ? $event['title'] : ''; ?>" required>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="">Select Category</option>
                            <?php if ($categories_result): ?>
                                <?php while ($cat = $categories_result->fetch_assoc()): ?>
                                    <option value="<?php echo $cat['name']; ?>" 
                                            <?php echo ($event && $event['category'] === $cat['name']) ? 'selected' : ''; ?>>
                                        <?php echo $cat['name']; ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="venue">Venue</label>
                        <input type="text" id="venue" name="venue" class="form-control" 
                               value="<?php echo $event ? $event['venue'] : ''; ?>">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div class="form-group">
                        <label for="starts_at">Start Date & Time *</label>
                        <input type="datetime-local" id="starts_at" name="starts_at" class="form-control" 
                               value="<?php echo $event ? date('Y-m-d\TH:i', strtotime($event['starts_at'])) : ''; ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="ends_at">End Date & Time *</label>
                        <input type="datetime-local" id="ends_at" name="ends_at" class="form-control" 
                               value="<?php echo $event ? date('Y-m-d\TH:i', strtotime($event['ends_at'])) : ''; ?>" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description" class="form-control" rows="5"><?php echo $event ? $event['description'] : ''; ?></textarea>
                </div>
                
                <!-- Event Image Upload -->
                <div class="form-group">
                    <?php if ($event && $event['image_path']): ?>
                        <div style="margin-bottom: 1rem;">
                            <label>Current Event Image</label>
                            <div style="margin-top: 0.5rem;">
                                <img src="../<?php echo htmlspecialchars($event['image_path']); ?>" 
                                     alt="Current Event Image" 
                                     style="max-width: 300px; height: auto; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <label for="event_image">
                        <?php echo ($event && $event['image_path']) ? 'Replace Event Image' : 'Upload Event Image'; ?>
                    </label>
                    <input type="file" id="event_image" name="event_image" class="form-control" 
                           accept="image/*,.webp,.svg">
                    <small style="color: #666; display: block; margin-top: 0.5rem;">
                        Supported formats: JPG, JPEG, PNG, GIF, WebP, SVG, BMP, TIFF, ICO. Max size: <?php echo ini_get('upload_max_filesize'); ?>
                    </small>
                </div>
            </div>
            
            <!-- Settings -->
            <div>
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                    <h3>Settings</h3>
                    
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" class="form-control">
                            <option value="draft" <?php echo ($event && $event['status'] === 'draft') ? 'selected' : ''; ?>>Draft</option>
                            <option value="published" <?php echo ($event && $event['status'] === 'published') ? 'selected' : ''; ?>>Published</option>
                            <option value="archived" <?php echo ($event && $event['status'] === 'archived') ? 'selected' : ''; ?>>Archived</option>
                        </select>
                    </div>
                </div>
                
                <div style="background: white; padding: 2rem; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 1.5rem;">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label for="organizer">Organizer</label>
                        <input type="text" id="organizer" name="organizer" class="form-control" 
                               value="<?php echo $event ? $event['organizer'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_organizer" value="1" 
                                   <?php echo (!$event || $event['show_organizer']) ? 'checked' : ''; ?>>
                            Show organizer publicly
                        </label>
                    </div>
                    
                    <div class="form-group">
                        <label for="booking_phone">Booking Phone</label>
                        <input type="tel" id="booking_phone" name="booking_phone" class="form-control" 
                               value="<?php echo $event ? $event['booking_phone'] : ''; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label>
                            <input type="checkbox" name="show_booking_phone" value="1" 
                                   <?php echo (!$event || $event['show_booking_phone']) ? 'checked' : ''; ?>>
                            Show booking phone publicly
                        </label>
                    </div>
                </div>
                
                <div style="text-align: center;">
                    <button type="submit" class="btn btn-success" style="width: 100%; margin-bottom: 1rem;">
                        <?php echo $event_id ? 'Update Event' : 'Create Event'; ?>
                    </button>
                    
                    <?php if ($event_id): ?>
                        <a href="../event_view.php?id=<?php echo $event_id; ?>" class="btn btn-outline" style="width: 100%;">
                            👁️ Preview Event
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </form>
</div>

<?php include '../includes/footer.php'; ?>
