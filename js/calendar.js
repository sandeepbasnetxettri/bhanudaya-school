// Academic Calendar JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadCalendarEvents();
    setupCalendarFilters();
});

function setupCalendarFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const filter = btn.getAttribute('data-filter');
            filterEvents(filter);
        });
    });
}

function loadCalendarEvents() {
    const events = getCalendarEvents();
    displayCalendarEvents(events);
}

function getCalendarEvents() {
    return [
        // Baisakh (April-May)
        {
            month: 'Baisakh',
            monthNumber: 1,
            events: [
                { date: 1, title: 'New Academic Session Begins', type: 'event', description: 'Welcome ceremony for new students' },
                { date: 14, title: 'Nepali New Year', type: 'holiday', description: 'Public Holiday' },
                { date: 18, title: 'Parent-Teacher Meeting', type: 'event', description: 'First PTM of the session' }
            ]
        },
        // Jestha (May-June)
        {
            month: 'Jestha',
            monthNumber: 2,
            events: [
                { date: 11, title: 'Buddha Jayanti', type: 'holiday', description: 'Public Holiday' },
                { date: 15, title: 'Class Tests Begin', type: 'exam', description: 'Monthly assessment' },
                { date: 29, title: 'Republic Day', type: 'holiday', description: 'Public Holiday' }
            ]
        },
        // Ashar (June-July)
        {
            month: 'Ashar',
            monthNumber: 3,
            events: [
                { date: 5, title: 'Science Exhibition', type: 'event', description: 'Annual science fair' },
                { date: 15, title: 'Project Submission Deadline', type: 'exam', description: 'First term projects' }
            ]
        },
        // Shrawan (July-August)
        {
            month: 'Shrawan',
            monthNumber: 4,
            events: [
                { date: 1, title: 'Raksha Bandhan', type: 'holiday', description: 'Festival holiday' },
                { date: 8, title: 'Janai Purnima', type: 'holiday', description: 'Public Holiday' },
                { date: 15, title: 'First Terminal Exam Begins', type: 'exam', description: 'Duration: 10 days' },
                { date: 25, title: 'First Terminal Exam Ends', type: 'exam', description: 'Last day of exam' },
                { date: 23, title: 'Krishna Janmashtami', type: 'holiday', description: 'Public Holiday' }
            ]
        },
        // Bhadra (August-September)
        {
            month: 'Bhadra',
            monthNumber: 5,
            events: [
                { date: 3, title: 'Teej Festival', type: 'holiday', description: 'Public Holiday' },
                { date: 10, title: 'First Terminal Results', type: 'exam', description: 'Result publication' },
                { date: 15, title: 'Independence Day Celebration', type: 'event', description: 'Cultural programs' },
                { date: 20, title: 'Admission Open - New Session', type: 'admission', description: 'For next academic year' }
            ]
        },
        // Ashwin (September-October)
        {
            month: 'Ashwin',
            monthNumber: 6,
            events: [
                { date: 5, title: 'Constitution Day', type: 'holiday', description: 'Public Holiday' },
                { date: 10, title: 'Dashain Vacation Begins', type: 'holiday', description: 'Duration: 15 days' },
                { date: 25, title: 'Dashain Vacation Ends', type: 'holiday', description: 'School reopens on Ashwin 26' }
            ]
        },
        // Kartik (October-November)
        {
            month: 'Kartik',
            monthNumber: 7,
            events: [
                { date: 1, title: 'Laxmi Puja', type: 'holiday', description: 'Tihar festival' },
                { date: 3, title: 'Bhai Tika', type: 'holiday', description: 'Tihar festival' },
                { date: 10, title: 'Inter-House Sports Competition', type: 'event', description: 'Week-long event' },
                { date: 20, title: 'Second Terminal Exam Begins', type: 'exam', description: 'Duration: 10 days' },
                { date: 26, title: 'Tihar Vacation Begins', type: 'holiday', description: 'Duration: 5 days' },
                { date: 30, title: 'Second Terminal Exam Ends / Tihar Vacation Ends', type: 'exam', description: 'Last day' }
            ]
        },
        // Mangsir (November-December)
        {
            month: 'Mangsir',
            monthNumber: 8,
            events: [
                { date: 5, title: 'Second Terminal Results', type: 'exam', description: 'Result publication' },
                { date: 10, title: 'Parent-Teacher Meeting', type: 'event', description: 'Second PTM' },
                { date: 15, title: 'Annual Sports Day', type: 'event', description: 'Track and field events' },
                { date: 20, title: 'Admission Interview', type: 'admission', description: 'For new admissions' }
            ]
        },
        // Poush (December-January)
        {
            month: 'Poush',
            monthNumber: 9,
            events: [
                { date: 1, title: 'Yomari Punhi', type: 'holiday', description: 'Festival holiday' },
                { date: 10, title: 'Science Fair', type: 'event', description: 'Student presentations' },
                { date: 15, title: 'Winter Vacation Begins', type: 'holiday', description: 'Duration: 20 days' }
            ]
        },
        // Magh (January-February)
        {
            month: 'Magh',
            monthNumber: 10,
            events: [
                { date: 5, title: 'Winter Vacation Ends', type: 'holiday', description: 'School reopens on Magh 6' },
                { date: 11, title: 'Maghe Sankranti', type: 'holiday', description: 'Public Holiday' },
                { date: 15, title: 'Admission Closed', type: 'admission', description: 'Last date for admission' },
                { date: 20, title: 'Sonam Lhosar', type: 'holiday', description: 'Public Holiday' },
                { date: 25, title: 'Pre-Final Exam', type: 'exam', description: 'For Class 10 & +2' }
            ]
        },
        // Falgun (February-March)
        {
            month: 'Falgun',
            monthNumber: 11,
            events: [
                { date: 1, title: 'Final Exam Begins', type: 'exam', description: 'Annual examination' },
                { date: 7, title: 'Mahashivaratri', type: 'holiday', description: 'Public Holiday' },
                { date: 8, title: 'International Women\'s Day', type: 'event', description: 'Special program' },
                { date: 15, title: 'Final Exam Ends', type: 'exam', description: 'Last day of exam' },
                { date: 20, title: 'Holi', type: 'holiday', description: 'Festival holiday' },
                { date: 25, title: 'Annual Day Celebration', type: 'event', description: 'Prize distribution' }
            ]
        },
        // Chaitra (March-April)
        {
            month: 'Chaitra',
            monthNumber: 12,
            events: [
                { date: 1, title: 'Admission Opens - New Session', type: 'admission', description: 'Next academic year' },
                { date: 5, title: 'Final Results Published', type: 'exam', description: 'Annual results' },
                { date: 8, title: 'Ghode Jatra', type: 'holiday', description: 'Public Holiday' },
                { date: 10, title: 'Parent-Teacher Meeting', type: 'event', description: 'Final PTM' },
                { date: 16, title: 'Summer Vacation Begins', type: 'holiday', description: 'Duration: 15 days' },
                { date: 30, title: 'Academic Session Ends', type: 'event', description: 'Last day of session' }
            ]
        }
    ];
}

function displayCalendarEvents(events) {
    const container = document.getElementById('calendarContainer');
    if (!container) return;

    let html = '';

    events.forEach(monthData => {
        html += `
            <div class="month-section">
                <div class="month-header">
                    <i class="fas fa-calendar-alt"></i>
                    <h3>${monthData.month} 2082</h3>
                    <span class="event-count">${monthData.events.length} events</span>
                </div>
                <div class="events-list">
        `;

        monthData.events.forEach(event => {
            const iconMap = {
                'exam': 'fa-file-alt',
                'holiday': 'fa-umbrella-beach',
                'event': 'fa-star',
                'admission': 'fa-user-plus'
            };

            html += `
                <div class="event-item ${event.type}-type" data-type="${event.type}">
                    <div class="event-date">
                        <div class="date-number">${event.date}</div>
                        <div class="date-month">${monthData.month}</div>
                    </div>
                    <div class="event-details">
                        <h4><i class="fas ${iconMap[event.type]}"></i> ${event.title}</h4>
                        <p>${event.description}</p>
                    </div>
                    <div class="event-badge ${event.type}-badge">${event.type}</div>
                </div>
            `;
        });

        html += `
                </div>
            </div>
        `;
    });

    container.innerHTML = html;
}

function filterEvents(filterType) {
    const eventItems = document.querySelectorAll('.event-item');
    
    eventItems.forEach(item => {
        if (filterType === 'all') {
            item.style.display = 'flex';
        } else {
            const itemType = item.getAttribute('data-type');
            item.style.display = itemType === filterType ? 'flex' : 'none';
        }
    });

    // Update month visibility
    document.querySelectorAll('.month-section').forEach(section => {
        const visibleEvents = section.querySelectorAll('.event-item[style="display: flex;"]');
        if (visibleEvents.length === 0) {
            section.style.display = 'none';
        } else {
            section.style.display = 'block';
        }
    });
}

function downloadCalendar() {
    alert('Downloading Academic Calendar 2025-2026...\n\nNote: In a full implementation, this would generate a PDF with all calendar events.');
    return false;
}

// Helper functions
function getStorageData(key) {
    try {
        return JSON.parse(localStorage.getItem(key));
    } catch (e) {
        return null;
    }
}

function setStorageData(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (e) {
        return false;
    }
}
