// Banner Slider Functionality
class BannerSlider {
    constructor() {
        this.slides = document.querySelectorAll('.slide');
        this.prevBtn = document.getElementById('prevBtn');
        this.nextBtn = document.getElementById('nextBtn');
        this.dotsContainer = document.getElementById('sliderDots');
        this.currentSlide = 0;
        this.slideInterval = null;

        this.init();
    }

    init() {
        if (this.slides.length === 0) return;

        this.createDots();
        this.startAutoSlide();
        this.addEventListeners();
    }

    createDots() {
        this.slides.forEach((_, index) => {
            const dot = document.createElement('div');
            dot.classList.add('dot');
            if (index === 0) dot.classList.add('active');
            dot.addEventListener('click', () => this.goToSlide(index));
            this.dotsContainer.appendChild(dot);
        });
    }

    goToSlide(n) {
        this.slides[this.currentSlide].classList.remove('active');
        this.dotsContainer.children[this.currentSlide].classList.remove('active');

        this.currentSlide = (n + this.slides.length) % this.slides.length;

        this.slides[this.currentSlide].classList.add('active');
        this.dotsContainer.children[this.currentSlide].classList.add('active');
    }

    nextSlide() {
        this.goToSlide(this.currentSlide + 1);
    }

    prevSlide() {
        this.goToSlide(this.currentSlide - 1);
    }

    startAutoSlide() {
        this.slideInterval = setInterval(() => {
            this.nextSlide();
        }, 5000);
    }

    stopAutoSlide() {
        if (this.slideInterval) {
            clearInterval(this.slideInterval);
        }
    }

    addEventListeners() {
        if (this.nextBtn) {
            this.nextBtn.addEventListener('click', () => {
                this.nextSlide();
                this.stopAutoSlide();
                this.startAutoSlide();
            });
        }

        if (this.prevBtn) {
            this.prevBtn.addEventListener('click', () => {
                this.prevSlide();
                this.stopAutoSlide();
                this.startAutoSlide();
            });
        }

        // Pause on hover
        const sliderContainer = document.querySelector('.slider-container');
        if (sliderContainer) {
            sliderContainer.addEventListener('mouseenter', () => {
                this.stopAutoSlide();
            });

            sliderContainer.addEventListener('mouseleave', () => {
                this.startAutoSlide();
            });
        }

        // Keyboard navigation
        document.addEventListener('keydown', (e) => {
            if (e.key === 'ArrowLeft') {
                this.prevSlide();
                this.stopAutoSlide();
                this.startAutoSlide();
            } else if (e.key === 'ArrowRight') {
                this.nextSlide();
                this.stopAutoSlide();
                this.startAutoSlide();
            }
        });
    }
}

// Initialize slider when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new BannerSlider();
});
