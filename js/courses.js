// Courses Page JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadCourses();
    setupCourseFilters();
});

function setupCourseFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            btn.classList.add('active');
            
            // Get level to filter
            const level = btn.getAttribute('data-level');
            
            // Filter courses
            filterCourses(level);
        });
    });
}

function filterCourses(level) {
    const courseSections = document.querySelectorAll('.course-level-section');
    
    courseSections.forEach(section => {
        if (level === 'all') {
            section.style.display = 'block';
        } else {
            const sectionLevel = section.getAttribute('data-level');
            section.style.display = sectionLevel === level ? 'block' : 'none';
        }
    });
}

function loadCourses() {
    loadPrimaryCourses();
    loadLowerSecondaryCourses();
    loadSecondaryCourses();
}

function loadPrimaryCourses() {
    const container = document.getElementById('primaryCourses');
    if (!container) return;
    
    const courses = [
        {
            name: "Class 1",
            subjects: ["Nepali", "English", "Mathematics", "General Knowledge", "Drawing", "Games"],
            description: "Foundation building with focus on basic literacy and numeracy skills.",
            duration: "1 Year",
            age: "5-6 years"
        },
        {
            name: "Class 2",
            subjects: ["Nepali", "English", "Mathematics", "Environmental Studies", "Art & Craft", "Games"],
            description: "Building upon foundational skills with introduction to environmental awareness.",
            duration: "1 Year",
            age: "6-7 years"
        },
        {
            name: "Class 3",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education"],
            description: "Introduction to formal science and social studies concepts.",
            duration: "1 Year",
            age: "7-8 years"
        },
        {
            name: "Class 4",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Basics"],
            description: "Enhanced curriculum with introduction to computer education.",
            duration: "1 Year",
            age: "8-9 years"
        },
        {
            name: "Class 5",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Basics", "Moral Science"],
            description: "Preparation for middle school with advanced concepts in all subjects.",
            duration: "1 Year",
            age: "9-10 years"
        }
    ];
    
    const html = courses.map(course => `
        <div class="course-card">
            <div class="course-header">
                <h3>${course.name}</h3>
                <div class="course-age">Age: ${course.age}</div>
            </div>
            <div class="course-body">
                <p>${course.description}</p>
                <div class="course-duration">
                    <i class="fas fa-clock"></i> Duration: ${course.duration}
                </div>
                <div class="course-subjects">
                    <h4><i class="fas fa-book"></i> Subjects:</h4>
                    <ul>
                        ${course.subjects.map(subject => `<li>${subject}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

function loadLowerSecondaryCourses() {
    const container = document.getElementById('lowerSecondaryCourses');
    if (!container) return;
    
    const courses = [
        {
            name: "Class 6",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Science", "Optional Maths (Basic)", "Sanskrit/Hindi"],
            description: "Transition to middle school with enhanced academic rigor and subject specialization.",
            duration: "1 Year",
            age: "10-11 years"
        },
        {
            name: "Class 7",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Science", "Optional Maths", "Sanskrit/Hindi"],
            description: "Deepening understanding of core subjects with introduction to optional mathematics.",
            duration: "1 Year",
            age: "11-12 years"
        },
        {
            name: "Class 8",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Science", "Optional Maths", "Sanskrit/Hindi", "Vocational Subjects"],
            description: "Preparation for secondary education with vocational skill introduction.",
            duration: "1 Year",
            age: "12-13 years"
        }
    ];
    
    const html = courses.map(course => `
        <div class="course-card">
            <div class="course-header">
                <h3>${course.name}</h3>
                <div class="course-age">Age: ${course.age}</div>
            </div>
            <div class="course-body">
                <p>${course.description}</p>
                <div class="course-duration">
                    <i class="fas fa-clock"></i> Duration: ${course.duration}
                </div>
                <div class="course-subjects">
                    <h4><i class="fas fa-book"></i> Subjects:</h4>
                    <ul>
                        ${course.subjects.map(subject => `<li>${subject}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
}

function loadSecondaryCourses() {
    const container = document.getElementById('secondaryCourses');
    if (!container) return;
    
    const courses = [
        {
            name: "Class 9",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Science", "Accountancy", "Optional Maths", "Sanskrit/Hindi/Vocational"],
            description: "Foundation for SEE examination with comprehensive subject coverage and exam preparation.",
            duration: "1 Year",
            age: "13-14 years",
            exam: "Internal Assessment"
        },
        {
            name: "Class 10",
            subjects: ["Nepali", "English", "Mathematics", "Science", "Social Studies", "Health & Physical Education", "Computer Science", "Accountancy", "Optional Maths", "Sanskrit/Hindi/Vocational"],
            description: "Final year preparing for SEE examination with intensive exam preparation and practice.",
            duration: "1 Year",
            age: "14-15 years",
            exam: "SEE Examination"
        }
    ];
    
    const html = courses.map(course => `
        <div class="course-card">
            <div class="course-header">
                <h3>${course.name}</h3>
                <div class="course-age">Age: ${course.age}</div>
            </div>
            <div class="course-body">
                <p>${course.description}</p>
                <div class="course-duration">
                    <i class="fas fa-clock"></i> Duration: ${course.duration}
                </div>
                ${course.exam ? `<div class="course-exam"><i class="fas fa-file-alt"></i> Final Exam: ${course.exam}</div>` : ''}
                <div class="course-subjects">
                    <h4><i class="fas fa-book"></i> Subjects:</h4>
                    <ul>
                        ${course.subjects.map(subject => `<li>${subject}</li>`).join('')}
                    </ul>
                </div>
            </div>
        </div>
    `).join('');
    
    container.innerHTML = html;
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
