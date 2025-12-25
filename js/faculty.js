// Faculty Page JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadFaculty();
});

function loadFaculty() {
    const facultyGrid = document.getElementById('facultyGrid');
    if (!facultyGrid) return;

    const faculty = getFacultyData();
    
    const html = faculty.map(teacher => `
        <div class="faculty-card">
            <img src="${teacher.image}" alt="${teacher.name}" 
                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22300%22 height=%22400%22%3E%3Crect fill=%22%23${getRandomColor()}%22 width=%22300%22 height=%22400%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2280%22 fill=%22white%22%3E${teacher.name.charAt(0)}%3C/text%3E%3C/svg%3E'">
            <div class="faculty-info">
                <h3>${teacher.name}</h3>
                <p class="position">${teacher.position}</p>
                <p class="subject">${teacher.subject}</p>
                <p class="qualifications">${teacher.qualifications}</p>
            </div>
        </div>
    `).join('');

    facultyGrid.innerHTML = html;
}

function getFacultyData() {
    return [
        {
            name: 'Dr. Shreekrishna Bista',
            position: 'Head of Mathematics Department',
            subject: 'Mathematics',
            qualifications: 'M.Sc., Ph.D. (Mathematics)',
            image: 'images/faculty/teacher1.jpg'
        },
        {
            name: 'Prof. stia Brown',
            position: 'Senior Teacher',
            subject: 'English Literature',
            qualifications: 'M.A. (English), B.Ed.',
            image: 'images/faculty/teacher2.jpg'
        },
        {
            name: 'Dr. maya Sharma',
            position: 'Head of Science Department',
            subject: 'Physics & Chemistry',
            qualifications: 'M.Sc., Ph.D. (Physics)',
            image: 'images/faculty/teacher3.jpg'
        },
        {
            name: 'Mr. kazi thapa',
            position: 'Computer Science Faculty',
            subject: 'Computer Science',
            qualifications: 'M.Tech (CS), B.Ed.',
            image: 'images/faculty/teacher4.jpg'
        },
        {
            name: 'Ms. k ga',
            position: 'Hotel Management Faculty',
            subject: 'Hotel Management',
            qualifications: 'MBA (Hotel Mgmt), Diploma',
            image: 'images/faculty/teacher5.jpg'
        },
        {
            name: 'Mr. ma',
            position: 'Social Studies Teacher',
            subject: 'History & Geography',
            qualifications: 'M.A. (History), B.Ed.',
            image: 'images/faculty/teacher6.jpg'
        },
        {
            name: 'Ms. h',
            position: 'Nepali Teacher',
            subject: 'Nepali Language',
            qualifications: 'M.A. (Nepali), B.Ed.',
            image: 'images/faculty/teacher7.jpg'
        },
        {
            name: 'Mr. hi',
            position: 'Biology Teacher',
            subject: 'Biology',
            qualifications: 'M.Sc. (Biology), B.Ed.',
            image: 'images/faculty/teacher8.jpg'
        },
        {
            name: 'Ms. hello',
            position: 'Art Teacher',
            subject: 'Fine Arts',
            qualifications: 'BFA, Diploma in Art Ed.',
            image: 'images/faculty/teacher9.jpg'
        },
        {
            name: 'Mr. kazi sir',
            position: 'Physical Education',
            subject: 'Sports & PE',
            qualifications: 'B.P.Ed., M.P.Ed.',
            image: 'images/faculty/teacher10.jpg'
        },
        {
            name: 'Ms. sir',
            position: 'Music Teacher',
            subject: 'Music & Performing Arts',
            qualifications: 'B.Mus., Diploma in Music',
            image: 'images/faculty/teacher11.jpg'
        },
        {
            name: 'Mr. kazi',
            position: 'Mathematics Teacher',
            subject: 'Mathematics',
            qualifications: 'M.Sc. (Math), B.Ed.',
            image: 'images/faculty/teacher12.jpg'
        }
    ];
}

function getRandomColor() {
    const colors = ['3498db', '2ecc71', 'e74c3c', 'f39c12', '9b59b6', '1abc9c', 'e67e22', '34495e'];
    return colors[Math.floor(Math.random() * colors.length)];
}
