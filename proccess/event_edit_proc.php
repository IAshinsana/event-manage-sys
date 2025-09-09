<?php
include '../includes/db.php';
include '../includes/auth.php';

if (!is_logged_in()) {
    echo "Please log in first";
    exit;
}

if ($_POST) {
    $event_id = (int)$_POST['event_id'];
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $venue = trim($_POST['venue'] ?? '');
    $category = trim($_POST['category'] ?? '');
    $starts_at = $_POST['starts_at'] ?? '';
    $ends_at = $_POST['ends_at'] ?? '';
    $organizer = trim($_POST['organizer'] ?? '');
    $booking_phone = trim($_POST['booking_phone'] ?? '');
    $show_organizer = isset($_POST['show_organizer']) ? 1 : 0;
    $show_booking_phone = isset($_POST['show_booking_phone']) ? 1 : 0;
    $status = $_POST['status'] ?? 'pending';
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if (!$event_id) {
        echo "Invalid event ID";
        exit;
    }
    
    if (empty($title) || empty($description) || empty($venue) || empty($starts_at) || empty($ends_at)) {
        echo "Please fill in all required fields.";
        exit;
    }
    
    if (strtotime($starts_at) >= strtotime($ends_at)) {
        echo "Event end time must be after start time.";
        exit;
    }
    
    if ($role !== 'admin') {
        $check_sql = "SELECT id FROM events WHERE id = $event_id AND created_by = $user_id";
        $check_result = $conn->query($check_sql);
        if (!$check_result || $check_result->num_rows === 0) {
            echo "You don't have permission to edit this event";
            exit;
        }
    }
    
    $new_image_path = null;
    if (isset($_FILES['event_image']) && $_FILES['event_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/events/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['event_image']['name'], PATHINFO_EXTENSION));
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($file_extension, $allowed_types)) {
            echo "Invalid image format. Please use JPG, PNG, GIF, or WebP.";
            exit;
        }
        
        $filename = 'event_' . time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $filename;
        
        if (move_uploaded_file($_FILES['event_image']['tmp_name'], $file_path)) {
            $old_image_sql = "SELECT image_path FROM events WHERE id = $event_id";
            $old_image_result = $conn->query($old_image_sql);
            if ($old_image_result && $old_image_result->num_rows > 0) {
                $old_image = $old_image_result->fetch_assoc();
                if ($old_image['image_path'] && file_exists('../' . $old_image['image_path'])) {
                    unlink('../' . $old_image['image_path']);
                }
            }
            
            $new_image_path = 'uploads/events/' . $filename;
        }
    }
    
    $sql = "UPDATE events SET 
            title = ?, category = ?, venue = ?, organizer = ?, booking_phone = ?,
            show_organizer = ?, show_booking_phone = ?, starts_at = ?, ends_at = ?,
            status = ?, description = ?" . 
            ($new_image_path ? ", image_path = ?" : "") . 
            " WHERE id = ?";
    
    $stmt = $conn->prepare($sql);
    
    if ($new_image_path) {
        $stmt->bind_param(
            "sssssiisssssi",
            $title, $category, $venue, $organizer, $booking_phone,
            $show_organizer, $show_booking_phone, $starts_at, $ends_at,
            $status, $description, $new_image_path, $event_id
        );
    } else {
        $stmt->bind_param(
            "ssssssissssi",
            $title, $category, $venue, $organizer, $booking_phone,
            $show_organizer, $show_booking_phone, $starts_at, $ends_at,
            $status, $description, $event_id
        );
    }
    
    if ($stmt->execute()) {
        echo "ok";
    } else {
        echo "Error updating event. Please try again.";
    }
    
} else {
    echo "No data received";
}

?>

