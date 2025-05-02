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


/**
 * Hace una peticion ajax con fetch
 * @param {string} url la url a la que se va a hacer la peticion
 * @param {Object} obj objeto con las opciones de la peticion
 * @param {string} obj.method el metodo de la peticion, por defecto es "GET"
 * @param {Object} obj.headers los headers de la peticion, por defecto solo tiene el content-type como application/json
 * @param {boolean} obj.useLoader si se va a mostrar un loader durante la peticion
 * @param {function} obj.before funcion que se va a ejecutar antes de la peticion
 * @param {function} obj.after funcion que se va a ejecutar despues de la peticion, recibe el estado de la respuesta y el contenido de la respuesta
 * @param {boolean} obj.focus si se va a deshabilitar el elemento que tiene el foco y habilitarlo despues de la peticion
 * @returns {Promise<string>} el contenido de la respuesta
 */
async function peticion (url,obj = {}) {

    let objdefault = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        cache:"no-store"
    }

    obj = {...objdefault,...obj}

    // si el objeto tiene un parametro before o after se ejecutan antes o despues de la peticion

        if(obj.useLoader) mostrarLoader(obj.useLoader,true);
        if(obj.before) obj.before();
        let focusElement;
        if(obj.focus && document.activeElement) {
            focusElement = document.activeElement;
            focusElement.blur();
            focusElement.disabled = true;
        }
        url = LOCAL_DIR+url;
        let response = await fetch(url, obj);
        
        let data = await response.text()
        
        if(obj.after) obj.after(response.ok,data);
        if(obj.useLoader) mostrarLoader(obj.useLoader,false);
        if(obj.focus) {
            console.log(focusElement)
            focusElement.disabled = false;
            focusElement.focus();
        }

        if (!response.ok) {
            mostrarError("Error de solicitud");
            console.error(data)
            return false;
        }
        
    return data;

}

/**
 * mostrar un loader sobre un elemento 
 * colocandolo al final del elemento 
 * @param {Element} element Elemento sobre el que se va a mostrar el loader
 * @param {boolean} [show] si se va a mostrar el loader o no
 * @returns {void}
 */
function mostrarLoader(element, show = true) {
    if (show) {
        if(element.querySelector(".loader")) return false;
        let loader = document.createElement("div");
        loader.className = "loader";
        loader.setAttribute("role", "status");
        loader.setAttribute("aria-hidden", "true");
        element.appendChild(loader);
        element.classList.add("position-relative");
    } else {
        element.querySelector(".loader").remove();
        element.classList.remove("position-relative");
    }
    console.log(element);
}

/**
 * @function setValidStatus
 * añade a las propiedades del prototipo de los inputs y select una funcion que establesca
 * las clases de bootstrap en los formularios si tiene la clase form-valid
 * @param {string} [message] el mensaje de error
 * @param {boolean} control si el input es valido o no
 * 
*/
HTMLSelectElement.prototype.setValidStatus = HTMLInputElement.prototype.setValidStatus = function (control = null, mensaje = "INVALIDO") {
    let smsContainer = document.getElementById(this.dataset.formText) || this.parentElement.querySelector('.form-text')
    if (control === true) {
        this.classList.add('is-valid')
        this.classList.remove('is-invalid')
        this.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
    } else if(control === false) {
        this.classList.add('is-invalid')
        this.classList.remove('is-valid')
        this.setCustomValidity(mensaje);
        smsContainer ? smsContainer.textContent = mensaje:null;
    }
    else {
        this.classList.remove('is-valid')
        this.classList.remove('is-invalid')
        this.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
    }
}