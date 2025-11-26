function agregarValidaciones() {
    // formulario
    const formulario = document.getElementById('form-departamento');
    if (!formulario) {
        console.log("No se encontro el formulario form-departamento");
        return
    };
    // campos
    const iNombre = document.getElementById('nombre')

    // crear handlers de validación para cada campo
    const validarNombre = () => validarCampoTexto(iNombre, {
        isRequired: true,
        minLength: 2,
    })

    // validar al desenfocar campo o al enviar formulario
    iNombre?.addEventListener('input', validarNombre)
    
    formulario.addEventListener('submit', event => {
        const btnSubmit = document.querySelector('button[type="submit"][form="form-departamento"]');
        if (btnSubmit) btnSubmit.disabled = true;

        const esNombreValido = validarNombre();

        const todoValido = esNombreValido;

        if (!todoValido) {
            event.preventDefault();
            event.stopPropagation();
            if (btnSubmit) btnSubmit.disabled = false;
        }
    })
}