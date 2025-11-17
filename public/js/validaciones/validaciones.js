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
/**
 * Valida un campo de tipo date.
 * @param {HTMLElement} inputEl - Elemento input[type="date"].
 * @param {object} options - Opciones de validación.
 * @param {boolean} [options.isRequired=false] - Si es obligatorio.
 * @param {boolean} [options.allowPastDate=true] - Si permite fechas pasadas.
 * @param {boolean} [options.allowFutureDate=true] - Si permite fechas futuras.
 * @param {HTMLElement|null} [options.customFeedbackEl=null] - Elemento personalizado para mensajes.
 * @returns {boolean} True si es válido.
 */
const validarCampoFecha = (inputEl, {
    isRequired = false,
    allowPastDate = true,
    allowFutureDate = true,
    customFeedbackEl = null
} = {}) => {
    if (!inputEl) return false;

    const feedbackEl = customFeedbackEl || inputEl.parentElement.querySelector('.form-text');
    const valor = (inputEl.value || '').trim();

    // Si no hay valor
    if (valor.length === 0) {
        if (isRequired) {
            setInvalid(inputEl, feedbackEl, "Este campo es obligatorio");
            return false;
        } else {
            setValid(inputEl, feedbackEl);
            return true;
        }
    }

    // Parsear fecha (esperando formato YYYY-MM-DD)
    const partes = valor.split('-').map(p => parseInt(p, 10));
    if (partes.length !== 3 || partes.some(p => Number.isNaN(p))) {
        setInvalid(inputEl, feedbackEl, "Fecha inválida");
        return false;
    }
    const [y, m, d] = partes;
    const fecha = new Date(y, m - 1, d);
    // Validar que la fecha creada coincida con los componentes (evita cosas como 2021-02-30)
    if (fecha.getFullYear() !== y || fecha.getMonth() !== (m - 1) || fecha.getDate() !== d) {
        setInvalid(inputEl, feedbackEl, "Fecha inválida");
        return false;
    }

    // Normalizar a medianoche para comparación solo por fecha
    const fechaComparar = new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate()).getTime();
    const hoy = new Date();
    const hoyComparar = new Date(hoy.getFullYear(), hoy.getMonth(), hoy.getDate()).getTime();

    if (!allowPastDate && fechaComparar < hoyComparar) {
        setInvalid(inputEl, feedbackEl, "No se permiten fechas pasadas");
        return false;
    }

    if (!allowFutureDate && fechaComparar > hoyComparar) {
        setInvalid(inputEl, feedbackEl, "No se permiten fechas futuras");
        return false;
    }

    setValid(inputEl, feedbackEl);
    return true;
};

/**
 * Valida un campo de tipo number.
 * @param {HTMLElement} inputEl - Elemento input[type="number"].
 * @param {object} options - Opciones de validación.
 * @param {boolean} [options.isRequired=false] - Si es obligatorio.
 * @param {number} [options.minValue=null] - Valor mínimo permitido.
 * @param {number} [options.maxValue=null] - Valor máximo permitido.
 * @param {boolean} [options.allowDecimals=true] - Si permite decimales.
 * @param {boolean} [options.allowZero=false] - Si permite el valor cero.
 * @param {HTMLElement|null} [options.customFeedbackEl=null] - Elemento personalizado para mensajes.
 * @returns {boolean} True si es válido.
 */
const validarCampoNumerico = (inputEl, {
    isRequired = false,
    minValue = null,
    maxValue = null,
    allowDecimals = true,
    allowZero = false,
    customFeedbackEl = null
} = {}) => {
    if (!inputEl) return false;

    const feedbackEl = customFeedbackEl || inputEl.parentElement.querySelector('.form-text');
    const valor = inputEl.value.trim();
    const numero = allowDecimals ? parseFloat(valor) : parseInt(valor, 10);

    // Validar si es obligatorio
    if (isRequired && (valor.length === 0 || isNaN(numero))) {
        setInvalid(inputEl, feedbackEl, "Este campo es obligatorio");
        return false;
    }

    // Si no es obligatorio y está vacío, es válido
    if (!isRequired && valor.length === 0) {
        setValid(inputEl, feedbackEl);
        return true;
    }

    // Validar si no se permiten decimales
    if (!allowDecimals && valor.includes('.')) {
        setInvalid(inputEl, feedbackEl, "No se permiten decimales");
        return false;
    }

    // Validar que no sea cero si no está permitido
    if (!allowZero && !isNaN(numero) && numero === 0) {
        setInvalid(inputEl, feedbackEl, "El valor no puede ser cero");
        return false;
    }

    // Validar Valor Mínimo
    if (minValue !== null && !isNaN(numero) && numero < minValue) {
        setInvalid(inputEl, feedbackEl, `El valor no puede ser menor que ${minValue}`);
        return false;
    }

    // Validar Valor Máximo
    if (maxValue !== null && !isNaN(numero) && numero > maxValue) {
        setInvalid(inputEl, feedbackEl, `El valor no puede ser mayor que ${maxValue}`);
        return false;
    }

    setValid(inputEl, feedbackEl);
    return true;
};