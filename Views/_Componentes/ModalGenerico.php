<div class="modal fade" id="modal-generico">
    <div class="modal-dialog"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-generico')

        modal.addEventListener('show.bs.modal', e => {
            const boton = event.relatedTarget
            // muestra div con la clase loader
            modal.innerHTML = `<div class="modal-dialog modal-lg">
                                    <div class="modal-content" style="height: calc(50vh);">
                                        <div class="loader"></div>
                                    </div>
                                </div>
            `;


            const url = boton.getAttribute('data-bs-url')

            let response = fetch(url,{cache: "no-store"})
            .then(response => {
                return response.text().then(data => {
                    if(!response.ok){
                        // si la respuesta tiene un status 4xx
                        // mostrara la respuesta si es un string valido si no mostrara el error generico
                        if(/^4\d\d$/.test(response.status) && /^[a-zA-Zá-íÁ-Í0-9\s]+$/.test(data)){
                            throw new Error("show::"+data);
                        }
                        else{
                            throw new Error(response.statusText)
                        }
                    }
                    return data
                })
            })
            .then(data => {
                modal.innerHTML = data
                const form = modal.querySelector('form')
                form.action = url
                const selects2 = modal.querySelectorAll('.select2')
                selects2.forEach(s => $(s).select2({
                    dropdownParent: $('#modal-generico')
                }))

                if (typeof agregarValidaciones === 'function') {
                    agregarValidaciones()
                }
            })
            .catch((error) => {
                // para mostrar un error personalizado la respuesta debio haber tenido un codigo 4xx
                // y ser un string valido

                // para asegurarme de que el mensaje no sea demasiado largo
                let truncateString = function(str, maxLength) {
                    if (str.length+6 > maxLength) {
                        return str.slice(6, maxLength - 3) + '...';
                    }
                    return str.slice(6);
                }
                let modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-generico'));
                setTimeout(() => {
                    modal.hide()
                    if(error.message.startsWith("show::")){
                        mostrarError(truncateString(error.message,150));
                    }
                    else{
                        mostrarError("Ha ocurrido un error en la solicitud")
                        console.error("modal-generico", error)
                    }
                }, 600)
            })
        })
    })
</script>