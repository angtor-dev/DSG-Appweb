<div class="modal fade" id="modal-generico">
    <div class="modal-dialog"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-generico')

        modal.addEventListener('show.bs.modal', async e => {
            const boton = event.relatedTarget

            const url = boton.getAttribute('data-bs-url')

            let response = await fetch(url)
            let data = await response.text()

            if (!response.ok) {
                mostrarError("Ha ocurrido un error al cargar el modal")
                console.error(data)
                return
            }

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
    })
</script>