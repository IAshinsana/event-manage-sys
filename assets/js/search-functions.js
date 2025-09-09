// SEARCH FUNCTIONS - Handle all SEARCH and RETRIEVE operations
// Clean and organized functions for finding and filtering data

// Helper function to get correct processor path
function getProcPath(filename) {
    const currentPath = window.location.pathname;
    return (currentPath.includes('/admin/') || currentPath.includes('/coordinator/')) ? 
        "../proccess/" + filename : "proccess/" + filename;
}

// MONTH FILTER FOR EVENTS
function loadEventsForMonth(month) {
    const eventsContainer = document.querySelector('.minimal-events-grid');
    if (!eventsContainer) return;
    
    eventsContainer.innerHTML = '<div class="loading-spinner">Loading events...</div>';
    
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

// MONTH BUTTON MANAGEMENT
function setActiveMonthButton(activeMonth) {
    const thisMonthBtn = document.querySelector('.month-filter[data-month="current"]');
    const nextMonthBtn = document.querySelector('.month-filter[data-month="next"]');
    
    if (thisMonthBtn && nextMonthBtn) {
        thisMonthBtn.classList.remove('btn-primary');
        thisMonthBtn.classList.add('btn-outline');
        nextMonthBtn.classList.remove('btn-primary');
        nextMonthBtn.classList.add('btn-outline');
        
        if (activeMonth === 'current') {
            thisMonthBtn.classList.remove('btn-outline');
            thisMonthBtn.classList.add('btn-primary');
        } else if (activeMonth === 'next') {
            nextMonthBtn.classList.remove('btn-outline');
            nextMonthBtn.classList.add('btn-primary');
        }
    }
}

// MONTH HEADING UPDATE
function updateMonthHeading(month) {
    const heading = document.querySelector('.section-title');
    if (!heading) return;
    
    const currentDate = new Date();
    let targetMonth;
    
    if (month === 'next') {
        targetMonth = new Date(currentDate.getFullYear(), currentDate.getMonth() + 1, 1);
    } else {
        targetMonth = new Date(currentDate.getFullYear(), currentDate.getMonth(), 1);
    }
    
    const monthNames = [
        'January', 'February', 'March', 'April', 'May', 'June',
        'July', 'August', 'September', 'October', 'November', 'December'
    ];
    
    const monthName = monthNames[targetMonth.getMonth()];
    heading.textContent = `What's happening in ${monthName}`;
}

// EVENT SEARCH INITIALIZATION
document.addEventListener('DOMContentLoaded', function() {
    const thisMonthBtn = document.querySelector('.month-filter[data-month="current"]');
    const nextMonthBtn = document.querySelector('.month-filter[data-month="next"]');
    
    if (thisMonthBtn && nextMonthBtn) {
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
