<div class="modal fade" id="modal-eliminar">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-top: 4px solid var(--bs-danger);">
            <div class="modal-body d-flex flex-column align-items-center gap-2">
                <span style="font-size: 48px; color: var(--bs-danger);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                <b>¿Estas seguro?</b>
                <span class="text-secondary text-center">
                    ¿Quieres eliminar <span class="modelo"></span> <b class="nombre"></b>?
                    <br>
                    Esta acción no puede revertirse
                </span>
                <div class="d-flex gap-3 w-100 mt-3">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <a href="#" class="btn btn-danger flex-grow-1 eliminar">
                        Eliminar
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', e => {
        const modal = document.getElementById('modal-eliminar')

        modal.addEventListener('show.bs.modal', e => {
            const boton = event.relatedTarget

            const modelo = boton.getAttribute('data-bs-modelo')
            const nombre = boton.getAttribute('data-bs-nombre')
            const url = boton.getAttribute('data-bs-url')

            const modeloEl = modal.querySelector('.modelo')
            const nombreEl = modal.querySelector('.nombre')
            const eliminarEl = modal.querySelector('.eliminar')

            modeloEl.textContent = modelo
            nombreEl.textContent = nombre
            eliminarEl.href = url
        })
    })
</script>