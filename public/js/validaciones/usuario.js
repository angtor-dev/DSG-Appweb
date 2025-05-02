// TODO pasar formulario a ajax


// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/
const regCedula = /^[0-9]{7,8}$/


// validaciones
function validarNombre() {
    const iNombre = document.getElementById('nombre')
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

function validarApellido() {
    const iApellido = document.getElementById('apellido')
    let valor = iApellido.value.trim()
    const elTexto = iApellido.parentElement.querySelector('.form-text')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iApellido.classList.add('is-invalid')
        return false
    }
    if (!regAlfanumerico.test(valor)) {
        elTexto.textContent = "Solo puede contener letras y números"
        iApellido.classList.add('is-invalid')
        return false
    }
    iApellido.classList.remove('is-invalid')
    iApellido.classList.add('is-valid')
    return true
}

function validarClave() {
    const iClave = document.getElementById('clave')
    let valor = iClave.value.trim()
    const elTexto = iClave.parentElement.parentElement.parentElement.querySelector('.form-text')
    elTexto.classList.add('d-block')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iClave.classList.add('is-invalid')
        return false
    }
    if (!regClave.test(valor)) {
        elTexto.textContent = "La clave debe contener al menos una letra y un numero, y ser 6 caracteres de longitud"
        iClave.classList.add('is-invalid')
        return false
    }
    iClave.classList.remove('is-invalid')
    iClave.classList.add('is-valid')
    elTexto.classList.remove('d-block')
    return true
}




function agregarValidaciones() {

    

    // TODO deshabilitar el submit
    // formulario
    const formulario = document.getElementById('form-usuario')
    // campos
    const iNombre = document.getElementById('nombre')
    const iCorreo = document.getElementById('correo')
    const iIdRol = document.getElementById('idRol')
    const iClave = document.getElementById('clave')
    const iCedula = document.getElementById('cedula')
    const iDepartamento = document.getElementById('departamento')
    const isubmit = document.getElementById('submit-modal')
    const iGenerador = document.getElementById('generarClave-btn')

    // validar al desenfocar campo o al enviar formulario
    iClave.addEventListener('blur', validarClave)

    console.log(iCedula)


      iCedula.onkeyup= async function(e){
        [iCorreo, iIdRol, iClave].forEach(element => {
            element.disabled = true
            element.setValidStatus();
            element.value = ""
        })
        iNombre.textContent = ""
        iDepartamento.textContent = ""
        this.setValidStatus();
        isubmit.disabled = true
        iGenerador.disabled = true



        if(regCedula.test(this.value)){
            let data = await peticion(`/Usuarios/Registrar?cedula=${this.value}`)
            data = JSON.parse(data)
            console.log(data)
            if(data.cedula && !data.usuario){

                iCedula.setValidStatus(true)

                iNombre.textContent = data.nombre
                iDepartamento.textContent = data.departamento

                iCorreo.disabled = false
                iIdRol.disabled = false
                iClave.disabled = false
                isubmit.disabled = false
                iGenerador.disabled = false


                
            }
            else{
                iCedula.setValidStatus(false,(data.usuario? "El usuario ya existe" : "El trabajador no existe"))
                // document.getElementById("btn-submit-registrar").disabled = false;
            }
        }
        else{
            iCedula.setValidStatus(false,"La cedula debe ser de 7 u 8 digitos")
        }
    }

    
    formulario.addEventListener('submit', event => {
        if (!validarNombre() || !validarClave() || !validarApellido()) {
            event.preventDefault()
            event.stopPropagation()
        }
    })
}