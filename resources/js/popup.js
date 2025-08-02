// Глобальные переменные
let modal = null;
let modalContent = null;
let modalClose = null;

// Инициализация модальных окон
function initModals() {
    modal = document.getElementById('modal');
    modalContent = document.getElementById('modal-content');
    modalClose = document.getElementById('modal-close');

    if (!modal || !modalContent || !modalClose) {
        console.warn('Modal elements not found');
        return;
    }

    // Делегирование событий для кнопок открытия
    document.addEventListener('click', function(e) {
        const button = e.target.closest('.open-modal-btn');
        if (!button) return;

        const targetId = button.dataset.modalTarget;
        const price = button.dataset.price;
        const tariff = button.dataset.tariff;
        if (!targetId) return;

        openModal(targetId, tariff);
    });

    // Закрытие по кнопке
    modalClose.addEventListener('click', closeModal);

    // Закрытие по клику вне окна
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            closeModal();
        }
    });

    // Закрытие по Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeModal();
        }
    });
}

// Открытие модального окна
function openModal(targetId, tariff) {
    const template = document.getElementById(targetId);
    const inputTariff = template.content.querySelector('form > input[name="tariff_id"]');
    if(inputTariff){
        inputTariff.value = tariff;
    }
    if (!template) {
        console.warn('Modal template not found:', targetId);
        return;
    }

    // Очищаем контент перед вставкой
    modalContent.innerHTML = '';
    modalContent.appendChild(template.content.cloneNode(true));
    modal.classList.remove('hidden');

    // Инициализируем компоненты внутри модального окна
    initModalComponents();
}

// Закрытие модального окна
function closeModal() {
    modal.classList.add('hidden');
    modalContent.innerHTML = '';
}

// Инициализация компонентов внутри модального окна
function initModalComponents() {
    // Инициализация маски телефона
    initPhoneMask();

    // Инициализация валидации полей
    initFormValidation();
}

// Инициализация маски телефона
function initPhoneMask() {
    const phoneInput = document.querySelector('.phone-input');
    if (!phoneInput) return;

    phoneInput.addEventListener('input', function(e) {
        let value = phoneInput.value.replace(/\D/g, '').substring(1);
        let formatted = '+7';

        if (value.length > 0) formatted += ' (' + value.substring(0, 3);
        if (value.length >= 3) formatted += ') ' + value.substring(3, 6);
        if (value.length >= 6) formatted += '-' + value.substring(6, 8);
        if (value.length >= 8) formatted += '-' + value.substring(8, 10);

        phoneInput.value = formatted;
    });
}

// Инициализация валидации формы
function initFormValidation() {
    const form = document.getElementById('modal-form');
    if (!form) return;

    const requiredFields = form.querySelectorAll('[data-required]');
    if (requiredFields.length === 0) return;

    requiredFields.forEach(function(input) {
        input.addEventListener('input', function(e) {
            validateField(e.target);
        });

        input.addEventListener('blur', function(e) {
            validateField(e.target);
        });
    });
}

// Валидация отдельного поля
function validateField(input) {
    const parent = input.parentNode;
    if (!parent) return;
    const isValid = input.value.trim().length >= 2;

    if (isValid) {
        parent.classList.add('bg-green-600');
        parent.classList.remove('bg-red-600');
    } else {
        parent.classList.add('bg-red-600');
        parent.classList.remove('bg-green-600');
    }
}

// Инициализация при загрузке DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initModals);
} else {
    initModals();
}
