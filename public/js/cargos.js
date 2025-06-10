// evento onload 
document.addEventListener("DOMContentLoaded", async function(){
    cargarCargos();
});


function cargarCargos(){
   peticionPromisse("/Cargos",{
        method: "POST",
        body: JSON.stringify({action: "LoadCargos"}),
        useLoader: "body"
        
    }).then(res => {
        let jsonRes = JSON.parse(res);
        
        if(jsonRes.success){
            renderCargos(jsonRes.data,{
                actualizar: jsonRes.actualizar,
                eliminar: jsonRes.eliminar
            });
        }
        else {
            mostrarError(jsonRes.message);
        }
    });

    
}

/**
 * Renders a list of cargos into an HTML table.
 *
 * @param {Array} cargos - An array of cargo objects to be rendered.
 * The function clears the current table body and creates a new row for each
 * cargo object, appending it to the table's body.
 */

function renderCargos(cargos,permisos = null){
    
    let table = document.querySelector("#tabla-cargos");
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
                        div.setAttribute("data-bs-url",`${LOCAL_DIR}/Cargos/Actualizar?id=${data.id}`);
                    }
                }
            )

        }
        if(permisos.eliminar){
            botones.push({icono:"fa-trash-can",accion:"Eliminar"
                ,function: function(button,data){
                    button.addEventListener("click",function(){
                        abrirModalEliminar("Eliminar el cargo "+data.nombre).then(() => {
                            peticionPromisse("/Cargos/Eliminar",{
                                method: "POST",
                                body: JSON.stringify({id: data.id,accion: "Eliminar"}),
                                useLoader: "body"
                            }).then(res => {
                                res = parsearJson(res);
                                if(res.success){
                                    mostrarExito(res.message);
                                    cargarCargos();
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

    optionObj = {
        columns: [
            {data: "id"},
            {data: "nombre"},
            {data: "nivel"},
        ],
        data: cargos,
        botones: botones,
        enableActionsColumn: true,
        
    }
    
    
    renderDataTable("#tabla-cargos",optionObj);
}

function agregarValidaciones(){
    const form = document.querySelector("#form-cargo");
    if(!form){
        console.error("No se encontro el formulario");
        return false;
    }
    const nombre = form.querySelector("#form-nombre");
    const nivel = form.querySelector("#form-nivel");

    form.addEventListener("submit",function(e){
        console.log("enviando");
        e.preventDefault();
        if(form.sending){
            return false;
        }
        nombre.setValidStatus();
        nivel.setValidStatus();

        const validarNombre = ()=>{
            const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9.,\s]*$/
            let ok = true;
            let valor = nombre.value.trim();
            if (valor.length <= 0) {
                nombre.setValidStatus(false, "Este campo es obligatorio");
                ok = false;
            }
            if (!regAlfanumerico.test(valor)) {
                nombre.setValidStatus(false, "Solo puede contener letras y números");
                ok = false;
            }
            return ok;
        }

        const validarNivel = ()=>{
            const numerico = /^[0-9]+$/
            let ok = true;
            let valor = nivel.value.trim();
            if (valor.length <= 0) {
                nivel.setValidStatus(false, "Este campo es obligatorio");
                ok = false;
            }
            if (!numerico.test(valor)) {
                nivel.setValidStatus(false, "Solo puede contener números");
                ok = false;
            }
            return ok;
        }

        if(!validarNombre() || !validarNivel()){
            return false;
        }

        let url = "/Cargos"+form.action.match(/(\/Actualizar.*|\/Registrar)$/)[1];
        let datos = new FormData(form);
        datos.append("accion", /Actualizar/.test(url) ? "Actualizar" : "Registrar");
        peticionPromisse(url, {
            method: "POST",
            body: datos.text(),
            useLoader: "body",
            before:function(){
                form.sending = true;
            },
            after:function(){
                form.sending = false;
            }

        }).then(res => {
            let jsonRes = parsearJson(res);
            if (jsonRes.success) {
                mostrarExito(jsonRes.message);
                form.closest(".modal").querySelector("button[data-bs-dismiss='modal']").click();
                cargarCargos();
            } else {
                mostrarError(jsonRes.message);
            }
        })

        
    });
}