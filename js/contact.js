// Contact Page JavaScript
document.addEventListener('DOMContentLoaded', () => {
    const contactForm = document.getElementById('contactForm');
    
    // Contact form handler
    if (contactForm) {
        contactForm.addEventListener('submit', (e) => {
            e.preventDefault();
            
            if (!validateForm('contactForm')) {
                return;
            }

            const formData = new FormData(contactForm);
            const data = {
                id: generateId(),
                name: formData.get('name'),
                email: formData.get('email'),
                phone: formData.get('phone'),
                subject: formData.get('subject'),
                message: formData.get('message'),
                date: new Date().toISOString(),
                status: 'unread'
            };

            // Store contact message
            let messages = getStorageData('contactMessages') || [];
            messages.push(data);
            setStorageData('contactMessages', messages);

            showMessage('Thank you for contacting us! We will get back to you soon.', 'success');
            contactForm.reset();
        });
    }

    // FAQ accordion
    setupFAQ();
});

function setupFAQ() {
    const faqQuestions = document.querySelectorAll('.faq-question');
    
    faqQuestions.forEach(question => {
        question.addEventListener('click', () => {
            const faqItem = question.parentElement;
            const isActive = faqItem.classList.contains('active');
            
            // Close all other FAQs
            document.querySelectorAll('.faq-item').forEach(item => {
                item.classList.remove('active');
            });
            
            // Toggle current FAQ
            if (!isActive) {
                faqItem.classList.add('active');
            }
        });
    });
}

// Add contact page specific styles
const contactStyles = `
    .contact-info-section {
        margin: 40px 0;
    }

    .contact-info-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 30px;
    }

    .contact-info-card {
        background: var(--white);
        padding: 40px 30px;
        text-align: center;
        border-radius: 10px;
        box-shadow: var(--shadow);
        transition: all 0.3s;
    }

    .contact-info-card:hover {
        transform: translateY(-10px);
        box-shadow: var(--shadow-hover);
    }

    .contact-info-card i {
        font-size: 48px;
        color: var(--secondary-color);
        margin-bottom: 15px;
    }

    .contact-info-card h3 {
        font-size: 22px;
        margin-bottom: 15px;
        color: var(--dark-text);
    }

    .contact-info-card p {
        color: var(--light-text);
        line-height: 1.8;
    }

    .contact-form-map {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        margin: 60px 0;
    }

    .contact-form-section h2,
    .map-section h2 {
        font-size: 28px;
        margin-bottom: 25px;
        color: var(--primary-color);
    }

    .map-container {
        border-radius: 10px;
        overflow: hidden;
        box-shadow: var(--shadow);
    }

    .faq-section {
        margin: 60px 0;
    }

    .faq-section h2 {
        font-size: 32px;
        margin-bottom: 30px;
        color: var(--primary-color);
        text-align: center;
    }

    .faq-list {
        max-width: 900px;
        margin: 0 auto;
    }

    .faq-item {
        background: var(--white);
        margin-bottom: 15px;
        border-radius: 8px;
        box-shadow: var(--shadow);
        overflow: hidden;
    }

    .faq-question {
        padding: 20px 25px;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        transition: background 0.3s;
    }

    .faq-question:hover {
        background: var(--light-bg);
    }

    .faq-question h4 {
        font-size: 18px;
        color: var(--dark-text);
        margin: 0;
    }

    .faq-question i {
        color: var(--secondary-color);
        transition: transform 0.3s;
    }

    .faq-item.active .faq-question i {
        transform: rotate(180deg);
    }

    .faq-answer {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s;
    }

    .faq-item.active .faq-answer {
        max-height: 300px;
    }

    .faq-answer p {
        padding: 0 25px 20px;
        color: var(--light-text);
        line-height: 1.8;
        margin: 0;
    }

    @media (max-width: 1024px) {
        .contact-form-map {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .contact-info-grid {
            grid-template-columns: 1fr;
        }
    }
`;

const styleSheet = document.createElement('style');
styleSheet.textContent = contactStyles;
document.head.appendChild(styleSheet);
