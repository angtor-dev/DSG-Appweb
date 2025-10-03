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


    const form = document.getElementById('form-reset')
form.addEventListener('submit',  (e) => {
        e.preventDefault();
        mostrarError("");
        if(form.code.disabled) return;

        
        const clave = form.clave.value.trim();
        const clave_comp = form.clave_comp.value.trim();
        const code = form.code.value.trim();
        //La clave debe tener al menos 6 caracteres, una letra mayúscula, una letra minúscula y un número
        if (!/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9]).{6,}$/.test(clave)) {
            return mostrarError("La clave debe tener al menos 6 caracteres, una letra mayúscula, una letra minúscula y un número");
        }
        if(clave != clave_comp) return mostrarError("Las contraseñas no coinciden");


        peticionPromisse('/Login/newPassword', {
            method: 'POST',
            body: JSON.stringify({
                code: code,
                clave: clave,
                action: "reset"
            }),
            useLoader: document.getElementById('main'),
            before: () => {
                form.code.disabled = true;
            },
        }).then((respuesta) => {
            respuesta = parsearJson(respuesta);
            if (respuesta.success) {
                mostrarExito(respuesta.message);
                mostrarLoader( document.getElementById('main') );
                setTimeout(() => {
                    window.location.href = LOCAL_DIR+"/Login";
                }, 3000);
            } else {
                mostrarError("El Proceso no puede continuar intente nuevamente");
                setTimeout(() => {
                    window.location.href = LOCAL_DIR+"/Login";
                }, 3000);
            }
        }).finally(() => {
            form.code.disabled = false;
        })

        

    });