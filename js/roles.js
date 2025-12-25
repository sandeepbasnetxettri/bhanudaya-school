// Roles Management Service
class RolesService {
    constructor() {
        this.roles = {
            admin: {
                name: 'Administrator',
                permissions: [
                    'manage_users',
                    'manage_students',
                    'manage_teachers',
                    'manage_classes',
                    'manage_subjects',
                    'manage_results',
                    'manage_attendance',
                    'manage_notices',
                    'manage_events',
                    'manage_gallery',
                    'view_all_data',
                    'generate_reports'
                ]
            },
            teacher: {
                name: 'Teacher',
                permissions: [
                    'view_assigned_classes',
                    'view_assigned_students',
                    'manage_attendance_for_class',
                    'manage_results_for_class',
                    'create_assignments',
                    'grade_submissions',
                    'view_personal_schedule'
                ]
            },
            student: {
                name: 'Student',
                permissions: [
                    'view_personal_profile',
                    'view_class_schedule',
                    'view_attendance',
                    'view_results',
                    'view_assignments',
                    'submit_assignments',
                    'view_notices'
                ]
            },
            parent: {
                name: 'Parent',
                permissions: [
                    'view_child_profile',
                    'view_child_attendance',
                    'view_child_results',
                    'view_child_assignments',
                    'view_notices'
                ]
            }
        };
    }
    
    // Get role details
    getRole(roleKey) {
        return this.roles[roleKey] || null;
    }
    
    // Check if user has a specific permission
    hasPermission(userRole, permission) {
        const role = this.roles[userRole];
        if (!role) return false;
        return role.permissions.includes(permission);
    }
    
    // Check if user has any of the specified permissions
    hasAnyPermission(userRole, permissions) {
        return permissions.some(permission => this.hasPermission(userRole, permission));
    }
    
    // Check if user has all of the specified permissions
    hasAllPermissions(userRole, permissions) {
        return permissions.every(permission => this.hasPermission(userRole, permission));
    }
    
    // Get all permissions for a role
    getPermissions(userRole) {
        const role = this.roles[userRole];
        return role ? [...role.permissions] : [];
    }
    
    // Get user role name for display
    getRoleName(userRole) {
        const role = this.roles[userRole];
        return role ? role.name : 'Unknown Role';
    }
    
    // Check if user can access a specific section
    canAccessSection(userRole, section) {
        const sectionPermissions = {
            'admin-dashboard': ['manage_users', 'manage_students', 'manage_teachers', 'view_all_data'],
            'student-profile': ['view_personal_profile', 'view_child_profile'],
            'class-management': ['manage_classes', 'view_assigned_classes'],
            'subject-management': ['manage_subjects'],
            'result-management': ['manage_results', 'manage_results_for_class', 'view_results'],
            'attendance-management': ['manage_attendance', 'manage_attendance_for_class', 'view_attendance'],
            'notice-board': ['manage_notices', 'view_notices'],
            'event-calendar': ['manage_events'],
            'gallery': ['manage_gallery', 'view_gallery'],
            'assignment-center': ['create_assignments', 'view_assignments', 'submit_assignments', 'grade_submissions']
        };
        
        const requiredPermissions = sectionPermissions[section];
        if (!requiredPermissions) return false;
        
        return this.hasAnyPermission(userRole, requiredPermissions);
    }
}

// Export a singleton instance
const rolesService = new RolesService();

// Make it available globally
window.rolesService = rolesService;