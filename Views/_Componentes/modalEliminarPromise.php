<div class="modal fade" id="modal-eliminar">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-top: 4px solid var(--bs-danger);">
            <div class="modal-body d-flex flex-column align-items-center gap-2">
                <span style="font-size: 48px; color: var(--bs-danger);">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                </span>
                <b>¿Estas seguro?</b>
                <span class="text-secondary text-center">
                    <span class="modal-texto">Texto de muestra</span>
                    <br>
                    Esta acción no puede revertirse
                </span>
                <div class="d-flex gap-3 w-100 mt-3">
                    <button type="button" class="btn btn-outline-secondary flex-grow-1 btn-cancelar-eliminar"
                        data-bs-dismiss="modal">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger flex-grow-1 btn-eliminar">Eliminar</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // crea una promesa que abra el modal de bootstrap v5 lo muestra y devuelve la promesa que se resuelva con el boton de eliminar
    // el argumento sera texto que aparecera en el modal
    function abrirModalEliminar(texto, value = "") {
        return new Promise((resolve, reject) => {
            const modalElement = document.getElementById('modal-eliminar');
            const modal = new bootstrap.Modal(modalElement);
            document.getElementById('modal-eliminar').Modal = modal;
            const modalTexto = document.querySelector('#modal-eliminar .modal-texto');
            modalTexto.textContent = texto;
            modal.show();
            const eliminar = document.querySelector('#modal-eliminar .btn-eliminar');
            eliminar.addEventListener("click", () => {
                resolve(value);
            });

            modalElement.addEventListener('hide.bs.modal', () => {
                reject();
            })
        }).finally(() => {
            document.activeElement.blur();
            document.getElementById('modal-eliminar').Modal.hide();
        });
    }
    
</script>