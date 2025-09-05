<?php
include '../includes/db.php';
session_start();
$error = '';

if ($_POST) {
    $username = $_POST['user'];
    $password = $_POST['pass'];
    
    if ($username && $password) {
        $sql = "SELECT * FROM users WHERE username = ? AND active = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result && $result->num_rows > 0) {
            $user = $result->fetch_assoc();
            
            // Check password (raw format only)
            if ($user['password'] === $password) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];     
                $_SESSION['phone'] = $user['phone'];                
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['coordinator_status'] = $user['coordinator_status'];
                
                // Redirect based on role
                if ($user['role'] === 'admin') {
                    echo "admin";
                    // header('Location: ../admin/index.php');
                } elseif ($user['role'] === 'coordinator') {
                    echo "coordinator";
                    // header('Location: ../coordinator/dashboard.php');
                } else {
                    // header('Location: ../index.php');
                    echo "user";
                }
                exit();
            } else {
                $error = 'Invalid username or password';
            }
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please enter both username and password';
    }
}

echo $error;

    // $username = $_POST['user'];
    // $password = $_POST['pass'];
    // echo $username ." ".$password;
    
?>