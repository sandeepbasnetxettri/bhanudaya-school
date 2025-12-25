// Admission Form Handler
document.addEventListener('DOMContentLoaded', () => {
    const admissionForm = document.getElementById('admissionForm');
    
    if (admissionForm) {
        // File upload handler
        const fileUpload = admissionForm.querySelector('input[type="file"]');
        const fileUploadDiv = admissionForm.querySelector('.file-upload');
        
        if (fileUploadDiv && fileUpload) {
            fileUploadDiv.addEventListener('click', () => {
                fileUpload.click();
            });

            fileUpload.addEventListener('change', (e) => {
                const file = e.target.files[0];
                if (file) {
                    const fileSize = file.size / 1024 / 1024; // in MB
                    if (fileSize > 2) {
                        alert('File size should not exceed 2MB');
                        fileUpload.value = '';
                        return;
                    }
                    fileUploadDiv.querySelector('p').innerHTML = `
                        <i class="fas fa-check-circle"></i> ${file.name} selected
                    `;
                }
            });
        }

        admissionForm.addEventListener('submit', (e) => {
            e.preventDefault();

            if (!validateForm('admissionForm')) {
                return;
            }

            const formData = new FormData(admissionForm);
            const data = {};
            
            for (let [key, value] of formData.entries()) {
                if (key !== 'photo') {
                    data[key] = value;
                }
            }

            // Add submission ID and date
            data.id = generateId();
            data.submittedDate = new Date().toISOString();
            data.status = 'pending';

            // Get existing admissions or initialize array
            let admissions = getStorageData('admissions') || [];
            admissions.push(data);
            setStorageData('admissions', admissions);

            // Show success message
            showMessage('Admission application submitted successfully! You will receive a confirmation email shortly.', 'success');
            
            // Reset form
            admissionForm.reset();
            if (fileUploadDiv) {
                fileUploadDiv.querySelector('p').innerHTML = `
                    <i class="fas fa-cloud-upload-alt"></i> Click to upload or drag and drop
                `;
            }

            // Scroll to top
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    }
});

// Download form function
function downloadForm(type) {
    let content = '';
    let filename = '';

    if (type === 'admission') {
        content = 'EXCELLENCE SCHOOL - ADMISSION FORM\n\n';
        content += '1. Student Information\n';
        content += '   Full Name: _________________________\n';
        content += '   Date of Birth: _____________________\n';
        content += '   Gender: ____________________________\n';
        content += '   Applying For Class: _______________\n\n';
        content += '2. Parent/Guardian Information\n';
        content += '   Father\'s Name: _____________________\n';
        content += '   Mother\'s Name: _____________________\n';
        content += '   Contact: ___________________________\n';
        content += '   Email: _____________________________\n\n';
        content += '3. Previous School Information\n';
        content += '   School Name: _______________________\n';
        content += '   Last Grade: ________________________\n\n';
        filename = 'admission-form.txt';
    } else if (type === 'guidelines') {
        content = 'EXCELLENCE SCHOOL - ADMISSION GUIDELINES 2025/26\n\n';
        content += '1. ELIGIBILITY CRITERIA\n';
        content += '   - Age requirement as per class level\n';
        content += '   - Required documents must be submitted\n';
        content += '   - Entrance test mandatory for grades 6 and above\n\n';
        content += '2. REQUIRED DOCUMENTS\n';
        content += '   - Birth certificate\n';
        content += '   - Previous academic certificates\n';
        content += '   - Character certificate\n';
        content += '   - Passport photos (4 copies)\n\n';
        content += '3. ADMISSION PROCESS\n';
        content += '   - Submit online application\n';
        content += '   - Attend entrance examination\n';
        content += '   - Interview\n';
        content += '   - Fee payment\n';
        filename = 'admission-guidelines.txt';
    }

    const blob = new Blob([content], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);

    showMessage('File downloaded successfully!', 'success');
}

// Add styles for admission page
const admissionStyles = `
    .admission-info {
        margin: 40px 0;
    }

    .admission-info h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-top: 30px;
    }

    .info-card {
        background: var(--white);
        padding: 30px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        text-align: center;
    }

    .info-card i {
        font-size: 50px;
        color: var(--secondary-color);
        margin-bottom: 15px;
    }

    .info-card h3 {
        font-size: 20px;
        margin-bottom: 20px;
        color: var(--dark-text);
    }

    .info-card ul {
        list-style: none;
        text-align: left;
    }

    .info-card ul li {
        padding: 8px 0;
        padding-left: 25px;
        position: relative;
        color: var(--dark-text);
    }

    .info-card ul li::before {
        content: '\\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
        position: absolute;
        left: 0;
        color: var(--success-color);
    }

    .documents-section {
        margin: 60px 0;
        background: var(--light-bg);
        padding: 40px;
        border-radius: 10px;
    }

    .documents-section h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .documents-list {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 20px;
    }

    .document-item {
        display: flex;
        align-items: center;
        gap: 15px;
        background: var(--white);
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: var(--shadow);
    }

    .document-item i {
        font-size: 24px;
        color: var(--secondary-color);
    }

    .fee-structure {
        margin: 60px 0;
    }

    .fee-structure h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .scholarship-section {
        margin: 60px 0;
        background: var(--light-bg);
        padding: 40px;
        border-radius: 10px;
    }

    .scholarship-section h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .scholarship-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .scholarship-card {
        background: var(--white);
        padding: 30px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        text-align: center;
        transition: all 0.3s;
    }

    .scholarship-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-hover);
    }

    .scholarship-card i {
        font-size: 50px;
        color: var(--warning-color);
        margin-bottom: 15px;
    }

    .scholarship-card h3 {
        font-size: 20px;
        margin-bottom: 10px;
        color: var(--dark-text);
    }

    .scholarship-card p {
        color: var(--light-text);
        line-height: 1.6;
    }

    .admission-form-section {
        margin: 60px 0;
    }

    .admission-form-section h2,
    .admission-form-section h3 {
        color: var(--primary-color);
        margin-bottom: 20px;
    }

    .download-section {
        margin-top: 40px;
        text-align: center;
    }

    .download-section h3 {
        font-size: 24px;
        margin-bottom: 20px;
        color: var(--dark-text);
    }

    .download-links {
        display: flex;
        gap: 20px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .download-btn {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 15px 30px;
        background: var(--secondary-color);
        color: var(--white);
        border-radius: 8px;
        transition: all 0.3s;
    }

    .download-btn:hover {
        background: #2980b9;
        transform: translateY(-2px);
        box-shadow: var(--shadow-hover);
    }

    .download-btn i {
        font-size: 20px;
    }
`;

// Inject styles
const styleSheet = document.createElement('style');
styleSheet.textContent = admissionStyles;
document.head.appendChild(styleSheet);
