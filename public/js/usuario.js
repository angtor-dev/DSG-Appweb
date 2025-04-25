const caracteres = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
    + "0123456789!@#$%^&*()_-+={}[];':\"\\|,.<>/?";
const longitudClave = 12
const regClave = /^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{6,}$/


document.querySelectorAll('.toggle-password').forEach(el => {
    el.addEventListener("click", alternarClave)
})

function claveSegura() {
    let clave = "";

    for (let i = 0; i < longitudClave; i++) {
        let indiceAleatorio = Math.floor(Math.random() * caracteres.length)
        clave += caracteres.charAt(indiceAleatorio)
    }

    if(!regClave.test(clave)){
        clave = claveSegura();
    }

    return clave
}

function generarClave() {
    const claveEl = document.getElementById('clave')
    const clave = claveSegura()

    claveEl.value = clave
    navigator.clipboard.writeText(clave)
    claveEl.dispatchEvent(new Event('blur'))
}

function alternarClave(event) {
    const boton = event.currentTarget
    const input = boton.parentElement.querySelector('input')

    if (input.type == 'password') {
        input.type = 'text'
        boton.classList.add('show')
    } else {
        input.type = 'password'
        boton.classList.remove('show')
    }
}