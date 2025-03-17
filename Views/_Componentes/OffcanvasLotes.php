<div class="offcanvas offcanvas-end" id="offcanvas-lotes">
    <div class="offcanvas-dialog offcanvas-dialog-centered offcanvas-lg"></div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const offcanvas = document.getElementById('offcanvas-lotes')

        offcanvas.addEventListener('show.bs.offcanvas', async e => {
            const boton = event.relatedTarget

            const id = boton.getAttribute('data-bs-id')

            let response = await fetch("<?= LOCAL_DIR ?>/Insumos/Lotes?id=" + id)
            let data = await response.text()

            offcanvas.innerHTML = data
        })
    })
</script>