// Admin Panel JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Initialize services
    const supabase = window.supabaseService;
    const roles = window.rolesService;
    const adminLoginForm = document.getElementById('adminLoginForm');
    const adminLogin = document.getElementById('adminLogin');
    const adminDashboard = document.getElementById('adminDashboard');
    const adminLogout = document.getElementById('adminLogout');
    const sidebarLinks = document.querySelectorAll('.admin-sidebar .sidebar-nav a[data-section]');
    const adminSectionTitle = document.getElementById('adminSectionTitle');

    // Demo credentials
    const adminCredentials = {
        username: 'admin',
        password: 'admin123'
    };

    // Check if user is logged in
    const currentUser = supabase.getCurrentUser();
    if (currentUser && currentUser.role === 'admin') {
        showAdminDashboard();
    } else if (currentUser) {
        // User is logged in but not admin, redirect to appropriate portal
        redirectToPortal(currentUser.role);
    }

    // Admin login handler
    if (adminLoginForm) {
        adminLoginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const username = document.getElementById('adminUsername').value;
            const password = document.getElementById('adminPassword').value;

            // Authenticate with Supabase
            supabase.signIn(username + '@excellenceschool.edu.np', password)
                .then(response => {
                    if (response.error) {
                        showMessage('Error: ' + response.error.message, 'error');
                    } else {
                        const user = response.data.user;
                        if (user.role === 'admin') {
                            // Store admin data
                            setStorageData('loggedInAdmin', user);
                            showAdminDashboard();
                        } else {
                            showMessage('Access denied. Admin privileges required.', 'error');
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

    // Admin logout handler
    if (adminLogout) {
        adminLogout.addEventListener('click', (e) => {
            e.preventDefault();
            localStorage.removeItem('loggedInAdmin');
            supabase.signOut();
            showAdminLogin();
        });
    }

    // Sidebar navigation
    sidebarLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            e.preventDefault();
            
            sidebarLinks.forEach(l => l.classList.remove('active'));
            link.classList.add('active');
            
            const section = link.dataset.section;
            showSection(section);
        });
    });

    // Load admin data
    loadAdminData();

    function showAdminDashboard() {
        if (adminLogin && adminDashboard) {
            adminLogin.style.display = 'none';
            adminDashboard.style.display = 'flex';
            loadAdminData();
        }
    }

    function showAdminLogin() {
        if (adminLogin && adminDashboard) {
            adminLogin.style.display = 'flex';
            adminDashboard.style.display = 'none';
        }
    }

    function loadAdminData() {
        loadAdmissions();
        loadNotices();
        updateAdmissionCount();
    }

    function loadAdmissions() {
        const admissionsTable = document.getElementById('admissionsTable');
        if (!admissionsTable) return;

        const admissions = getStorageData('admissions') || [];
        
        if (admissions.length === 0) {
            admissionsTable.innerHTML = '<tr><td colspan="6" style="text-align: center;">No admission applications yet.</td></tr>';
            return;
        }

        const html = admissions.map(admission => `
            <tr>
                <td>${admission.id}</td>
                <td>${admission.fullName}</td>
                <td>${admission.class}</td>
                <td>${formatDate(admission.submittedDate)}</td>
                <td><span class="status-badge status-${admission.status}">${admission.status}</span></td>
                <td>
                    <button class="btn-icon" onclick="viewAdmission('${admission.id}')" title="View">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-icon" onclick="approveAdmission('${admission.id}')" title="Approve">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn-icon" onclick="rejectAdmission('${admission.id}')" title="Reject">
                        <i class="fas fa-times"></i>
                    </button>
                </td>
            </tr>
        `).join('');

        admissionsTable.innerHTML = html;
    }

    function loadNotices() {
        const adminNoticesList = document.getElementById('adminNoticesList');
        if (!adminNoticesList) return;

        const news = getStorageData('news') || getDefaultNews();
        
        const html = news.map(notice => `
            <div class="admin-notice-item">
                <div class="notice-header">
                    <div>
                        <h4>${notice.title}</h4>
                        <small>${formatDate(notice.date)} | ${notice.type}</small>
                    </div>
                    <div class="notice-actions">
                        <button class="btn-icon" onclick="editNotice(${notice.id})" title="Edit">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn-icon" onclick="deleteNotice(${notice.id})" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <p>${notice.description}</p>
            </div>
        `).join('');

        adminNoticesList.innerHTML = html;
    }

    function updateAdmissionCount() {
        const admissionCount = document.getElementById('admissionCount');
        if (admissionCount) {
            const admissions = getStorageData('admissions') || [];
            admissionCount.textContent = admissions.length;
        }
    }

    // Notice form handler
    const noticeForm = document.getElementById('noticeForm');
    if (noticeForm) {
        noticeForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const formData = new FormData(noticeForm);
            const news = getStorageData('news') || getDefaultNews();
            
            const newNotice = {
                id: news.length + 1,
                title: formData.get('title'),
                type: formData.get('type'),
                description: formData.get('description'),
                date: formData.get('date'),
                image: 'images/news-default.jpg'
            };
            
            news.unshift(newNotice);
            setStorageData('news', news);
            
            showMessage('Notice published successfully!', 'success');
            noticeForm.reset();
            hideAddNoticeForm();
            loadNotices();
        });
    }
});

// Global functions for admin actions
function showSection(sectionName) {
    document.querySelectorAll('.section-content').forEach(s => {
        s.classList.remove('active');
    });
    
    const section = document.getElementById(sectionName);
    if (section) {
        section.classList.add('active');
    }
    
    const titles = {
        overview: 'Dashboard Overview',
        students: 'Student Management',
        teachers: 'Teacher Management',
        'admissions-list': 'Admission Applications',
        'notices-manage': 'Manage Notices',
        'results-manage': 'Manage Results',
        'attendance-manage': 'Attendance Management',
        'gallery-manage': 'Gallery Management',
        'timetable-manage': 'Timetable Management',
        'events-manage': 'Manage Events',
        'users-manage': 'Manage Users',
        settings: 'System Settings'
    };
    
    const adminSectionTitle = document.getElementById('adminSectionTitle');
    if (adminSectionTitle) {
        adminSectionTitle.textContent = titles[sectionName] || 'Dashboard';
    }
}

function showAddNoticeForm() {
    const form = document.getElementById('addNoticeForm');
    if (form) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function hideAddNoticeForm() {
    const form = document.getElementById('addNoticeForm');
    if (form) {
        form.style.display = 'none';
    }
}

function showAddStudentForm() {
    showMessage('Add student form would be displayed here.', 'success');
}

function viewAdmission(id) {
    const admissions = getStorageData('admissions') || [];
    const admission = admissions.find(a => a.id === id);
    
    if (admission) {
        let details = 'ADMISSION APPLICATION DETAILS\n\n';
        details += `Name: ${admission.fullName}\n`;
        details += `Class: ${admission.class}\n`;
        details += `Date of Birth: ${admission.dob}\n`;
        details += `Gender: ${admission.gender}\n`;
        details += `Address: ${admission.address}\n`;
        details += `Father's Name: ${admission.fatherName}\n`;
        details += `Mother's Name: ${admission.motherName}\n`;
        details += `Contact: ${admission.phone}\n`;
        details += `Email: ${admission.email}\n`;
        details += `Previous School: ${admission.previousSchool || 'N/A'}\n`;
        details += `Last Grade: ${admission.lastGrade || 'N/A'}\n`;
        details += `Additional Info: ${admission.additionalInfo || 'N/A'}\n`;
        
        alert(details);
    }
}

function approveAdmission(id) {
    const admissions = getStorageData('admissions') || [];
    const admission = admissions.find(a => a.id === id);
    
    if (admission) {
        admission.status = 'approved';
        setStorageData('admissions', admissions);
        showMessage('Admission approved successfully!', 'success');
        
        // Reload the table
        const event = new Event('DOMContentLoaded');
        document.dispatchEvent(event);
    }
}

function rejectAdmission(id) {
    const admissions = getStorageData('admissions') || [];
    const admission = admissions.find(a => a.id === id);
    
    if (admission) {
        admission.status = 'rejected';
        setStorageData('admissions', admissions);
        showMessage('Admission rejected.', 'success');
        
        // Reload the table
        const event = new Event('DOMContentLoaded');
        document.dispatchEvent(event);
    }
}

function editNotice(id) {
    showMessage('Edit notice functionality would be implemented here.', 'success');
}

function deleteNotice(id) {
    if (confirm('Are you sure you want to delete this notice?')) {
        let news = getStorageData('news') || getDefaultNews();
        news = news.filter(n => n.id !== id);
        setStorageData('news', news);
        showMessage('Notice deleted successfully!', 'success');
        
        // Reload notices
        const event = new Event('DOMContentLoaded');
        document.dispatchEvent(event);
    }
}

// Redirect to appropriate portal based on user role
function redirectToPortal(role) {
    switch(role) {
        case 'student':
            window.location.href = 'student-portal.html';
            break;
        case 'teacher':
            showMessage('Teacher portal coming soon!', 'success');
            break;
        default:
            window.location.href = '../index.html';
    }
}

// Events Management Functions
function showAddEventForm() {
    const form = document.getElementById('addEventForm');
    if (form) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function hideAddEventForm() {
    const form = document.getElementById('addEventForm');
    if (form) {
        form.style.display = 'none';
    }
}

function loadEvents() {
    // This would load events from the database
    const eventsList = document.getElementById('eventsList');
    if (eventsList) {
        eventsList.innerHTML = '<p>No events found. Add a new event to get started.</p>';
    }
}

// Users Management Functions
function showAddUserForm() {
    const form = document.getElementById('addUserForm');
    if (form) {
        form.style.display = 'block';
        form.scrollIntoView({ behavior: 'smooth' });
    }
}

function hideAddUserForm() {
    const form = document.getElementById('addUserForm');
    if (form) {
        form.style.display = 'none';
    }
}

function loadUsers() {
    // This would load users from the database
    const usersTableBody = document.getElementById('usersTableBody');
    if (usersTableBody) {
        usersTableBody.innerHTML = '<tr><td colspan="5" style="text-align: center;">No users found.</td></tr>';
    }
}

// Initialize event form
document.addEventListener('DOMContentLoaded', function() {
    const eventForm = document.getElementById('eventForm');
    if (eventForm) {
        eventForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // This would save the event to the database
            showMessage('Event saved successfully!', 'success');
            eventForm.reset();
            hideAddEventForm();
            loadEvents();
        });
    }
    
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            e.preventDefault();
            // This would save the user to the database
            showMessage('User created successfully!', 'success');
            userForm.reset();
            hideAddUserForm();
            loadUsers();
        });
    }
    
    // Load initial data
    loadEvents();
    loadUsers();
});