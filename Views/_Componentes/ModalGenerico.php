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

            let response = fetch(url).then(response => {
                console.log(response)
                if (!response.ok) {
                    throw new Error(response.statusText)
                }
                response.text().then(data => {
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

            }).catch(error => {
                let modal = bootstrap.Modal.getOrCreateInstance(document.getElementById('modal-generico'));
                setTimeout(() => {
                    modal.hide()
                    mostrarError("Ha ocurrido un error en la solicitud")
                    console.error("modal-generico", error)
                }, 600)
            })
        })
    })
</script>