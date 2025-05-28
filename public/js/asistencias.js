
// evento onload
let departamento = document.getElementById("departamento");
let turno = document.getElementById("turno");
let fecha = document.getElementById("fecha");
document.addEventListener("DOMContentLoaded", async function(){
    const modalEliminar = document.getElementById('modal-eliminar')
    // quito el evento del modal

    modalEliminar.removeEventListener('show.bs.modal',modalEliminar.eventModal);



    // TODO quitar esta parte
    document.querySelector("#departamento").value = "1";
    document.querySelector("#turno").value = "Mañana";

    // agrego la fecha actual al campo de fecha
    document.querySelector("#fecha").value = new Date().toISOString().split("T")[0];

    // evento submit para el formulario
    document.querySelector("#form-table-asistencias").addEventListener("submit", async function(e){
        if(document.querySelector("#form-table-asistencias").sending) return;
        e.preventDefault();
        let objSend = {
            fecha: document.querySelector("#fecha").value,
            turno: document.querySelector("#turno").value,
            idDepartamento: document.querySelector("#departamento").value,
            trabajadores: getRowsObj(),
            action:"Registrar"
        };

        if(objSend.trabajadores === false) {
            mostrarError("No se pudo enviar la información");
            return;
        };

        if(!Array.isArray(objSend.trabajadores) || objSend.trabajadores.length === 0) {
            mostrarAdvertencia("No hay registros de asistencias nuevos para guardar");
            return;
        };

        // TODO validaciones cliente

        let response = await peticion("/Asistencias/Registrar",{
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(objSend),
            useLoader: "body",
            before: () => {
                document.querySelector("#form-table-asistencias").sending = true;
                
            },
            after: () => {
                delete document.querySelector("#form-table-asistencias").sending;
            }
        });

        if(response = parsearJson(response)) {
            if(response.success) {
                mostrarExito(response.message);
                cargarDepartamentos();
            }
            else {
                mostrarError(response.message);
            }
        }
    });
    
    // evento onchange para los selectores principales
    [departamento, turno, fecha].forEach(input => {
        input.addEventListener("change", () => {
            document.querySelector("#form-table-asistencias table tbody").innerHTML = "";
            document.getElementById("tabla-asistencias").classList.add("d-none");
        });
    });


    // Evento para eliminar las asistencias desde el boton #eliminar-asistencias
    document.querySelector("#eliminar-asistencias").addEventListener("click", async function(e){
        e.preventDefault();
        abrirModalEliminar("Eliminar la asistencia seleccionada").then(async() => {
            if(document.querySelector("#fechaAsistencia").value === "") {
                mostrarError("La fecha de asistencia no esta seleccionada o no esta registrada");
                return;
            }

            // valido que el valor del input sea un numero
            if(isNaN(document.querySelector("#fechaAsistencia").value)) {
                mostrarError("La fecha de asistencia no es valida");
                return;
            }

            let objSend = {
                idAsistencia: document.querySelector("#fechaAsistencia").value,
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

    // evento para el input #fechaAsistencia si hay un valor muestra el boton de eliminar (onchange)
    document.querySelector("#fechaAsistencia").addEventListener("change", () => {
        if(document.querySelector("#fechaAsistencia").value !== "") {
            // si el boton existe le quita la clase d-none al padre
            if(document.querySelector("#eliminar-asistencias")){
                document.querySelector("#eliminar-asistencias").parentNode.classList.remove("d-none");
            }
        }
        else {
            // si el boton existe le agrega la clase d-none al padre
            if(document.querySelector("#eliminar-asistencias")){
                document.querySelector("#eliminar-asistencias").parentNode.classList.add("d-none");
            }
        }
    })


    
    
});










// Functions ************************************************************

    async function cargarDepartamentos(control = true) {

        let departamento = document.getElementById("departamento");
        let turno = document.getElementById("turno");
        let fecha = document.getElementById("fecha");
        let btnCargar = document.getElementById("btn-cargar");



        [departamento, turno, fecha].forEach(input => {
            input.setValidStatus();
        })

        if(control){
            // validar los campos

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
                    document.querySelector("#form-table-asistencias table tbody").innerHTML = "";
                    document.getElementById("tabla-asistencias").classList.add("d-none");
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

            generateTable(response.listaTrabajadores);
            document.querySelector("#fechaAsistencia").value = response.fechaAsistencia;
            document.querySelector("#fechaAsistencia").dispatchEvent(new Event("change"));


            document.getElementById("tabla-asistencias").classList.remove("d-none");
            
        }
        else {

            [departamento, turno, fecha].forEach(input => {
                input.value = "";
            })
            document.querySelector("#form-table-asistencias table tbody").innerHTML = "";
            document.getElementById("tabla-asistencias").classList.add("d-none");

        }


       


    }


    /**
     * funcion que devuelve una fila de la tabla de asistencias
     * si la fecha de la asistencia ya fue registrada se agregan cambios extra a la fila:
     *  1. se agrega la clase "no-aplica" a la celda de inasistencia
     * @param {object} obj trabajador
     * @param {number} i
     * @param {boolean} fecha_registrada_control si la fecha de la asistencia ya fue registrada
     */
    function tableRow ( obj, i , fecha_registrada_control) {
        //split(":", 2).join(":")

        let registrado = (fecha_registrada_control === true && obj.registro != "" ) ? true : false;

        let no_aplica = (fecha_registrada_control === true && obj.registro == "" ) ? true : false;
        // parametros para el modal tipo url get
        

        return `
                <td class="align-content-center nombre">
                <span class="">CI: ${obj.cedula}</span>
                <span>
                    ${obj.nombre}
                </span>
                </td>
                <td class="cell-inasistencia align-content-center text-center px-3 ${(no_aplica) ? "no-aplica" : ""} ">
                    <label class="py-2 text-nowrap no-select cursor-pointer d-flex align-items-center justify-content-between inasistencia-label">
                        <span>Inasistencia</span>
                        <input type="checkbox" id="inasistencia-check-${i}" class="inasistencia-check" name="inasistencia[]" ${(obj.status)? (parseInt(obj.status) == 1 ) ? "checked" : "" : ""}>
                        <div class="check-feedback ms-1"></div>
                    </label>
                    <label class="py-2 text-nowrap no-select cursor-pointer d-flex align-items-center justify-content-between check-radio-like">
                        <span>No aplica</span>
                        <input type="checkbox" id="no-aplica-check-${i}" class="no-aplica-check" name="no-aplica[]" ${(no_aplica) ? "checked" : ""}>
                        <div class="check-feedback ms-1"></div>
                    </label>
                </td>
                <td class="cell-horas w-100 align-content-center">
                    <div class="d-flex">
                        <div class="w-50 pe-1">
                            <label class="form-label text-nowrap" for="hora_entrada-${i}">Hora de Entrada</label>
                            <input type="time" class="form-control" id="hora_entrada-${i}" name="hora_entrada[]" data-form-text="invalid-span-hora_entrada-${i}" value="${ (obj.fechaIn) ? obj.fechaIn.split(":", 2).join(":"): "" }">
                            <div id="invalid-span-hora_entrada-${i}" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="w-50 pe-1">
                            <label class="form-label text-nowrap" for="hora_salida-${i}">Hora de salida</label>
                            <input type="time" class="form-control" id="hora_salida-${i}" name="hora_salida[]" data-form-text="invalid-span-hora_salida-${i}" value="${ (obj.fechaOut) ? obj.fechaOut.split(":", 2).join(":"): "" }">
                            <div id="invalid-span-hora_salida-${i}" class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                </td>
                <td class="cell-justificacion w-100 align-content-center">
                    <div class="d-flex">
                        <div class="w-50 pe-1">
                            <label for="justificacion-select-${i}" class="form-label text-nowrap">Justificación</label>
                                <select name="justificacion[]" class="form-select" id="justificacion-select-${i}" data-form-text="invalid-span-justificacion-${i}">
                                    <option value=""></option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 1 )? "selected"  :""} value="1">${justificacionesEnum[0]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 2 )? "selected"  :""} value="2">${justificacionesEnum[1]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 3 )? "selected"  :""} value="3">${justificacionesEnum[2]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 4 )? "selected"  :""} value="4">${justificacionesEnum[3]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 5 )? "selected"  :""} value="5">${justificacionesEnum[4]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 6 )? "selected"  :""} value="5">${justificacionesEnum[5]}</option>
                                    <option ${(obj.tipo_justificacion && obj.tipo_justificacion == 7 )? "selected"  :""} value="5">${justificacionesEnum[6]}</option>
                                </select>
                                <div id="invalid-span-justificacion-${i}" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="w-50 pe-1">
                            <label for="justificacion-descripcion-${i}" class="form-label text-nowrap">Descripción</label>
                            <input type="text" class="form-control" id="justificacion-descripcion-${i}" name="justificacion-descripcion[]" data-span="invalid-span-justificacion-descripcion-${i}" value="${(obj.observacion_justificacion)? obj.observacion_justificacion : ""}">
                            <div id="invalid-span-justificacion-descripcion-${i}" class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                </td>
                <td class="no-aplica-cell text-center align-content-center">
                    No aplica la asistencia seleccionada 
                </td>

                
        `

    }


    function generateTable(obj){
        document.querySelector("#form-table-asistencias table tbody").innerHTML = "";
        document.querySelector("#submit-asistencias").disabled = false;
        let i = 0;
        let registrado_control = false; // si se encuentra un trabajador con registros de asistencias en la fecha se activa
        
        
        
        
        
        // response es un objeto, recorre las propiedades no iterables
        for(const [key, value] of Object.entries(obj)) {
            if(value.registro == 1) { // si hay almenos un registro los que no aplica se desactivan
                registrado_control = true;
                break;
            }
        }





        for (const [key, value] of Object.entries(obj)) {
            let tr = document.createElement("tr");
            // value es otro objeto ahora se deben parsear todos los valores de value a string
            for (const [keyDos, valueDos] of Object.entries(value)) {
                obj[key][keyDos] = String((valueDos) ? valueDos : "");
            }

            
            tr.innerHTML = tableRow(value, i++, registrado_control);
            tr.objTrabajador = value;
            document.querySelector("#form-table-asistencias table tbody").appendChild(tr);
        }
        if(i === 0) {
            let tr = document.createElement("tr");
            tr.innerHTML = `<td colspan="4" class="text-center">No se encontraron registros</td>`;
            document.querySelector("#submit-asistencias").disabled = true;
            document.querySelector("#form-table-asistencias table tbody").appendChild(tr);
        }
        


        // agrega evento al checkbox
        let checkList = document.querySelectorAll("input.inasistencia-check");
        checkList.forEach(check => {
            check.addEventListener("change", function(){
                let check = this;
                let td = check.closest("td");
                let tr = td.closest("tr");
                if (check.checked) {
                    td.classList.add("inasistencia-true");
                    // desabilita la hora de entrada y salida de la fila
                    tr.querySelector("input[name='hora_entrada[]']").disabled = true;
                    tr.querySelector("input[name='hora_salida[]']").disabled = true;
                    // habilita la justificacion de la fila
                    tr.querySelector("select[name='justificacion[]']").disabled = false;
                    tr.querySelector("input[name='justificacion-descripcion[]']").disabled = false;
                } else {
                    tr.querySelector("input[name='hora_entrada[]']").disabled = false;
                    tr.querySelector("input[name='hora_salida[]']").disabled = false;
                    tr.querySelector("select[name='justificacion[]']").disabled = true;
                    tr.querySelector("input[name='justificacion-descripcion[]']").disabled = true;
                    td.classList.remove("inasistencia-true");
                }
            })
            check.dispatchEvent(new Event("change"));
        });

        let no_aplica = document.querySelectorAll("input[id^='no-aplica-check-']");
        // si no aplica deshabilita todos los campos en la fila
        // si no esta checkeado habilita todos los campos y llama al evento change del checkbox .inasistencia-check
        no_aplica.forEach(check => {

            check.addEventListener("change", function(){
                let check = this;
                let td = check.closest("td");
                let tr = td.closest("tr");
                if (check.checked) {
                    // desabilita la hora de entrada y salida de la fila
                    td.classList.add("no-aplica");
                    tr.querySelector("input[name='hora_entrada[]']").disabled = true;
                    tr.querySelector("input[name='hora_salida[]']").disabled = true;
                    // habilita la justificacion de la fila
                    tr.querySelector("select[name='justificacion[]']").disabled = true;
                    tr.querySelector("input[name='justificacion-descripcion[]']").disabled = true;
                    tr.querySelector("input[name='inasistencia[]']").disabled = true;
                } else {
                    td.classList.remove("no-aplica");
                    tr.querySelector("input[name='hora_entrada[]']").disabled = false;
                    tr.querySelector("input[name='hora_salida[]']").disabled = false;
                    tr.querySelector("select[name='justificacion[]']").disabled = false;
                    tr.querySelector("input[name='justificacion-descripcion[]']").disabled = false;
                    tr.querySelector("input[name='inasistencia[]']").disabled = false;
                    tr.querySelector("input[name='inasistencia[]']").dispatchEvent(new Event("change"));

                }
            })
            check.dispatchEvent(new Event("change"));
        })

        let fields = document.querySelectorAll("#form-table-asistencias tr input:not(.inasistencia-check):not(input[name='hora_entrada[]']), #form-table-asistencias tr select");
        fields.forEach(field => {
            field.addEventListener("change", () => {
                field.setValidStatus();
            })
        })
        document.querySelectorAll("#form-table-asistencias tr input[name='hora_entrada[]']").forEach(field => {
            field.addEventListener("change", () => {
                let selfSalida = field.closest("tr").querySelector("input[name='hora_salida[]']");
                field.setValidStatus();
                selfSalida.setValidStatus();
                selfSalida.min= field.value;
                selfSalida.value = "";
            })
        });
    }

    /**
     * funcion que devuelve un objeto con el departamento, el turno, la fecha y los valores de las form-table-asistencias
     */
    function getRowsObj() {
        let tbody = document.querySelector("#form-table-asistencias table tbody");
        let rows = tbody.querySelectorAll("tr");
        let rowsObj = [];
        for(let tr of rows) {
            let rowObj = {};
            tr.querySelector("select[name='justificacion[]']").setValidStatus();

            rowObj.idAsistencia = tr.objTrabajador.idAsistencia ? tr.objTrabajador.idAsistencia : null;
            rowObj.idTrabajador = tr.objTrabajador.idTrabajador;

            rowObj.inasistencia = (tr.querySelector("input.inasistencia-check").checked) ? 1 : 0 ;
            rowObj.no_aplica = (tr.querySelector("input.no-aplica-check").checked) ? 1 : 0 ;
            
            

            if(rowObj.inasistencia == 1){ 
                rowObj.fechaIn = "";
                rowObj.fechaOut = "";
                rowObj.justificacion = tr.querySelector("select[name='justificacion[]']").value;
                rowObj.justificacion_descripcion = tr.querySelector("input[name='justificacion-descripcion[]']").value;

                if(rowObj.justificacion == ""){
                    tr.querySelector("select[name='justificacion[]']").setValidStatus(false, "seleccione una justificacion");
                    tr.querySelector("select[name='justificacion[]']").focus();
                    

                    return;
                }
            }
            else{
                rowObj.fechaIn = tr.querySelector("input[name='hora_entrada[]']").value;
                rowObj.fechaOut = tr.querySelector(`input[name='hora_salida[]']`).value;
                rowObj.justificacion = "";
                rowObj.justificacion_descripcion = "";
                if(rowObj.fechaIn == "" && rowObj.fechaOut == ""){
                    rowObj.inasistencia = 1;
                }
                else if(rowObj.inasistencia == 1){
                    rowObj.fechaIn = "";
                    rowObj.fechaOut = "";
                }
            }

            // verifico si hay algun cambio con respecto al registro original
            let ok = 0;

            if(tr.objTrabajador.idAsistencia){

                tr.objTrabajador.status = (tr.objTrabajador.status== 1 || tr.objTrabajador.status== "1" ) ? 1 : 0;
                
                if( String(rowObj.inasistencia) !== String(tr.objTrabajador.status) ) ok = 1;
                if( String(rowObj.justificacion) !== String(tr.objTrabajador.tipo_justificacion) ) ok = 1;
                if( String(rowObj.justificacion_descripcion) !== String(tr.objTrabajador.observacion_justificacion) ) ok = 1;
                if( String(rowObj.fechaIn) !== String(tr.objTrabajador.fechaIn.split(":", 2).join(":")) ) ok = 1;
                if( String(rowObj.fechaOut) !== String(tr.objTrabajador.fechaOut.split(":", 2).join(":")) ) ok = 1;
                
            }
            if(rowObj.no_aplica && tr.objTrabajador.idAsistencia) ok = 1;

            if(ok) {
                rowObj.original = tr.objTrabajador;
            }

            if(
                rowObj.fechaIn == "" && /^\d\d:\d\d$/.test(rowObj.fechaOut) ||
                rowObj.fechaOut == "" && /^\d\d:\d\d$/.test(rowObj.fechaIn)
            ){
                rowObj.inconclusa = 1;
            }


            rowObj.modify = ok;

            //console.log(rowObj.modify, rowObj.idAsistencia);

            if((!rowObj.modify && rowObj.idAsistencia !== null) || ( rowObj.no_aplica && rowObj.idAsistencia === null ) ) continue;
                
            
            rowsObj.push(rowObj);
        }
        return rowsObj;
    }

    

