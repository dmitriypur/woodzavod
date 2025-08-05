import './bootstrap';
import { Navigation, Pagination, Autoplay } from "swiper/modules";
import 'swiper/css';
import 'swiper/css/navigation';
import 'swiper/css/pagination';
import './popup';
import './modal-form';
import LazyLoad from "vanilla-lazyload";

// Глобальные переменные
let lazyMedia = null;
let swiperInstances = new Map();

function initLazyLoad() {
    if (!lazyMedia) {
        lazyMedia = new LazyLoad({
            elements_selector: '[data-src]:not([fetchpriority="high"])',
            class_loaded: '_lazy-loaded',
            use_native: true,
            callback_loaded: (img) => {
                img.classList.add('loaded');
            },
            callback_error: (img) => {
                console.warn('Failed to load image:', img.dataset.src);
            }
        });
    }
    return lazyMedia;
}

// Smooth scrolling for navigation links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Header scroll effect
window.addEventListener('scroll', () => {
    const header = document.getElementById('header');
    if (window.scrollY > 100) {
        header.classList.add('shadow-lg');
    } else {
        header.classList.remove('shadow-lg');
    }
});

// Mobile menu toggle
const burger = document.getElementById('burger');
const nav = document.querySelector('nav');

if (burger && nav) {
    burger.addEventListener('click', (e) => {
    nav.classList.toggle('hidden');
    nav.classList.toggle('block');

    // Animate burger
    const spans = burger.querySelectorAll('span');
    spans[0].classList.toggle('rotate-45');
    spans[0].classList.toggle('translate-y-1.5');
    spans[1].classList.toggle('opacity-0');
    spans[2].classList.toggle('-rotate-45');
    spans[2].classList.toggle('-translate-y-1.5');
    });

    // Close mobile menu when clicking on navigation links
    const navLinks = nav.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Check if menu is currently open (visible)
            if (!nav.classList.contains('hidden')) {
                nav.classList.add('hidden');
                nav.classList.remove('block');

                // Reset burger animation
                const spans = burger.querySelectorAll('span');
                spans[0].classList.remove('rotate-45', 'translate-y-1.5');
                spans[1].classList.remove('opacity-0');
                spans[2].classList.remove('-rotate-45', '-translate-y-1.5');
            }
        });
    });
}



// Modal functionality
function openModal(modalId) {
    const modal = document.getElementById(modalId + 'Modal');
    if (modal) {
        modal.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
}

function closeModal(modalId) {
    const modal = document.getElementById(modalId + 'Modal');
    if (modal) {
        modal.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
}

// Close modal when clicking outside
window.addEventListener('click', (e) => {
    if (e.target.id.includes('Modal')) {
        e.target.classList.add('hidden');
        document.body.style.overflow = 'auto';
    }
});

// Form submissions
const contactForm = document.getElementById('contactForm');
if (contactForm) {
    contactForm.addEventListener('submit', function(e) {
    e.preventDefault();

    // Get form data
    const name = this.querySelector('input[type="text"]').value;
    const phone = this.querySelector('input[type="tel"]').value;

    // Simple validation
    if (!name || !phone) {
        alert('Пожалуйста, заполните все поля');
        return;
    }

    // Simulate form submission
    alert('Спасибо! Мы свяжемся с вами в ближайшее время.');
    this.reset();
    });
}


// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observe elements for animation
document.querySelectorAll('.stat-item, .project-card, .step, .review-card').forEach(el => {
    el.style.opacity = '0';
    el.style.transform = 'translateY(30px)';
    el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
    observer.observe(el);
});

// Portfolio image lightbox effect
document.querySelectorAll('.portfolio-item').forEach(item => {
    item.addEventListener('click', () => {
        const img = item.querySelector('img');
        const lightbox = document.createElement('div');
        lightbox.className = 'fixed inset-0 bg-black/90 flex items-center justify-center z-50';
        lightbox.innerHTML = `
            <div class="relative max-w-5xl max-h-5xl p-4">
                <img src="${img.src}" alt="${img.alt}" class="w-full h-full object-contain">
                <span class="absolute -top-10 right-0 text-white text-3xl cursor-pointer hover:text-primary">&times;</span>
            </div>
        `;

        document.body.appendChild(lightbox);
        document.body.style.overflow = 'hidden';

        lightbox.addEventListener('click', (e) => {
            if (e.target === lightbox || e.target.textContent === '×') {
                document.body.removeChild(lightbox);
                document.body.style.overflow = 'auto';
            }
        });
    });
});

// Parallax effect for hero section
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const heroSection = document.querySelector('.hero-bg');
    if (heroSection) {
        heroSection.style.setProperty('--scroll', `${scrolled * 0.5}px`);
    }
});

// Counter animation for stats
function animateCounter(element, target) {
    let current = 0;
    const increment = target / 100;
    const timer = setInterval(() => {
        current += increment;
        if (current >= target) {
            current = target;
            clearInterval(timer);
        }
        element.textContent = Math.floor(current) + (target > 100 ? '+' : '');
    }, 20);
}

// Trigger counter animation when stats come into view
const statsObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const number = entry.target.querySelector('.font-evolventa');
            if (number) {
                const text = number.textContent;
                const target = parseInt(text.replace(/\D/g, ''));
                if (target && target > 0) {
                    animateCounter(number, target);
                }
            }
            statsObserver.unobserve(entry.target);
        }
    });
}, { threshold: 0.5 });

// Observe stat items for counter animation
document.querySelectorAll('.text-center.p-8').forEach(item => {
    if (item.querySelector('.font-evolventa')) {
        statsObserver.observe(item);
    }
});

// Инициализация Swiper слайдера для галереи домов
function initHouseGallerySwiper() {
    const swiperElement = document.querySelector('.house-gallery-swiper');
    if (swiperElement) {
        import('swiper').then(({ Swiper }) => {
            const swiper = new Swiper('.house-gallery-swiper', {
                modules: [Navigation, Pagination, Autoplay],
                slidesPerView: 1,
                spaceBetween: 20,
                loop: true,
                // autoplay: {
                //     delay: 4000,
                //     disableOnInteraction: false,
                // },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
            swiperInstances.set('house-gallery', swiper);
        });
    }
}

function initReviewsSwiper() {
    const reviewsSwiper = document.querySelector('.reviews-swiper');
    if (reviewsSwiper) {
        import('swiper').then(({ Swiper }) => {
            const swiper = new Swiper('.reviews-swiper', {
                modules: [Navigation, Pagination, Autoplay],
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-reviews-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-reviews-next',
                    prevEl: '.swiper-reviews-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                },
            });
            swiperInstances.set('reviews', swiper);
        });
    }
}

function initProjectsSwiper() {
    const projectsSwiper = document.querySelector('.projects-swiper');
    if (projectsSwiper) {
        import('swiper').then(({ Swiper }) => {
            const swiper = new Swiper('.projects-swiper', {
                modules: [Navigation, Pagination, Autoplay],
                slidesPerView: 1,
                spaceBetween: 30,
                loop: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-projects-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-projects-next',
                    prevEl: '.swiper-projects-prev',
                },
                breakpoints: {
                    768: {
                        slidesPerView: 2,
                        spaceBetween: 30,
                    },
                    1024: {
                        slidesPerView: 3,
                        spaceBetween: 30,
                    },
                },
            });
            swiperInstances.set('reviews', swiper);
        });
    }
}

function initApp() {
    // Инициализируем LazyLoad
    initLazyLoad();
    initHouseGallerySwiper();
    initReviewsSwiper();
    initProjectsSwiper();
}

// Запускаем инициализацию
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    initApp();
}
