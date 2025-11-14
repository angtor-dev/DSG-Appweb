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

    const scrollPos = sessionStorage.getItem("sideMenuScroll");
    if (scrollPos) {
        menu.scrollTop = parseInt(scrollPos, 10);
    }

    window.addEventListener("beforeunload", e => {
        sessionStorage.setItem("sideMenuScroll", menu?.scrollTop ?? 0);
    })
})()

// Funcionalidad de las notificaciones
async function marcarNotificacionLeida(id, buttonEl) {
    const res = await fetch(LOCAL_DIR+'/Notificaciones/MarcarLeida/?id=' + id)
    if (res.ok) {
        const data = (GenericResponse.fromJson(await res.json()))
        if (!data.success) {
            mostrarError(data.message)
            return;
        }
        // eliminar el punto rojo de notificación
        const notifDot = buttonEl.querySelector('.notif-dot');
        notifDot?.remove();

        // actualizar el contador de notificaciones nuevas
        actualizarContadorNotificaciones();
    }
    else {
        mostrarError('Error al marcar la notificación como leída.')
    }
}

async function marcarTodasNotificacionesLeidas() {
    const res = await fetch(LOCAL_DIR+'/Notificaciones/MarcarTodasLeidas/')
    if (res.ok) {
        const data = (GenericResponse.fromJson(await res.json()))
        if (!data.success) {
            mostrarError(data.message)
            return;
        }
        // eliminar todos los puntos rojos de notificación
        const notifDots = document.querySelectorAll('.notif-dot');
        notifDots.forEach(dot => dot.remove());
        // actualizar el contador de notificaciones nuevas
        actualizarContadorNotificaciones(true);
    }
    else {
        mostrarError('Error al marcar todas las notificaciones como leídas.')
    }
}

function actualizarContadorNotificaciones(vaciar = false) {
    const notifTotal = document.getElementById('notif-nuevas-total');
    const notifTotalCount = document.getElementById('notif-nuevas-cont');
    if (notifTotal) {
        let total = vaciar ? 1 : parseInt(notifTotal.textContent);
        if (isNaN(total)) {
            return;
        }
        total -= 1;
        notifTotalCount.textContent = total > 99 ? "99+" : total.toString();
        if (total <= 0) {
            notifTotalCount.remove();
        }
        notifTotal.textContent = total.toString();
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