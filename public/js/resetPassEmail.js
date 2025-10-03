
    const form = document.getElementById('form-reset')
    function mostrarError(mensaje) {
        const error = document.getElementById('error');
        error.textContent = mensaje;
        error.classList.remove('text-success');
        error.classList.add('text-danger');
    }
    function mostrarExito(mensaje) {
        const error = document.getElementById('error');
        error.textContent = mensaje;
        error.classList.remove('text-danger');
        error.classList.add('text-success');
    }

    form.addEventListener('submit',  (e) => {
        e.preventDefault();
        mostrarError("");
        if(form.correo.disabled) return;

        const correo = form.correo.value.trim();

        peticionPromisse('/Login/ResetPassword', {
            method: 'POST',
            body: JSON.stringify({
                correo: correo,
                action: "Enviar"
            }),
            useLoader: document.getElementById('main'),
            before: () => {
                form.correo.disabled = true;
            },
        }).then((respuesta) => {
            respuesta = parsearJson(respuesta);
            if (respuesta.success) {
                mostrarExito(respuesta.message);
                setTimeout(() => {
                    window.location.href = LOCAL_DIR+"/Login/newPassword";
                }, 2000);
            } else {
                mostrarError(respuesta.message);
            }
        }).finally(() => {
            form.correo.disabled = false;
        })

        

    });
