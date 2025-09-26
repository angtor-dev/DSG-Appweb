// nombre del turno
const turnoRegex = /^[A-Za-zá-úÁ-ÚñÑ\s]{1,50}$/;


document.addEventListener("DOMContentLoaded", async function(){
    cargarTurnos();
});


function cargarTurnos(){
   peticionPromisse("/Turnos",{
        method: "POST",
        body: JSON.stringify({action: "LoadTurnos"}),
        useLoader: "body"
        
    }).then(res => {
        let jsonRes = JSON.parse(res);
        
        if(jsonRes.success){
            renderTurnos(jsonRes.data,{
                actualizar: jsonRes.actualizar,
                eliminar: jsonRes.eliminar
            });
        }
        else {
            mostrarError(jsonRes.message);
        }
    });

    
}

function renderTurnos(turnos,permisos = null){

    let dias = {
        lunes: "Lunes",
        martes: "Martes",
        miercoles: "Miércoles",
        jueves: "Jueves",
        viernes: "Viernes",
        sabado: "Sábado",
        domingo: "Domingo"
    };
    /**
      turnos = [
        {"id":1,"nombre":"Mañana","lunes":"1","martes":"1","miercoles":"1","jueves":"1","viernes":"0","sabado":"0","domingo":"0"},
        {"id":2,"nombre":"Tarde","lunes":"0","martes":"0","miercoles":"0","jueves":"0","viernes":"1","sabado":"1","domingo":"1"}
      ]
     */
    turnos_Modificados = [];
    turnos.forEach(turno => {
        let arreglo = [];
        for(let dia in dias){
            if(turno[dia] == 1){
                arreglo.push(dias[dia]);
            }
            if(arreglo.length > 0){
                turno.dias = arreglo.join(", ");
            }
        }
        turnos_Modificados.push(turno);
    });

    turnos = turnos_Modificados;



    

    
    
    let table = document.querySelector("#tabla-turnos");
    let tbody = table.querySelector("tbody");
    tbody.innerHTML = "";

    let botones = [];
    if(permisos){
        if(permisos.actualizar){
            botones.push(
                {
                    icono:"fa-pen-to-square"
                    ,accion:"Editar"
                    ,function: function(button,data){
                        let div = button.getElementsByTagName("div")[0];
                        div.setAttribute("data-bs-toggle","modal");
                        div.setAttribute("data-bs-target","#modal-generico");
                        div.setAttribute("data-bs-url",`${LOCAL_DIR}/Turnos/Actualizar?id=${encodeURIComponent(data.id)}`);
                    }
                }
            )

        }
        if(permisos.eliminar){
            botones.push({icono:"fa-trash-can",accion:"Eliminar"
                ,function: function(button,data){
                    button.addEventListener("click",function(){
                        abrirModalEliminar("Eliminar el turno \""+data.nombre+'"').then((a) => {
                            peticionPromisse("/Turnos/Eliminar",{
                                method: "POST",
                                body: JSON.stringify({id: data.id,accion: "Eliminar"}),
                                useLoader: "body"
                            }).then(res => {
                                res = parsearJson(res);
                                if(res.success){
                                    mostrarExito(res.message);
                                    cargarTurnos();
                                }
                                else{
                                    mostrarError(res.message);
                                }
                            })
                        }).catch((a)=>{
                        })
                    })
                }
            })
        }
    }

    turnos = turnos.map(turno => {
        return {
            id: turno.id,
            nombre: turno.nombre,
            horario_entrada: turno.horario_entrada.replace(/:\d\d$/,""),
            horario_salida: turno.horario_salida.replace(/:\d\d$/,""),
            dias: turno.dias
        }
    });

    optionObj = {
        columns: [
            //{data: "id"},
            {data: "nombre"},
            {data: "horario_entrada"},
            {data: "horario_salida"},
            {data: "dias"},
        ],
        data: turnos,
        botones: botones,
        enableActionsColumn: true,
        
    }
    
    
    renderDataTable("#tabla-turnos",optionObj);
}


function agregarValidaciones(){
    const form = document.querySelector("#form-turno");
    if(!form){
        console.error("No se encontro el formulario");
        return false;
    }

    form.addEventListener("submit",function(e){
        e.preventDefault();
        if(form.sending){
            return false;
        }

        let nombre = form.querySelector("#form-nombre");
        let lunes = form.querySelector("#form-lunes");
        let martes = form.querySelector("#form-martes");
        let miercoles = form.querySelector("#form-miercoles");
        let jueves = form.querySelector("#form-jueves");
        let viernes = form.querySelector("#form-viernes");
        let sabado = form.querySelector("#form-sabado");
        let domingo = form.querySelector("#form-domingo");
        let hora_entrada = form.querySelector("#form-horaIn");
        let hora_salida = form.querySelector("#form-horaOut");
        [nombre,hora_entrada,hora_salida].forEach(input => {
            input.setValidStatus();
            input.addEventListener("change", function(){
                this.setValidStatus();
            })
        });

        const auxiliarValidarDias = function(){
            let ok = false;
            [lunes,martes,miercoles,jueves,viernes,sabado,domingo].forEach(dia => {
                if(dia.checked){
                    ok = true;
                }
            });
            if(!ok){
                mostrarError("Debe seleccionar al menos un dia");
            }
            return ok;
        }
        
        const auxiliarValidarnombre = function(){
            nombre.value = nombre.value.trim();
            nombre.setValidStatus();
            if(!turnoRegex.test(nombre.value) || nombre.value.length == 0){
                nombre.setValidStatus(false,"El nombre es invalido debe tener solo letras y espacios");
                return false;
            }
            nombre.setValidStatus(true);
            return true;
        }

        const auxiliarValidarHoras = function(){
            let ok = true;
            let horaRegex = /^([0-1][0-9]|2[0-3]):([0-5][0-9])$/;
            if(!horaRegex.test(hora_entrada.value)){
                hora_entrada.setValidStatus(false,"La hora debe estar en el formato HH:MM");
                ok = false;
            }
            if(!horaRegex.test(hora_salida.value)){
                hora_salida.setValidStatus(false,"La hora debe estar en el formato HH:MM");
                ok = false;
            }
            // validar que la hora de entrada sea menor a la de salida
            // pasando de string a date
            let hora_entrada_date = new Date("2000-01-01 "+hora_entrada.value);
            let hora_salida_date = new Date("2000-01-01 "+hora_salida.value);
            if(hora_entrada_date >= hora_salida_date){
                hora_salida.setValidStatus(false,"");
                hora_entrada.setValidStatus(false,"");
                mostrarError("La hora de salida debe ser mayor a la de entrada");
                ok = false;
            }
            return ok;
        }

        if(!auxiliarValidarnombre() || !auxiliarValidarDias() || !auxiliarValidarHoras()){
            return false;
        }

        //let url = /Actualizar\?id=[0-9]+$/.test(form.action) ? `/Turnos/Actualizar` : `/Turnos/Registrar`;
        let url = form.action.match(/(\/Turnos\/.*)$/)[1];
        let datos = new FormData(form);
        let accion = /Actualizar\?id=.+$/.test(form.action) ? "Actualizar" : "Registrar";
        datos.append("accion",accion);
        if(!confirm(`Estas seguro de ${accion} este turno?`)){
            return false;
        }
        peticionPromisse(url,{
            method: "POST",
            body: datos.text(),
            useLoader: "body",
            before: function(){
                form.sending = true;
            },

            after: function(){
                form.sending = false;
            }

        }).then((response) => {
            response = parsearJson(response);
            if(response.success){
                let jsonRes = response;
                mostrarExito(jsonRes.message);
                form.closest(".modal").querySelector("button[data-bs-dismiss='modal']").click();
                cargarTurnos();
                
            }
            else {
                mostrarError(response.message || "Ocurrio un error al registrar el turno");
            }
        }).catch((error) => {
            console.error(error);
        });

        
    })

}