// expresiones regulares
// TODO abortar cedula en registro
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/
const regCedula = /^[0-9]{7,8}$/
const regTelefono = /^[0-9]{11}$/


document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll(".accion-eliminar").forEach(el => {
        el.addEventListener("click", async () => {
            abrirModalEliminar(`Eliminar al trabajador ${el.dataset.trabajador} ${el.dataset.nombre}`).then(async () => {
                let response = await peticion("/Trabajadores/Eliminar", {
                    method: "POST",
                    body: JSON.stringify({
                        cedulaSeleccion: el.dataset.trabajador,
                        action: "Eliminar"
                    })
                })
                if (response = parsearJson(response)) {
                    if (response.success) {
                        mostrarExito(response.message)
                        mostrarLoader("body");
                        setTimeout(() => {
                            location.reload()
                        }, 1000);
                    } else {
                        mostrarError(response.message)
                    }
                }
            }).finally(() => {
                document.activeElement.blur();
                document.getElementById('modal-eliminar').Modal.hide();
            });
        })
    })
})



// validaciones
function validarNombre(id) {
    const iNombre = document.getElementById(id)
    let valor = iNombre.value.trim()
    const elTexto = iNombre.parentElement.querySelector('.form-text')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iNombre.classList.add('is-invalid')
        return false
    }
    if (!regAlfanumerico.test(valor)) {
        elTexto.textContent = "Solo puede contener letras y números"
        iNombre.classList.add('is-invalid')
        return false
    }
    iNombre.classList.remove('is-invalid')
    iNombre.classList.add('is-valid')
    return true
}
function validarSelect(id) {
    const iSelect = document.getElementById(id)
    let valor = iSelect.value.trim()
    const elTexto = iSelect.parentElement.querySelector('.form-text')

    if (valor.length <= 0 || iSelect.disabled == true) {
        elTexto.textContent = "Este campo es obligatorio"
        iSelect.classList.add('is-invalid')
        return false
    }
    iSelect.classList.remove('is-invalid')
    iSelect.classList.add('is-valid')
    return true
}
function validarCedula(id) {
    const iCedula = document.getElementById(id)
    let valor = iCedula.value.trim()
    const elTexto = iCedula.parentElement.querySelector('.form-text')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iCedula.classList.add('is-invalid')
        return false
    }
    if (!regCedula.test(valor)) {
        elTexto.textContent = "La cedula debe ser de 7 u 8 digitos"
        iCedula.classList.add('is-invalid')
        return false
    }
    iCedula.classList.remove('is-invalid')
    iCedula.classList.add('is-valid')
    return true
}
function validarTelefono(id) {
    const iTelefono = document.getElementById(id)
    let valor = iTelefono.value.trim()
    const elTexto = iTelefono.parentElement.querySelector('.form-text')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iTelefono.classList.add('is-invalid')
        return false
    }
    if (!regTelefono.test(valor)) {
        elTexto.textContent = "El telefono debe ser de 11 digitos"
        iTelefono.classList.add('is-invalid')
        return false
    }
    iTelefono.classList.remove('is-invalid')
    iTelefono.classList.add('is-valid')
    return true
}

function validarFecha(id) {
    const iFecha = document.getElementById(id)
    let valor = iFecha.value.trim()
    iFecha.setValidStatus();
    if(/\d{4}-\d{2}-\d{2}/.test(valor)) {
        iFecha.setValidStatus(true)
        return true
    }
    else {
        iFecha.setValidStatus(false, "Formato de fecha incorrecto")
        return false
    };
}

function actualizarTrabajador (){

}







function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-trabajador')
    // campos
    const iNombre = document.getElementById('nombre')
    const iApellido = document.getElementById('apellido')
    const iCedula = document.getElementById('cedula')
    const iCargo = document.getElementById('cargo')
    const iTurno = document.getElementById('turno')
    const iDepartamento = document.getElementById('departamento')
    const iTelefono = document.getElementById('telefono')
    const iFechaIngreso = document.getElementById('fecha_ingreso')
    // validar al desenfocar campo o al enviar formulario

    iNombre.addEventListener('blur', () => {validarNombre('nombre')})
    iApellido.addEventListener('blur', () => {validarNombre('apellido')})
    iCargo.addEventListener('change', () => {validarSelect('cargo')})
    iTurno.addEventListener('change', () => {validarSelect('turno')})
    iDepartamento.addEventListener('change', () => {validarSelect('departamento')})
    iTelefono.addEventListener('blur', () => {validarTelefono('telefono')})
    iFechaIngreso.addEventListener('change', () => {validarFecha('fecha_ingreso')})

    // carga los campos desde el local storage
    


    if(/Registrar$/.test(formulario.action)){
        iCedula.onkeyup= async function(e){
            iNombre.disabled = true
            iApellido.disabled = true
            iCargo.disabled = true
            iTurno.disabled = true
            iDepartamento.disabled = true
            iTelefono.disabled = true
            iFechaIngreso.disabled = true
            document.getElementById("btn-submit-registrar").disabled = true


            if(regCedula.test(this.value)){
                let data = await peticion(`/Trabajadores/Registrar?cedula=${this.value}`)
                data = JSON.parse(data)
                console.log(data)
                if(data.cedula){
                    iCedula.classList.add('is-invalid')
                    iCedula.classList.remove('is-valid')
                    iCedula.parentElement.querySelector('.form-text').textContent = "La cedula ya se encuentra registrada"
                    
                }
                else{
                    iCedula.classList.remove('is-invalid')
                    iCedula.classList.add('is-valid')
                    iCedula.parentElement.querySelector('.form-text').textContent = ""
                    iNombre.disabled = false
                    iApellido.disabled = false
                    iCargo.disabled = false
                    iTurno.disabled = false
                    iDepartamento.disabled = false
                    iTelefono.disabled = false
                    iFechaIngreso.disabled = false
                    document.getElementById("btn-submit-registrar").disabled = false;
                    console.log("entro")
                }
            }
            else{
                iCedula.classList.add('is-invalid')
                iCedula.classList.remove('is-valid')
                iCedula.parentElement.querySelector('.form-text').textContent = "La cedula debe ser de 7 u 8 digitos"
            }
        }
        
    }
    
    formulario.addEventListener('submit',async event => {


        console.log("entro formulario");
        event.preventDefault()
        event.stopPropagation()
        if (
            validarNombre('nombre') ||
            validarNombre('apellido') ||
            validarSelect('cargo') ||
            validarSelect('turno') ||
            validarCedula('cedula') ||
            validarSelect('departamento') ||
            validarTelefono('telefono') ||
            validarFecha('fecha_ingreso')
        ) {

            let datos = new FormData(formulario);

            let url = formulario.action
            url = url.replace(/^.*\/DSG-Appweb/, "")

            datos.append("action", (/Registrar$/.test(url)) ? "Registrar" : "Actualizar");

            let respuesta = await peticion(url, {
                method: 'POST',
                body: datos.text(),
                useLoader: 'body',
                blur: true,
            });

            respuesta = parsearJson(respuesta);

            if (respuesta.success) {
                mostrarLoader("body");
                location.reload();
            } else {
                mostrarError(respuesta.message);
            }
            
        }
    })


    


    
    
}