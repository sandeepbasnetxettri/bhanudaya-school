// Notice Board JavaScript
document.addEventListener('DOMContentLoaded', () => {
    loadNoticeBoard();
    setupNoticeFilter();
});

function loadNoticeBoard() {
    const noticeBoard = document.getElementById('noticeBoard');
    if (!noticeBoard) return;

    const news = getStorageData('news') || getDefaultNews();
    
    const html = news.map(notice => {
        const badgeColor = getBadgeColor(notice.type);
        return `
            <div class="notice-item" data-type="${notice.type}">
                <div class="notice-header">
                    <div class="notice-title">${notice.title}</div>
                    <div class="notice-meta">
                        <span class="notice-badge" style="background: ${badgeColor};">${notice.type}</span>
                        <span class="notice-date">${formatDate(notice.date)}</span>
                    </div>
                </div>
                <div class="notice-content">${notice.description}</div>
            </div>
        `;
    }).join('');

    noticeBoard.innerHTML = html || '<p class="no-data">No notices available at the moment.</p>';
}

function setupNoticeFilter() {
    const filterBtns = document.querySelectorAll('.notice-filter .filter-btn');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', () => {
            filterBtns.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
            
            const type = btn.dataset.type;
            const noticeItems = document.querySelectorAll('.notice-item');
            
            noticeItems.forEach(item => {
                if (type === 'all' || item.dataset.type === type) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
}

function getBadgeColor(type) {
    const colors = {
        'Notice': '#3498db',
        'Event': '#9b59b6',
        'Achievement': '#27ae60',
        'Holiday': '#e74c3c'
    };
    return colors[type] || '#95a5a6';
}

// Add notice page specific styles
const noticeStyles = `
    .notice-filter {
        background: var(--white);
        padding: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow);
        margin-bottom: 30px;
    }

    .notice-filter h3 {
        font-size: 20px;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .filter-buttons {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .notices-container .notice-item {
        background: var(--white);
        padding: 25px;
        margin-bottom: 20px;
        border-left: 4px solid var(--secondary-color);
        border-radius: 5px;
        box-shadow: var(--shadow);
        transition: all 0.3s;
    }

    .notices-container .notice-item:hover {
        transform: translateX(5px);
        box-shadow: var(--shadow-hover);
    }

    .notices-container .notice-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 15px;
        gap: 20px;
    }

    .notices-container .notice-title {
        font-size: 22px;
        color: var(--dark-text);
        font-weight: 600;
        flex: 1;
    }

    .notice-meta {
        display: flex;
        gap: 15px;
        align-items: center;
        flex-wrap: wrap;
    }

    .notices-container .notice-badge {
        color: var(--white);
        padding: 5px 15px;
        border-radius: 15px;
        font-size: 12px;
        font-weight: 600;
        white-space: nowrap;
    }

    .notices-container .notice-date {
        color: var(--light-text);
        font-size: 14px;
        white-space: nowrap;
    }

    .notices-container .notice-content {
        color: var(--dark-text);
        line-height: 1.8;
        font-size: 15px;
    }

    .no-data {
        text-align: center;
        padding: 60px 20px;
        color: var(--light-text);
        font-size: 18px;
    }

    @media (max-width: 768px) {
        .notices-container .notice-header {
            flex-direction: column;
        }

        .notice-meta {
            width: 100%;
        }

        .filter-buttons {
            flex-direction: column;
        }

        .filter-buttons .filter-btn {
            width: 100%;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = noticeStyles;
document.head.appendChild(styleSheet);
