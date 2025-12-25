// Timetable JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadTimetable();
    setupTimetableControls();
});

function setupTimetableControls() {
    const classSelect = document.getElementById('classSelect');
    const downloadBtn = document.getElementById('downloadBtn');

    if (classSelect) {
        classSelect.addEventListener('change', loadTimetable);
    }

    if (downloadBtn) {
        downloadBtn.addEventListener('click', downloadTimetable);
    }
}

function loadTimetable() {
    const classSelect = document.getElementById('classSelect');
    const selectedClass = classSelect ? classSelect.value : '10';
    
    const timetableData = getTimetableData(selectedClass);
    displayTimetable(timetableData, selectedClass);
    displayLegend(timetableData.legend);
}

function getTimetableData(classValue) {
    const timetables = {
        '10': {
            className: 'Class 10',
            schedule: {
                'Sunday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'A103' },
                    { time: '2:40-3:20', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '3:20-4:00', subject: 'Optional Mathematics', teacher: 'Mr. Kumar', room: 'Lab 2' },
                ],
                'Monday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'A103' },
                    { time: '2:40-3:20', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '3:20-4:00', subject: 'Optional Mathematics', teacher: 'Mr. Kumar', room: 'Lab 2' },
                ],
                'Tuesday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'A103' },
                    { time: '2:40-3:20', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '3:20-4:00', subject: 'Optional Mathematics', teacher: 'Mr. Kumar', room: 'Lab 2' },
                ],
                'Wednesday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'A103' },
                    { time: '2:40-3:20', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '3:20-4:00', subject: 'Optional Mathematics', teacher: 'Mr. Kumar', room: 'Lab 2' },
                ],
                'Thursday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'A103' },
                    { time: '2:40-3:20', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '3:20-4:00', subject: 'Optional Mathematics', teacher: 'Mr. Kumar', room: 'Lab 2' },
                ],
                'Friday': [
                    { time: '10:00-11:00', subject: 'Mathematics', teacher: 'Mr. Sharma', room: 'A101' },
                    { time: '11:00-11:45', subject: 'English', teacher: 'Ms. Rai', room: 'A102' },
                    { time: '11:45-12:30', subject: 'Science', teacher: 'Dr. Patel', room: 'Lab 1' },
                    { time: '12:30-1:15', subject: 'Nepali', teacher: 'Mrs. Adhikari', room: 'A101' },
                    { time: '12:30-1:15', subject: 'BREAK', teacher: '-', room: '-' },
                ]
            },
            legend: [
                { subject: 'Mathematics', teacher: 'Mr. Sharma', code: 'MATH' },
                { subject: 'English', teacher: 'Ms. Rai', code: 'ENG' },
                { subject: 'Science', teacher: 'Dr. Patel', code: 'SCI' },
                { subject: 'Social Studies', teacher: 'Mr. Thapa', code: 'SS' },
                { subject: 'Nepali', teacher: 'Mrs. Adhikari', code: 'NEP' },
                { subject: 'Computer', teacher: 'Mr. Kumar', code: 'COMP' },
                { subject: 'Optional Mathematics', teacher: 'Mrs. Joshi', code: 'OPT-M' }
            ]
        },
        '11-cs': {
            className: '+2 Computer Science',
            schedule: {
                'Sunday': [
                    { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '2:40-3:20', subject: 'Database', teacher: 'Mrs. Gurung', room: 'Lab 2' },
                    { time: '3:20-4:00', subject: 'Chemistry', teacher: 'Dr. Acharya', room: 'Lab 4' },
                ],
                'Monday': [
                     { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '2:40-3:20', subject: 'Database', teacher: 'Mrs. Gurung', room: 'Lab 2' },
                    { time: '3:20-4:00', subject: 'Chemistry', teacher: 'Dr. Acharya', room: 'Lab 4' },
                ],
                'Tuesday': [
                     { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '2:40-3:20', subject: 'Database', teacher: 'Mrs. Gurung', room: 'Lab 2' },
                    { time: '3:20-4:00', subject: 'Chemistry', teacher: 'Dr. Acharya', room: 'Lab 4' },
                ],
                'Wednesday': [
                     { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '2:40-3:20', subject: 'Database', teacher: 'Mrs. Gurung', room: 'Lab 2' },
                    { time: '3:20-4:00', subject: 'Chemistry', teacher: 'Dr. Acharya', room: 'Lab 4' },
                ],
                'Thursday': [
                   { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '2:00-2:40', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '2:40-3:20', subject: 'Database', teacher: 'Mrs. Gurung', room: 'Lab 2' },
                    { time: '3:20-4:00', subject: 'Chemistry', teacher: 'Dr. Acharya', room: 'Lab 4' },
                ],
                'Friday': [
                   { time: '10:00-11:00', subject: 'C Programming', teacher: 'Mr. Kumar', room: 'Lab 2' },
                    { time: '11:45-11:45', subject: 'Mathematics', teacher: 'Dr. Shrestha', room: 'B201' },
                    { time: '11:45-12:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '12:30-1:15', subject: 'Physics', teacher: 'Mr. Pandey', room: 'Lab 3' },
                    { time: '1:15-2:00', subject: 'BREAK', teacher: '-', room: '-' },
                ]
            },
            legend: [
                { subject: 'C Programming', teacher: 'Mr. Kumar', code: 'C-PROG' },
                { subject: 'Database Management', teacher: 'Mrs. Gurung', code: 'DBMS' },
                { subject: 'Mathematics', teacher: 'Dr. Shrestha', code: 'MATH' },
                { subject: 'Physics', teacher: 'Mr. Pandey', code: 'PHY' },
                { subject: 'Chemistry', teacher: 'Dr. Acharya', code: 'CHEM' },
                { subject: 'English', teacher: 'Ms. Rai', code: 'ENG' },
                { subject: 'Nepali', teacher: 'Mr. Khatri', code: 'NEP' }
            ]
        },
        '11-hm': {
            className: '+2 Hotel Management',
            schedule: {
                'Sunday': [
                    { time: '7:00-7:50', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '7:50-8:40', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '8:40-9:30', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '10:50-11:40', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '11:40-12:30', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'B203' },
                    { time: '12:30-1:00', subject: 'Practical', teacher: 'Lab Staff', room: 'Kitchen' }
                ],
                'Monday': [
                    { time: '7:00-7:50', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '7:50-8:40', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '8:40-9:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '10:50-11:40', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '11:40-12:30', subject: 'Nepali', teacher: 'Mr. Khatri', room: 'B203' },
                    { time: '12:30-1:00', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'B203' }
                ],
                'Tuesday': [
                    { time: '7:00-7:50', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '7:50-8:40', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '8:40-9:30', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '10:50-11:40', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '11:40-12:30', subject: 'Hospitality Mgmt', teacher: 'Mr. Gurung', room: 'B201' },
                    { time: '12:30-1:00', subject: 'Practical', teacher: 'Lab Staff', room: 'Kitchen' }
                ],
                'Wednesday': [
                    { time: '7:00-7:50', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '7:50-8:40', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '8:40-9:30', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '10:50-11:40', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '11:40-12:30', subject: 'Nepali', teacher: 'Mr. Khatri', room: 'B203' },
                    { time: '12:30-1:00', subject: 'Social Studies', teacher: 'Mr. Thapa', room: 'B203' }
                ],
                'Thursday': [
                    { time: '7:00-7:50', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '7:50-8:40', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '8:40-9:30', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '10:50-11:40', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '11:40-12:30', subject: 'Hospitality Mgmt', teacher: 'Mr. Gurung', room: 'B201' },
                    { time: '12:30-1:00', subject: 'Practical', teacher: 'Lab Staff', room: 'Kitchen' }
                ],
                'Friday': [
                    { time: '7:00-7:50', subject: 'English', teacher: 'Ms. Rai', room: 'B202' },
                    { time: '7:50-8:40', subject: 'Food Production', teacher: 'Chef Rai', room: 'Kitchen' },
                    { time: '8:40-9:30', subject: 'F&B Service', teacher: 'Mr. Lama', room: 'F&B Lab' },
                    { time: '9:30-10:00', subject: 'BREAK', teacher: '-', room: '-' },
                    { time: '10:00-10:50', subject: 'Front Office', teacher: 'Mrs. Sherpa', room: 'FO Lab' },
                    { time: '10:50-11:40', subject: 'Accommodation', teacher: 'Ms. Tamang', room: 'HK Lab' },
                    { time: '11:40-12:30', subject: 'Hospitality Mgmt', teacher: 'Mr. Gurung', room: 'B201' },
                    { time: '12:30-1:00', subject: 'Nepali', teacher: 'Mr. Khatri', room: 'B203' }
                ]
            },
            legend: [
                { subject: 'Food Production', teacher: 'Chef Rai', code: 'FP' },
                { subject: 'Food & Beverage Service', teacher: 'Mr. Lama', code: 'F&B' },
                { subject: 'Front Office Operations', teacher: 'Mrs. Sherpa', code: 'FO' },
                { subject: 'Accommodation Operations', teacher: 'Ms. Tamang', code: 'ACC' },
                { subject: 'Hospitality Management', teacher: 'Mr. Gurung', code: 'HM' },
                { subject: 'English', teacher: 'Ms. Rai', code: 'ENG' },
                { subject: 'Nepali', teacher: 'Mr. Khatri', code: 'NEP' }
            ]
        }
    };

    return timetables[classValue] || timetables['10'];
}

function displayTimetable(data, classValue) {
    const container = document.getElementById('timetableContainer');
    if (!container) return;

    const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'];
    
    let html = `
        <div class="timetable-header">
            <h3>${data.className} - Weekly Schedule</h3>
        </div>
        <div class="timetable-scroll">
            <table class="timetable">
                <thead>
                    <tr>
                        <th>Time</th>
                        ${days.map(day => `<th>${day}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
    `;

    // Get max periods
    const maxPeriods = Math.max(...days.map(day => data.schedule[day].length));

    for (let i = 0; i < maxPeriods; i++) {
        html += '<tr>';
        
        // Time column
        const timeSlot = data.schedule[days[0]][i]?.time || '-';
        html += `<td class="time-cell">${timeSlot}</td>`;
        
        // Day columns
        days.forEach(day => {
            const period = data.schedule[day][i];
            if (period) {
                const isBreak = period.subject === 'BREAK';
                const cellClass = isBreak ? 'break-cell' : 'subject-cell';
                html += `
                    <td class="${cellClass}">
                        <div class="subject-name">${period.subject}</div>
                        ${!isBreak ? `
                            <div class="teacher-name">${period.teacher}</div>
                            <div class="room-name">${period.room}</div>
                        ` : ''}
                    </td>
                `;
            } else {
                html += '<td class="empty-cell">-</td>';
            }
        });
        
        html += '</tr>';
    }

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function displayLegend(legend) {
    const legendGrid = document.getElementById('legendGrid');
    if (!legendGrid) return;

    const html = legend.map(item => `
        <div class="legend-item">
            <div class="legend-code">${item.code}</div>
            <div class="legend-details">
                <div class="legend-subject">${item.subject}</div>
                <div class="legend-teacher">${item.teacher}</div>
            </div>
        </div>
    `).join('');

    legendGrid.innerHTML = html;
}

function downloadTimetable() {
    const classSelect = document.getElementById('classSelect');
    const selectedClass = classSelect ? classSelect.value : '10';
    
    alert('Downloading timetable for ' + classSelect.options[classSelect.selectedIndex].text + '...\n\nNote: In a full implementation, this would generate a PDF using a library like jsPDF or html2pdf.');
    
    // In a real implementation, you would use jsPDF or similar library:
    // const element = document.getElementById('timetableContainer');
    // html2pdf().from(element).save('timetable.pdf');
}

// Helper function for storage (from main.js)
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
