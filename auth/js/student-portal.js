// Student Portal JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Initialize services
    const supabase = window.supabaseService;
    const roles = window.rolesService;
    const loginForm = document.getElementById('loginForm');
    const loginScreen = document.getElementById('loginScreen');
    const dashboard = document.getElementById('dashboard');
    const logoutBtn = document.getElementById('logoutBtn');
    const sidebarLinks = document.querySelectorAll('.sidebar-nav a[data-section]');
    const sectionTitle = document.getElementById('sectionTitle');

    // Demo credentials
    const demoCredentials = {
        studentId: 'STU2025001',
        password: 'student123'
    };

    // Check if already logged in
    const currentUser = supabase.getCurrentUser();
    if (currentUser && currentUser.role === 'student') {
        showDashboard();
    } else if (currentUser) {
        // User is logged in but not student, redirect to appropriate portal
        redirectToPortal(currentUser.role);
    }

    // Login form handler
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const studentId = document.getElementById('studentId').value;
            const password = document.getElementById('password').value;

            // Authenticate with Supabase
            supabase.signIn(studentId + '@excellenceschool.edu.np', password)
                .then(response => {
                    if (response.error) {
                        showMessage('Error: ' + response.error.message, 'error');
                    } else {
                        const user = response.data.user;
                        if (user.role === 'student') {
                            setStorageData('loggedInStudent', user);
                            showDashboard();
                        } else {
                            showMessage('Access denied. Student privileges required.', 'error');
                            // Redirect to appropriate portal
                            setTimeout(() => {
                                redirectToPortal(user.role);
                            }, 2000);
                        }
                    }
                })
                .catch(error => {
                    showMessage('Error: ' + error.message, 'error');
                });
        });
    }

    // Logout handler
    if (logoutBtn) {
        logoutBtn.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('loggedInStudent');
            supabase.signOut();
            showLogin();
        });
    }

    // Sidebar navigation
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            // Remove active class from all links
            sidebarLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            link.classList.add('active');
            
            // Get section to show
            const section = link.dataset.section;
            
            // Hide all sections
            document.querySelectorAll('.section-content').forEach(s => {
                s.classList.remove('active');
            });
            
            // Show selected section
            const selectedSection = document.getElementById(section);
            if (selectedSection) {
                selectedSection.classList.add('active');
            }
            
            // Update title
            const titles = {
                overview: 'Dashboard Overview',
                profile: 'My Profile',
                attendance: 'Attendance Record',
                results: 'Examination Results',
                assignments: 'Assignments',
                homework: 'Homework',
                timetable: 'Class Timetable',
                notices: 'Notices & Announcements'
            };
            sectionTitle.textContent = titles[section] || 'Dashboard';
        });
    });

    // Load initial data
    loadDashboardData();

    function showDashboard() {
        if (loginScreen && dashboard) {
            loginScreen.style.display = 'none';
            dashboard.style.display = 'flex';
            loadDashboardData();
        }
    }

    function showLogin() {
        if (loginScreen && dashboard) {
            loginScreen.style.display = 'flex';
            dashboard.style.display = 'none';
        }
    }

    function loadDashboardData() {
        loadRecentNotices();
        loadAssignments();
        loadHomework();
    }

    function loadRecentNotices() {
        const recentNotices = document.getElementById('recentNotices');
        const noticesList = document.getElementById('noticesList');
        const news = getStorageData('news') || getDefaultNews();
        const notices = news.filter(n => n.type === 'Notice').slice(0, 5);

        const noticeHTML = notices.map(notice => `
            <div class="notice-list-item">
                <div class="notice-header">
                    <div class="notice-title">${notice.title}</div>
                    <span class="notice-date">${formatDate(notice.date)}</span>
                </div>
                <p>${notice.description}</p>
            </div>
        `).join('');

        if (recentNotices) {
            recentNotices.innerHTML = notices.slice(0, 3).map(notice => `
                <div class="event-item">
                    <div class="event-date">${new Date(notice.date).getDate()}</div>
                    <div class="event-details">
                        <h4>${notice.title}</h4>
                        <p>${notice.description.substring(0, 50)}...</p>
                    </div>
                </div>
            `).join('');
        }

        if (noticesList) {
            noticesList.innerHTML = noticeHTML || '<p>No notices available.</p>';
        }
    }

    function loadAssignments() {
        const assignmentsList = document.getElementById('assignmentsList');
        if (!assignmentsList) return;

        const assignments = getStorageData('assignments') || getDefaultAssignments();
        
        const html = assignments.map(assignment => `
            <div class="assignment-item">
                <div class="assignment-header">
                    <div class="assignment-title">${assignment.subject}: ${assignment.title}</div>
                    <div class="due-date">
                        <i class="fas fa-clock"></i> Due: ${formatDate(assignment.dueDate)}
                    </div>
                </div>
                <div class="assignment-description">${assignment.description}</div>
                <div class="assignment-actions">
                    <button class="btn btn-primary" onclick="submitAssignment('${assignment.id}')">
                        <i class="fas fa-upload"></i> Submit
                    </button>
                    <button class="btn btn-outline" onclick="downloadAssignment('${assignment.id}')">
                        <i class="fas fa-download"></i> Download
                    </button>
                </div>
            </div>
        `).join('');

        assignmentsList.innerHTML = html || '<p>No pending assignments.</p>';
    }

    function loadHomework() {
        const homeworkList = document.getElementById('homeworkList');
        if (!homeworkList) return;

        const homework = getStorageData('homework') || getDefaultHomework();
        
        const html = homework.map(hw => `
            <div class="homework-item">
                <div class="homework-header">
                    <div class="homework-title">${hw.subject}</div>
                    <div class="due-date">
                        <i class="fas fa-calendar"></i> ${formatDate(hw.date)}
                    </div>
                </div>
                <div class="homework-description">${hw.description}</div>
            </div>
        `).join('');

        homeworkList.innerHTML = html || '<p>No homework assigned.</p>';
    }

    function getDefaultAssignments() {
        return [
            {
                id: 'asn1',
                subject: 'Mathematics',
                title: 'Quadratic Equations',
                description: 'Solve exercises from Chapter 5: Quadratic Equations. Include all steps and formulas used.',
                dueDate: '2025-02-20'
            },
            {
                id: 'asn2',
                subject: 'English',
                title: 'Essay Writing',
                description: 'Write a 500-word essay on "The Impact of Technology on Education". Include introduction, body, and conclusion.',
                dueDate: '2025-02-18'
            },
            {
                id: 'asn3',
                subject: 'Science',
                title: 'Lab Report',
                description: 'Complete the lab report on the chemical reactions experiment conducted last week.',
                dueDate: '2025-02-22'
            }
        ];
    }

    function getDefaultHomework() {
        return [
            {
                id: 'hw1',
                subject: 'Mathematics',
                description: 'Complete exercises 1-10 from page 45',
                date: '2025-01-16'
            },
            {
                id: 'hw2',
                subject: 'Science',
                description: 'Read Chapter 8 and answer review questions',
                date: '2025-01-16'
            }
        ];
    }

    // Initialize default data
    if (!getStorageData('assignments')) {
        setStorageData('assignments', getDefaultAssignments());
    }
    if (!getStorageData('homework')) {
        setStorageData('homework', getDefaultHomework());
    }
});

// Assignment submission
function submitAssignment(id) {
    const input = document.createElement('input');
    input.type = 'file';
    input.accept = '.pdf,.doc,.docx';
    input.onchange = (e) => {
        const file = e.target.files[0];
        if (file) {
            showMessage(`Assignment "${file.name}" submitted successfully!`, 'success');
            // Update assignment status
            const assignments = getStorageData('assignments') || [];
            const assignment = assignments.find(a => a.id === id);
            if (assignment) {
                assignment.status = 'submitted';
                assignment.submittedDate = new Date().toISOString();
                setStorageData('assignments', assignments);
            }
        }
    };
    input.click();
}

// Download assignment
function downloadAssignment(id) {
    const assignments = getStorageData('assignments') || [];
    const assignment = assignments.find(a => a.id === id);
    
    if (assignment) {
        const content = `ASSIGNMENT: ${assignment.title}

Subject: ${assignment.subject}

Description:
${assignment.description}

Due Date: ${formatDate(assignment.dueDate)}`;
        const blob = new Blob([content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = `${assignment.subject}_${assignment.title}.txt`;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
        
        showMessage('Assignment downloaded successfully!', 'success');
    }
}

// Redirect to appropriate portal based on user role
function redirectToPortal(role) {
    switch(role) {
        case 'admin':
            window.location.href = 'admin-login.html';
            break;
        case 'teacher':
            showMessage('Teacher portal coming soon!', 'success');
            break;
        default:
            window.location.href = '../index.html';
    }
}