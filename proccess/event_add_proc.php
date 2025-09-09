<?php
include '../includes/db.php';
include '../includes/auth.php';

if (!is_logged_in()) {
    echo "Please log in first";
    exit;
}

if ($_POST) {
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
    $user_id = $_SESSION['user_id'];
    $role = $_SESSION['role'];
    
    if (empty($title) || empty($description) || empty($venue) || empty($starts_at) || empty($ends_at)) {
        echo "Please fill in all required fields.";
        exit;
    }
    
    if (strtotime($starts_at) >= strtotime($ends_at)) {
        echo "Event end time must be after start time.";
        exit;
    }
    
    $image_path = null;
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
            $image_path = 'uploads/events/' . $filename;
        }
    }
    
    if ($role === 'admin') {
        $approval_status = 'approved';
        $status = 'published';
        $approved_by = $user_id;
        $approved_at = date('Y-m-d H:i:s');
    } else {
        $approval_status = 'pending';
        $status = 'pending';
        $approved_by = null;
        $approved_at = null;
    }
    
    $sql = "INSERT INTO events (
                title, category, venue, organizer, booking_phone, show_organizer, 
                show_booking_phone, starts_at, ends_at, status, description, 
                created_by, approval_status, approved_by, approved_at, image_path
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "sssssiisssssisss",
        $title, $category, $venue, $organizer, $booking_phone,
        $show_organizer, $show_booking_phone, $starts_at, $ends_at,
        $status, $description, $user_id, $approval_status, 
        $approved_by, $approved_at, $image_path
    );
    
    if ($stmt->execute()) {
        $event_id = $conn->insert_id;
        echo "ok|$event_id";
    } else {
        echo "Error creating event. Please try again.";
    }
    
} else {
    echo "No data received";
}

?>

