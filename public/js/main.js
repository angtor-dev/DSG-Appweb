/** Logica y scripts de la plantilla principal */

// Funcionalidad del menu lateral
(() => {
    const boton = document.getElementById('sidebar-toggle')
    const menu = document.getElementById('menu-lateral')
    const header = document.querySelector('.main-header')
    const contenido = document.querySelector('.main-content')

    boton.addEventListener('click', e => {
        menu.classList.toggle('sidebar-hide')
        header.classList.toggle('sidebar-hide')
        contenido.classList.toggle('sidebar-hide')
    })
})()