// Simple Countdown Timer - Easy to understand
function startCountdown(eventDateTime, countdownElementId) {
    // Get the countdown container from the page
    const countdownElement = document.getElementById(countdownElementId);
    
    // Convert event date to milliseconds for calculations
    const eventDate = new Date(eventDateTime).getTime();
    
    // Update the countdown every second
    const timer = setInterval(function() {
        // Get current time
        const now = new Date().getTime();
        
        // Calculate how much time is left
        const timeLeft = eventDate - now;
        
        // If event has ended, stop the timer
        if (timeLeft < 0) {
            clearInterval(timer);
            showEventEnded();
            return;
        }
        
        // Calculate days, hours, minutes, seconds
        const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
        const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        // Show the countdown on the page
        showCountdown(days, hours, minutes, seconds);
        
    }, 1000);
    
    // Function to display the countdown timer
    function showCountdown(days, hours, minutes, seconds) {
        // Add leading zeros (01, 02, etc.)
        const d = days.toString().padStart(2, '0');
        const h = hours.toString().padStart(2, '0');
        const m = minutes.toString().padStart(2, '0');
        const s = seconds.toString().padStart(2, '0');
        
        // Create the countdown HTML
        countdownElement.innerHTML = `
            <div class="countdown-title">🎯 Event Starts In</div>
            <div class="countdown-timer">
                <div class="countdown-unit">
                    <span class="countdown-number">${d}</span>
                    <span class="countdown-label">Days</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${h}</span>
                    <span class="countdown-label">Hours</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${m}</span>
                    <span class="countdown-label">Minutes</span>
                </div>
                <div class="countdown-unit">
                    <span class="countdown-number">${s}</span>
                    <span class="countdown-label">Seconds</span>
                </div>
            </div>
        `;
    }
    
    // Function to show when event has ended
    function showEventEnded() {
        countdownElement.innerHTML = `
            <div class="event-ended">
                <span class="event-ended-icon">⏰</span>
                <div class="event-ended-title">Event Has Ended</div>
                <div class="event-ended-subtitle">This event has already concluded</div>
            </div>
        `;
        
        // Disable ticket purchase buttons
        disableTicketButtons();
    }
    
    // Function to disable ticket purchasing when event ends
    function disableTicketButtons() {
        const buttons = document.querySelectorAll('.btn-book-now, .btn-login, .btn-book-tickets');
        
        buttons.forEach(function(button) {
            // Change button appearance
            button.style.background = '#6c757d';
            button.style.cursor = 'not-allowed';
            button.style.opacity = '0.7';
            
            // Disable button clicks
            button.onclick = function(e) {
                e.preventDefault();
                alert('Sorry, this event has already ended.');
                return false;
            };
            
            // Change button text
            button.innerHTML = 'Event Ended';
        });
    }
}

// Simple helper function to format date and time
function formatDateTime(dateTimeString) {
    const date = new Date(dateTimeString);
    return date.toLocaleDateString('en-US', {
        weekday: 'long',
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: '2-digit',
        hour12: true
    });
}

// Simple function to check event status
function getEventStatus(eventDateTime) {
    const now = new Date().getTime();
    const eventDate = new Date(eventDateTime).getTime();
    const timeLeft = eventDate - now;
    
    // If event has ended
    if (timeLeft < 0) {
        return 'Event has ended';
    }
    
    // Calculate days left
    const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
    
    if (days > 0) {
        return `${days} day${days !== 1 ? 's' : ''} remaining`;
    } else {
        return 'Event is today!';
    }
}

// When page loads, set up smooth scrolling
document.addEventListener('DOMContentLoaded', function() {
    // Make "Book Tickets" button scroll smoothly to tickets section
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
});
