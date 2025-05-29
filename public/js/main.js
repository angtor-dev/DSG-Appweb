/** Logica y scripts de la plantilla principal */

// Funcionalidad del menu lateral
(() => {
    const boton = document.getElementById('sidebar-toggle')
    const menu = document.getElementById('menu-lateral')
    const header = document.querySelector('.main-header')
    const contenido = document.querySelector('.main-content')

    boton.addEventListener('click', e => {
        menu.classList.toggle('sidebar-hide')
        header.classList.toggle('sidebar-hide')
        contenido.classList.toggle('sidebar-hide')
    })
})()

// Funcionalidad de las notificaciones
async function marcarNotificacionLeida(id) {
    console.log(window.location.host);
    
    const res = await fetch('Notificaciones/MarcarLeida/?id=' + id)
    if (res.ok) {
        const data = (GenericResponse.fromJson(await res.json()))
        if (!data.success) {
            mostrarError(data.message)
        }
        console.log(data);
        
    }
    else {
        mostrarError('Error al marcar la notificación como leída.')
    }
}

class GenericResponse {
    /**
     * @param {boolean} success
     * @param {string} message
     */
    constructor(success, message) {
        this.success = success;
        this.message = message;
    }

    /**
     * Crea una instancia de GenericResponse desde un objeto JSON.
     * @param {object} jsonData El objeto JSON con los datos.
     * @returns {GenericResponse}
     */
    static fromJson(jsonData) {
        return new GenericResponse(jsonData.success, jsonData.message);
    }
}