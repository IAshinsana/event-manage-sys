// INSERT FUNCTIONS - Handle all CREATE operations
// Clean and organized functions for adding new data

// Helper function to get correct processor path
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}

// USER REGISTRATION & LOGIN
function login() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

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
                document.getElementById("reg_ok").innerHTML = "Register Success";
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

// COORDINATOR REGISTRATION
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

// EVENT CREATION
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

// TICKET CREATION
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
