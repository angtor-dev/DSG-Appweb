<?php
requiereAutenticacion();
requierePermiso(Modulo::AREAS, Permiso::CONSULTAR);
require_once "Models/Area.php";

/** @var Area[] $areas */
$areas = Area::listar();

foreach ($areas as $area) {
    if ($area->idArea != null) {
        $areaPadreArray = array_filter($areas, fn($a) => $a->id == $area->idArea);
        $area->areaPadre = reset($areaPadreArray);
    }
}

renderView();