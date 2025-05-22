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

function mostrarAdvertencia(mensaje) {
    Toastify({
        duration: 5000,
        text: mensaje,
        gravity: "bottom",
        position: "center",
        stopOnFocus: true,
        close: true,
        style: {
            background: "var(--bs-warning)",
            borderRadius: "8px",
            color: "var(--bs-dark)"
        }
    }).showToast();
}


/**
 * Hace una peticion fetch y retorna la respuesta o false en caso de error
 * Recordar que para metodos POST el controlador debe tener 
 * $_POST = json_decode(file_get_contents("php://input"), true);
 * no se agrega en el index porque daña los que envían la info desde el formulario
 * @param {string} url la url a la que se va a hacer la peticion
 * @param {Object} obj objeto con las opciones de la peticion
 * @param {string} obj.method el metodo de la peticion, por defecto es "GET"
 * @param {Object} obj.headers los headers de la peticion, por defecto solo tiene el content-type como application/json
 * @param {string} obj.useLoader Elemento sobre el que se va a mostrar el loader
 * @param {function} obj.before funcion que se va a ejecutar antes de la peticion
 * @param {function} obj.after funcion que se va a ejecutar despues de la peticion, recibe el estado de la respuesta y el contenido de la respuesta
 * @param {boolean} obj.focus si se va a deshabilitar el elemento que tiene el foco y habilitarlo despues de la peticion
 * @returns {string|false} el contenido de la respuesta o false en caso de error
 */
async function peticion (url,obj = {}) {
    // inicializando
    let focusElement;
    const beforeHandler = ()=> {
        if(obj.useLoader) mostrarLoader(obj.useLoader,true);
        if(obj.before) obj.before();
        if(obj.blur) document.activeElement.blur();
        if(obj.focus) {
            focusElement = document.activeElement;
            focusElement.blur();
            focusElement.disabled = true;
        }
    }
    const afterHandler = (response = {},data = null) => {
        if(obj.after) obj.after(response,data);
        if(obj.useLoader) mostrarLoader(obj.useLoader,false);
        if(obj.focus && focusElement) {
            focusElement = document.activeElement;
            focusElement.disabled = false;
            focusElement.focus();
        }
    }
    let objdefault = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        },
        cache:"no-store"
    }

    obj = {...objdefault,...obj}
    let data;

    // Petición

    try {
        beforeHandler ();
        
        url = LOCAL_DIR+url;
        let response = await fetch(url, obj);

        if (!response.ok) {
            throw new Error(`Response status: ${response.status}`);
        }
        
        data = await response.text()

        afterHandler(response,data);
    } catch (error) {
        if(obj.signal && obj.signal.aborted){
            if(obj.useLoader) mostrarLoader(obj.useLoader,false);
            return false;
        } 
        afterHandler({},error);
        mostrarError("Error de solicitud");
        console.error(error)
        return false;
    }
    return data;
}




/**
 * Muestra u oculta un loader en un elemento. Si el elemento es pasado como string, se buscar  como selector.
 * @param {HTMLElement|string} element - Elemento en el que se mostrar  el loader
 * @param {boolean} [show=true] - Indica si se debe mostrar o ocultar el loader
 * @returns {boolean} Falso si el elemento no existe, verdadero en caso contrario
 */
function mostrarLoader(element, show = true) {

    if(typeof element === "string") element = document.querySelector(element);
    if(!element){
        console.error("Elemento no encontrado para el loader");
        return false;
    }
    let loaderCount = (element.loaderCount || 0);
    if (show) {
        
        loaderCount++;
        element.loaderCount = loaderCount;
        if(element.querySelector(".loader")) return false;
        let loader = document.createElement("div");
        loader.className = "loader";

        loader.setAttribute("role", "status");
        loader.setAttribute("aria-hidden", "true");
        if(element.tagName == document.body.tagName) loader.classList.add("loader-body");
        element.appendChild(loader);
        if(element.classList.contains("position-relative")){
            element.havedPosition = true;
        }
        else{
            element.classList.add("position-relative");
        }
    } else {
        loaderCount--;
        if(loaderCount <= 0) loaderCount = 0;
        if(loaderCount > 0) {
            element.loaderCount = loaderCount;
            return false
        };

        if(!element.querySelector(".loader")) return false;
        element.querySelector(".loader").remove();
        if(element.havedPosition){
            element.classList.remove("position-relative");
            delete element.havedPosition;
        }
        delete element.loaderCount;
    }
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
        this.isValid = ()=>{ return true }
    } else if(control === false) {
        this.classList.add('is-invalid')
        this.classList.remove('is-valid')
        this.setCustomValidity(mensaje);
        smsContainer ? smsContainer.textContent = mensaje:null;
        this.isValid = ()=>{ return false }
    }
    else {
        this.classList.remove('is-valid')
        this.classList.remove('is-invalid')
        this.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
        this.isValid = ()=>{ return false }
    }
}

/**
 * Parses a JSON string and returns the corresponding JavaScript object.
 * If parsing fails, logs the error and displays an error message.
 * 
 * @param {string} json - The JSON string to be parsed.
 * @returns {Object} The parsed JavaScript object.
 */
function parsearJson(json) {
    try {
        return JSON.parse(json);
    } catch (error) {
        return {
            success: false,
            message: "Error en la solicitud parser",
            Error: error
        }
    }
}

FormData.prototype.json = function () {
    let data = {};
    this.forEach((value, key) => data[key] = value);
    return data;
}
FormData.prototype.text = function () {
    return JSON.stringify(this.json());
}