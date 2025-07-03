// expresiones regulares
const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/

// validaciones
function validarCantidad() {
    const iCantidad = document.getElementById('cantidad')
    let valor = iCantidad.value
    const elTexto = iCantidad.parentElement.querySelector('.form-text')

    if (isNaN(valor) || valor.trim() === "") {
        elTexto.textContent = "Debe ingresar un número válido"
        iCantidad.classList.add('is-invalid')
        return false
    }
    if (valor == 0) {
        elTexto.textContent = "La cantidad no puede ser cero (0)"
        iCantidad.classList.add('is-invalid')
        return false
    }
    iCantidad.classList.remove('is-invalid')
    iCantidad.classList.add('is-valid')
    return true
}

function validarDescripcion() {
    const iDescripcion = document.getElementById('descripcion')
    let valor = iDescripcion.value.trim()
    const elTexto = iDescripcion.parentElement.querySelector('.form-text')

    if (valor.length <= 0) {
        elTexto.textContent = "Este campo es obligatorio"
        iDescripcion.classList.add('is-invalid')
        return false
    }
    if (!regAlfanumerico.test(valor)) {
        elTexto.textContent = "Solo puede contener letras y números"
        iDescripcion.classList.add('is-invalid')
        return false
    }
    iDescripcion.classList.remove('is-invalid')
    iDescripcion.classList.add('is-valid')
    return true
}

function validarFechaIncidente() {
    const iFechaIncidente = document.getElementById('fechaIncidente')
    const elTexto = iFechaIncidente.parentElement.querySelector('.form-text')
    const valor = iFechaIncidente.value.trim()

    if (!valor) {
        elTexto.textContent = "La fecha es obligatoria"
        iFechaIncidente.classList.add('is-invalid')
        return false
    }

    const fechaIngresada = new Date(valor)
    const hoy = new Date()
    hoy.setHours(0, 0, 0, 0)

    if (isNaN(fechaIngresada.getTime())) {
        elTexto.textContent = "Ingrese una fecha válida"
        iFechaIncidente.classList.add('is-invalid')
        return false
    }

    if (fechaIngresada > hoy) {
        elTexto.textContent = "La fecha no puede ser futura"
        iFechaIncidente.classList.add('is-invalid')
        return false
    }

    iFechaIncidente.classList.remove('is-invalid')
    iFechaIncidente.classList.add('is-valid')
    return true
}

function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-ajuste')
    // campos
    const iCantidad = document.getElementById('cantidad')
    const iDescripcion = document.getElementById('descripcion')
    const iFechaIncidente = document.getElementById('fechaIncidente')

    // validar al desenfocar campo o al enviar formulario
    iCantidad.addEventListener('blur', validarCantidad)
    iDescripcion.addEventListener('blur', validarDescripcion)
    iFechaIncidente.addEventListener('blur', validarFechaIncidente)
    
    formulario.addEventListener('submit', event => {
        if (!validarCantidad() || !validarDescripcion() || !validarFechaIncidente()) {
            event.preventDefault()
            event.stopPropagation()
        }
    })
}