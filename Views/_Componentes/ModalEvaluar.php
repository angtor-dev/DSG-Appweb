<div class="modal fade" id="modal-evaluar">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <!-- El contenido se cargará aquí dinámicamente -->
            <div class="d-flex justify-content-center align-items-center h-100">
                <div class="loader"></div>
            </div>
        </div>
    </div>
</div>


<style>
/* Añade esto en tu CSS */
#modal-tareas .modal-body {
    min-height: 60vh; /* Altura mínima para evitar saltos */
    overflow-y: auto; /* Scroll si el contenido es muy largo */
}

#modal-tareas .tab-content {
    height: 100%;
}

#modal-tareas .tab-pane {
    height: 100%;
    overflow: hidden; /* Evita desbordamientos */
}
</style>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('modal-orden');
    const modalContent = modal.querySelector('.modal-content');
    let bsModal = bootstrap.Modal.getOrCreateInstance(modal);

  

    // Limpiar el modal al cerrarse (opcional)
    modal.addEventListener('hidden.bs.modal', function() {
        modalContent.innerHTML = '<div class="d-flex justify-content-center align-items-center h-100"><div class="loader"></div></div>';
    });
});
</script>