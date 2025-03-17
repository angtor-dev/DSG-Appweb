<div class="modal fade" id="modal-servicios">
    <div class="modal-dialog modal-dialog-centered modal-lg"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-servicios')

        modal.addEventListener('show.bs.modal', async e => {
            console.log("asd")
            const boton = event.relatedTarget

            const id = boton.getAttribute('data-bs-id')

            let response = await fetch("<?= LOCAL_DIR ?>/Citas/Servicios?id=" + id)
            let data = await response.text()

            modal.innerHTML = data
        })
    })
</script>