// --- Expresiones Regulares ---
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., _-]*$/;
const regLetra = /[A-Za-zá-úÁ-ÚñÑ]/;

// --- Helpers de UI ---
const setInvalid = (inputEl, feedbackEl, message) => {
    feedbackEl.textContent = message;
    feedbackEl.classList.add('d-inline');
    inputEl.classList.add('is-invalid');
    inputEl.classList.remove('is-valid');
};

const setValid = (inputEl, feedbackEl) => {
    feedbackEl.textContent = "";
    feedbackEl.classList.remove('d-inline');
    inputEl.classList.remove('is-invalid');
    inputEl.classList.add('is-valid');
};

// --- Funciones de Validación Genéricas ---

/**
 * Valida un campo de texto genérico con más opciones.
 * @param {HTMLElement} inputEl - Elemento input.
 * @param {object} options - Opciones de validación.
 * @param {boolean} [options.isRequired=false] - Si es obligatorio.
 * @param {number} [options.minLength=0] - Longitud mínima.
 * @param {number} [options.maxLength=30] - Longitud máxima.
 * @param {bool} [options.allowOnlyNumbers=false] - Si permite cadena de solo números.
 * @param {HTMLElement|null} [options.customFeedbackEl=null] - Elemento personalizado para mensajes.
 * @returns {boolean} True si es válido.
 */
const validarCampoTexto = (inputEl, {
    isRequired = false,
    minLength = 0,
    maxLength = 30,
    allowOnlyNumbers = false,
    customFeedbackEl = null
} = {}) => {
    if (!inputEl) return false;
    
    const feedbackEl = customFeedbackEl || inputEl.parentElement.querySelector('.form-text');
    const valor = inputEl.value.trim();
    const longitud = valor.length;

    // Validar si es obligatorio
    if (isRequired && longitud === 0) {
        setInvalid(inputEl, feedbackEl, "Este campo es obligatorio");
        return false;
    }

    // Validar Longitud Mínima (solo si hay algo escrito)
    if (longitud > 0 && longitud < minLength) {
        setInvalid(inputEl, feedbackEl, `Debe tener al menos ${minLength} caracteres`);
        return false;
    }

    // Validar Longitud Máxima
    if (longitud > maxLength) {
        setInvalid(inputEl, feedbackEl, `No puede exceder los ${maxLength} caracteres`);
        return false;
    }

    // Validar Alfanumérico (solo si hay algo escrito)
    if (longitud > 0 && !regAlfanumerico.test(valor)) {
        setInvalid(inputEl, feedbackEl, "Solo puede contener letras y números");
        return false;
    }

    // Validar que contenga al menos una letra (solo si hay algo escrito)
    if (!allowOnlyNumbers && longitud > 0 && !regLetra.test(valor)) {
        setInvalid(inputEl, feedbackEl, "Debe contener al menos una letra");
        return false;
    }

    setValid(inputEl, feedbackEl);
    return true;
};

/**
 * Valida un campo de selección (select). Recibe el elemento en lugar del id.
 * @param {HTMLElement} inputEl - Elemento select.
 * @param {string} errorMsg - Mensaje de error a mostrar.
 * @returns {boolean} True si es válido.
 */
const validarCampoSelect = (inputEl, errorMsg) => {
    if (!inputEl) return false;

    const feedbackEl = inputEl.parentElement.parentElement.querySelector('.form-text');
    const valor = parseInt(inputEl.value, 10);

    if (isNaN(valor) || valor <= 0) {
        setInvalid(inputEl, feedbackEl, errorMsg);
        return false;
    }

    setValid(inputEl, feedbackEl);
    return true;
};