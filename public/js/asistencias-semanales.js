
// evento onload
let departamento = document.getElementById("departamento");
let turno = document.getElementById("turno");
let fecha = document.getElementById("fecha");
let preventLostControl = false;
var mostrarBotonEliminar = false;
document.addEventListener("DOMContentLoaded", async function(){
    window.addEventListener('beforeunload', preventLost);
    addValidAlfaNum('#observacion', false, 255);
    const modalEliminar = document.getElementById('modal-eliminar')
    // quito el evento del modal

    modalEliminar.removeEventListener('show.bs.modal',modalEliminar.eventModal);




    // agrego la fecha actual al campo de fecha

    const _hoy = new Date();


    document.querySelector("#fecha").value = _hoy.getFullYear() + "-" + (_hoy.getMonth() + 1) + "-" + _hoy.getDate();


    // evento submit para el formulario
    
    // evento onchange para los selectores principales
    [departamento, turno, fecha].forEach(input => {
        input.addEventListener("change", (e) => {
            document.querySelector("#tabla-asistencias-semanales table tbody").innerHTML = "";
            document.getElementById("tabla-asistencias-semanales").classList.add("d-none");
        });
    });

    fecha.addEventListener("change", (e) => {
        let resp = fechaNoFuture(e.target, true);
    })


    // Evento para eliminar las asistencias desde el boton #eliminar-asistencias
    document.querySelector("#eliminar-asistencias").addEventListener("click", async function(e){
        e.preventDefault();
        abrirModalEliminar("Eliminar la asistencia seleccionada").then(async() => {
            if(document.querySelector("#fecha").value === "") {
                mostrarError("La fecha de asistencia no esta seleccionada o no esta registrada");
                return;
            }

            // valido que el valor del input sea un numero
            if(!/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/.test(document.querySelector("#fecha").value)) {
                mostrarError("La fecha de asistencia no es valida");
                return;
            }

            let objSend = {
                fecha: document.querySelector("#fecha").value,
                turno: document.querySelector("#turno").value,
                idDepartamento: document.querySelector("#departamento").value,
                action:"Eliminar"
            };



            let response = await peticion("/Asistencias/Eliminar",{
                method: "POST",
                before: () => {
                    document.querySelector("#form-table-asistencias").sending = true;
                },
                after: () => {
                    delete document.querySelector("#form-table-asistencias").sending;
                },
                useLoader: "body",
                body: JSON.stringify(objSend)

            });
            if(response = parsearJson(response)) {
                if(response.success) {
                    mostrarExito(response.message);
                    preventLostControl = false;
                    cargarDepartamentos(false);
                }
                else {
                    mostrarError(response.message);
                }
            }
        }).finally(() => {
            document.activeElement.blur();
            document.getElementById('modal-eliminar').Modal.hide();
        });
        
    });

    document.getElementById("formInasistencia").addEventListener("submit", async (e) => {
        e.preventDefault();
        document.getElementById('modalInasistencia').resolvePromise({
            justificacion: document.getElementById("justificacion").value,
            descripcion: document.getElementById("observacion").value
        });
        
        //this.resolvePromise();
        //this.rejectPromise;
        //this.bootstrapModal;
    });

    


    
});



// Functions ************************************************************
    
    function preventLost(event = null){
        if(preventLostControl) {
            if(event) {
                event.preventDefault();
                event.returnValue = '';
            }
        }

    }

    async function cargarDepartamentos(control = true, preventlostDefault = true) {

        if(preventLostControl){
            if(confirm("Se perderan los cambios realizados. ¿Desea continuar?")) {
                preventLostControl = false;
            }
            else {
                return;
            }
        }

        let departamento = document.getElementById("departamento");
        let turno = document.getElementById("turno");
        let fecha = document.getElementById("fecha");
        let btnCargar = document.getElementById("btn-cargar");



        [departamento, turno, fecha].forEach(input => {
            input.setValidStatus();
        })

        if(control){
            // validar los campos

            fecha.dispatchEvent(new Event("change"));
            if(fecha.classList.contains("is-invalid")) return;

            if(departamento.value === "") {
                departamento.setValidStatus(false, "seleccione un departamento");
                return;
            }
            if(turno.value === "") {
                turno.setValidStatus(false, "seleccione un turno");
                return;
            }
            if(fecha.value === "" && fecha.type === "date") {
                fecha.setValidStatus(false, "seleccione una fecha valida");
                return;
            }

            // cargar tabla de la base de datos 



            let response = await peticion("/Asistencias", {
                method: "POST",
                body: JSON.stringify({
                    idDepartamento: departamento.value,
                    turno: turno.value,
                    fecha: fecha.value,
                    consultar: true
                }),
                before: () => {
                    document.querySelector("#tabla-asistencias-semanales table tbody").innerHTML = "";
                    document.getElementById("tabla-asistencias-semanales").classList.add("d-none");
                    departamento.disabled = true;
                    turno.disabled = true;
                    fecha.disabled = true;
                    btnCargar.disabled = true;
                },
                after: () => {
                    departamento.disabled = false;
                    turno.disabled = false;
                    fecha.disabled = false;
                    btnCargar.disabled = false;
                },
                useLoader: "body"

            });
            response = JSON.parse(response);


            if(response.success){

                [departamento, turno, fecha].forEach(input => {
                    input.prevValue = input.value;
                })

                generateTable(response.listaTrabajadores, response.semana);
                document.getElementById("tabla-asistencias-semanales").classList.remove("d-none");
                // let primeratd = document.querySelector("#tabla-asistencias table tbody tr > td");
                // if(primeratd.getAttribute("colspan") === "4") {
                //     preventLostControl = false;
                // }
                // else preventLostControl = preventlostDefault;

                // // TODO arreglar esto para que el mensaje aparezca si hay algun cambio despues de guardar 

            }
            else {
                mostrarError(response.message);
            }

            
        }
        else {

            // [departamento, turno, fecha].forEach(input => {
            //     input.value = "";
            // })
            document.querySelector("#tabla-asistencias-semanales table tbody").innerHTML = "";
            document.getElementById("tabla-asistencias-semanales").classList.add("d-none");
            preventLostControl = false;

        }


        eventoSubmitTabla();


       


    }
    

/**
 * @typedef {Object} controlAsistencias
 * @property {string} fecha - La fecha de la asistencia.
 * @property {string | null} horaEntrada - La hora de entrada, puede ser nulo.
 * @property {string | null} horaSalida - La hora de salida, puede ser nulo.
 * @property {string | null} justificacion - La justificación de la asistencia.
 * @property {string | null} descripcion_justificacion - La descripción de la justificación.
 * @property {number} idAsistencia_inasistencia - El ID de asistencia/inasistencia.
 * @property {boolean} esAsistencia - Indica si es una asistencia (0) o una inasistencia (1).
 */




/**
 * @typedef {Object} diasObj
 * @prop {string} fecha - fecha en formato dd/mm/aaaa
 * @prop {boolean} laborable - true si la fecha es laborable
 */

/**
 * @typedef {Object} objTrabajador
 * @prop {string} nombre
 * @prop {string} apellido
 * @prop {string} cedula
 * @prop {string} fullName
 * @prop {controlAsistencias} controlAsistencias
 */
/**
 * 
 * @param {object<objTrabajador>} obj 
 * @param {object} semana - el objeto de la semana
 * @param {diasObj} semana.lunes - el lunes de la semana
 * @param {diasObj} semana.martes - el martes de la semana
 * @param {diasObj} semana.miercoles - el miercoles de la semana
 * @param {diasObj} semana.jueves - el jueves de la semana
 * @param {diasObj} semana.viernes - el viernes de la semana
 * @param {diasObj} semana.sabado - el sabado de la semana
 * @param {diasObj} semana.domingo - el domingo de la semana
 
 */
function generateTable(obj, semana){
    let mostrarBotonEliminar = false;

    let table = document.querySelector("#tabla-asistencias-semanales table tbody");
    table.innerHTML = "";

    theadCheckboxEvent(semana, true);



    for(let trabajadorKey in obj){
        let trabajador = obj[trabajadorKey];

        let saveTrabajador = {
            cedula:trabajador.cedula,
            dias:{}
        }

        let fila = crearElemento("tr");

        celdasTrabajador(fila,trabajador.cedula,trabajador.fullName);

        let arregloDias = ["lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo"];

        arregloDias.forEach((dia) =>{
            let checkedDia = true;
            let fecha = semana[dia].fecha

            let diaRegistrado = trabajador.controlAsistencias[fecha];

            saveTrabajador.dias[fecha] = {
                fecha:semana[dia].fecha,
                laborable:semana[dia].laborable,
                dia: dia
            };

            if(diaRegistrado){
                mostrarBotonEliminar = true;
                if(diaRegistrado.esAsistencia === 0){
                    saveTrabajador.dias[fecha].tipo_justificacion = diaRegistrado.justificacion;
                    saveTrabajador.dias[fecha].descripcion_justificacion = diaRegistrado.descripcion_justificacion;
                    if(diaRegistrado.laborable !== 0) checkedDia = false; // si el dia no es laborable no se marca la casilla
                    else{
                        delete saveTrabajador.dias[fecha].tipo_justificacion;
                        delete saveTrabajador.dias[fecha].descripcion_justificacion;
                    }
                    
                }
                saveTrabajador.dias[fecha].idAsistencia_inasistencia = diaRegistrado.idAsistencia_inasistencia;
                if(diaRegistrado.laborable === 0){
                    // desmarcar el checkbox del th
                    let selector = "input.laborable_check[type='checkbox'][data-dia='" + semana[dia].fecha + "']";
                    let thCheck = document.querySelector(selector);
                    thCheck.checked = false;
                    let queso;
                }
            }



            let tdDia = celdaCheckbox(trabajador.cedula, fecha, !semana[dia].laborable, checkedDia);

            

            

            fila.appendChild(tdDia);
            fila.saveTrabajador = saveTrabajador;
        });

        



        table.appendChild(fila);
    }

    let btn = document.getElementById("eliminar-asistencias");
    let info =document.getElementById("guardar-asistencias-info");
    if(mostrarBotonEliminar){
        if(btn) btn.parentNode.classList.remove("d-none");
        if(info) info.classList.add("d-none");
    }
    else{
        if(btn) btn.parentNode.classList.add("d-none");
        if(info) info.classList.remove("d-none");
    }

    if(obj.length === 0){
        let row = document.createElement("tr");
        let td = crearElemento("td",{
            class:"text-center",
            colspan:"8",
            textContent:"No se encontraron registros"
        });
        
        row.appendChild(td);
        table.appendChild(row);
        table.parentNode.classList.add("no-records-found");
        document.getElementById("form-table-asistencias").sending = true; // para desactivar el boton
    }
    else{
        table.parentNode.classList.remove("no-records-found");
        document.getElementById("form-table-asistencias").sending = false; // para desactivar el boton

        document.querySelectorAll("input.laborable_check[type='checkbox']").forEach(checkbox => {
            if(!checkbox.disabled){
                checkbox.onchange();
            }
        })

    }


    

    
    


}


function theadCheckboxEvent(semana){
    let arregloDias = ["lunes", "martes", "miercoles", "jueves", "viernes", "sabado", "domingo"];

    arregloDias.forEach(dia => {
        // primero los checkbox de la semana

            let chekName = dia.substring(0,1).toUpperCase() + dia.substring(1,2).toLowerCase();

            let checkboxControl = document.getElementById(`chekbox_${chekName}`);
            if(semana[dia].laborable){
                checkboxControl.checked = true;
                checkboxControl.disabled = false;
                checkboxControl.parentNode.title = "";
            }
            else{
                checkboxControl.checked = false;
                checkboxControl.disabled = true;
                checkboxControl.parentNode.title = "El dia no es laborable para el turno seleccionado";
            }
            checkboxControl.setAttribute('data-dia', semana[dia].fecha);
            EventoCheckboxLaborable(checkboxControl);

            document.querySelector(`#dia_th_${chekName}`).textContent = parseFecha(semana[dia].fecha);

    });
}

function EventoCheckboxLaborable(checkbox){

    let small = checkbox.closest("th").querySelector(".laborable-info");

    if (checkbox.checked) {
        small.innerHTML = "Laborable";
        small.classList.remove("text-danger");
        small.classList.add("text-success");
    } else {
        small.innerHTML = "No Laborable";
        small.classList.remove("text-success");
        small.classList.add("text-danger");
    }





    checkbox.onchange = function(e){
        let filas = document.querySelectorAll("#tabla-asistencias-semanales table tbody tr");
        let fechaDia = this.dataset.dia;
        if(this.checked){

            small.innerHTML = "Laborable";
            small.classList.remove("text-danger");
            small.classList.add("text-success");

        }
        else{
            small.innerHTML = "No Laborable";
            small.classList.remove("text-success");
            small.classList.add("text-danger");
            this.parentNode.title = "El dia se marcara como 'no laborable' para el turno seleccionado y los trabajadores registrados actualmente";
        }
        this.parentNode.title = "";
        for(let fila of filas){
            let objDias = fila.saveTrabajador.dias;
            for(let dia in objDias){
                if(dia == fechaDia){
                    objDias[dia].laborable = this.checked;
                    fila.querySelector("input[type='checkbox'][data-dia='" + dia + "']").disabled = !this.checked;
                }
            }
        }
        // console.log(filas[0].saveTrabajador);
    }
    
}

function celdaCheckbox(cedula,diaFecha, disabled = false, checked){
    
    let props = {
        class: "asistencia-checkbox",
        'data-trabajador': cedula,
        'data-dia': diaFecha,
        type:'checkbox'
    }
    let input = crearElemento("input",props);
    input.disabled = disabled;
    input.checked = checked

    eventoJustificacion(input);

    let button = crearElemento("button",{
        class:"btn btn-sm btn-link editar-inasistencia",
        type:"button"
    });
    let button_content = crearElemento("div",{class:"accion pointer", "data-bs-toggle": "tooltip", "data-bs-title": "Editar"});
    new bootstrap.Tooltip(button_content);
    let buttonEditar = crearElemento("div");
    buttonEditar.innerHTML = '<i class="fa-solid fa-fw fa-pen"></i>';
    button_content.appendChild(buttonEditar);

    button.appendChild(button_content);

    button.addEventListener('click', eventoEditarInasistencia);

    let td = crearElemento("td",{class:(disabled ? "dia-disabled" : "")});
    let divContainer = crearElemento("div",{"class":"celda-dia"});
    divContainer.appendChild(input);
    divContainer.appendChild(button);
    td.appendChild(divContainer);
    return td;



}

function celdasTrabajador(fila,cedula,nombre){

    let cedulatd = crearElemento("td",{textContent:cedula});
    let nombretd = crearElemento("td",{textContent:nombre});
    fila.appendChild(cedulatd);
    fila.appendChild(nombretd);
    
}



function parseFecha(fecha, reverser = false){
    
    if(!reverser){
        let aux = fecha.split("-");
        return aux[2] + "/" + aux[1] + "/" + aux[0];
    }
    else{
        let aux = fecha.split("/");
        return aux[0] + "-" + aux[1] + "-" + aux[2];
    }
    
}


function eventoJustificacion(checkbox){

    checkbox.addEventListener('change', async function(){
        let checkboxDia = this.dataset.dia;

        let fila = this.closest("tr");
        let obj = fila.saveTrabajador;
        let nombre = fila.getElementsByTagName("td")[1].textContent;
        
        if(this.checked){

            delete obj.dias[checkboxDia].tipo_justificacion;
            delete obj.dias[checkboxDia].descripcion_justificacion;
            
        }
        else{
            elModal = document.getElementById('modalInasistencia');
            promesaModal(elModal,nombre,obj.cedula,checkboxDia).then((response) => {
                obj.dias[checkboxDia].tipo_justificacion = response.justificacion;
                obj.dias[checkboxDia].descripcion_justificacion = response.descripcion;
                elModal.bootstrapModal.hide();
            }).catch(() => {
                checkbox.checked = true;
                checkbox.dispatchEvent(new Event('change'));
            }).finally(() => {
                document.getElementById("submit-asistencias").disabled = false;
                document.getElementById("form-table-asistencias").sending = false;
                document.getElementById("form-table-asistencias").updating = false;
                elModal.resolvePromise = null;
                elModal.rejectPromise = null;
                elModal.bootstrapModal = null;
            });
        }
    });

}


function promesaModal(elModal,nombre, cedula,dia, justificacion = null, jusdescripcion = null){
    let modal = new bootstrap.Modal(elModal);
    elModal.querySelector("#modalTrabajadorId").value = cedula;
    elModal.querySelector("#modalDia").value = dia;
    elModal.querySelector("#modalTrabajadorInfo").textContent = `${nombre} (${parseFecha(dia)})`;
    document.getElementById("submit-asistencias").disabled = true;
    document.getElementById("form-table-asistencias").updating = true;
    invalidStatus(elModal.querySelector("#observacion"));
    if(justificacion){
        elModal.querySelector("#justificacion").value = justificacion;
        elModal.querySelector("#observacion").value = jusdescripcion;
    }
    else{
        elModal.querySelector("#justificacion").value = "";
        elModal.querySelector("#observacion").value = "";
    }
    modal.show();
    return new Promise((resolveModal, rejectModal) => {
        elModal.resolvePromise = resolveModal;
        elModal.rejectPromise = rejectModal;
        elModal.bootstrapModal = modal;
        
        


        elModal.addEventListener('hide.bs.modal', () => {
            rejectModal();
        });
    })

    
}
async function eventoEditarInasistencia(){
    let fila = this.closest("tr");
    let checkbox = this.parentNode.getElementsByTagName("input")[0];
    let checkboxDia = checkbox.dataset.dia;
    let obj = fila.saveTrabajador;
    let nombre = fila.getElementsByTagName("td")[1].textContent;
    let elModal = document.getElementById('modalInasistencia');
    let justificacion = obj.dias[checkboxDia].tipo_justificacion;
    let jusdescripcion = obj.dias[checkboxDia].descripcion_justificacion;

    if(checkbox.disabled){
        return;
    }

    promesaModal(elModal,nombre,obj.cedula,checkboxDia,justificacion,jusdescripcion).then((response) => {
        obj.dias[checkboxDia].tipo_justificacion = response.justificacion;
        obj.dias[checkboxDia].descripcion_justificacion = response.descripcion;
        elModal.bootstrapModal.hide();
    }).catch(() => {
        
    }).finally(() => {
        document.getElementById("submit-asistencias").disabled = false;
        document.getElementById("form-table-asistencias").sending = false;
        document.getElementById("form-table-asistencias").updating = false;
        elModal.resolvePromise = null;
        elModal.rejectPromise = null;
        elModal.bootstrapModal = null;
    });
}


function eventoSubmitTabla(){
    let form = document.getElementById("form-table-asistencias");

    let func = async function(e){
        
        e.preventDefault();
        if(this.updating){
            mostrarError("Debe esperar a que se actualice la asistencia");
            return;
        }
        if(this.sending){
            return;
        }

        let objSend = {
            fecha: document.querySelector("#fecha").value,
            turno: document.querySelector("#turno").value,
            idDepartamento: document.querySelector("#departamento").value,
            trabajadores: getTrabajadoresObject(),
            action:"Registrar"
        };

        // console.log(JSON.stringify(objSend.trabajadores, null, 4));

        if(objSend.trabajadores === false) {
            console.error("no se puede listar los datos del trabajador");
            mostrarError("No se pudo enviar la información");
            return;
        };

        if(!Array.isArray(objSend.trabajadores) || objSend.trabajadores.length === 0) {
            mostrarAdvertencia("No hay registros de asistencias nuevos para guardar");
            return;
        };

        let validData = usoValidar(objSend.trabajadores);

        if(!validData.valido){
            mostrarError(validData.error);
            return;
        }


        let resp = await peticion("/Asistencias/Registrar",{
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            useLoader: "body",
            body: JSON.stringify(objSend),
            before: () => {
                form.sending = true;
            },
            after: () => {
                form.sending = false;
            }
        });

        resp = parsearJson(resp);

        if(resp.success) {
            mostrarExito(resp.message || "Asistencias guardadas correctamente");
            cargarDepartamentos();
        } else {
            mostrarError(resp.message || "Error al guardar asistencias");
        }
    }
    // se elimina el evento y se vuelve a agregar
    form.onsubmit = func;
}

function getTrabajadoresObject(){
    let tbody = document.querySelector("#tabla-asistencias-semanales table tbody");
    if(tbody === null) return false;
    let rows = tbody.querySelectorAll("tr");
    let trabajadores = [];
    for(let tr of rows) {
        let obj = tr.saveTrabajador;
        trabajadores.push(obj);
    }
    return trabajadores;
}


/************************************************************************/


function validarAsistencias(arregloAsistencias) {
    // Validar que el parámetro sea un arreglo
    if (!Array.isArray(arregloAsistencias)) {
        return {
            valido: false,
            error: 'El parámetro debe ser un arreglo'
        };
    }

    // Validar que el arreglo no esté vacío
    if (arregloAsistencias.length === 0) {
        return {
            valido: false,
            error: 'El arreglo no puede estar vacío'
        };
    }

    // Validar cada objeto del arreglo
    for (let i = 0; i < arregloAsistencias.length; i++) {
        const empleado = arregloAsistencias[i];
        
        // Validar estructura básica del objeto empleado
        if (typeof empleado !== 'object' || empleado === null) {
            return {
                valido: false,
                error: `El elemento en la posición ${i} no es un objeto válido`
            };
        }

        // Validar cédula
        if (!empleado.hasOwnProperty('cedula') || typeof empleado.cedula !== 'string' || empleado.cedula.trim() === '') {
            return {
                valido: false,
                error: `El elemento en la posición ${i} no tiene una cédula válida`
            };
        }

        // Validar días
        if (!empleado.hasOwnProperty('dias') || typeof empleado.dias !== 'object' || empleado.dias === null || Array.isArray(empleado.dias)) {
            return {
                valido: false,
                error: `El empleado con cédula ${empleado.cedula} no tiene un objeto 'dias' válido`
            };
        }

        // Validar cada día
        const fechas = Object.keys(empleado.dias);
        
        if (fechas.length === 0) {
            return {
                valido: false,
                error: `El empleado con cédula ${empleado.cedula} no tiene días registrados`
            };
        }

        for (const fecha of fechas) {
            const dia = empleado.dias[fecha];
            
            // Validar estructura del día
            if (typeof dia !== 'object' || dia === null) {
                return {
                    valido: false,
                    error: `El día ${fecha} del empleado ${empleado.cedula} no es un objeto válido`
                };
            }

            // Validar campos obligatorios del día
            const camposObligatorios = ['fecha', 'laborable', 'dia'];
            for (const campo of camposObligatorios) {
                if (!dia.hasOwnProperty(campo)) {
                    return {
                        valido: false,
                        error: `El día ${fecha} del empleado ${empleado.cedula} no tiene el campo '${campo}'`
                    };
                }
            }

            // Validar tipos de datos de los campos obligatorios
            if (typeof dia.fecha !== 'string' || dia.fecha.trim() === '') {
                return {
                    valido: false,
                    error: `El campo 'fecha' del día ${fecha} del empleado ${empleado.cedula} no es válido`
                };
            }

            if (typeof dia.laborable !== 'boolean') {
                return {
                    valido: false,
                    error: `El campo 'laborable' del día ${fecha} del empleado ${empleado.cedula} debe ser booleano`
                };
            }

            if (typeof dia.dia !== 'string' || dia.dia.trim() === '') {
                return {
                    valido: false,
                    error: `El campo 'dia' del día ${fecha} del empleado ${empleado.cedula} no es válido`
                };
            }

            // Validar que la fecha en la clave coincida con la fecha en el objeto
            if (dia.fecha !== fecha) {
                return {
                    valido: false,
                    error: `La fecha en la clave (${fecha}) no coincide con la fecha en el objeto (${dia.fecha}) para el empleado ${empleado.cedula}`
                };
            }

            // Validar formato de fecha (YYYY-MM-DD)
            const fechaRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (!fechaRegex.test(dia.fecha)) {
                return {
                    valido: false,
                    error: `El formato de fecha '${dia.fecha}' del empleado ${empleado.cedula} no es válido (debe ser YYYY-MM-DD)`
                };
            }

            // Validar días de la semana
            const diasSemanaValidos = ['lunes', 'martes', 'miercoles', 'miércoles', 'jueves', 'viernes', 'sabado', 'sábado', 'domingo'];
            if (!diasSemanaValidos.includes(dia.dia.toLowerCase())) {
                return {
                    valido: false,
                    error: `El día '${dia.dia}' del empleado ${empleado.cedula} no es un día de la semana válido`
                };
            }

            let num = 1;



            // Validar campos de justificación si existen
            if (dia.hasOwnProperty('tipo_justificacion')) {
                // que no sea un numero o una cadena con numeros
                //dia.tipo_justificacion = dia.tipo_justificacion.toString();

                let auxTipoJustificacion = function(tipo_justificacion) {
                    let ok = true;
                    if(typeof tipo_justificacion !== 'number' && typeof tipo_justificacion !== 'string'){
                        ok = false;
                    }
                    else if (typeof tipo_justificacion === 'string' && !/^\d+$/.test(tipo_justificacion)) {
                        ok = false;
                    }
                    return ok;
                }

                if(dia.laborable){
                    if (!auxTipoJustificacion(dia.tipo_justificacion)) {
                        return {
                            valido: false,
                            error: `El campo 'tipo_justificacion' del día ${fecha} del empleado ${empleado.cedula} no es válido`
                        };
                    }
    
                    // Validar que si existe tipo_justificacion, también exista descripcion_justificacion
                    if (!dia.hasOwnProperty('descripcion_justificacion')) {
                        return {
                            valido: false,
                            error: `El día ${fecha} del empleado ${empleado.cedula} tiene 'tipo_justificacion' pero no tiene 'descripcion_justificacion'`
                        };
                    }
    
                    if (typeof dia.descripcion_justificacion !== 'string') {
                        return {
                            valido: false,
                            error: `El campo 'descripcion_justificacion' del día ${fecha} del empleado ${empleado.cedula} debe ser una cadena de texto`
                        };
                    }

                }
                
            }

            // Validar que si existe descripcion_justificacion, también exista tipo_justificacion
            if (dia.hasOwnProperty('descripcion_justificacion') && dia.descripcion_justificacion !== null && !dia.hasOwnProperty('tipo_justificacion')) {
                return {
                    valido: false,
                    error: `El día ${fecha} del empleado ${empleado.cedula} tiene 'descripcion_justificacion' pero no tiene 'tipo_justificacion'`
                };
            }
        }
    }

    return {
        valido: true,
        mensaje: 'La estructura del JSON es válida'
    };
}

// Función adicional para contar asistencias e inasistencias
function resumenAsistencias(arregloAsistencias) {
    const validacion = validarAsistencias(arregloAsistencias);
    if (!validacion.valido) {
        return validacion;
    }

    const resumen = {
        totalEmpleados: arregloAsistencias.length,
        empleados: []
    };

    for (const empleado of arregloAsistencias) {
        let asistencias = 0;
        let inasistencias = 0;
        let diasNoLaborables = 0;

        const fechas = Object.keys(empleado.dias);
        
        for (const fecha of fechas) {
            const dia = empleado.dias[fecha];
            
            if (!dia.laborable) {
                diasNoLaborables++;
            } else if (dia.hasOwnProperty('tipo_justificacion')) {
                inasistencias++;
            } else {
                asistencias++;
            }
        }

        resumen.empleados.push({
            cedula: empleado.cedula,
            asistencias,
            inasistencias,
            diasNoLaborables,
            totalDias: fechas.length
        });
    }

    return resumen;
}


function usoValidar(asistencias, resumen = false){
    // Ejemplo de uso

    const resultadoValidacion = validarAsistencias(asistencias);
    if(resumen){

        console.log('Validación:', resultadoValidacion);

        // Obtener resumen
        if (resultadoValidacion.valido) {
            const resumen = resumenAsistencias(asistencias);
            console.log('Resumen:', resumen);
        }

    }

    return resultadoValidacion
    
}