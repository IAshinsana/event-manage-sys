// Advanced Countdown Timer with Event Status Management
function startCountdown(eventDateTime, countdownElementId) {
    const countdownElement = document.getElementById(countdownElementId);
    const eventDate = new Date(eventDateTime).getTime();
    
    function updateCountdown() {
        const now = new Date().getTime();
        const distance = eventDate - now;
        
        // If event has ended
        if (distance < 0) {
            showEventEnded(countdownElement);
            disableTicketPurchasing();
            return;
        }
        
        // Calculate time units
        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
        
        // Display countdown
        displayCountdown(countdownElement, days, hours, minutes, seconds);
        
        // Update every second
        setTimeout(updateCountdown, 1000);
    }
    
    function displayCountdown(element, days, hours, minutes, seconds) {
        const timeRemaining = days > 0 ? `${days} day${days !== 1 ? 's' : ''}` : 'Today';
        
        element.innerHTML = `
            <div class="countdown-title">🎯 Event Starts In</div>
            <div class="countdown-timer">
                <div class="countdown-unit">
                    <span class="countdown-number">${days.toString().padStart(2, '0')}</span>
                    <span class="countdown-label">Days</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${hours.toString().padStart(2, '0')}</span>
                    <span class="countdown-label">Hours</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${minutes.toString().padStart(2, '0')}</span>
                    <span class="countdown-label">Minutes</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${seconds.toString().padStart(2, '0')}</span>
                    <span class="countdown-label">Seconds</span>
                </div>
            </div>
        `;
        
        // Add urgency styling for last 24 hours
        if (days === 0 && hours < 24) {
            element.style.background = 'linear-gradient(135deg, rgba(255,193,7,0.3), rgba(255,193,7,0.2))';
            element.style.borderColor = 'rgba(255,193,7,0.5)';
            
            // Add urgent animation for last hour
            if (hours === 0) {
                element.style.background = 'linear-gradient(135deg, rgba(220,53,69,0.3), rgba(220,53,69,0.2))';
                element.style.borderColor = 'rgba(220,53,69,0.5)';
                element.style.animation = 'pulse 1s infinite';
            }
        }
    }
    
    function showEventEnded(element) {
        element.innerHTML = `
            <div class="event-ended">
                <span class="event-ended-icon">⏰</span>
                <div class="event-ended-title">Event Has Ended</div>
                <div class="event-ended-subtitle">This event has already concluded</div>
            </div>
        `;
    }
    
    function disableTicketPurchasing() {
        // Disable all ticket purchase buttons
        const bookButtons = document.querySelectorAll('.btn-book-now, .btn-login, .btn-book-tickets');
        bookButtons.forEach(button => {
            button.style.background = '#6c757d';
            button.style.cursor = 'not-allowed';
            button.style.opacity = '0.7';
            button.onclick = function(e) {
                e.preventDefault();
                alert('Sorry, ticket sales have ended as this event has already concluded.');
                return false;
            };
            
            if (button.tagName === 'A') {
                button.href = 'javascript:void(0)';
            } else {
                button.disabled = true;
            }
            
            // Update button text
            if (button.classList.contains('btn-book-tickets')) {
                button.innerHTML = '⏰ Event Ended';
            } else {
                button.innerHTML = 'Event Ended';
            }
        });
        
        // Add notice to tickets section
        const ticketsSection = document.getElementById('tickets');
        if (ticketsSection) {
            const notice = document.createElement('div');
            notice.style.cssText = `
                background: linear-gradient(135deg, #f8d7da, #f5c6cb);
                border: 1px solid #f5c6cb;
                color: #721c24;
                padding: 1rem 1.5rem;
                border-radius: 10px;
                margin-bottom: 1.5rem;
                text-align: center;
                font-weight: 600;
            `;
            notice.innerHTML = '⚠️ Ticket sales have ended as this event has already concluded.';
            ticketsSection.insertBefore(notice, ticketsSection.firstChild.nextSibling);
        }
        
        // Update availability text
        const availabilityElements = document.querySelectorAll('.ticket-availability');
        availabilityElements.forEach(element => {
            element.innerHTML = '<span style="color: #dc3545;">Event Ended</span>';
        });
    }
    
    // Start the countdown
    updateCountdown();
}

// Additional utility functions
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    const options = {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    };
    return date.toLocaleDateString('en-US', options);
}

function getTimeUntilEvent(eventDateTime) {
    const now = new Date().getTime();
    const eventDate = new Date(eventDateTime).getTime();
    const distance = eventDate - now;
    
    if (distance < 0) {
        return { status: 'ended', message: 'Event has ended' };
    }
    
    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
    
    if (days > 0) {
        return { status: 'upcoming', message: `${days} day${days !== 1 ? 's' : ''} remaining` };
    } else if (hours > 0) {
        return { status: 'today', message: `${hours} hour${hours !== 1 ? 's' : ''} remaining` };
    } else {
        return { status: 'urgent', message: 'Starting soon!' };
    }
}

// Initialize when document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth scrolling to ticket section
    const bookTicketsButton = document.querySelector('a[href="#tickets"]');
    if (bookTicketsButton) {
        bookTicketsButton.addEventListener('click', function(e) {
            e.preventDefault();
            const ticketsSection = document.getElementById('tickets');
            if (ticketsSection) {
                ticketsSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    }
    
    // Add loading animation to buttons
    const buttons = document.querySelectorAll('.btn-book-now, .btn-login');
    buttons.forEach(button => {
        button.addEventListener('click', function() {
            if (!this.disabled && this.href !== 'javascript:void(0)') {
                this.innerHTML = 'Loading...';
            }
        });
    });
});
