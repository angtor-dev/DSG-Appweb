// formulario
const formulario = document.getElementById('form-rol')
// campos
const iNombre = document.getElementById('nombre')
const iDescripcion = document.getElementById('descripcion')

// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/

// validaciones
function validarNombre() {
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

function validarDescripcion() {
    let valor = iDescripcion.value.trim()
    const elTexto = iDescripcion.parentElement.querySelector('.form-text')

    if (!regAlfanumerico.test(valor)) {
        elTexto.textContent = "Solo puede contener letras y números"
        iDescripcion.classList.add('is-invalid')
        return false
    }
    iDescripcion.classList.remove('is-invalid')
    iDescripcion.classList.add('is-valid')
    return true
}

// validar al desenfocar campo o al enviar formulario
iNombre.addEventListener('blur', validarNombre)
iDescripcion.addEventListener('blur', validarDescripcion)

formulario.addEventListener('submit', event => {
    if (!validarNombre() || !validarDescripcion()) {
        event.preventDefault()
        event.stopPropagation()
    }
})