// Simple JavaScript utilities for the event system

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
                showFieldError(this, 'Please enter a valid Sri Lankan mobile number (070xxxxxxx)');
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

// Simple countdown timer
function startCountdown(targetDate, elementId) {
    const countdownElement = document.getElementById(elementId);
    if (!countdownElement) return;

    const targetTime = new Date(targetDate).getTime();

    const timer = setInterval(function () {
        const now = new Date().getTime();
        const distance = targetTime - now;

        if (distance < 0) {
            clearInterval(timer);
            countdownElement.innerHTML = "Event Started!";
            return;
        }

        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

        countdownElement.innerHTML = `${days}d ${hours}h ${minutes}m ${seconds}s`;
    }, 1000);
}

// Mobile menu toggle (if needed)
function toggleMobileMenu() {
    const navLinks = document.getElementById('navLinks');
    const navButtons = document.getElementById('navButtons');

    if (navLinks) {
        navLinks.classList.toggle('active');
    }
    if (navButtons) {
        navButtons.classList.toggle('active');
    }
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

    r.open("POST", "proccess/coor_proc.php");
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