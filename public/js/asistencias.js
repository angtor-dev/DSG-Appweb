
// evento onload
let departamento = document.getElementById("departamento");
let turno = document.getElementById("turno");
let fecha = document.getElementById("fecha");
let preventLostControl = false;
document.addEventListener("DOMContentLoaded", async function(){
    window.addEventListener('beforeunload', preventLost);
    const modalEliminar = document.getElementById('modal-eliminar')
    // quito el evento del modal

    modalEliminar.removeEventListener('show.bs.modal',modalEliminar.eventModal);



    // quitar esta parte
    // document.querySelector("#departamento").value = "5";
    // document.querySelector("#turno").value = "5";

    // agrego la fecha actual al campo de fecha
    document.querySelector("#fecha").value = new Date().toISOString().split("T")[0];
    document.querySelector("#fecha").value = "2025-11-15";


    // evento submit para el formulario
    document.querySelector("#form-table-asistencias").addEventListener("submit", async function(e){
        if(document.querySelector("#form-table-asistencias").sending) return;
        e.preventDefault();
        
        let objSend = {
            fecha: document.querySelector("#fecha").value,
            turno: document.querySelector("#turno").value,
            idDepartamento: document.querySelector("#departamento").value,
            trabajadores: getTrabajadoresObject(),
            action:"Registrar"
        };

        if(objSend.trabajadores === false) {
            console.error("no se puede listar los datos del trabajador");
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
                preventLostControl = false;
                cargarDepartamentos(true,false);


                

            }
            else {
                mostrarError(response.message);
            }
        }
    });

    
    // evento onchange para los selectores principales
    [departamento, turno, fecha].forEach(input => {
        input.addEventListener("change", (e) => {
            if(preventLostControl){
                if(confirm("Se perderan los cambios realizados. ¿Desea continuar?")) {
                    preventLostControl = false;
                }
                else {
                    if(input.prevValue) input.value = input.prevValue;
                    e.preventDefault();
                    return;
                }
            }
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
            if(!/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/.test(document.querySelector("#fechaAsistencia").value)) {
                mostrarError("La fecha de asistencia no es valida");
                return;
            }

            let objSend = {
                fecha: document.querySelector("#fechaAsistencia").value,
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
                    consultarDia: true
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


            if(response.success){

                [departamento, turno, fecha].forEach(input => {
                    input.prevValue = input.value;
                })

                generateTable(response.listaTrabajadores, response.turnoHorario);
                document.querySelector("#fechaAsistencia").value = response.fechaAsistencia;
                document.querySelector("#fechaAsistencia").dispatchEvent(new Event("change"));


                document.getElementById("tabla-asistencias").classList.remove("d-none");
                let primeratd = document.querySelector("#tabla-asistencias table tbody tr > td");
                if(primeratd.getAttribute("colspan") === "4") {
                    preventLostControl = false;
                }
                else preventLostControl = preventlostDefault;

                // TODO arreglar esto para que el mensaje aparezca si hay algun cambio despues de guardar 

            }
            else {
                mostrarError(response.message);
            }

            
        }
        else {

            [departamento, turno, fecha].forEach(input => {
                input.value = "";
            })
            document.querySelector("#form-table-asistencias table tbody").innerHTML = "";
            document.getElementById("tabla-asistencias").classList.add("d-none");
            preventLostControl = false;

        }


       


    }


    /**
     * funcion que devuelve una fila de la tabla de asistencias
     * si la fecha de la asistencia ya fue registrada se agregan cambios extra a la fila:
     * @param {object} obj trabajador
     * @param {number} i
     * @param {boolean} fecha_registrada_control si la fecha de la asistencia ya fue registrada
     */
    function tableRow ( obj, i ,horario, fecha_registrada_control) {

        console.log(obj);


        let hora_entrada = obj.horaEntrada ? obj.horaEntrada : (horario.hora_entrada) ? horario.hora_entrada : "";
        let hora_salida = obj.horaSalida ? obj.horaSalida : (horario.hora_salida) ? horario.hora_salida : "";
        if(hora_entrada != "" && hora_salida != "") {
            hora_entrada = hora_entrada.split(":", 2).join(":");
            hora_salida = hora_salida.split(":", 2).join(":");
        }

        let justificacionOptions = "";
        for (const [key, value] of Object.entries(justificacionesEnum)) {
            justificacionOptions += `<option ${(obj.tipo_justificacion && obj.tipo_justificacion == value) ? "selected" : ""} value="${value}">${key}</option>`;
        }        

       

        return `
                <td class="align-content-center nombre">
                    <span class="">${obj.cedula}</span>
                </td>
                <td class="align-content-center nombre">
                    <span>
                        ${obj.nombre} ${obj.apellido}
                    </span>
                </td>
                <td class="cell-inasistencia align-content-center text-center px-3">
                    <label class="py-2 text-nowrap no-select cursor-pointer d-flex align-items-center justify-content-between inasistencia-label">
                        <span>Inasistencia</span>
                        <input type="checkbox" id="inasistencia-check-${i}" class="inasistencia-check" name="inasistencia[]" ${(obj.Es_Asistencia && obj.Es_Asistencia != "1")? "checked" : ""}>
                        <div class="check-feedback ms-1"></div>
                    </label>
                </td>
                <td class="cell-horas w-100 align-content-center">
                    <div class="d-flex">
                        <div class="w-50 pe-1">
                            <label class="form-label text-nowrap" for="hora_entrada-${i}">Hora de Entrada</label>
                            <input required type="time" class="form-control hora-entrada-cl" id="hora_entrada-${i}" name="hora_entrada[]" data-form-text="invalid-span-hora_entrada-${i}" value="${hora_entrada}">
                            <div id="invalid-span-hora_entrada-${i}" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="w-50 pe-1">
                            <label class="form-label text-nowrap" for="hora_salida-${i}">Hora de salida</label>
                            <input required type="time" class="form-control hora-salida-cl" id="hora_salida-${i}" name="hora_salida[]" data-form-text="invalid-span-hora_salida-${i}" value="${ hora_salida}">
                            <div id="invalid-span-hora_salida-${i}" class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                </td>
                <td class="cell-justificacion w-100 align-content-center">
                    <div class="d-flex">
                        <div class="w-50 pe-1">
                            <label for="justificacion-select-${i}" class="form-label text-nowrap">Justificación</label>
                                <select required name="justificacion[]" class="form-select justificacion-cl" id="justificacion-select-${i}" data-form-text="invalid-span-justificacion-${i}">
                                    <option value=""></option>
                                    ${justificacionOptions}
                                </select>
                                <div id="invalid-span-justificacion-${i}" class="form-text invalid-feedback"></div>
                        </div>
                        <div class="w-50 pe-1">
                            <label for="justificacion-descripcion-${i}" class="form-label text-nowrap">Descripción</label>
                            <input maxlength="255" type="text" class="form-control" id="justificacion-descripcion-${i}" name="justificacion-descripcion[]" data-span="invalid-span-justificacion-descripcion-${i}" value="${(obj.descripcion_justificacion)? obj.descripcion_justificacion : ""}">
                            <div id="invalid-span-justificacion-descripcion-${i}" class="form-text invalid-feedback"></div>
                        </div>
                    </div>
                </td>
        `;

    }


    function generateTable(obj, horario){

        
        let tbody = document.querySelector("#form-table-asistencias table tbody");
        tbody.innerHTML = "";
        document.querySelector("#submit-asistencias").disabled = false;
        let i = 0;
        let registrado_control = false; // si se encuentra un trabajador con registros de asistencias en la fecha se activa
        
        
        
        
        
        // response es un objeto, recorre las propiedades no iterables
        for(const [key, value] of Object.entries(obj)) {
            // si hay almenos un registro se considera que la fecha esta registrada
            if(value.registro == 1) { 
                registrado_control = true;
                break;
            }
        }




        // crea las filas
        for (const [key, value] of Object.entries(obj)) {
            let tr = document.createElement("tr");

            
            // paso a string los valores
            for (const [keyDos, valueDos] of Object.entries(value)) {
                obj[key][keyDos] = String((valueDos) ? valueDos : "");
                if(keyDos == "Es_Asistencia" && valueDos == "") {
                    obj[key][keyDos] = "0";
                }
            }

            
            tr.innerHTML = tableRow(value, i++,horario, registrado_control);
            tr.objTrabajador = value;
            tbody.appendChild(tr);
        }
        if(i === 0) {
            let tr = document.createElement("tr");
            tr.innerHTML = `<td colspan="4" class="text-center">No se encontraron registros</td>`;
            document.querySelector("#submit-asistencias").disabled = true;
            tbody.appendChild(tr);
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
        // eventos a los inputs
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
    function getTrabajadoresObject(){
        let tbody = document.querySelector("#form-table-asistencias table tbody");
        let rows = tbody.querySelectorAll("tr");
        let trabajadores = [];
        
        let isset = (variable) => {
            if(typeof variable !== "undefined") return variable;
            else return "";
        };

            // procedure variables necesarias
            // * IN p_id_asistencia_inasistencia INT, -- NULL para nuevo registro, valor para actualización
            // * IN p_fecha DATE, -- Fecha de la asistencia/inasistencia
            // * IN p_id_asignacion_laboral INT, -- ID de la asignación laboral del trabajador
            // * IN p_tipo_registro ENUM('Asistencia', 'Inasistencia'), -- Tipo de registro
            // -- Parámetros para asistencia

            // * IN p_hora_entrada TIME, -- Solo para tipo 'Asistencia'
            // * IN p_hora_salida TIME, -- Solo para tipo 'Asistencia'
            // -- Parámetros para inasistencia
            // * IN p_tipo_inasistencia ENUM('Injustificado','Vacaciones','Medico','Emergencia','Judicial','Enfermedad','Muerte De Un Familiar','Otro'), -- Solo para tipo 'Inasistencia'
            // * IN p_descripcion TEXT, -- Solo para tipo 'Inasistencia'
        for(let tr of rows) {
            let fieldcheckInasistencia = tr.querySelector("input[name='inasistencia[]']");
            let fieldvalueHoraEntrada = tr.querySelector("input[name='hora_entrada[]']");
            let fieldvalueHoraSalida = tr.querySelector("input[name='hora_salida[]']");
            let fieldvalueJustificacion = tr.querySelector("select[name='justificacion[]']");
            let fieldvalueJustificacionDescripcion = tr.querySelector("input[name='justificacion-descripcion[]']");


            let valueCheckInasistencia = fieldcheckInasistencia.checked ? "2" : "1";
            let valueHoraEntrada = fieldvalueHoraEntrada.value;
            let valueHoraSalida = fieldvalueHoraSalida.value;
            let valueJustificacion = fieldvalueJustificacion.value;
            let valueJustificacionDescripcion = fieldvalueJustificacionDescripcion.value;

            if(valueCheckInasistencia == "2") {
                valueHoraEntrada = "";
                valueHoraSalida = "";
            }
            else{
                valueJustificacion = "";
                valueJustificacionDescripcion = "";
            }



            let trabajadorOrigen = tr.objTrabajador;

            if(!trabajadorOrigen.idTrabajador) return false;



            let objTrabajador = {
                idTrabajador : trabajadorOrigen.idTrabajador,
                idAsistencia_inasistencia : isset(trabajadorOrigen.idAsistencia_inasistencia),
                tipo_registro : valueCheckInasistencia,
                horaEntrada : valueHoraEntrada,
                horaSalida : valueHoraSalida,
                tipo_justificacion : valueJustificacion,
                descripcion_justificacion : valueJustificacionDescripcion,
                registrado : isset(trabajadorOrigen.registro)
            };
            // verificar si se ha modificado el registro
            // de asistencia u inasistencia, horas de entrada y salida
            // si asi fue se le agrega la propiedad modificado = 1 si no se le agrega la propiedad modificado = 0
            if(isset(trabajadorOrigen.registro) == "1"){

                let tipoAsistencia = (trabajadorOrigen.Es_Asistencia == "1") ? "1" : "2";

                objTrabajador.modificado = "0";

                if(
                    isset(trabajadorOrigen.horaEntrada.split(":", 2).join(":")) != valueHoraEntrada ||
                    isset(trabajadorOrigen.horaSalida.split(":", 2).join(":")) != valueHoraSalida ||
                    tipoAsistencia != valueCheckInasistencia ||
                    isset(trabajadorOrigen.tipo_justificacion) != valueJustificacion ||
                    isset(trabajadorOrigen.descripcion_justificacion) != valueJustificacionDescripcion
                ){
                    objTrabajador.modificado = "1";
                }

            }

            trabajadores.push(objTrabajador);


        }
        return trabajadores;
    }

    

