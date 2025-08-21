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
document.addEventListener('DOMContentLoaded', function() {
    // Email validation
    const emailInputs = document.querySelectorAll('input[type="email"]');
    emailInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
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
    mobileInputs.forEach(function(input) {
        input.addEventListener('blur', function() {
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

    const timer = setInterval(function() {
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
