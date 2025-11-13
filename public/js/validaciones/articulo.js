function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-articulo');
    if (!formulario) return;
    // campos
    const iNombre = document.getElementById('nombre')
    const iDescripcion = document.getElementById('descripcion')
    const iCategoria = document.getElementById('idCategoria')
    const iMedida = document.getElementById('idMedida')

    // crear handlers de validación para cada campo
    const validarNombre = () => validarCampoTexto(iNombre, {
        isRequired: true,
        minLength: 2,
    })
    const validarDescripcion = () => validarCampoTexto(iDescripcion, {
        maxLength: 100,
        allowOnlyNumbers: true
    })
    const validarCategoria = () => validarCampoSelect(iCategoria, 'Debe seleccionar una categoría válida')
    const validarMedida = () => validarCampoSelect(iMedida, 'Debe seleccionar una medida válida')

    // validar al desenfocar campo o al enviar formulario
    iNombre?.addEventListener('input', validarNombre)
    iDescripcion?.addEventListener('input', validarDescripcion)
    $(iCategoria).on('change', validarCategoria)
    $(iMedida).on('change', validarMedida)
    
    formulario.addEventListener('submit', event => {
        const btnSubmit = formulario.querySelector('button[type="submit"]');
        if (btnSubmit) btnSubmit.disabled = true;

        const esNombreValido = validarNombre();
        const esDescValida = validarDescripcion();
        const esCatValida = validarCategoria();
        const esMedidaValida = validarMedida();

        const todoValido = esNombreValido && esDescValida && esCatValida && esMedidaValida;

        if (!todoValido) {
            event.preventDefault();
            event.stopPropagation();
            if (btnSubmit) btnSubmit.disabled = false;
        }
    })
}