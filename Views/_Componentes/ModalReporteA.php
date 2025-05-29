<div class="modal fade" id="modal-estadistica">
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
