// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/

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

function validarDescripcion() {
    const iDescripcion = document.getElementById('descripcion')
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

function validarCategoria() {
    const iCategoria = document.getElementById('idCategoria')
    const elTexto = iCategoria.parentElement.parentElement.querySelector('.form-text')
    const valor = parseInt(iCategoria.value, 10)

    if (!iCategoria || isNaN(valor) || valor <= 0) {
        elTexto.textContent = "Debe seleccionar una categoría válida"
        iCategoria.classList.add('is-invalid')
        return false
    }
    iCategoria.classList.remove('is-invalid')
    iCategoria.classList.add('is-valid')
    return true
}

function validarMedida() {
    const iMedida = document.getElementById('idMedida')
    const elTexto = iMedida.parentElement.parentElement.querySelector('.form-text')
    const valor = parseInt(iMedida.value, 10)

    if (!iMedida || isNaN(valor) || valor <= 0) {
        elTexto.textContent = "Debe seleccionar una categoría válida"
        iMedida.classList.add('is-invalid')
        return false
    }
    iMedida.classList.remove('is-invalid')
    iMedida.classList.add('is-valid')
    return true
}

function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-articulo')
    // campos
    const iNombre = document.getElementById('nombre')
    const iDescripcion = document.getElementById('descripcion')
    const iCategoria = document.getElementById('idCategoria')
    const iMedida = document.getElementById('idMedida')

    // validar al desenfocar campo o al enviar formulario
    iNombre.addEventListener('blur', validarNombre)
    iDescripcion.addEventListener('blur', validarDescripcion)
    iCategoria.addEventListener('blur', validarCategoria)
    iMedida.addEventListener('blur', validarMedida)
    
    formulario.addEventListener('submit', event => {
        if (!validarNombre() || !validarDescripcion() || !validarCategoria() || !validarMedida()) {
            event.preventDefault()
            event.stopPropagation()
        }
    })
}