<?php

    $articuloObj = new Tarea();
    $materiales = $articuloObj->listarConCategoriaYUnidad();

    header('Content-Type: application/json');
    echo json_encode($materiales);
    exit;
