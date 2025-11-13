// formulario
const formulario = document.getElementById('form-rol')
// campos
const iNombre = document.getElementById('nombre')
const iDescripcion = document.getElementById('descripcion')

// expresiones regulares
//const regAlfanumerico = /^[A-Za-zá-úÁ-ÚñÑ0-9., ]*$/

// validaciones

document.addEventListener('DOMContentLoaded', () => {
    addValidAlfaNum(iNombre, true, 20);
    addValidAlfaNum(iDescripcion, false, 200);
})

// validar al desenfocar campo o al enviar formulario

formulario.addEventListener('submit', event => {
    if (!checkValidStatus([iNombre, iDescripcion])) {
        event.preventDefault()
        event.stopPropagation()
    }
    else{
        mostrarLoader("body", true);
    }
})