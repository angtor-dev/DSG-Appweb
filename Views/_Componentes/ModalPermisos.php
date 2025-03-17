<div class="modal fade" id="modal-permisos">
    <div class="modal-dialog modal-dialog-centered modal-lg"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-permisos')

        modal.addEventListener('show.bs.modal', async e => {
            const boton = event.relatedTarget

            const id = boton.getAttribute('data-bs-id')

            let response = await fetch("<?= LOCAL_DIR ?>/Seguridad/Roles/Permisos?id=" + id)
            let data = await response.text()

            modal.innerHTML = data
        })
    })
</script>