// Глобальные переменные
let formSubmissions = new Set();

// Делегирование событий для обработки динамических форм
document.addEventListener('submit', function (e) {
    if (!e.target.classList.contains('modal-form')) return;
    e.preventDefault();

    // Предотвращаем множественные отправки
    if (formSubmissions.has(e.target)) return;

    handleBitrixFormSubmit(e.target);
});

/**
 * Основная функция обработки отправки формы
 */
function handleBitrixFormSubmit(form) {
    if (!validateBitrixForm(form)) return;
    const formData = prepareBitrixFormData(form);
    sendBitrixFormData(form, formData);
}

/**
 * Валидация формы
 */
function validateBitrixForm(form) {
    const requiredFields = form.querySelectorAll('[data-required]');
    let isValid = true;
    requiredFields.forEach(input => {
        if (!input.value.trim()) {
            isValid = false;
            markFieldAsInvalid(input);
        } else {
            markFieldAsValid(input);
        }
    });

    if (!isValid) {
        showBitrixAlert(form, 'Пожалуйста, заполните обязательные поля', 'error');
    }

    return isValid;
}

/**
 * Подготовка данных формы
 */
function prepareBitrixFormData(form) {
    return new FormData(form);
}

/**
 * Отправка данных формы
 */
function sendBitrixFormData(form, formData) {
    const loader = form.querySelector('.form-loader');
    toggleBitrixLoader(loader, true);

    // Добавляем форму в список отправляемых
    formSubmissions.add(form);

    fetch('/submit-form', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': getCsrfToken()
        },
        body: formData
    })
        .then(response => handleBitrixResponse(form, loader, response))
        .catch(error => handleBitrixError(form, loader, error))
        .finally(() => {
            toggleBitrixLoader(loader, false);
            // Удаляем форму из списка отправляемых
            formSubmissions.delete(form);
        });
}

/**
 * Обработка ответа сервера
 */
function handleBitrixResponse(form, loader, response) {
    return response.json()
        .then(data => {
            if (data.success) {
                showBitrixAlert(form, 'Заявка успешно отправлена!', 'success');
                form.reset();
                clearFormValidation(form);

                // Закрытие модального окна после успешной отправки
                const modal = form.closest('#modal');
                if (modal) {
                    setTimeout(() => modal.classList.add('hidden'), 1000);
                }
            } else {
                showBitrixAlert(form, data.message || 'Ошибка отправки. Попробуйте позже.', 'error');
            }
        })
        .catch(error => {
            console.error('JSON parsing error:', error);
            showBitrixAlert(form, 'Ошибка обработки ответа сервера.', 'error');
        });
}

/**
 * Обработка ошибок
 */
function handleBitrixError(form, loader, error) {
    showBitrixAlert(form, 'Ошибка сети. Повторите попытку.', 'error');
    console.error('Form submission error:', error);
}

/**
 * Вспомогательные функции
 */
function toggleBitrixLoader(loader, show) {
    if (!loader) return;
    loader.classList.toggle('hidden', !show);
}

function showBitrixAlert(form, message, type) {
    const alertBox = form.querySelector('.form-alert');
    if (!alertBox) return;

    alertBox.textContent = message;
    alertBox.className = `form-alert text-xs text-center text-white p-2 rounded-xl ${type === 'success' ? 'bg-green-600' : 'bg-red-400'}`;
    alertBox.classList.remove('hidden');

    // Автоматически скрываем alert через 5 секунд
    setTimeout(() => {
        alertBox.classList.add('hidden');
    }, 5000);
}

function markFieldAsInvalid(field) {
    field.classList.add('border-red-500');
    const parent = field.parentNode;
    if (parent) {
        parent.classList.add('before:bg-red-600');
        parent.classList.remove('before:bg-green-600', 'before:bg-linear-(--white2-gr)', 'before:bg-linear-(--violet-gr)');
    }
}

function markFieldAsValid(field) {

    field.classList.remove('border-red-500');
    const parent = field.parentNode;

    if (parent) {
        parent.classList.add('before:bg-green-600');
        parent.classList.remove('before:bg-red-600', 'before:bg-linear-(--white2-gr)', 'before:bg-linear-(--violet-gr)');
    }
}

function clearFormValidation(form) {
    const fields = form.querySelectorAll('[data-required]');
    fields.forEach(field => {
        field.classList.remove('border-red-500');
        const parent = field.parentNode;
        if (parent) {
            parent.classList.remove('before:bg-red-600', 'before:bg-green-600');
            parent.classList.add('before:bg-linear-(--white2-gr)');
        }
    });
}

function getCsrfToken() {
    return document.querySelector('meta[name="csrf-token"]')?.content || '';
}

// Очистка при размонтировании
window.addEventListener('beforeunload', () => {
    formSubmissions.clear();
});
