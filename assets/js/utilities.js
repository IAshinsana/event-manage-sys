// UTILITIES - Helper functions and validations
// Clean and organized utility functions used across the application

// EMAIL VALIDATION
function validateEmail(email) {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
}

// SRI LANKA MOBILE VALIDATION
function validateSriLankaMobile(mobile) {
    const mobileRegex = /^(070|071|072|074|075|076|077|078)\d{7}$/;
    return mobileRegex.test(mobile);
}

// FORM ERROR DISPLAY
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

// MOBILE MENU TOGGLE
function toggleMobileMenu() {
    const navLinks = document.getElementById('navLinks');
    navLinks.classList.toggle('active');
}

// HELPER FUNCTION FOR PROCESSOR PATHS
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}

// FORM VALIDATION ON PAGE LOAD
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
