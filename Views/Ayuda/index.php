<?php 
    agregarCss("ayuda");

    // cargara en un array las secciones con el require a las secciones de ayuda
    $arreglo_de_secciones = [
        "indice",
        "introduccion",
        "login",
        "interfaz",
        "areas",
        "turnos",
        "trabajador",
    ];


    
 ?>



<div class="panel-header">
    <div class="page-inner">
        <div class="d-flex align-items-center justify-content-between flex-column flex-md-row">
            <div class="text-white">
                <h3 class="pb-2">Ayuda</h3>
                <span class="opacity-75 mb-2">Modulo de ayuda para el manejo del sistema</span>
            </div>
            <div>
                <button style="padding: .65rem 1.4rem;" class="btn btn-outline-light rounded-pill" id="imprimir">
                    <i class="fa-solid fa-print me-2"></i>
                    Imprimir
                </button>
            </div>
        </div>
    </div>
</div>

<div class="page-inner mt--5">
    <div class="card border-0 box-shadow-alt">
        <div class="card-body p-4">
            <div class="ayuda-title"><h2 class="text-center">Manual de Ayuda para el sistema de gestion de tareas para la direccion de servicios generales</h2></div>
            <?php 
                foreach ($arreglo_de_secciones as $seccion) {
                    require_once("Views/Ayuda/secciones/".$seccion.".php");
                    echo "<hr>";
                }
             ?>
        </div>
    </div>
</div>

<script>
    document.getElementById("imprimir").addEventListener("click", () => {
        window.print();
    })
</script>
