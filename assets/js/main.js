// MAIN.JS - Core application entry point (CLEAN VERSION)
// This file now includes only essential global functions
// Most CRUD functions have been organized into separate files for better maintainability

// IMPORTANT: Include these files in your HTML pages as needed:
// <script src="assets/js/utilities.js"></script>        // Validation, helpers, mobile menu
// <script src="assets/js/insert-functions.js"></script> // CREATE operations (login, signup, addEvent, addTicket)
// <script src="assets/js/update-functions.js"></script> // UPDATE operations (editEvent, updateUser, approveEvent)
// <script src="assets/js/delete-functions.js"></script> // DELETE operations (deleteEvent, deleteUser, deleteTicket)
// <script src="assets/js/search-functions.js"></script> // SEARCH operations (month filters, event loading)

// This main.js file can be included for global functionality that doesn't fit into CRUD categories
// All functions have been moved to their respective organized files

console.log('EventGate JavaScript modules loaded successfully!');
console.log('Available modules: utilities, insert-functions, update-functions, delete-functions, search-functions');

// Any global application initialization code can go here
document.addEventListener('DOMContentLoaded', function() {
    console.log('EventGate application initialized');
});

// Email validation
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// Sri Lanka mobile validation
function validateSriLankaMobile(mobile) {
    const mobileRegex = /^(070|071|072|074|075|076|077|078)\d{7}$/;
    return mobileRegex.test(mobile);
}

// Form validation on submit
document.addEventListener('DOMContentLoaded', function () {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            if (this.value && !validateEmail(this.value)) {
                this.style.borderColor = '#dc3545';
                showFieldError(this, 'Please enter a valid email address');
            } else {
                this.style.borderColor = '#28a745';
                hideFieldError(this);
            }
        });
    });

    // Mobile validation
    const mobileInputs = document.querySelectorAll('input[name="mobile"], input[name="phone"]');
    mobileInputs.forEach(function (input) {
        input.addEventListener('blur', function () {
            if (this.value && !validateSriLankaMobile(this.value)) {
                this.style.borderColor = '#dc3545';
                showFieldError(this, 'Please enter a valid mobile number (070xxxxxxx)');
            } else {
                this.style.borderColor = '#28a745';
                hideFieldError(this);
            }
        });
    });
});

function showFieldError(field, message) {
    hideFieldError(field);
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error';
    errorDiv.style.color = '#dc3545';
    errorDiv.style.fontSize = '0.875rem';
    errorDiv.style.marginTop = '0.25rem';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
}

function hideFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// Helper function to get correct processor path
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}




function login() {
    // alert("hello");
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    // alert(username);
    // alert(password);

    var newForm = new FormData();
    newForm.append("user", username);
    newForm.append("pass", password);

    var req = new XMLHttpRequest();

    req.onreadystatechange = function () {

        if (req.readyState == 4) {
            var text = req.responseText;


            if (text == "admin") {
                window.location = "admin/index.php";

            } else if (text == "coordinator") {
                window.location = "coordinator/dashboard.php";
            } else if (text == "user") {
                window.location = "index.php";
            } else {
                var regOk = document.getElementById("regOk");
                if (regOk) {
                    regOk.className = "d-none";
                }
                var error = document.getElementById("error");
                error.className = "alert alert-danger";
                error.innerHTML = text;
                document.getElementById("password").value = "";
            }

        }
    }
    

    req.open("POST", "proccess/login_proc.php");
    req.send(newForm);

}

function signUp() {
    var name = document.getElementById("name").value;
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;
    var confirm_password = document.getElementById("confirm_password").value;



    var newForm = new FormData();
    newForm.append("name", name);
    newForm.append("username", username);
    newForm.append("password", password);
    newForm.append("confirm_password", confirm_password);

    var req = new XMLHttpRequest();

    req.onreadystatechange = function () {

        if (req.readyState == 4) {
            var text = req.responseText;
            if (text == "ok") {
                document.getElementById("reg_ok").className = "alert alert-success";
                document.getElementById("reg_ok").innerHTML = "Register Succuss";
                window.location = "login.php?msg=reg_succuss";

            } else {
                document.getElementById("reg_error").className = "alert alert-danger";
                document.getElementById("reg_error").innerHTML = text;


            }

        }
    }
    req.open("POST", "proccess/reg_proc.php");
    req.send(newForm);

}


function regCoor() {
    var fullName = document.getElementById("fullName").value;
    var username = document.getElementById("username").value
    var password = document.getElementById("password").value;
    var cPassword = document.getElementById("cPassword").value
    var email = document.getElementById("email").value;
    var phone = document.getElementById("phone").value
    var organization_name = document.getElementById("organization_name").value;
    var organization_type = document.getElementById("organization_type").value
    var experience_years = document.getElementById("experience_years").value;
    var website = document.getElementById("website").value
    var previous_events = document.getElementById("previous_events").value;
    var motivation = document.getElementById("motivation").value
    var social_media = document.getElementById("social_media").value

    var f = new FormData();
    f.append("fullName", fullName);
    f.append("username", username);
    f.append("password", password);
    f.append("cPassword", cPassword);
    f.append("email", email);
    f.append("phone", phone);
    f.append("organization_name", organization_name);
    f.append("organization_type", organization_type);
    f.append("experience_years", experience_years);
    f.append("website", website);
    f.append("previous_events", previous_events);
    f.append("motivation", motivation);
    f.append("social_media", social_media);



    var r = new XMLHttpRequest();


    r.onreadystatechange = function () {

        if (r.readyState == 4) {
            var text = r.responseText;

            if (text == "ok") {
                document.getElementById("errId").className = "d-none";
                document.getElementById("succ").className = "";
                window.location = "#top";

            } else {
                document.getElementById("errId").className = "alert alert-danger";
                document.getElementById("errId").innerHTML = text;
                window.location = "#top";

            }
        }
    }

    r.open("POST", getProcPath("coor_proc.php"));
    r.send(f);

}

// Event management functions
function addEvent() {
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
            var parts = text.split("|");
            
            if (parts[0] == "ok") {
                document.getElementById("errId").className = "d-none";
                document.getElementById("succ").className = "alert alert-success";
                document.getElementById("succ").innerHTML = "Event created successfully!";
                
                // Redirect to event edit page if event_id is returned
                if (parts[1]) {
                    setTimeout(function() {
                        window.location.href = "event_edit.php?id=" + parts[1];
                    }, 1500);
                }
            } else {
                document.getElementById("errId").className = "alert alert-danger";
                document.getElementById("errId").innerHTML = text;
            }
        }
    }

    r.open("POST", getProcPath("event_add_proc.php"));
    r.send(f);
}

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
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    // Dynamic path detection based on current page location
    const currentPath = window.location.pathname;
    const procPath = currentPath.includes('/admin/') || currentPath.includes('/coordinator/') ? 
        "../proccess/event_delete_proc.php" : "proccess/event_delete_proc.php";

    r.open("POST", procPath);
    r.send(f);
}

function approveRejectEvent(event_id, action) {
    var rejection_reason = "";
    if (action === 'reject') {
        rejection_reason = prompt("Please provide a reason for rejection:");
        if (rejection_reason === null) return; // User cancelled
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

// Ticket management functions
function addTicket() {
    var event_id = document.getElementById("event_id").value;
    var name = document.getElementById("ticket_name").value;
    var price = document.getElementById("ticket_price").value;
    var quantity = document.getElementById("ticket_quantity").value;

    var f = new FormData();
    f.append("action", "add_ticket");
    f.append("event_id", event_id);
    f.append("name", name);
    f.append("price", price);
    f.append("quantity", quantity);

    var r = new XMLHttpRequest();

    r.onreadystatechange = function () {
        if (r.readyState == 4) {
            var text = r.responseText;
            
            if (text == "ok") {
                alert("Ticket type added successfully!");
                location.reload();
            } else {
                alert("Error: " + text);
            }
        }
    }

    r.open("POST", getProcPath("ticket_manage_proc.php"));
    r.send(f);
}

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

// User management functions
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

// Coordinator application management
function approveRejectApplication(application_id, action) {
    var admin_notes = prompt("Add notes (optional):");
    if (admin_notes === null) return; // User cancelled

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

// Delete functions for comprehensive CRUD operations
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

    // Dynamic path detection based on current page location
    const currentPath = window.location.pathname;
    const procPath = currentPath.includes('/admin/') || currentPath.includes('/coordinator/') ? 
        "../proccess/order_delete_proc.php" : "proccess/order_delete_proc.php";

    r.open("POST", procPath);
    r.send(f);
}

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

    // Dynamic path detection based on current page location
    const currentPath = window.location.pathname;
    const procPath = currentPath.includes('/admin/') || currentPath.includes('/coordinator/') ? 
        "../proccess/user_delete_proc.php" : "proccess/user_delete_proc.php";

    r.open("POST", procPath);
    r.send(f);
}

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

    // Dynamic path detection based on current page location
    const currentPath = window.location.pathname;
    const procPath = currentPath.includes('/admin/') || currentPath.includes('/coordinator/') ? 
        "../proccess/coordinator_application_proc.php" : "proccess/coordinator_application_proc.php";

    r.open("POST", procPath);
    r.send(f);
}

// Profile update function
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
                
                // Clear password fields
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

// Homepage month filter functionality
document.addEventListener('DOMContentLoaded', function() {
    // Month filter buttons
    const thisMonthBtn = document.querySelector('.month-filter[data-month="current"]');
    const nextMonthBtn = document.querySelector('.month-filter[data-month="next"]');
    
    if (thisMonthBtn && nextMonthBtn) {
        // Add click event listeners
        thisMonthBtn.addEventListener('click', function() {
            loadEventsForMonth('current');
            setActiveMonthButton('current');
            updateMonthHeading('current');
        });
        
        nextMonthBtn.addEventListener('click', function() {
            loadEventsForMonth('next');
            setActiveMonthButton('next');
            updateMonthHeading('next');
        });
    }
});

// Function to load events for specific month
function loadEventsForMonth(month) {
    // Show loading state
    const eventsContainer = document.querySelector('.minimal-events-grid');
    if (!eventsContainer) return;
    
    eventsContainer.innerHTML = '<div class="loading-spinner">Loading events...</div>';
    
    // Create AJAX request
    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'proccess/get_month_events.php?month=' + month, true);
    
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            if (xhr.status === 200) {
                eventsContainer.innerHTML = xhr.responseText;
            } else {
                eventsContainer.innerHTML = '<div class="text-center p-4" style="color: var(--danger);">Error loading events. Please try again.</div>';
            }
        }
    };
    
    xhr.send();
}

// Function to set active month button
function setActiveMonthButton(activeMonth) {
    const thisMonthBtn = document.querySelector('.month-filter[data-month="current"]');
    const nextMonthBtn = document.querySelector('.month-filter[data-month="next"]');
    
    if (thisMonthBtn && nextMonthBtn) {
        // Remove active class from both buttons
        thisMonthBtn.classList.remove('btn-primary');
        thisMonthBtn.classList.add('btn-outline');
        nextMonthBtn.classList.remove('btn-primary');
        nextMonthBtn.classList.add('btn-outline');
        
        // Add active class to selected button
        if (activeMonth === 'current') {
            thisMonthBtn.classList.remove('btn-outline');
            thisMonthBtn.classList.add('btn-primary');
        } else if (activeMonth === 'next') {
            nextMonthBtn.classList.remove('btn-outline');
            nextMonthBtn.classList.add('btn-primary');
        }
    }
}

// Function to update month heading
function updateMonthHeading(month) {
    const heading = document.querySelector('.section-title');
    if (!heading) return;
    
    const currentDate = new Date();
    let targetMonth;
    
    if (month === 'next') {
        // Get next month
        targetMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
    } else {
        // Current month
        targetMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    }
    
    // Get month name
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    const monthName = monthNames[targetMonth.getMonth()];
    heading.textContent = `What's happening in ${monthName}`;
}