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

// Select2
document.querySelectorAll('select.select2').forEach(s => $(s).select2({
    minimumResultsForSearch: 5,
    theme: 'bootstrap-5'
}))

/* Utilidades */

/**
 * Muestra un mensaje como una alerta de error
 * @param {string} mensaje
 */
function mostrarError(mensaje) {
    Toastify({
        duration: 10000,
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
        close: true,
        style: {
            background: "var(--bs-success)",
            borderRadius: "8px"
        }
    }).showToast();
}

function mostrarAdvertencia(mensaje) {
    Toastify({
        duration: 6000,
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
        // LOCAL_DIR = '/DSG-Appweb';// constante global en el head
        
        url = LOCAL_DIR+url;
        let response = await fetch(url, obj);

        if (!response.ok) {
            throw new Error(response.status);
        }
        
        data = await response.text()

        afterHandler(response,data);
    } catch (error) {
        if(obj.signal && obj.signal.aborted){
            if(obj.useLoader) mostrarLoader(obj.useLoader,false);
            return false;
        } 
        afterHandler({},error);
        if(error.message == 403){
            mostrarError("No tienes permiso para realizar esta accion");
        }
        else if(error.message == 404){
            mostrarError("Recurso no encontrado");
        }
        else{
            mostrarError("Error de solicitud");
        }
        console.error(error)
        return false;
    }
    return data;
}



/**
 * Hace una peticion fetch y retorna una promesa con la respuesta o un error
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
 * @returns {Promise} una promesa con la respuesta o un error
 */
function peticionPromisse (url,obj = {}) {
    return new Promise((resolve, reject) => {
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

        try {
            beforeHandler ();
            // LOCAL_DIR = '/DSG-Appweb';// constante global en el head
            
            url = LOCAL_DIR+url;
            fetch(url, obj)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(response.status);
                    }
                    return response.text().then(data => ({ response, data }));
                })
                .then(({ response, data }) => {
                    afterHandler(response, data);
                    resolve(data);
                })
                .catch(error => {
                    if(obj.signal && obj.signal.aborted){
                        if(obj.useLoader) mostrarLoader(obj.useLoader,false);
                        reject(false);
                    } else {
                        afterHandler({},error);
                        if(error.message === "403"){
                            mostrarError("No tienes permiso para realizar esta acción");
                        }
                        else if(error.message === "404"){
                            mostrarError("Recurso no encontrado");
                        }
                        else{
                            mostrarError("Error de solicitud");
                        }
                        console.error(error)
                        reject(error);
                    }
                });
        } catch (error) {
            afterHandler({},error);
            mostrarError("Error de solicitud");
            console.error(error)
            reject(error);
        }
    });
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
    let elementIsBody = (element.tagName == document.body.tagName);
    if (show) {
        
        loaderCount++;
        element.loaderCount = loaderCount;
        if(element.querySelector(".loader")) return false;
        //let loader = document.createElement("div");
        let loader = crearElemento("div",{
            class: "loader",
            role: "status",
            "aria-label": "loading",
            "tabindex": "-1",
        })
        
        if(element.tagName == document.body.tagName) loader.classList.add("loader-body");
        element.appendChild(loader);
        if(element.classList.contains("position-relative")){
            element.havedPosition = true;
        }
        else{
            element.classList.add("position-relative");
        }
        if(elementIsBody){
            document.body.querySelector("main").setAttribute("inert","");
        }
        else{
            element.setAttribute("inert","");
        }
        loader.focus();
    } else {
        loaderCount--;
        if(loaderCount <= 0) loaderCount = 0;
        if(loaderCount > 0) {
            element.loaderCount = loaderCount;
            return false
        };

        if(!element.querySelector(".loader")) return false;
        element.querySelector(".loader").remove();
        if(!element.havedPosition){
            element.classList.remove("position-relative");
            delete element.havedPosition;
        }
        delete element.loaderCount;
        if(elementIsBody){
            document.body.querySelector("main").removeAttribute("inert");
        }
        else{
            element.removeAttribute("inert");
        }
        const firstFocusable = element ? element.querySelector('a, button, input, select, textarea, [tabindex]:not([tabindex="-1"])') : null;
        if (firstFocusable) {
            firstFocusable.focus();
        } else {
            document.body.focus();
        }
    }
}

/**
 * Solo para development
 * 
 * agrega al prototipo de los formularios un metodo para guardar en el localstorage 
 * los valores de sus inputs y select y otro para agregarlos desde el localstorage
 * esto solo se guardara en un item llamado savedForms
 * y se guardan en formato JSON y para asegurarse de que pertenescan al formulario adecuado 
 * utiliza el formdata para optener los valores y el name para identificarlos
 * 
 */
HTMLFormElement.prototype.saveForm = function () {
    let formData = new FormData(this);
    let savedForms = localStorage.getItem("savedForms") || "{}";
    savedForms = JSON.parse(savedForms);
    savedForms[this.action] = {};
    formData.forEach((value, key) => {
        savedForms[this.action][key] = value;
    });
    localStorage.setItem("savedForms", JSON.stringify(savedForms));
    mostrarAdvertencia("formulario guardado en el localstorage");
}
// loadForm

HTMLFormElement.prototype.loadForm = function (...exceptions) {
    let savedForms = localStorage.getItem("savedForms") || "{}";
    savedForms = JSON.parse(savedForms);
    // se carga sin el form data 

    elementos = this.querySelectorAll('input[name], select[name]');
    for (const item of elementos) {
        if(!savedForms[this.action] || !savedForms[this.action][item.name]) continue;
        if(exceptions.includes(item.name)) continue;
        if(item.tagName == "INPUT" && item.type == "checkbox") item.checked = (savedForms[this.action][item.name]) ? true : false;
        else item.value = savedForms[this.action][item.name];
    }
    mostrarAdvertencia("formulario cargado desde el localstorage");
}

// funcion para limpiar el localstorage de los formularios
HTMLFormElement.prototype.clearForm = function () {
    let savedForms = localStorage.getItem("savedForms") || "{}";
    savedForms = JSON.parse(savedForms);
    delete savedForms[this.action];
    localStorage.setItem("savedForms", JSON.stringify(savedForms));
    console.log(`forumulario ${this.action} limpiado`);
}
// funcion para vaciar el localstorage de los formularios
HTMLFormElement.prototype.clearForms = function () {
    localStorage.removeItem("savedForms");
    mostrarAdvertencia("localstorage de formularios limpiado");
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
    let smsContainer = document.getElementById(this.dataset.formText) || document.getElementById(this.dataset.formtext) || this.parentElement.querySelector('.form-text')
    this.classList.remove('is-processing');
    if (control === true) {
        this.classList.add('is-valid')
        this.classList.remove('is-invalid')
        this.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
        smsContainer ? smsContainer.classList.remove("d-block"):null;
        this.isValid = ()=>{ return true }
    } else if(control === false) {
        this.classList.add('is-invalid')
        this.classList.remove('is-valid')
        this.setCustomValidity(mensaje);
        smsContainer ? smsContainer.textContent = mensaje:null;
        smsContainer ? smsContainer.classList.add("d-block"):null;

        this.isValid = ()=>{ return false }
    }
    else {
        this.classList.remove('is-valid')
        this.classList.remove('is-invalid')
        this.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
        this.isValid = ()=>{ return true }
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




/**
 * Renderiza un datatable en el selector especificado
 * @param {string} selector - selector del elemento que se va a renderizar el datatable
 * @param {Object} options - opciones del datatable
 * @param {string} options.pagingType - tipo de paginacion, por defecto simple_numbers
 * @param {Object} options.language - idioma del datatable, por defecto espanol
 * @param {string} options.language.url - url del archivo de idioma
 * @param {Object} options.layout - layout del datatable, por defecto un boton de excel, pdf y print
 * @param {Array} options.columns - columnas del datatable, por defecto una columna vacia para las acciones
 * @param {boolean} options.enableActionsColumn - habilita o deshabilita la columna de acciones
 * @param {Array} options.botones - botones que se van a renderizar en la columna de acciones
 * @param {string} options.botones.accion - nombre del boton
 * @param {string} options.botones.icono - icono del boton, por defecto fa-pen-to-square
 * @param {function} options.botones.function - funcion que se ejecuta al crear el boton para personalizarlo
 * @returns {DataTable} - el objeto datatable
 */
function renderDataTable(selector, options = {}) {


    let defaultOptions = {
        pagingType: 'simple_numbers',
        language: {
            url: LOCAL_DIR+'/public/lib/DataTables/datatables-spanish.json'
        },
        layout: {
            
            bottom1Start: {
                pageLength: true
            },
            bottom1End: {
                buttons: [
                    {
                        // Elemento de texto personalizado
                        text: 'Exportar: ',
                        // Puedes añadir una clase para estilizarlo si es necesario
                        className: 'dt-export-button',
                        // Esto evita que se comporte como un botón real
                        action: function (e, dt, node, config) {
                            // No hacer nada al hacer clic
                            e.preventDefault();
                        }
                    },
                    'excel', 'pdf', 'print'
                ]
            }
        },
        columns: [],
        enableActionsColumn: false,
    }

    let mergedOptions = Object.assign({}, defaultOptions, options);

    if (mergedOptions.enableActionsColumn) {


        let createButtonAuxiliar = function (accion,icono = "fa-pen-to-square",btnFunction = null, data){
            button = crearElemento("div",{class: "accion pointer", "data-bs-toggle": "tooltip", "data-bs-title": accion});
            let div = crearElemento("div");
            div.appendChild(crearElemento("i",{"class": `fa-solid fa-fw ${icono}`}));
            button.appendChild(div);
            if(typeof btnFunction == "function"){
                btnFunction(button,data);
            }
            new bootstrap.Tooltip(button);
            return button;
            
        };



        mergedOptions.columns.push({
            data: null,
            searchable: false,
            orderable: false,
            render: function (data, type, row) {
                return "";
            },
            createdCell: function (td, data) {
                let botones = mergedOptions.botones;
                let container = crearElemento("div",{"class": "d-flex justify-content-evenly w-100 gap-3"});
                for(let objBoton of botones){
                    container.appendChild(createButtonAuxiliar(objBoton.accion,objBoton.icono,objBoton.function,data));
                }
                td.appendChild(container);
            }
        });
    }

    let table = null;

    if (DataTable.isDataTable(document.querySelector(selector))) {
        table = new DataTable(document.querySelector(selector));
        table.destroy();
    }

    table = new DataTable(document.querySelector(selector), mergedOptions);

    return table;
}

/**
 * Crea un elemento HTML y lo configura con los atributos dados.
 * @param {string} elemento - El nombre del elemento HTML a crear.
 * @param {object} [atributos] - Los atributos del elemento. Si no se pasan, se crea el elemento sin atributos.
 * @returns {HTMLElement} El elemento creado.
 */
function crearElemento(elemento, atributos) {
    let el = document.createElement(elemento);
    if(typeof(atributos) !== "object") return el;
    for (let i in atributos) {
        if(i == "textContent") el.textContent = atributos[i];
        else el.setAttribute(i, atributos[i]);
    }
    return el;
}
/**
 * la funcion recibe una fecha en formato 2025-07-08 11:00:53
 * y retorna la fecha en formato 08/07/2025
 * @param {string} fecha 
 * @param {string} separador 
 */
function getFecha(fecha){
    return fecha.split(" ")[0].split("-").reverse().join("/");
}
/**
 * la funcion recibe una fecha en formato "2025-07-08 17:00:53" o "17:00:53"
 * y retorna la hora en formato 5:00 PM
 * @param {string} fecha 
 * @returns 
 */
function getHora(fecha){
    let fechaFormat = "";
    if(fecha.includes("-")){
        fechaFormat = fecha.split(" ")[1];
    }else if(fecha.includes(":")){
        fechaFormat = fecha;
    }
    else{
        return "00:00";
    }

    let hora = new Date("2000-01-01 "+fechaFormat);
    let horas = hora.getHours();
    let minutos = hora.getMinutes();
    let ampm = horas >= 12 ? 'PM' : 'AM';
    horas = horas % 12;
    horas = horas ? horas : 12; // the hour '0' should be '12'
    minutos = minutos < 10 ? '0'+minutos : minutos;
    let strTime = horas + ':' + minutos + ' ' + ampm;
    return strTime;
}

/**
 * para formularios sin js
 * @param {string|Array} elem query Selector
 * @param {*} maxLength 
 */
function addPreventLongStringInput(elem, maxLength = 50) {

    if(Array.isArray(elem)){

        elem.forEach(element => {
            addPreventLongStringInput(element,maxLength);
        });
    }
    else{
        let input = document.querySelector(elem);
        if(!input) return;
        input.addEventListener('input', function () {

            let sms = false;
            if(input.dataset.formtext){
                sms = document.getElementById(input.dataset.formtext);
                if(!sms) sms = false;
            }
            if(!sms){
                sms = input.nextElementSibling.classList.contains("invalid-feedback") ? input.nextElementSibling: false;
            }

            let funcAux = function (sms,texto=""){
                if(texto != ''){
                    sms.textContent = texto;
                    sms.classList.add("d-block");
                }
                else{
                    sms.classList.remove("d-block");
                    sms.textContent = "";
                }
            }






            if (this.value.length > maxLength) {
                if (sms) {
                    funcAux(sms,`El campo no puede tener mas de ${maxLength} caracteres`);
                    if(this.tagName == "TEXTAREA"){
                        this.setCustomValidity(`El campo no puede tener mas de ${maxLength} caracteres`);
                        return;
                    }
                }
            }
            else{
                if(sms){
                    funcAux(sms);
                    if(this.tagName == "TEXTAREA"){
                        this.setCustomValidity(``);
                    }
                }
            }

        });
        input.pattern = `.{1,${maxLength}}`;
        input.title = `El campo no puede tener mas de ${maxLength} caracteres`;
    }

};


function inputValid(elem,maxLength = 50 ,regex = null, message = "INVALIDO", required = false) {
    if(typeof elem == "string") elem = document.querySelector(elem);
    else if(typeof elem == "object") elem = elem;
    else{
        console.error("Elemento no valido");
        return;
    }

    let smsContainer = document.getElementById(elem.dataset.formText) || document.getElementById(elem.dataset.formtext) || elem.parentElement.querySelector('.form-text')
    if(!smsContainer) console.error("Elemento no tiene form-text");


    elem.addEventListener("input", function () {
        invalidStatus(this);
        if(required && this.value.length <= 0) {
            invalidStatus(this, false, "Este campo es obligatorio");
            return;
        }
        if(this.value.length > maxLength) {
            invalidStatus(this, false, "El campo no puede tener mas de "+maxLength+" caracteres");
            return;
        }
        else{
            invalidStatus(this, true);
        }

        if(regex){
            if(!regex.test(this.value)){
                invalidStatus(this, false, message);
                return;
            }
            else{
                invalidStatus(this, true);
            }
        }
    })
}






function invalidStatus (elem, control = null, mensaje = "INVALIDO") {
    if(typeof elem == "string") elem = document.querySelector(elem);
    else if(typeof elem == "object") elem = elem;
    else{
        console.error("Elemento no valido");
        return;
    }
    let smsContainer = document.getElementById(elem.dataset.formText) || document.getElementById(elem.dataset.formtext) || elem.parentElement.querySelector('.form-text')
    elem.classList.remove('is-processing');
    if (control === true) {
        elem.classList.add('is-valid')
        elem.classList.remove('is-invalid')
        elem.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
        smsContainer ? smsContainer.classList.remove("d-block"):null;
        elem.isValid = ()=>{ return true }
    } else if(control === false) {
        elem.classList.add('is-invalid')
        elem.classList.remove('is-valid')
        elem.setCustomValidity(mensaje);
        smsContainer ? smsContainer.textContent = mensaje:null;
        smsContainer ? smsContainer.classList.add("d-block"):null;

        elem.isValid = ()=>{ return false }
    }
    else {
        elem.classList.remove('is-valid')
        elem.classList.remove('is-invalid')
        elem.setCustomValidity("");
        smsContainer ? smsContainer.textContent = "":null;
        elem.isValid = ()=>{ return true }
    }
}






/**
 * Verifica si la fecha del elemento es valida y no es futura
 * @param {HTMLElement|string} elem - elemento o selector css del elemento a verificar
 * @param {boolean} required - si es obligatorio el campo
 * @returns {boolean} - true si la fecha es valida y no es futura, false en caso contrario
 */
function fechaNoFuture(elem, required = false) {
    if(typeof elem == "string") elem = document.querySelector(elem);
    else if(typeof elem == "object") elem = elem;
    else{
        console.error("Elemento no valido");
        return false;
    }
    if(required && elem.value.length <= 0) {
        invalidStatus(elem, false, "Este campo es obligatorio");
        return false;
    }
    
    if((date = AObjetoFecha(elem.value))){
        let today = new Date();
        if(date.valueOf() > today.valueOf()){
            invalidStatus(elem, false, "La fecha no puede ser futura");
            return false;
        }
    }
    else{
        invalidStatus(elem, false, "La fecha no es valida");
        return false;
    }

    return true;
}



function elemBlur(){
    if(document.activeElement){
        document.activeElement.blur();
    }
}


function AObjetoFecha(fechaString) {
    const formatoValido = /^\d{4}-\d{2}-\d{2}$/;
    if (!formatoValido.test(fechaString)) {
        console.error(`Error: El formato de la fecha '${fechaString}' no es 'YYYY-MM-DD'.`);
        return null;
    }
    const objetoFecha = new Date(fechaString+" 00:00:00");
    if (isNaN(objetoFecha)) {
        console.error(`Error: La fecha '${fechaString}' es sintácticamente inválida (ej. día o mes incorrecto).`);
        return null;
    }
    return objetoFecha;
}

/** para la travesia de las validaciones */

function bodyLoader($loaderLocated = "body") {
    elemBlur();
    mostrarLoader($loaderLocated,true);
}


/**
 * bloquea el formulario
 * @param {string} form 
 */
function addblockFrom(form, $loaderLocated = "body") {
    form = document.querySelector(form);
    if(form){
        form.addEventListener("submit",(e)=>{
            elemBlur();
            mostrarLoader($loaderLocated,true);
            form.onsubmit = () => false;
        })
    }
}

/**
 * recibe dos inputs typo date o el selector
 * verifica que desde y hasta no sean una fecha futura y la hasta sea mayor a la desde
 * @param {HTMLInputElement|string} desde 
 * @param {HTMLInputElement|string} hasta 
 */
function addValidDesdeHasta(desde,hasta){
    if(typeof desde == "string") desde = document.querySelector(desde);
    else if(typeof desde == "object") desde = desde;
    else{
        console.error("Elemento no valido");
        return;
    }
    if(typeof hasta == "string") hasta = document.querySelector(hasta);
    else if(typeof hasta == "object") hasta = hasta;
    else{
        console.error("Elemento no valido");
        return;
    }


    const fechas = function (){
        invalidStatus(desde);// reiniciar estado
        invalidStatus(hasta);// reiniciar estado

        // Obtener los elementos de fecha
        const fechaDesdeInput = desde;
        const fechaHastaInput = hasta;
        // Convertir los valores a objetos Date
        const fechaDesde = new Date(fechaDesdeInput.value);
        const fechaHasta = new Date(fechaHastaInput.value);
        // Obtener la fecha actual (sin hora, para comparar solo el día)
        const hoy = new Date();
        hoy.setHours(0, 0, 0, 0); 
        // Variable para almacenar el mensaje de error
        // 1. Validación: "Desde" no puede ser mayor que "Hasta"
        // Solo aplica si ambos campos tienen un valor
        
        // 2. Validación: Ninguna fecha puede ser del futuro
        if (fechaDesdeInput.value && fechaDesde > hoy) {
            invalidStatus(fechaDesdeInput, false, "La fecha no puede ser una fecha futura.");
            return false;
        }
        else if(fechaDesde > fechaHasta){
            invalidStatus(fechaDesdeInput, false, "La fecha 'desde' no puede ser mayor a la fecha 'hasta'.");
            return false;
        }
        
        if (fechaHastaInput.value && fechaHasta > hoy) {
            invalidStatus(fechaHastaInput, false, "La fecha no puede ser una fecha futura.");
            return false;
        }
        else if(fechaHasta < fechaDesde){
            invalidStatus(fechaHastaInput, false, "La fecha 'hasta' no puede ser menor a la fecha 'desde'.");
            return false;
        }

        return true;
        
    }

    desde.addEventListener("input", fechas);
    hasta.addEventListener("input", fechas);
    desde.addEventListener("change", fechas);
    hasta.addEventListener("change", fechas);
}

function addValidNombre(elem, required = false, maxLength = 50) {
    let regexString = `^[A-Za-zÑñÁáÉéÍíÓóÚúÜü\\s,.-]{${required?1:0},${maxLength}}$`;
    let regex = new RegExp(regexString);
    inputValid(elem,50,regex,"El campo solo acepta letras, espacios, comas, puntos y guiones",required);
}


function addValidAlfaNum(elem, required = false, maxLength = 50) {
    let regexString = `^[A-Za-z0-9ÑñÁáÉéÍíÓóÚúÜü\\s,.-]{${required?1:0},${maxLength}}$`;
    let regex = new RegExp(regexString);
    inputValid(elem,maxLength,regex,"El campo solo acepta letras, numeros, espacios, comas, puntos y guiones",required);
}

function addValidNum(elem, required = false, maxLength = 50) {
    let regexString = `^[0-9]{${required?1:0},${maxLength}}$`;
    let regex = new RegExp(regexString);
    inputValid(elem,maxLength,regex,"El campo solo acepta numeros",required);
}

function addValidTelefono(elem,required = false, maxLength = 11) {
    let regexString = `^[0-9]{${required?1:0},${maxLength}}$`;
    let regex = new RegExp(regexString);
    inputValid(elem,maxLength,regex,"El campo solo acepta numeros",required);
}

/**
 * validar si el elemento es valido
 * 
 * @param {string|HTMLElement|Array<string|HTMLElement>} elem 
 * - puede ser un querySelector, un HTMLElement o un array
 * - si es un querySelector, se convierte a un HTMLElement y se valida
 * - si es un HTMLElement, se valida
 * - si es un array, se valida todos los elementos
 * @returns {boolean|null|undefined}
 */
function checkValidStatus(elem){
    

    let dispatch = true;
    if(typeof elem == "string") {
        elem = document.querySelector(elem)
        if(!elem) {
            console.error("Elemento no valido");
            return;
        }
    }
    else if(Array.isArray(elem)){
        return elem.every(e => checkValidStatus(e));
        //dispatch = false;
    }
    else if(typeof elem == "object") elem = elem;
    else{
        console.error("Elemento no valido");
        return;
    }
    
    
    if(dispatch){
        
        elem.dispatchEvent(new Event('input'));
        elem.dispatchEvent(new Event('change'));
        if(typeof elem.isValid == "function"){
            return elem.isValid() || false;
        }
        else{
            console.error("Elemento no valido "+elem.name);
            return false;
        }
    }
}


