// UPDATE FUNCTIONS - Handle all UPDATE operations
// Clean and organized functions for modifying existing data

// Helper function to get correct processor path
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}

// EVENT UPDATES
function editEvent() {
    var event_id = document.getElementById("event_id").value;
    var title = document.getElementById("title").value;
    var description = document.getElementById("description").value;
    var venue = document.getElementById("venue").value;
    var category = document.getElementById("category").value;
    var starts_at = document.getElementById("starts_at").value;
    var ends_at = document.getElementById("ends_at").value;
    var organizer = document.getElementById("organizer").value;
    var booking_phone = document.getElementById("booking_phone").value;
    var show_organizer = document.getElementById("show_organizer").checked;
    var show_booking_phone = document.getElementById("show_booking_phone").checked;
    var status = document.getElementById("status").value;
    var event_image = document.getElementById("event_image").files[0];

    var f = new FormData();
    f.append("event_id", event_id);
    f.append("title", title);
    f.append("description", description);
    f.append("venue", venue);
    f.append("category", category);
    f.append("starts_at", starts_at);
    f.append("ends_at", ends_at);
    f.append("organizer", organizer);
    f.append("booking_phone", booking_phone);
    f.append("show_organizer", show_organizer ? 1 : 0);
    f.append("show_booking_phone", show_booking_phone ? 1 : 0);
    f.append("status", status);
    if (event_image) {
        f.append("event_image", event_image);
    }

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                document.getElementById("errId").className = "d-none";
                document.getElementById("succ").className = "alert alert-success";
                document.getElementById("succ").innerHTML = "Event updated successfully!";
            } else {
                document.getElementById("errId").className = "alert alert-danger";
                document.getElementById("errId").innerHTML = text;
            }
        }
    }

    r.open("POST", getProcPath("event_edit_proc.php"));
    r.send(f);
}

// EVENT APPROVAL/REJECTION
function approveRejectEvent(event_id, action) {
    var rejection_reason = "";
    if (action === 'reject') {
        rejection_reason = prompt("Please provide a reason for rejection:");
        if (rejection_reason === null) return;
    }

    var f = new FormData();
    f.append("event_id", event_id);
    f.append("action", action);
    f.append("rejection_reason", rejection_reason);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Event " + action + "d successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("event_approval_proc.php"));
    r.send(f);
}

// TICKET UPDATES
function editTicket(ticket_id) {
    var event_id = document.getElementById("event_id").value;
    var name = document.getElementById("edit_name_" + ticket_id).value;
    var price = document.getElementById("edit_price_" + ticket_id).value;
    var quantity = document.getElementById("edit_quantity_" + ticket_id).value;

    var f = new FormData();
    f.append("action", "update_ticket");
    f.append("event_id", event_id);
    f.append("ticket_id", ticket_id);
    f.append("name", name);
    f.append("price", price);
    f.append("quantity", quantity);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Ticket type updated successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("ticket_manage_proc.php"));
    r.send(f);
}

// USER MANAGEMENT UPDATES
function updateUserRole(user_id, role) {
    var f = new FormData();
    f.append("action", "update_role");
    f.append("user_id", user_id);
    f.append("role", role);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("User role updated successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", "proccess/user_manage_proc.php");
    r.send(f);
}

function toggleUserStatus(user_id) {
    var f = new FormData();
    f.append("action", "toggle_status");
    f.append("user_id", user_id);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("User status updated successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", "proccess/user_manage_proc.php");
    r.send(f);
}

// COORDINATOR APPLICATION MANAGEMENT
function approveRejectApplication(application_id, action) {
    var admin_notes = prompt("Add notes (optional):");
    if (admin_notes === null) return;

    var f = new FormData();
    f.append("application_id", application_id);
    f.append("action", action);
    f.append("admin_notes", admin_notes);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Application " + action + "d successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", "proccess/coordinator_application_proc.php");
    r.send(f);
}

// PROFILE UPDATES
function updateProfile() {
    var name = document.getElementById("name").value;
    var email = document.getElementById("email").value;
    var phone = document.getElementById("phone").value;
    var current_password = document.getElementById("current_password").value;
    var new_password = document.getElementById("new_password").value;
    var confirm_password = document.getElementById("confirm_password").value;

    var f = new FormData();
    f.append("name", name);
    f.append("email", email);
    f.append("phone", phone);
    f.append("current_password", current_password);
    f.append("new_password", new_password);
    f.append("confirm_password", confirm_password);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                document.getElementById("errId").className = "d-none";
                document.getElementById("succ").className = "alert alert-success";
                document.getElementById("succ").innerHTML = "Profile updated successfully!";
                
                document.getElementById("current_password").value = "";
                document.getElementById("new_password").value = "";
                document.getElementById("confirm_password").value = "";
            } else {
                document.getElementById("errId").className = "alert alert-danger";
                document.getElementById("errId").innerHTML = text;
            }
        }
    }

    r.open("POST", "proccess/profile_update_proc.php");
    r.send(f);
}
