<?php
$page_title = "Manage Users - Admin";
include '../includes/header.php';
include '../includes/db.php';
require_admin();

// Handle user role updates
if (isset($_POST['action']) && $_POST['action'] === 'update_role' && isset($_POST['user_id']) && isset($_POST['role'])) {
    $user_id = (int)$_POST['user_id'];
    $role = $conn->real_escape_string($_POST['role']);
    
    if (in_array($role, ['ordinary', 'admin', 'checker', 'coordinator'])) {
        $update_sql = "UPDATE users SET role = ? WHERE id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $role, $user_id);
        
        if ($stmt->execute()) {
            $success_message = "User role updated successfully!";
        } else {
            $error_message = "Error updating user role.";
        }
    }
}

// Handle user status toggle
if (isset($_POST['action']) && $_POST['action'] === 'toggle_status' && isset($_POST['user_id'])) {
    $user_id = (int)$_POST['user_id'];
    
    $toggle_sql = "UPDATE users SET active = NOT active WHERE id = ?";
    $stmt = $conn->prepare($toggle_sql);
    $stmt->bind_param("i", $user_id);
    
    if ($stmt->execute()) {
        $success_message = "User status updated successfully!";
    } else {
        $error_message = "Error updating user status.";
    }
}

// Get search parameters
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : '';

// Build query with filters
$where_conditions = [];
$params = [];
$param_types = '';

if ($search) {
    $where_conditions[] = "(name LIKE ? OR username LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
    $param_types .= 'sss';
}

if ($role_filter) {
    $where_conditions[] = "role = ?";
    $params[] = $role_filter;
    $param_types .= 's';
}

if ($status_filter !== '') {
    $where_conditions[] = "active = ?";
    $params[] = ($status_filter === 'active') ? 1 : 0;
    $param_types .= 'i';
}

$where_clause = empty($where_conditions) ? '' : 'WHERE ' . implode(' AND ', $where_conditions);

// Get total count for pagination
$count_sql = "SELECT COUNT(*) as total FROM users $where_clause";
if (!empty($params)) {
    $count_stmt = $conn->prepare($count_sql);
    $count_stmt->bind_param($param_types, ...$params);
    $count_stmt->execute();
    $total_result = $count_stmt->get_result();
} else {
    $total_result = $conn->query($count_sql);
}
$total_users = $total_result->fetch_assoc()['total'];

// Get users with filters
$users_sql = "SELECT id, name, username, email, role, active, created_at FROM users $where_clause ORDER BY id DESC LIMIT 50";
if (!empty($params)) {
    $stmt = $conn->prepare($users_sql);
    $stmt->bind_param($param_types, ...$params);
    $stmt->execute();
    $users_result = $stmt->get_result();
} else {
    $users_result = $conn->query($users_sql);
}
?>

<div class="container" style="margin-top: 2rem;">
    <h1>👥 Manage Users</h1>
    
    <?php if (isset($success_message)): ?>
        <div style="background: #d4edda; color: #155724; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $success_message; ?>
        </div>
    <?php endif; ?>
    
    <?php if (isset($error_message)): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 1rem; border-radius: 5px; margin-bottom: 1rem;">
            <?php echo $error_message; ?>
        </div>
    <?php endif; ?>
    
    <!-- Search and Filter Section -->
    <div class="admin-section" style="margin-bottom: 1.5rem;">
        <h3>🔍 Search & Filter Users</h3>
        <form method="GET" style="background: #f8f9fa; padding: 1.5rem; border-radius: 8px; border: 1px solid #e9ecef;">
            <div style="display: grid; grid-template-columns: 1fr 200px 150px auto; gap: 1rem; align-items: end;">
                <div>
                    <label for="search" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Search Users:</label>
                    <input type="text" 
                           id="search" 
                           name="search" 
                           placeholder="Search by name, username, or email..." 
                           value="<?php echo htmlspecialchars($search); ?>"
                           style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                </div>
                
                <div>
                    <label for="role" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Role:</label>
                    <select id="role" name="role" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All Roles</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                        <option value="coordinator" <?php echo $role_filter === 'coordinator' ? 'selected' : ''; ?>>Coordinator</option>
                        <option value="checker" <?php echo $role_filter === 'checker' ? 'selected' : ''; ?>>Checker</option>
                        <option value="ordinary" <?php echo $role_filter === 'ordinary' ? 'selected' : ''; ?>>Ordinary</option>
                    </select>
                </div>
                
                <div>
                    <label for="status" style="display: block; margin-bottom: 0.5rem; font-weight: 500;">Status:</label>
                    <select id="status" name="status" style="width: 100%; padding: 0.75rem; border: 1px solid #ddd; border-radius: 5px;">
                        <option value="">All Status</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Active</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
                
                <div style="display: flex; gap: 0.5rem;">
                    <button type="submit" style="background: #007bff; color: white; border: none; padding: 0.75rem 1rem; border-radius: 5px; cursor: pointer;">
                        🔍 Search
                    </button>
                    <a href="users_list.php" style="background: #6c757d; color: white; text-decoration: none; padding: 0.75rem 1rem; border-radius: 5px; display: inline-block;">
                        ✖️ Clear
                    </a>
                </div>
            </div>
            
            <!-- Mobile responsive version -->
            <style>
                @media (max-width: 768px) {
                    .admin-section form > div {
                        grid-template-columns: 1fr !important;
                        grid-template-rows: auto auto auto auto;
                    }
                    
                    .admin-section form > div > div:last-child {
                        justify-self: center;
                        width: 100%;
                    }
                    
                    .admin-section form > div > div:last-child > * {
                        width: 100%;
                        margin-bottom: 0.5rem;
                    }
                }
            </style>
        </form>
        
        <!-- Results Summary -->
        <div style="margin-top: 1rem; padding: 0.75rem; background: #e9ecef; border-radius: 5px;">
            <strong>Results:</strong> Showing <?php echo $users_result->num_rows; ?> of <?php echo $total_users; ?> users
            <?php if ($search || $role_filter || $status_filter !== ''): ?>
                <span style="color: #0066cc;">
                    (filtered)
                    <?php if ($search): ?>
                        <span style="background: #cce5ff; padding: 0.25rem 0.5rem; border-radius: 3px; margin-left: 0.5rem;">Search: "<?php echo htmlspecialchars($search); ?>"</span>
                    <?php endif; ?>
                    <?php if ($role_filter): ?>
                        <span style="background: #ffe6cc; padding: 0.25rem 0.5rem; border-radius: 3px; margin-left: 0.5rem;">Role: <?php echo ucfirst($role_filter); ?></span>
                    <?php endif; ?>
                    <?php if ($status_filter !== ''): ?>
                        <span style="background: #e6ffe6; padding: 0.25rem 0.5rem; border-radius: 3px; margin-left: 0.5rem;">Status: <?php echo ucfirst($status_filter); ?></span>
                    <?php endif; ?>
                </span>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="admin-section">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
            <h2>📋 Users List</h2>
        </div>
        
        <?php if ($users_result && $users_result->num_rows > 0): ?>
            <div style="overflow-x: auto;">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($user = $users_result->fetch_assoc()): ?>
                            <tr>
                                <td>#<?php echo $user['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                </td>
                                <td>
                                    <code style="background: #f8f9fa; padding: 0.25rem 0.5rem; border-radius: 3px; font-size: 0.9em;">
                                        <?php echo htmlspecialchars($user['username']); ?>
                                    </code>
                                </td>
                                <td>
                                    <small style="color: #666;">
                                        <?php echo htmlspecialchars($user['email']); ?>
                                    </small>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: 
                                        <?php 
                                        $role_colors = [
                                            'admin' => '#dc3545',
                                            'coordinator' => '#6f42c1',
                                            'checker' => '#ffc107',
                                            'ordinary' => '#28a745'
                                        ];
                                        echo $role_colors[$user['role']] ?? '#6c757d';
                                        ?>; 
                                        color: <?php echo $user['role'] === 'checker' ? '#000' : 'white'; ?>;">
                                        <?php echo ucfirst($user['role']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge" style="background: 
                                        <?php echo $user['active'] ? '#28a745' : '#dc3545'; ?>; 
                                        color: white;">
                                        <?php echo $user['active'] ? '✅ Active' : '❌ Inactive'; ?>
                                    </span>
                                </td>
                                <td>
                                    <small style="color: #666;">
                                        <?php echo date('M j, Y', strtotime($user['created_at'])); ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if ($user['id'] != $_SESSION['user_id']): // Don't allow changing own role/status ?>
                                        <div style="display: flex; gap: 0.5rem; align-items: center; flex-wrap: wrap;">
                                            <!-- Role Update Form -->
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="action" value="update_role">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <select name="role" onchange="this.form.submit()" style="padding: 0.25rem; border-radius: 4px; border: 1px solid #ddd; font-size: 0.85em;">
                                                    <option value="ordinary" <?php echo $user['role'] === 'ordinary' ? 'selected' : ''; ?>>User</option>
                                                    <option value="checker" <?php echo $user['role'] === 'checker' ? 'selected' : ''; ?>>Checker</option>
                                                    <option value="coordinator" <?php echo $user['role'] === 'coordinator' ? 'selected' : ''; ?>>Coordinator</option>
                                                    <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                                </select>
                                            </form>
                                            
                                            <!-- Status Toggle Form -->
                                            <form method="POST" style="display: inline-block;">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                                <button type="submit" 
                                                        style="background: <?php echo $user['active'] ? '#dc3545' : '#28a745'; ?>; 
                                                               color: white; 
                                                               border: none; 
                                                               padding: 0.25rem 0.5rem; 
                                                               border-radius: 4px; 
                                                               cursor: pointer; 
                                                               font-size: 0.8em;"
                                                        onclick="return confirm('Are you sure you want to <?php echo $user['active'] ? 'deactivate' : 'activate'; ?> this user?')">
                                                    <?php echo $user['active'] ? '🚫 Deactivate' : '✅ Activate'; ?>
                                                </button>
                                            </form>
                                        </div>
                                    <?php else: ?>
                                        <span style="color: #666; font-style: italic; font-size: 0.9em;">
                                            🔒 Your Account
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 3rem; color: #666;">
                <div style="font-size: 3rem; margin-bottom: 1rem;">�</div>
                <h3>No Users Found</h3>
                <?php if ($search || $role_filter || $status_filter !== ''): ?>
                    <p>No users match your current search criteria.</p>
                    <a href="users_list.php" style="color: #007bff; text-decoration: none;">
                        ← Clear filters to see all users
                    </a>
                <?php else: ?>
                    <p>There are no users in the system yet.</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    
    <!-- User Statistics -->
    <div class="admin-section">
        <h2>📊 User Statistics</h2>
        
        <?php
        $stats_sql = "SELECT 
            role,
            COUNT(*) as count
            FROM users 
            GROUP BY role";
        $stats_result = $conn->query($stats_sql);
        $role_stats = [];
        while ($stat = $stats_result->fetch_assoc()) {
            $role_stats[$stat['role']] = $stat['count'];
        }
        ?>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem;">
            <div class="admin-card">
                <div class="icon" style="color: #28a745;">👤</div>
                <div class="number"><?php echo $role_stats['user'] ?? 0; ?></div>
                <div class="label">Regular Users</div>
            </div>
            
            <div class="admin-card">
                <div class="icon" style="color: #ffc107;">✅</div>
                <div class="number"><?php echo $role_stats['checker'] ?? 0; ?></div>
                <div class="label">Ticket Checkers</div>
            </div>
            
            <div class="admin-card">
                <div class="icon" style="color: #dc3545;">👨‍💼</div>
                <div class="number"><?php echo $role_stats['admin'] ?? 0; ?></div>
                <div class="label">Administrators</div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="admin-section">
        <h2>⚡ Quick Actions</h2>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem;">
            <a href="index.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">🏠</div>
                    <h4>Back to Dashboard</h4>
                    <p style="color: #666; font-size: 0.9rem;">Return to admin dashboard</p>
                </div>
            </a>
            
            <a href="orders_list.php" style="text-decoration: none; color: inherit;">
                <div class="admin-card" style="border: 1px solid #eee; cursor: pointer;">
                    <div class="icon">📋</div>
                    <h4>View Orders</h4>
                    <p style="color: #666; font-size: 0.9rem;">Check recent user orders</p>
                </div>
            </a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
