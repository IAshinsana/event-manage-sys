// Delete Functions with Dynamic Path Resolution for Admin Panel
// This file handles all delete operations with proper path detection

// Helper function to get correct processor path
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}

// Order deletion function
function deleteOrder(order_id, action) {
    let confirmMessage = action === 'delete' ? 
        "Are you sure you want to permanently delete this order? This action cannot be undone." :
        "Are you sure you want to cancel this order? This will refund the tickets.";
        
    if (!confirm(confirmMessage)) {
        return;
    }

    var f = new FormData();
    f.append("order_id", order_id);
    f.append("action", action);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Order " + action + "d successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("order_delete_proc.php"));
    r.send(f);
}

// User deletion function
function deleteUser(user_id) {
    if (!confirm("Are you sure you want to permanently delete this user account? This action cannot be undone and will remove all user data except orders/events.")) {
        return;
    }

    var f = new FormData();
    f.append("user_id", user_id);
    f.append("action", "delete");

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("User deleted successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("user_delete_proc.php"));
    r.send(f);
}

// User status update function
function updateUserStatus(user_id, action) {
    let message = action === 'activate' ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${message} this user?`)) {
        return;
    }

    var f = new FormData();
    f.append("user_id", user_id);
    f.append("action", action);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert(`User ${message}d successfully!`);
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("user_delete_proc.php"));
    r.send(f);
}

// Event deletion function
function deleteEvent(event_id, action) {
    if (!confirm("Are you sure you want to " + action + " this event?")) {
        return;
    }

    var f = new FormData();
    f.append("event_id", event_id);
    f.append("action", action);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Event " + action + "d successfully!");
                window.location.reload();
            } else {
                window.location.reload();
            }
        }
    }

    r.open("POST", getProcPath("event_delete_proc.php"));
    r.send(f);
}

// Coordinator application deletion function
function deleteApplication(application_id) {
    if (!confirm("Are you sure you want to permanently delete this coordinator application? This action cannot be undone.")) {
        return;
    }

    var f = new FormData();
    f.append("application_id", application_id);
    f.append("action", "delete");
    f.append("admin_notes", "");

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Application deleted successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("coordinator_application_proc.php"));
    r.send(f);
}

// Ticket deletion function
function deleteTicket(event_id, ticket_id) {
    if (!confirm("Are you sure you want to delete this ticket type?")) {
        return;
    }

    var f = new FormData();
    f.append("action", "delete_ticket");
    f.append("event_id", event_id);
    f.append("ticket_id", ticket_id);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Ticket type deleted successfully!");
                window.location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("ticket_manage_proc.php"));
    r.send(f);
}
