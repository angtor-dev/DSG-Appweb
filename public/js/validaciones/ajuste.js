function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-ajuste')
    if (!formulario) return;
    // campos
    const iArticulo = document.getElementById('idInventario')
    const iCantidad = document.getElementById('cantidad')
    const iFechaIncidente = document.getElementById('fechaIncidente')
    const iDescripcion = document.getElementById('descripcion')

    // crear handlers de validación para cada campo
    const validarArticulo = () => validarCampoSelect(iArticulo, 'Debe seleccionar un artículo')
    const validarCantidad = () => validarCampoNumerico(iCantidad, {
        isRequired: true,
    })
    const validarFechaIncidente = () => validarCampoFecha(iFechaIncidente, {
        isRequired: true,
        allowFutureDate: false
    })
    const validarDescripcion = () => validarCampoTexto(iDescripcion, {
        isRequired: true,
        allowOnlyNumbers: false,
        minLength: 3,
        maxLength: 200
    })

    // validar al desenfocar campo o al enviar formulario
    $(iArticulo).on('change', validarArticulo)
    iCantidad?.addEventListener('input', validarCantidad)
    iFechaIncidente?.addEventListener('input', validarFechaIncidente)
    iDescripcion?.addEventListener('input', validarDescripcion)
    
    formulario.addEventListener('submit', event => {
        const btnSubmit = document.querySelector('button[type="submit"][form="form-ajuste"]');
        if (btnSubmit) btnSubmit.disabled = true;

        const esArticuloValido = validarArticulo();
        const esCantidadValida = validarCantidad();
        const esFechaIncidenteValida = validarFechaIncidente();
        const esDescripcionValida = validarDescripcion();

        const todoValido = esArticuloValido && esCantidadValida && esFechaIncidenteValida && esDescripcionValida;

        if (!todoValido) {
            event.preventDefault()
            event.stopPropagation()
            if (btnSubmit) btnSubmit.disabled = false;
        }
    })
}