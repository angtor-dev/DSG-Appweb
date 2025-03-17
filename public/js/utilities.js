/**
 * Script con la logica de los componentes y
 * funciones de utilidades para facilitar la vida
 */

/* Inicializar componentes */
// Acordeones
document.querySelectorAll('.acordeon-toggle').forEach(a => {
    a.addEventListener('click', e =>
        e.currentTarget.parentElement.classList.toggle('show'))
})

// Tooltips
document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(tooltipEl => {
    new bootstrap.Tooltip(tooltipEl)
})

/* Utilidades */

/**
 * Muestra un mensaje como una alerta de error
 * @param {string} mensaje
 */
function mostrarError(mensaje) {
    Toastify({
        duration: -1,
        text: mensaje,
        gravity: "bottom",
        position: "center",
        stopOnFocus: true,
        close: true,
        style: {
            background: "var(--bs-danger)",
            borderRadius: "8px"
        }
    }).showToast();
}

/**
 * Muestra un mensaje como una alerta de exito
 * @param {string} mensaje
 */
function mostrarExito(mensaje) {
    Toastify({
        duration: 5000,
        text: mensaje,
        gravity: "bottom",
        position: "center",
        stopOnFocus: true,
        style: {
            background: "var(--bs-success)",
            borderRadius: "8px"
        }
    }).showToast();
}