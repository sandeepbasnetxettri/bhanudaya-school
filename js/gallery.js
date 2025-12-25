// Gallery JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadGallery();
    setupFilters();
    setupModal();
});

function loadGallery() {
    const galleryGrid = document.getElementById('galleryGrid');
    if (!galleryGrid) return;

    const galleryData = getStorageData('gallery') || getDefaultGallery();
    
    const html = galleryData.map(item => `
        <div class="gallery-item" data-category="${item.category}">
            <img src="${item.image}" alt="${item.title}" 
                onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22400%22 height=%22300%22%3E%3Crect fill=%22%23${getRandomColor()}%22 width=%22400%22 height=%22300%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-size=%2220%22 fill=%22white%22%3E${item.category}%3C/text%3E%3C/svg%3E'"
                onclick="openModal(this, '${item.title}')">
            <div class="gallery-overlay">
                <h4>${item.title}</h4>
                <p>${item.date}</p>
            </div>
        </div>
    `).join('');

    galleryGrid.innerHTML = html;

    // Store gallery data if not exists
    if (!getStorageData('gallery')) {
        setStorageData('gallery', galleryData);
    }
}

function getDefaultGallery() {
    return [
        { id: 1, title: 'Annual Sports Day 2024', category: 'sports', date: '2024-12-10', image: '../Gallery/1.jpg' },
        { id: 2, title: 'Science Exhibition', category: 'events', date: '2024-11-15', image: '../Gallery/2.jpg' },
        { id: 3, title: 'Computer Lab', category: 'labs', date: '2024-10-20', image: '../Gallery/3.jpg' },
        { id: 4, title: 'Cultural Program', category: 'events', date: '2024-09-25', image: '../Gallery/4.jpg' },
        { id: 5, title: 'Basketball Tournament', category: 'sports', date: '2024-12-05', image: '../Gallery/5.jpg' },
        { id: 6, title: 'Hotel Management Kitchen', category: 'labs', date: '2024-10-15', image: '../Gallery/6.jpg' },
        { id: 7, title: 'Art & Craft Class', category: 'activities', date: '2024-11-20', image: '../Gallery/7.jpg' },
        { id: 8, title: 'Inter-School Competition', category: 'achievements', date: '2024-12-01', image: '../Gallery/8.jpg' },
        { id: 9, title: 'Football Match', category: 'sports', date: '2024-11-30', image: '../Gallery/9.jpg' },
        { id: 10, title: 'Annual Function 2024', category: 'events', date: '2024-12-20', image: '../Gallery/10.jpg' },
        { id: 11, title: 'Library Facilities', category: 'labs', date: '2024-10-10', image: '../Gallery/11.jpg' },
        { id: 12, title: 'Dance Performance', category: 'activities', date: '2024-11-25', image: '../Gallery/12.jpg' },
        { id: 13, title: 'SEE Excellence Award', category: 'achievements', date: '2024-12-15', image: '../Gallery/13.jpg' },
        { id: 14, title: 'Robotics Workshop', category: 'activities', date: '2024-10-25', image: '../Gallery/14.jpg' },
        { id: 15, title: 'Field Trip', category: 'events', date: '2024-09-15', image: '../Gallery/15.jpg' },
        { id: 16, title: 'Chemistry Lab', category: 'labs', date: '2024-10-18', image: '../Gallery/16.jpg' },
        { id: 17, title: 'Music Class', category: 'activities', date: '2024-11-10', image: '../Gallery/17.jpg' },
        { id: 18, title: 'National Championship', category: 'achievements', date: '2024-12-12', image: '../Gallery/18.jpg' },
        { id: 19, title: 'School Assembly', category: 'events', date: '2024-09-20', image: '../Gallery/19.jpg' },
        { id: 20, title: 'Mathematics Olympiad', category: 'achievements', date: '2024-10-05', image: '../Gallery/20.jpg' },
        { id: 21, title: 'Physics Laboratory', category: 'labs', date: '2024-10-22', image: '../Gallery/21.jpg' },
        { id: 22, title: 'Drawing Competition', category: 'activities', date: '2024-11-18', image: '../Gallery/22.jpg' },
        { id: 23, title: 'Debate Competition', category: 'activities', date: '2024-11-22', image: '../Gallery/23.jpg' },
        { id: 24, title: 'Quiz Contest', category: 'activities', date: '2024-11-28', image: '../Gallery/24.jpg' },
        { id: 25, title: 'Sports Meet Preparation', category: 'sports', date: '2024-12-01', image: '../Gallery/25.jpg' },
        { id: 26, title: 'Environmental Awareness', category: 'events', date: '2024-09-30', image: '../Gallery/26.jpg' },
        { id: 27, title: 'Biology Practical', category: 'labs', date: '2024-10-28', image: '../Gallery/27.jpg' },
        { id: 28, title: 'Literary Club Meeting', category: 'activities', date: '2024-11-12', image: '../Gallery/28.jpg' },
        { id: 29, title: 'Cricket Coaching', category: 'sports', date: '2024-11-27', image: '../Gallery/29.jpg' },
        { id: 30, title: 'Science Fair', category: 'events', date: '2024-12-08', image: '../Gallery/30.jpg' },
        { id: 31, title: 'Classroom Session', category: 'events', date: '2024-09-10', image: '../Gallery/31.jpg' },
        { id: 32, title: 'Student Council Meeting', category: 'events', date: '2024-09-18', image: '../Gallery/32.jpg' },
        { id: 35, title: 'Mathematics Lab', category: 'labs', date: '2024-10-24', image: '../Gallery/35.jpg' },
        { id: 36, title: 'Art Exhibition', category: 'activities', date: '2024-11-05', image: '../Gallery/36.jpg' },
        { id: 37, title: 'Annual Cultural Fest', category: 'events', date: '2024-12-18', image: '../Gallery/37.jpg' },
        { id: 38, title: 'Swimming Training', category: 'sports', date: '2024-11-14', image: '../Gallery/38.jpg' },
        { id: 39, title: 'Morning Prayer', category: 'events', date: '2024-09-05', image: '../Gallery/39.jpg' },
        { id: 40, title: 'Library Reading Hour', category: 'labs', date: '2024-10-08', image: '../Gallery/40.jpg' },
        { id: 41, title: 'Yoga Session', category: 'activities', date: '2024-10-12', image: '../Gallery/41.jpg' },
        { id: 42, title: 'Computer Class', category: 'labs', date: '2024-10-26', image: '../Gallery/42.jpg' },
        { id: 43, title: 'Language Club', category: 'activities', date: '2024-11-02', image: '../Gallery/43.jpg' },
        { id: 44, title: 'Drama Rehearsal', category: 'activities', date: '2024-11-08', image: '../Gallery/44.jpg' },
        { id: 45, title: 'Chess Competition', category: 'activities', date: '2024-11-16', image: '../Gallery/45.jpg' },
        { id: 46, title: 'School Picnic', category: 'events', date: '2024-12-03', image: '../Gallery/46.jpg' },
        { id: 47, title: 'Award Distribution', category: 'achievements', date: '2024-12-22', image: '../Gallery/47.jpg' }
    ];
}

function setupFilters() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            // Remove active class from all buttons
            filterBtns.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            btn.classList.add('active');
            
            // Get filter category
            const filter = btn.dataset.filter;
            
            // Filter gallery items
            const galleryItems = document.querySelectorAll('.gallery-item');
            galleryItems.forEach(item => {
                if (filter === 'all' || item.dataset.category === filter) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

function setupModal() {
    const modal = document.getElementById('imageModal');
    const closeBtn = document.querySelector('.modal-close');
    
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });
    }
    
    // Close modal when clicking outside the image
    modal.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') {
            modal.style.display = 'none';
        }
    });
}

function openModal(img, caption) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    const modalCaption = document.getElementById('modalCaption');
    
    modal.style.display = 'block';
    modalImg.src = img.src;
    modalCaption.textContent = caption;
}

function getRandomColor() {
    const colors = ['3498db', '2ecc71', 'e74c3c', 'f39c12', '9b59b6', '1abc9c'];
    return colors[Math.floor(Math.random() * colors.length)];
}

// Add modal styles dynamically
const modalStyles = `
    .modal {
        display: none;
        position: fixed;
        z-index: 10000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgba(0,0,0,0.9);
    }

    .modal-content {
        margin: auto;
        display: block;
        max-width: 90%;
        max-height: 80%;
        animation: zoom 0.3s;
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
    }

    @keyframes zoom {
        from {transform: translate(-50%, -50%) scale(0.5)}
        to {transform: translate(-50%, -50%) scale(1)}
    }

    .modal-close {
        position: absolute;
        top: 30px;
        right: 50px;
        color: #fff;
        font-size: 40px;
        font-weight: bold;
        cursor: pointer;
        z-index: 10001;
    }

    .modal-close:hover {
        color: #ccc;
    }

    .modal-caption {
        margin: auto;
        display: block;
        width: 80%;
        max-width: 700px;
        text-align: center;
        color: #ccc;
        padding: 20px;
        position: fixed;
        bottom: 50px;
        left: 50%;
        transform: translateX(-50%);
        font-size: 18px;
        background: rgba(0,0,0,0.7);
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .modal-content {
            max-width: 95%;
            max-height: 70%;
        }

        .modal-close {
            top: 10px;
            right: 20px;
            font-size: 30px;
        }

        .modal-caption {
            font-size: 14px;
            bottom: 20px;
            width: 90%;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = modalStyles;
document.head.appendChild(styleSheet);
