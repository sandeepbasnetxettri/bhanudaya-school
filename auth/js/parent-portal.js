// Parent Portal JavaScript
document.addEventListener('DOMContentLoaded', () => {
    // Handle avatar click to go to profile page
    const dashboardAvatar = document.getElementById('dashboardAvatar');
    const changeAvatarBtn = document.getElementById('changeAvatarBtn');
    
    if (dashboardAvatar) {
        dashboardAvatar.addEventListener('click', () => {
            window.location.href = 'parent-profile.php';
        });
    }
    
    if (changeAvatarBtn) {
        changeAvatarBtn.addEventListener('click', (e) => {
            e.stopPropagation(); // Prevent triggering the avatar click
            window.location.href = 'parent-profile.php';
        });
    }
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
        parentId: 'PAR2025001',
        password: 'parent123'
    };

    // Check if already logged in
    const currentUser = supabase.getCurrentUser();
    if (currentUser && currentUser.role === 'parent') {
        showDashboard();
    } else if (currentUser) {
        // User is logged in but not parent, redirect to appropriate portal
        redirectToPortal(currentUser.role);
    }

    // Login form handler
    if (loginForm) {
        loginForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            const parentId = document.getElementById('parentId').value;
            const password = document.getElementById('password').value;

            // Authenticate with Supabase
            supabase.signIn(parentId + '@excellenceschool.edu.np', password)
                .then(response => {
                    if (response.error) {
                        showMessage('Error: ' + response.error.message, 'error');
                    } else {
                        const user = response.data.user;
                        if (user.role === 'parent') {
                            setStorageData('loggedInParent', user);
                            showDashboard();
                        } else {
                            showMessage('Access denied. Parent privileges required.', 'error');
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
            localStorage.removeItem('loggedInParent');
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
                children: 'My Children',
                fees: 'Fee Management',
                attendance: 'Attendance Records',
                results: 'Academic Results',
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
        loadFeeStatus();
        loadChildrenSchedule();
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

    function loadFeeStatus() {
        const feeList = document.getElementById('feeList');
        if (!feeList) return;

        const fees = getStorageData('fees') || getDefaultFees();
        
        const html = fees.map(fee => `
            <div class="fee-item">
                <div class="fee-header">
                    <div class="fee-title">${fee.child}: ${fee.title}</div>
                    <div class="due-date">
                        <i class="fas fa-clock"></i> Due: ${formatDate(fee.dueDate)}
                    </div>
                </div>
                <div class="fee-description">${fee.description}</div>
                <div class="fee-status ${fee.status}">
                    ${fee.status.charAt(0).toUpperCase() + fee.status.slice(1)}
                </div>
            </div>
        `).join('');

        feeList.innerHTML = html || '<p>No fee records available.</p>';
    }

    function loadChildrenSchedule() {
        const scheduleList = document.getElementById('childrenSchedule');
        if (!scheduleList) return;

        const schedules = getStorageData('schedules') || getDefaultSchedules();
        
        const html = schedules.map(schedule => `
            <div class="schedule-item">
                <div class="schedule-header">
                    <div class="schedule-title">${schedule.subject} - ${schedule.child}</div>
                    <div class="schedule-time">
                        <i class="fas fa-clock"></i> ${schedule.time}
                    </div>
                </div>
                <div class="schedule-location">${schedule.location}</div>
            </div>
        `).join('');

        scheduleList.innerHTML = html || '<p>No schedule available.</p>';
    }

    function getDefaultFees() {
        return [
            {
                id: 'fee1',
                child: 'John Doe',
                title: 'Monthly Tuition',
                description: 'Regular monthly tuition fee for January',
                dueDate: '2025-01-10',
                status: 'paid'
            },
            {
                id: 'fee2',
                child: 'Jane Doe',
                title: 'Exam Fees',
                description: 'Quarterly examination fees',
                dueDate: '2025-01-15',
                status: 'pending'
            }
        ];
    }

    function getDefaultSchedules() {
        return [
            {
                id: 'sch1',
                child: 'John Doe',
                subject: 'Mathematics',
                time: '09:00 - 10:00',
                location: 'Room 101'
            },
            {
                id: 'sch2',
                child: 'Jane Doe',
                subject: 'Science',
                time: '10:15 - 11:15',
                location: 'Lab 2'
            }
        ];
    }

    // Initialize default data
    if (!getStorageData('fees')) {
        setStorageData('fees', getDefaultFees());
    }
    if (!getStorageData('schedules')) {
        setStorageData('schedules', getDefaultSchedules());
    }
});

// Payment processing
function processPayment(feeId) {
    showMessage('Redirecting to payment gateway...', 'info');
    // Simulate payment processing
    setTimeout(() => {
        showMessage('Payment processed successfully!', 'success');
        // Update fee status
        const fees = getStorageData('fees') || [];
        const fee = fees.find(f => f.id === feeId);
        if (fee) {
            fee.status = 'paid';
            fee.paidDate = new Date().toISOString();
            setStorageData('fees', fees);
        }
    }, 2000);
}

// Download report
function downloadReport(type) {
    const content = `REPORT: ${type} Report

Generated on: ${new Date().toLocaleDateString()}

This is a sample report for demonstration purposes.`;
    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = `${type}_report.txt`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showMessage('Report downloaded successfully!', 'success');
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
        case 'student':
            window.location.href = 'student-portal.html';
            break;
        default:
            window.location.href = '../index.html';
    }
}